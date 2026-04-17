<?php
session_start();
include('../../config/config.php');
function escape($mysqli, $data) {
    return mysqli_real_escape_string($mysqli, $data);
}
function goForm($msg, $action){
    echo "<script>
            alert('$msg');
            window.location.href='../../index.php?action=$action';
          </script>";
    exit();
}
function goList($msg, $action){
    echo "<script>
            alert('$msg');
            window.location.href='../../index.php?action=$action&query=lietke';
          </script>";
    exit();
}
if(isset($_POST['themchuyenbay'])){

    $machuyenbay = strtoupper(escape($mysqli, $_POST['MACHUYENBAY'] ?? ''));
    $mamaybay    = escape($mysqli, $_POST['MAMAYBAY'] ?? '');
    $matuyen     = strtoupper(escape($mysqli, $_POST['MATUYEN'] ?? ''));
    $ngaydi      = $_POST['NGAYDI'] ?? '';
    $giodi       = $_POST['GIODI'] ?? '';

    if(empty($machuyenbay) || empty($mamaybay) || empty($matuyen) || empty($ngaydi) || empty($giodi)){
        goForm("Vui lòng nhập đầy đủ!", "them");
    }

    $thoigiandi = $ngaydi . ' ' . $giodi . ':00';
    $time_start = strtotime($thoigiandi);

    // ko cho <nơ
    if($time_start < time()){
        goForm("❌ Không được chọn thời gian trong quá khứ!", "them");
    }

    // truoc 10 da
    if($time_start < strtotime("+10 days")){
        goForm("❌ Phải tạo chuyến bay trước ít nhất 10 ngày!", "them");
    }

    // check trùng mã
    $check = mysqli_query($mysqli, "SELECT 1 FROM chuyenbay WHERE MACHUYENBAY='$machuyenbay'");
    if(mysqli_num_rows($check) > 0){
        goForm("❌ Mã chuyến bay đã tồn tại!", "them");
    }

    // lấy thời gian bay
    $res = mysqli_query($mysqli, "SELECT THOIGIANBAY FROM tuyenbay WHERE MATUYEN='$matuyen'");
    if(mysqli_num_rows($res) == 0){
        goForm("❌ Tuyến bay không tồn tại!", "them");
    }

    $tuyen = mysqli_fetch_assoc($res);

    $thoigianden = date('Y-m-d H:i:s',
        strtotime("+{$tuyen['THOIGIANBAY']} minutes", $time_start)
    );

    // check trùng máy bay
    $check_overlap = mysqli_query($mysqli, "
        SELECT 1 FROM chuyenbay
        WHERE MAMAYBAY='$mamaybay'
        AND (
            ('$thoigiandi' < THOIGIANDEN)
            AND ('$thoigianden' > THOIGIANDI)
        )
    ");

    if(mysqli_num_rows($check_overlap) > 0){
        goForm("❌ Máy bay đang bận có chuyến bay!", "them");
    }

    // insert
    mysqli_query($mysqli, "
        INSERT INTO chuyenbay 
        (MACHUYENBAY, MAMAYBAY, MATUYEN, THOIGIANDI, THOIGIANDEN, TRANGTHAI_CB)
        VALUES 
        ('$machuyenbay','$mamaybay','$matuyen','$thoigiandi','$thoigianden',1)
    ");

    goList("✅ Thêm thành công!", "quanlychuyenbay");
}

elseif(isset($_POST['suachuyenbay'])){
    $machuyenbay = escape($mysqli, $_POST['MACHUYENBAY'] ?? '');
    $mamaybay  = escape($mysqli, $_POST['MAMAYBAY'] ?? '');
    $matuyen = escape($mysqli, $_POST['MATUYEN'] ?? '');
    $ngaydi = $_POST['NGAYDI'] ?? '';
    $giodi = $_POST['GIODI'] ?? '';

    if(empty($machuyenbay) || empty($mamaybay) || empty($matuyen) || empty($ngaydi) || empty($giodi)){
        goList("❌ Thiếu dữ liệu", "quanlychuyenbay");
    }

    // cho sua khi ch bay
    $check = mysqli_query($mysqli, "SELECT TRANGTHAI_CB FROM chuyenbay WHERE MACHUYENBAY='$machuyenbay'");
    $row = mysqli_fetch_assoc($check);

    if($row['TRANGTHAI_CB'] != 1){
        goList("❌ Chỉ được sửa chuyến chưa bay!", "quanlychuyenbay");
    }

    $thoigiandi = $ngaydi . ' ' . $giodi . ':00';
    $time_start = strtotime($thoigiandi);

    if($time_start < time()){
        goList("❌ Không chọn thời gian quá khứ", "quanlychuyenbay");
    }

    if($time_start < strtotime("+10 days")){
        goList("❌ Phải chỉnh trước ít nhất 10 ngày", "quanlychuyenbay");
    }

    $res = mysqli_query($mysqli, "SELECT THOIGIANBAY FROM tuyenbay WHERE MATUYEN='$matuyen'");
    $tuyen = mysqli_fetch_assoc($res);

    $thoigianden = date('Y-m-d H:i:s',
        strtotime("+{$tuyen['THOIGIANBAY']} minutes", $time_start)
    );

    // check trùng máy bay
    $check = mysqli_query($mysqli, "
        SELECT 1 FROM chuyenbay
        WHERE MAMAYBAY='$mamaybay'
        AND MACHUYENBAY != '$machuyenbay'
        AND (
            ('$thoigiandi' < THOIGIANDEN)
            AND ('$thoigianden' > THOIGIANDI)
        )
    ");

    if(mysqli_num_rows($check) > 0){
        goList("❌ Máy bay bị trùng lịch", "quanlychuyenbay");
    }

    mysqli_query($mysqli, "
        UPDATE chuyenbay SET
            MAMAYBAY='$mamaybay',
            MATUYEN='$matuyen',
            THOIGIANDI='$thoigiandi',
            THOIGIANDEN='$thoigianden'
        WHERE MACHUYENBAY='$machuyenbay'
    ");

    goList("✅ Cập nhật thành công", "quanlychuyenbay");
}

elseif(isset($_GET['type']) && $_GET['type'] == 'huy'){
    $id = escape($mysqli, $_GET['machuyenbay']);
    $res = mysqli_query($mysqli, "SELECT TRANGTHAI_CB FROM chuyenbay WHERE MACHUYENBAY='$id'");
    $row = mysqli_fetch_assoc($res);
    if($row['TRANGTHAI_CB'] != 1){
        goList("❌ Chỉ được hủy chuyến chưa bay!", "quanlychuyenbay");
    }
    mysqli_query($mysqli, "
        UPDATE chuyenbay 
        SET TRANGTHAI_CB = 0 
        WHERE MACHUYENBAY='$id'
    ");
    goList("✅ Đã hủy chuyến!", "quanlychuyenbay");
}
?>
