<?php
// --- GIỮ NGUYÊN LOGIC PHP CỦA BẠN ---
$sql_sanbay = "SELECT sb.MASANBAY, dd.TENDIADIEM 
               FROM sanbay sb 
               JOIN diadiem dd ON sb.MADIADIEM = dd.MADIADIEM";

$result = mysqli_query($mysqli, $sql_sanbay);

// Đổ ra 1 mảng để dùng 2 lần cho Sân bay đi và Sân bay đến
$ds_sanbay = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ds_sanbay[] = $row;
}
?>

<style>
/* =========================
   TỔNG THỂ
========================= */
.admin-wrap {
    font-family: 'Segoe UI', sans-serif;
    max-width: 750px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,114,255,0.15);
    overflow: hidden;
}

/* =========================
   HEADER
========================= */
.form-header {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    padding: 20px 25px;
    position: relative;
}

.form-header h2 {
    margin: 0;
    font-size: 18px;
    color: #fff;
    text-align: center;
    font-weight: 700;
}

/* NÚT BACK */
.btn-back {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    text-decoration: none;
    color: #fff;
    font-size: 13px;
    opacity: 0.85;
    transition: 0.2s;
}

.btn-back:hover {
    opacity: 1;
}

/* =========================
   BODY
========================= */
.form-body {
    padding: 30px;
    background: #f8fbff;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 6px;
    text-transform: uppercase;
}

/* INPUT */
.form-group input,
.form-group select {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid #dbeafe;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
    transition: 0.25s;
    outline: none;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #0072ff;
    box-shadow: 0 0 0 3px rgba(0,114,255,0.15);
}

/* GRID */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* =========================
   BUTTON
========================= */
.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.25s;
    box-shadow: 0 4px 12px rgba(0,114,255,0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,114,255,0.4);
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
.form-header {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    padding: 20px 25px;

    display: flex;
    align-items: center;
    justify-content: center;

    position: relative;
}

.form-header h2 {
    margin: 0;
    font-size: 18px;
    color: #fff;
    font-weight: 700;
}

/* FIX NÚT QUAY LẠI */
.btn-back {
    position: absolute;
    left: 20px;

    text-decoration: none;
    color: #fff;
    font-size: 13px;

    padding: 6px 10px;
    border-radius: 6px;
    background: rgba(255,255,255,0.1);

    transition: 0.2s;
}

.btn-back:hover {
    background: rgba(255,255,255,0.2);
}
</style>

<div class="admin-wrap">
    <div class="form-header">
        <a href="index.php?action=lietke_tuyenbay" class="btn-back" style="float: left; margin: 0;">← Quay lại</a>
        <h2 style="display: inline-block;">Thêm Tuyến Bay Mới</h2>
    </div>

    <div class="form-body">
        <form method="POST" action="modules/quanlytuyenbay/xuly_tuyen.php">

            <!-- MÃ TUYẾN -->
            <div class="form-group">
                <label>Mã Tuyến Bay</label>
                <input type="text" name="MATUYEN" placeholder="Ví dụ: TB101, HAN-SGN..." required>
            </div>

            <!-- SÂN BAY -->
            <div class="form-row">
                <div class="form-group">
                    <label>Sân Bay Đi</label>
                    <select name="SANBAYDI" required>
                        <option value="" disabled selected>-- Chọn sân bay đi --</option>
                        <?php foreach ($ds_sanbay as $row): ?>
                            <option value="<?= $row['MASANBAY'] ?>">
                                <?= $row['TENDIADIEM'] ?> (<?= $row['MASANBAY'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sân Bay Đến</label>
                    <select name="SANBAYDEN" required>
                        <option value="" disabled selected>-- Chọn sân bay đến --</option>
                        <?php foreach ($ds_sanbay as $row): ?>
                            <option value="<?= $row['MASANBAY'] ?>">
                                <?= $row['TENDIADIEM'] ?> (<?= $row['MASANBAY'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- GIÁ + THỜI GIAN -->
            <div class="form-row">
                <div class="form-group">
                    <label>Giá Vé Cơ Bản (VNĐ)</label>
                    <input type="number" name="GIACOBAN" placeholder="Ví dụ: 850000" required>
                </div>

                <div class="form-group">
                    <label>Thời Gian Bay (Phút)</label>
                    <input type="number" name="THOIGIANBAY" placeholder="Ví dụ: 120" required>
                </div>
            </div>

            <!-- NÚT XÁC NHẬN -->
            <div class="form-actions">
                <button type="submit" name="themtuyenbay" class="btn-submit">
                    XÁC NHẬN TẠO TUYẾN BAY
                </button>
            </div>

        </form>
    </div>
</div>