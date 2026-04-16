<?php
// include('../../config/config.php'); // Đảm bảo đã include ở file cha hoặc tại đây

$id = isset($_GET['MAMAYBAY']) ? mysqli_real_escape_string($mysqli, $_GET['MAMAYBAY']) : '';

if ($id == '') {
    die("<div style='padding:20px; color:red;'>❌ Thiếu mã máy bay!</div>");
}

/* ======================================================
   1. KIỂM TRA TRẠNG THÁI VẬN HÀNH (CHUYẾN BAY)
   ====================================================== */
// Đếm tổng số chuyến bay của máy bay này
$stmt1 = mysqli_prepare($mysqli, "SELECT COUNT(*) AS total_cb FROM chuyenbay WHERE MAMAYBAY = ?");
mysqli_stmt_bind_param($stmt1, "s", $id);
mysqli_stmt_execute($stmt1);
$res1 = mysqli_stmt_get_result($stmt1);
$row1 = mysqli_fetch_assoc($res1);
$total_cb = (int)$row1['total_cb'];

// Tìm thời gian của chuyến bay sớm nhất sắp diễn ra
$stmt2 = mysqli_prepare($mysqli, "SELECT MIN(THOIGIANDI) AS min_time FROM chuyenbay WHERE MAMAYBAY = ? AND THOIGIANDI >= NOW()");
mysqli_stmt_bind_param($stmt2, "s", $id);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$row2 = mysqli_fetch_assoc($res2);
$min_time = $row2['min_time'];

/* Logic cho phép sửa: 
   - Nếu không có chuyến bay nào: Cho sửa thoải mái.
   - Nếu có chuyến bay: Chỉ cho sửa nếu chuyến bay sớm nhất còn cách hiện tại > 10 ngày.
*/
$allow_edit = true;
$lock_reason = "";

if ($total_cb > 0) {
    if (!empty($min_time)) {
        $days_diff = (strtotime($min_time) - time()) / (60 * 60 * 24);
        if ($days_diff <= 10) {
            $allow_edit = false;
            $lock_reason = "Chuyến bay sắp khởi hành trong vòng 10 ngày.";
        }
    } else {
        // Có chuyến trong quá khứ nhưng không có chuyến tương lai: Vẫn cho sửa hoặc khóa tùy bạn
        // Ở đây ta tạm để true nếu chỉ có chuyến cũ đã hoàn tất.
    }
}

/* ======================================================
   2. LẤY DỮ LIỆU CHI TIẾT MÁY BAY
   ====================================================== */
$sql = "SELECT * FROM maybay WHERE MAMAYBAY='$id' LIMIT 1";
$query = mysqli_query($mysqli, $sql);
if (!$query || mysqli_num_rows($query) == 0) {
    die("<div style='padding:20px; color:red;'>❌ Không tìm thấy thông tin máy bay!</div>");
}
$dong = mysqli_fetch_assoc($query);
?>

