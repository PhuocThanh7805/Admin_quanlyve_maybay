<?php
include('../../config/config.php');

/* ================== CHECK TUYẾN CÓ CHUYẾN BAY ================== */
function checkTuyenCoTheSuaXoa($mysqli, $matuyen){
    $matuyen = mysqli_real_escape_string($mysqli, $matuyen);

    // 🔥 CHECK TẤT CẢ CHUYẾN BAY (QUAN TRỌNG)
    $sql = "SELECT 1 FROM chuyenbay WHERE MATUYEN = '$matuyen' LIMIT 1";
    $res = mysqli_query($mysqli, $sql);

    return mysqli_num_rows($res) == 0; // true = không có chuyến bay → cho xóa/sửa
}

/* ================== THÊM TUYẾN BAY ================== */
if(isset($_POST['themtuyenbay'])){

    $matuyen = mysqli_real_escape_string($mysqli, $_POST['MATUYEN']);
    $sb_di   = mysqli_real_escape_string($mysqli, $_POST['SANBAYDI']);
    $sb_den  = mysqli_real_escape_string($mysqli, $_POST['SANBAYDEN']);
    $gia     = mysqli_real_escape_string($mysqli, $_POST['GIACOBAN']);
    $time    = mysqli_real_escape_string($mysqli, $_POST['THOIGIANBAY']);

    // Check trùng sân bay
    if($sb_di == $sb_den){
        echo "<script>alert('❌ Sân bay đi và đến không được trùng nhau!'); history.back();</script>";
        exit();
    }

    // Check trùng mã tuyến
    $check = mysqli_query($mysqli, "SELECT 1 FROM tuyenbay WHERE MATUYEN='$matuyen'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('❌ Mã tuyến đã tồn tại!'); history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO tuyenbay(MATUYEN, SANBAYDI, SANBAYDEN, GIACOBAN, THOIGIANBAY)
            VALUES('$matuyen','$sb_di','$sb_den','$gia','$time')";

    if(mysqli_query($mysqli, $sql)){
        header('Location:../../index.php?action=lietke_tuyenbay');
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($mysqli);
    }
}

/* ================== SỬA TUYẾN BAY ================== */
elseif(isset($_POST['suatuyenbay'])){

    $matuyen = mysqli_real_escape_string($mysqli, $_POST['MATUYEN']);
    $sb_di   = mysqli_real_escape_string($mysqli, $_POST['SANBAYDI']);
    $sb_den  = mysqli_real_escape_string($mysqli, $_POST['SANBAYDEN']);
    $gia     = mysqli_real_escape_string($mysqli, $_POST['GIACOBAN']);
    $time    = mysqli_real_escape_string($mysqli, $_POST['THOIGIANBAY']);

    // Check sân bay
    if($sb_di == $sb_den){
        echo "<script>alert('❌ Sân bay đi và đến không được trùng nhau!'); history.back();</script>";
        exit();
    }

    // 🔥 CHECK FK (rất quan trọng)
    if(!checkTuyenCoTheSuaXoa($mysqli, $matuyen)){
        echo "<script>alert('❌ Tuyến này đang có chuyến bay! Không thể sửa.'); history.back();</script>";
        exit();
    }

    $sql = "UPDATE tuyenbay SET 
                SANBAYDI='$sb_di',
                SANBAYDEN='$sb_den',
                GIACOBAN='$gia',
                THOIGIANBAY='$time'
            WHERE MATUYEN='$matuyen'";

    if(mysqli_query($mysqli, $sql)){
        header('Location:../../index.php?action=lietke_tuyenbay');
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($mysqli);
    }
}

/* ================== XÓA TUYẾN BAY ================== */
elseif(isset($_GET['type']) && $_GET['type']=='xoa'){

    $id = mysqli_real_escape_string($mysqli, $_GET['id']);

    // 🔥 CHECK FK trước khi xóa
    if(!checkTuyenCoTheSuaXoa($mysqli, $id)){
        echo "<script>alert('❌ Không thể xóa! Tuyến này đang có chuyến bay.'); history.back();</script>";
        exit();
    }

    $sql = "DELETE FROM tuyenbay WHERE MATUYEN='$id'";

    if(mysqli_query($mysqli, $sql)){
        header('Location:../../index.php?action=lietke_tuyenbay');
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($mysqli);
    }
}
?>