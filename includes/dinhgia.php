<?php
function tinhGiaDong($mysqli, $machuyenbay) {
    $machuyenbay = mysqli_real_escape_string($mysqli, $machuyenbay);

    $sql = "SELECT cb.THOIGIANDI, tb.GIACOBAN, mb.MAMAYBAY,
            (SELECT COUNT(*) FROM ghe WHERE MAMAYBAY = mb.MAMAYBAY) AS tong_ghe,
            (SELECT COUNT(*) FROM ve WHERE MACHUYENBAY = cb.MACHUYENBAY) AS da_ban
            FROM chuyenbay cb
            JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
            JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
            WHERE cb.MACHUYENBAY = '$machuyenbay' LIMIT 1";

    $res = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($res);

    if (!$row || $row['tong_ghe'] == 0) return 0;

    $gia_goc = (float)$row['GIACOBAN'];
    
    // dat som gia re
    $hours = (strtotime($row['THOIGIANDI']) - time()) / 3600;
    if ($hours > 168) $hs_time = 1.0;     
    elseif ($hours > 72) $hs_time = 1.2;  
    else $hs_time = 1.5;                 

    // full ghees
    $tyle = $row['da_ban'] / $row['tong_ghe'];
    if ($tyle < 0.3) $hs_seat = 0.9;      
    elseif ($tyle < 0.7) $hs_seat = 1.2;
    else $hs_seat = 1.6;            
    
    //lee
$ngaybay = date('m-d', strtotime($row['THOIGIANDI']));
$hs_holiday = 1.0;

if ($ngaybay == '04-30' || $ngaybay == '05-01') {
    $hs_holiday = 1.5; // tăng 50%
}

    // gio cao diem
    $gio = (int)date('H', strtotime($row['THOIGIANDI']));
    $hs_peak = ($gio >= 7 && $gio <= 12) ? 1.2 : 1.0;

return round($gia_goc * $hs_time * $hs_seat * $hs_peak * $hs_holiday, -3);
}

function tinhGiaVeDayDu($mysqli, $machuyenbay) {
    $machuyenbay = mysqli_real_escape_string($mysqli, $machuyenbay);
    $sql = "SELECT 
            (SELECT COUNT(*) FROM ghe g JOIN chuyenbay cb ON g.MAMAYBAY = cb.MAMAYBAY WHERE cb.MACHUYENBAY = '$machuyenbay') AS tong,
            (SELECT COUNT(*) FROM ve WHERE MACHUYENBAY = '$machuyenbay') AS da_ban";
    $res = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($res);

    return [
        'tong_gia' => tinhGiaDong($mysqli, $machuyenbay),
        'tong_ghe' => (int)($row['tong'] ?? 0),
        'so_ghe_con' => (int)(($row['tong'] ?? 0) - ($row['da_ban'] ?? 0))
    ];
}

/**
 * Giá đầy đủ theo:
 * giá động chuyến bay × hệ số hãng × hệ số loại ghế -> trang ds chuyến bay và chọn hạng vé
 */
function tinhGiaTheoLoaiVe($mysqli, $machuyenbay, $maloaighe) {
    $machuyenbay = mysqli_real_escape_string($mysqli, $machuyenbay);
    $maloaighe = mysqli_real_escape_string($mysqli, $maloaighe);

    $giaDong = tinhGiaDong($mysqli, $machuyenbay);
    if ($giaDong <= 0) return 0;

    $sql = "SELECT hmb.HESO_GIA AS HESO_HANG, lg.HESO_GIA AS HESO_LOAIGHE
            FROM chuyenbay cb
            JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
            JOIN hangmaybay hmb ON mb.MAHANG = hmb.MAHANG
            JOIN loaighe lg ON lg.MALOAIGHE = '$maloaighe'
            WHERE cb.MACHUYENBAY = '$machuyenbay'
            LIMIT 1";

    $res = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($res);

    if (!$row) return $giaDong;

    $hsHang = (float)$row['HESO_HANG'];
    $hsLoaiGhe = (float)$row['HESO_LOAIGHE'];

    return round($giaDong * $hsHang * $hsLoaiGhe, -3);
}

?>