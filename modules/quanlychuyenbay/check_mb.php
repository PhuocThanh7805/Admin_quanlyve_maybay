<?php
include('connect.php');
header('Content-Type: application/json');

$mb = $_POST['maybay'] ?? '';
$time = $_POST['datetime'] ?? '';

if (!$mb || !$time) {
    echo json_encode(['error' => true, 'message' => 'Thiếu dữ liệu']);
    exit;
}

//chuyenbay gan
$sql = "
SELECT t.SANBAYDEN, cb.THOIGIANDEN
FROM chuyenbay cb
JOIN tuyenbay t ON cb.MATUYEN = t.MATUYEN
WHERE cb.MAMAYBAY = '$mb'
AND cb.THOIGIANDEN <= '$time'
ORDER BY cb.THOIGIANDEN DESC
LIMIT 1
";

$res = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($res);
//vitrimaby
$currentAirport = $row['SANBAYDEN'] ?? null;

//may bay dang bay ko cho tao
$sql_check_flying = "
SELECT *
FROM chuyenbay
WHERE MAMAYBAY = '$mb'
AND THOIGIANDI <= '$time'
AND THOIGIANDEN >= '$time'
LIMIT 1
";

$res2 = mysqli_query($mysqli, $sql_check_flying);

if (mysqli_num_rows($res2) > 0) {
    echo json_encode([
        'error' => true,
        'message' => 'Máy bay đang bay trong thời gian này'
    ]);
    exit;
}

if ($currentAirport) {
    $sql_route = "
    SELECT tb.MATUYEN, tb.SANBAYDI, tb.SANBAYDEN,
           dd1.TENDIADIEM AS DI,
           dd2.TENDIADIEM AS DEN,
           tb.THOIGIANBAY
    FROM tuyenbay tb
    JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
    JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
    JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
    JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
    WHERE tb.SANBAYDI = '$currentAirport'
    ";
} else {
    // Máy bay mới → lấy tất cả tuyến
    $sql_route = "
    SELECT tb.MATUYEN, tb.SANBAYDI, tb.SANBAYDEN,
           dd1.TENDIADIEM AS DI,
           dd2.TENDIADIEM AS DEN,
           tb.THOIGIANBAY
    FROM tuyenbay tb
    JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
    JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
    JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
    JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
    ";
}

$route_res = mysqli_query($mysqli, $sql_route);

$data = [];
while ($r = mysqli_fetch_assoc($route_res)) {
    $data[] = $r;
}

echo json_encode([
    'error' => false,
    'current_airport' => $currentAirport,
    'routes' => $data
]);