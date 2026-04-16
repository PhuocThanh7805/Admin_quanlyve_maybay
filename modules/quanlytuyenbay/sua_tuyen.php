<?php
// --- GIỮ NGUYÊN LOGIC PHP CỦA BẠN ---
$id = isset($_GET['id']) ? mysqli_real_escape_string($mysqli, $_GET['id']) : '';
if ($id == '') { die("Thiếu ID tuyến bay!"); }

$check = mysqli_prepare($mysqli, "SELECT MIN(THOIGIANDI) as min_time FROM chuyenbay WHERE MATUYEN = ?");
mysqli_stmt_bind_param($check, "s", $id);
mysqli_stmt_execute($check);
$result_check = mysqli_stmt_get_result($check);
$row_check = mysqli_fetch_assoc($result_check);

$allow_edit = true;
if (!empty($row_check['min_time'])) {
    $min_time = strtotime($row_check['min_time']);
    if ($min_time <= strtotime("+10 days")) {
        $allow_edit = false;
    }
}

$stmt = mysqli_prepare($mysqli, "SELECT * FROM tuyenbay WHERE MATUYEN = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$query_sua = mysqli_stmt_get_result($stmt);
$dong = mysqli_fetch_assoc($query_sua);
if (!$dong) { die("Không tìm thấy tuyến bay!"); }

$sql_sanbay = "SELECT sb.MASANBAY, dd.TENDIADIEM FROM sanbay sb JOIN diadiem dd ON sb.MADIADIEM = dd.MADIADIEM";
$query_sanbay = mysqli_query($mysqli, $sql_sanbay);
$ds_sanbay = [];
while ($row = mysqli_fetch_assoc($query_sanbay)) { $ds_sanbay[] = $row; }
?>

<style>
/* ===== CAMEO SKY SYSTEM ===== */
.admin-wrap {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    max-width: 800px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.08);
    overflow: hidden;
}

/* ===== HEADER (ĐỒNG BỘ HEADER CHÍNH) ===== */
.form-header {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    padding: 18px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-header h2 {
    margin: 0;
    font-size: 18px;
    color: #fff;
    font-weight: 700;
}

/* NÚT QUAY LẠI (FIX KHÔNG NHẢY) */
.btn-back {
    text-decoration: none;
    color: #fff;
    font-size: 13px;
    padding: 6px 12px;
    border-radius: 6px;
    background: rgba(255,255,255,0.15);
    transition: 0.2s;
}

.btn-back:hover {
    background: rgba(255,255,255,0.25);
}

/* ===== BODY ===== */
.form-body {
    padding: 30px;
}

/* ===== GROUP ===== */
.form-group {
    margin-bottom: 22px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}

/* ===== INPUT ===== */
.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #dbeafe;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.25s;
    outline: none;
}

/* FOCUS EFFECT */
.form-group input:focus,
.form-group select:focus {
    border-color: #0072ff;
    box-shadow: 0 0 0 3px rgba(0,114,255,0.15);
}

/* DISABLED */
.form-group input[readonly],
.form-group select[disabled] {
    background: #f1f5f9;
    color: #94a3b8;
}

/* GRID */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* ALERT */
.alert-warning {
    background: #fff5f5;
    border-left: 4px solid #ef4444;
    padding: 14px;
    border-radius: 6px;
    color: #b91c1c;
    margin-bottom: 20px;
}

/* ===== BUTTON ===== */
.btn-action {
    padding: 10px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: 0.25s;
}

/* PRIMARY */
.btn-gradient {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,114,255,0.3);
}

.btn-gradient:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0,114,255,0.4);
}

/* DISABLED */
.btn-disabled {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
}
</style>

<div class="admin-wrap">
    <div class="form-header">
        <h2>✏️ Sửa Tuyến Bay</h2>
        <a href="index.php?action=lietke_tuyenbay" class="btn-action btn-light">← Quay lại</a>
    </div>

    <div class="form-body">
        <?php if(!$allow_edit): ?>
            <div class="alert-warning">
                <strong>Thông báo:</strong> Không thể chỉnh sửa thông tin tuyến này vì đã có các chuyến bay sắp khởi hành trong vòng 10 ngày tới.
            </div>
        <?php endif; ?>

        <form method="POST" action="modules/quanlytuyenbay/xuly_tuyen.php">
            <!-- MÃ TUYẾN -->
            <div class="form-group">
                <label>Mã tuyến bay (Cố định)</label>
                <input type="text" name="MATUYEN" value="<?= $dong['MATUYEN'] ?>" readonly>
            </div>

            <!-- SÂN BAY -->
            <div class="form-row">
                <div class="form-group">
                    <label>Sân bay đi</label>
                    <select name="SANBAYDI" required <?= !$allow_edit ? 'disabled' : '' ?>>
                        <?php foreach ($ds_sanbay as $row): ?>
                            <option value="<?= $row['MASANBAY'] ?>"
                                <?= ($row['MASANBAY'] == $dong['SANBAYDI']) ? 'selected' : '' ?>>
                                <?= $row['TENDIADIEM'] ?> (<?= $row['MASANBAY'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sân bay đến</label>
                    <select name="SANBAYDEN" required <?= !$allow_edit ? 'disabled' : '' ?>>
                        <?php foreach ($ds_sanbay as $row): ?>
                            <option value="<?= $row['MASANBAY'] ?>"
                                <?= ($row['MASANBAY'] == $dong['SANBAYDEN']) ? 'selected' : '' ?>>
                                <?= $row['TENDIADIEM'] ?> (<?= $row['MASANBAY'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- GIÁ + THỜI GIAN -->
            <div class="form-row">
                <div class="form-group">
                    <label>Giá vé cơ bản (VNĐ)</label>
                    <input type="number" name="GIACOBAN"
                           value="<?= $dong['GIACOBAN'] ?>"
                           placeholder="Ví dụ: 1200000"
                           required <?= !$allow_edit ? 'readonly' : '' ?>>
                </div>

                <div class="form-group">
                    <label>Thời gian bay (Phút)</label>
                    <input type="number" name="THOIGIANBAY"
                           value="<?= $dong['THOIGIANBAY'] ?>"
                           placeholder="Ví dụ: 60"
                           required <?= !$allow_edit ? 'readonly' : '' ?>>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

            <!-- ACTION -->
            <div style="text-align: right;">
                <?php if($allow_edit): ?>
                    <button type="submit" name="suatuyenbay" class="btn-action btn-gradient">
                        💾 Lưu thay đổi
                    </button>
                <?php else: ?>
                    <button type="button" class="btn-action btn-disabled" title="Bị khóa do ràng buộc thời gian">
                        🚫 Không thể cập nhật
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>