<style>
    .container { max-width: 800px; margin: 30px auto; background: #fff; border-radius: 12px; border: 1px solid #ddd; box-shadow: 0 6px 20px rgba(0,0,0,0.05); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .header { padding: 15px 20px; background: linear-gradient(135deg, #0072ff, #00c6ff); color: #fff; font-weight: bold; text-transform: uppercase; border-radius: 12px 12px 0 0; }
    .content { padding: 25px; }
    .row { margin-bottom: 20px; }
    .row label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px; }
    input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
    input[readonly] { background-color: #f9f9f9; color: #777; cursor: not-allowed; }
    
    /* Alert Box */
    .info-box { padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid; font-size: 14px; line-height: 1.6; }
    .status-warning { background: #fff4e5; color: #663c00; border-color: #ff9800; } /* Có chuyến nhưng vẫn cho sửa */
    .status-locked { background: #fdeded; color: #5f2120; border-color: #f44336; }  /* Bị khóa */
    
    .btn-group { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; }
    .btn { padding: 10px 25px; border: none; color: #fff; background: #0072ff; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-back { color: #666; text-decoration: none; font-size: 14px; }
    .btn-back:hover { color: #0072ff; }
    .btn-disabled { background: #ccc; cursor: not-allowed; }
</style>

<div class="container">
    <div class="header">✏ Chỉnh sửa thông tin máy bay</div>

    <div class="content">
        <!-- HIỂN THỊ THÔNG BÁO NẾU CÓ CHUYẾN BAY -->
        <?php if ($total_cb > 0): ?>
            <div class="info-box <?= $allow_edit ? 'status-warning' : 'status-locked' ?>">
                <strong>📊 Thông tin vận hành:</strong><br>
                - Máy bay này đang có <b><?= $total_cb ?></b> chuyến bay trong hệ thống.<br>
                <?php if (!empty($min_time)): ?>
                    - Chuyến bay sắp tới: <b><?= date('d/m/Y H:i', strtotime($min_time)) ?></b><br>
                <?php endif; ?>
                
                <?php if ($allow_edit): ?>
                    <span style="color: #2e7d32;">✅ <b>Hệ thống vẫn cho phép sửa:</b> Vì chuyến bay còn cách xa (> 10 ngày).</span>
                <?php else: ?>
                    <span>❌ <b>Đã khóa chỉnh sửa:</b> <?= $lock_reason ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="modules/quanlymaybay/xuly_maybay.php">
            <!-- Input ẩn để gửi mã máy bay qua POST -->
            <input type="hidden" name="MAMAYBAY" value="<?= $dong['MAMAYBAY'] ?>">

            <div class="row">
                <label>Mã máy bay (Không thể sửa)</label>
                <input type="text" value="<?= $dong['MAMAYBAY'] ?>" readonly disabled>
            </div>

            <div class="row">
                <label>Tên máy bay</label>
                <input type="text" name="TENMAYBAY" value="<?= htmlspecialchars($dong['TENMAYBAY']) ?>" <?= !$allow_edit ? 'readonly' : '' ?> required>
            </div>

            <div class="row">
                <label>Hãng hàng không</label>
                <select name="MAHANG" <?= !$allow_edit ? 'disabled' : '' ?> required>
                    <?php
                    $sql_hang = "SELECT * FROM hangmaybay ORDER BY TENHANG ASC";
                    $query_hang = mysqli_query($mysqli, $sql_hang);
                    while ($row_hang = mysqli_fetch_assoc($query_hang)) {
                        $selected = ($row_hang['MAHANG'] == $dong['MAHANG']) ? 'selected' : '';
                        echo '<option '.$selected.' value="'.$row_hang['MAHANG'].'">'.$row_hang['TENHANG'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <label>Cấu hình số lượng ghế</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div>
                        <small>Hạng Nhất (FC)</small>
                        <input type="number" name="SOGHE_HANGNHAT" value="<?= $dong['SOGHE_FC'] ?>" min="0" <?= !$allow_edit ? 'readonly' : '' ?> required>
                    </div>
                    <div>
                        <small>Thương Gia (BC)</small>
                        <input type="number" name="SOGHE_THUONGGIA" value="<?= $dong['SOGHE_BC'] ?>" min="0" <?= !$allow_edit ? 'readonly' : '' ?> required>
                    </div>
                    <div>
                        <small>Phổ Thông (EC)</small>
                        <input type="number" name="SOGHE_PHOTHONG" value="<?= $dong['SOGHE_EC'] ?>" min="0" <?= !$allow_edit ? 'readonly' : '' ?> required>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <a href="javascript:history.back()" class="btn-back">⬅ Quay lại danh sách</a>
                
                <?php if ($allow_edit): ?>
                    <button type="submit" name="sua_maybay" class="btn">💾 Cập nhật thay đổi</button>
                <?php else: ?>
                    <button type="button" class="btn btn-disabled">🚫 Đã khóa chỉnh sửa</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>