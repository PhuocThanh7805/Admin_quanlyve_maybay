<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$now = time();
$sql = "SELECT * FROM chuyenbay";
$result = mysqli_query($mysqli, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $machuyenbay = mysqli_real_escape_string($mysqli, $row['MACHUYENBAY']);
        $trangthai_hientai = (int)$row['TRANGTHAI_CB'];
        $di  = strtotime($row['THOIGIANDI']);
        $den = strtotime($row['THOIGIANDEN']);
        if (!$di || !$den) continue;
        // chi duoc huy neu ch bay
        if ($trangthai_hientai === 0 && $now < $di) continue;

        // tinh tt
        if ($now < ($di - 7200)) {
            $trangthai_moi = 1;
        } 
        elseif ($now < $di) {
            $trangthai_moi = 2;
        } 
        elseif ($now < $den) {
            $trangthai_moi = 3;
        } 
        else {
            $trangthai_moi = 4;
        }
        if ($trangthai_hientai !== $trangthai_moi) {
            mysqli_query($mysqli, "
                UPDATE chuyenbay 
                SET TRANGTHAI_CB = $trangthai_moi
                WHERE MACHUYENBAY = '$machuyenbay'
            ");
        }
    }
}
?>