<?php
include('../../config/config.php');

if (!isset($mysqli)) {
    die("❌ Chưa kết nối database!");
}
$TENMAYBAY = mysqli_real_escape_string($mysqli, $_POST['TENMAYBAY'] ?? '');
$MAHANG    = mysqli_real_escape_string($mysqli, $_POST['MAHANG'] ?? '');
$SOGHE_FC  = (int)($_POST['SOGHE_HANGNHAT'] ?? 0); 
$SOGHE_BC  = (int)($_POST['SOGHE_THUONGGIA'] ?? 0); 
$SOGHE_EC  = (int)($_POST['SOGHE_PHOTHONG'] ?? 0); 
function redirectBack($mysqli) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location:../../index.php?action=quanlymaybay&query=lietke");
    }
    exit();
}
if (isset($_POST['them_maybay'])) {
    $MAMAYBAY = mysqli_real_escape_string($mysqli, $_POST['MAMAYBAY']);
    // Kiểm tra trùng mã
    $check = mysqli_query($mysqli, "SELECT 1 FROM maybay WHERE MAMAYBAY='$MAMAYBAY'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Mã máy bay đã tồn tại!'); history.back();</script>";
        exit();
    }
    $sql = "INSERT INTO maybay (MAMAYBAY, TENMAYBAY, SOGHE_FC, SOGHE_BC, SOGHE_EC, MAHANG)
            VALUES ('$MAMAYBAY', '$TENMAYBAY', $SOGHE_FC, $SOGHE_BC, $SOGHE_EC, '$MAHANG')";
    if (mysqli_query($mysqli, $sql)) {
        taoDanhSachGhe($mysqli, $MAMAYBAY, $SOGHE_FC, $SOGHE_BC, $SOGHE_EC);
        redirectBack($mysqli);
    } else {
        die("❌ Lỗi INSERT: " . mysqli_error($mysqli));
    }
}
if (isset($_POST['sua_maybay'])) {
    $MAMAYBAY = mysqli_real_escape_string($mysqli, $_POST['MAMAYBAY']);
    // Kiểm tra logic 10 ngày (ngăn chặn bypass từ phía người dùng)
    $check_cb = mysqli_query($mysqli, "SELECT MIN(THOIGIANDI) as min_time FROM chuyenbay WHERE MAMAYBAY='$MAMAYBAY' AND THOIGIANDI >= NOW()");
    $row_cb = mysqli_fetch_assoc($check_cb);
    if ($row_cb['min_time']) {
        $days_diff = (strtotime($row_cb['min_time']) - time()) / (60 * 60 * 24);
        if ($days_diff <= 10) {
            echo "<script>alert('❌ Không thể sửa: Chuyến bay sắp khởi hành trong vòng 10 ngày!'); history.back();</script>";
            exit();
        }
    }
    $sql = "UPDATE maybay SET
                TENMAYBAY='$TENMAYBAY',
                SOGHE_FC=$SOGHE_FC,
                SOGHE_BC=$SOGHE_BC,
                SOGHE_EC=$SOGHE_EC,
                MAHANG='$MAHANG'
            WHERE MAMAYBAY='$MAMAYBAY'";
    if (mysqli_query($mysqli, $sql)) {
        // Xóa ghế cũ và tạo lại ghế mới dựa trên cấu hình vừa sửa
        mysqli_query($mysqli, "DELETE FROM ghe WHERE MAMAYBAY='$MAMAYBAY'");
        taoDanhSachGhe($mysqli, $MAMAYBAY, $SOGHE_FC, $SOGHE_BC, $SOGHE_EC);
        redirectBack($mysqli);
    } else {
        die("❌ Lỗi UPDATE: " . mysqli_error($mysqli));
    }
}

if (isset($_GET['type']) && $_GET['type'] == 'xoa') {
    $MAMAYBAY = mysqli_real_escape_string($mysqli, $_GET['MAMAYBAY']);
    // không cho xóa nếu máy bay đã có chuyến bay (bất kể thời gian)
    $check = mysqli_query($mysqli, "SELECT 1 FROM chuyenbay WHERE MAMAYBAY='$MAMAYBAY' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Không thể xóa: Máy bay này đã có dữ liệu chuyến bay!'); history.back();</script>";
        exit();
    }
    // Xóa ghế trước, xóa máy bay sau (khóa ngoại)
    mysqli_query($mysqli, "DELETE FROM ghe WHERE MAMAYBAY='$MAMAYBAY'");
    if (mysqli_query($mysqli, "DELETE FROM maybay WHERE MAMAYBAY='$MAMAYBAY'")) {
        redirectBack($mysqli);
    } else {
        die("❌ Lỗi xóa: " . mysqli_error($mysqli));
    }
}
function taoDanhSachGhe($mysqli, $maMB, $fc, $bc, $ec) {
    $data = [];
    $stt = 1;
    $configs = [
        ['prefix' => 'F', 'loai' => 'FC', 'sl' => $fc, 'gia' => 16000000], // First Class
        ['prefix' => 'B', 'loai' => 'BC', 'sl' => $bc, 'gia' => 8000000],  // Business Class
        ['prefix' => 'E', 'loai' => 'EC', 'sl' => $ec, 'gia' => 4000000]   // Economy Class
    ];

    foreach ($configs as $c) {
        for ($i = 1; $i <= $c['sl']; $i++) {
            $maghe = $maMB . '-' . $c['prefix'] . $i;
            $data[] = "('$maghe', '$maMB', '{$c['loai']}', $stt, {$c['gia']}, 'TRONG')";
            $stt++;
        }
    }

    if (!empty($data)) {
        $sql = "INSERT INTO ghe (MAGHE, MAMAYBAY, MALOAIGHE, SOGHE, GIAGHE, TRANGTHAI)
                VALUES " . implode(',', $data);
        mysqli_query($mysqli, $sql);
    }
}
?>
