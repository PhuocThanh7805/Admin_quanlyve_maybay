<?php
//  KIỂM TRA ID
if(isset($_GET['id'])){
    $machuyenbay = mysqli_real_escape_string($mysqli, $_GET['id']);
} else {
    echo "<script>alert('❌ Không tìm thấy ID!'); window.location='index.php?action=quanlychuyenbay&query=lietke';</script>";
    exit();
}

// LẤY DỮ LIỆU
$sql = mysqli_query($mysqli, "SELECT * FROM chuyenbay WHERE MACHUYENBAY='$machuyenbay' LIMIT 1");
$cb = mysqli_fetch_assoc($sql);
if(!$cb) die("❌ Không tìm thấy chuyến bay!");

// UPDATE
if(isset($_POST['suachuyenbay'])){
    $mamaybay = $_POST['MAMAYBAY'];
    $matuyen  = $_POST['MATUYEN'];
    $ngaydi   = $_POST['NGAYDI'];
    $giodi    = $_POST['GIODI'];

    if(empty($mamaybay) || empty($matuyen) || empty($ngaydi) || empty($giodi)){
        echo "<script>alert('❌ Thiếu dữ liệu!');</script>";
    } else {
        $thoigiandi = $ngaydi . ' ' . $giodi . ':00';

        $tuyen_res = mysqli_query($mysqli, "SELECT THOIGIANBAY FROM tuyenbay WHERE MATUYEN='$matuyen'");
        $tuyen = mysqli_fetch_assoc($tuyen_res);

        $thoigianden = date('Y-m-d H:i:s',
            strtotime("+{$tuyen['THOIGIANBAY']} minutes", strtotime($thoigiandi))
        );

        mysqli_query($mysqli, "
            UPDATE chuyenbay SET 
                MAMAYBAY='$mamaybay', 
                MATUYEN='$matuyen', 
                THOIGIANDI='$thoigiandi', 
                THOIGIANDEN='$thoigianden' 
            WHERE MACHUYENBAY='$machuyenbay'
        ");

        echo "<script>alert('✅ Cập nhật thành công!'); window.location='index.php?action=quanlychuyenbay';</script>";
        exit();
    }
}

// DATA
$tuyenbay_query = mysqli_query($mysqli, "
    SELECT tb.MATUYEN, dd1.TENDIADIEM AS DI, dd2.TENDIADIEM AS DEN, tb.GIACOBAN, tb.THOIGIANBAY
    FROM tuyenbay tb
    JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
    JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
    JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
    JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
");

$maybay_query = mysqli_query($mysqli, "SELECT * FROM maybay");

$ngay_ht = date('Y-m-d', strtotime($cb['THOIGIANDI']));
$gio_ht  = date('H:i', strtotime($cb['THOIGIANDI']));
?>

<style>
:root{
    --primary: #0072ff;
    --gradient: linear-gradient(135deg, #0072ff, #00c6ff);
    --border: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
}

/* ===== CONTAINER ===== */
.admin-wrap{
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}
.form-header{
    padding: 18px 25px;
    background: var(--gradient);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-header h3{
    margin: 0;
    color: #fff;
    font-size: 18px;
    font-weight: 700;
}

.form-body{
    padding: 30px;
}
.form-group{
    margin-bottom: 20px;
}

.form-group label{
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 6px;
    text-transform: uppercase;
}

.form-group input,
.form-group select{
    width: 100%;
    padding: 11px 14px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
    transition: 0.2s;
}

.form-group input:focus,
.form-group select:focus{
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,114,255,0.1);
    outline: none;
}

/* ===== ROW ===== */
.form-row{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.preview-box{
    background: #f1f5f9;
    padding: 12px;
    border-left: 4px solid var(--primary);
    border-radius: 6px;
    font-size: 13px;
    margin-bottom: 20px;
}

.form-actions{
    margin-top: 25px;
    text-align: right;
}

.btn-submit{
    background: var(--gradient);
    color: #fff;
    padding: 12px 25px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s;
    box-shadow: 0 3px 10px rgba(0,114,255,0.25);
}

.btn-submit:hover{
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(0,114,255,0.35);
}

.btn-back{
    background: #fff;
    border: 1px solid var(--border);
    color: var(--text-muted);
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    margin-left: 10px;
}

.btn-back:hover{
    background: #f8fafc;
}
</style>
<div class="admin-wrap">
    <div class="form-header">
        <h3>✏️ SỬA CHUYẾN BAY</h3>
        <a href="index.php?action=quanlychuyenbay" class="btn-back">← Quay lại</a>
    </div>

    <div class="form-body">
        <form method="POST">

            <div class="form-row">
                <div class="form-group">
                    <label>Máy bay</label>
                    <select name="MAMAYBAY" required>
                        <?php while($mb = mysqli_fetch_assoc($maybay_query)){ ?>
                            <option value="<?= $mb['MAMAYBAY'] ?>" <?= ($mb['MAMAYBAY']==$cb['MAMAYBAY'])?'selected':'' ?>>
                                <?= $mb['TENMAYBAY'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tuyến bay</label>
                    <select name="MATUYEN" id="tuyenbay" onchange="tinhGioDen()" required>
                        <?php while($tb = mysqli_fetch_assoc($tuyenbay_query)){ ?>
                            <option value="<?= $tb['MATUYEN'] ?>"
                                data-time="<?= $tb['THOIGIANBAY'] ?>"
                                data-price="<?= $tb['GIACOBAN'] ?>"
                                <?= ($tb['MATUYEN']==$cb['MATUYEN'])?'selected':'' ?>>
                                <?= $tb['DI'] ?> → <?= $tb['DEN'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="preview-box" id="preview-info"></div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ngày đi</label>
                    <input type="date" name="NGAYDI" id="ngaydi" value="<?= $ngay_ht ?>" onchange="tinhGioDen()" required>
                </div>

                <div class="form-group">
                    <label>Giờ đi</label>
                    <input type="time" name="GIODI" id="giodi" value="<?= $gio_ht ?>" onchange="tinhGioDen()" required>
                </div>
            </div>

            <div class="form-group">
                <label>Giờ đến dự kiến</label>
                <input type="text" id="thoigianden_preview" readonly>
            </div>

            <div class="form-actions">
                <input type="hidden" name="back_url" value="<?= $_GET['back'] ?? '' ?>">
                <button type="submit" name="suachuyenbay" class="btn-base btn-gradient">
    💾 CẬP NHẬT
</button>
            </div>

        </form>
    </div>
</div>
<script>
function tinhGioDen(){
    const ngay = document.getElementById('ngaydi').value;
    const gio  = document.getElementById('giodi').value;
    const opt  = document.getElementById('tuyenbay').selectedOptions[0];

    if(!ngay || !gio || !opt) return;

    const time = parseInt(opt.dataset.time);
    const start = new Date(ngay + 'T' + gio);
    const end = new Date(start.getTime() + time * 60000);

    document.getElementById('preview-info').innerHTML =
        `⏱ ${opt.dataset.time} phút | 💸 ${new Intl.NumberFormat().format(opt.dataset.price)} VNĐ`;

    document.getElementById('thoigianden_preview').value =
        end.getHours().toString().padStart(2,'0') + ':' +
        end.getMinutes().toString().padStart(2,'0');
}

window.onload = tinhGioDen;
</script>
