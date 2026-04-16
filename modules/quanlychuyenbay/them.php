<?php
// Truy vấn Tuyến bay
$tuyenbay_query = mysqli_query($mysqli, "
    SELECT tb.MATUYEN, dd1.TENDIADIEM AS DI, dd2.TENDIADIEM AS DEN,
           sb1.TENSANBAY AS SANBAY_DI, sb2.TENSANBAY AS SANBAY_DEN,
           tb.SANBAYDI, tb.SANBAYDEN, tb.THOIGIANBAY
    FROM tuyenbay tb
    JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
    JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
    JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
    JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
");

// Truy vấn Máy bay và trạng thái cuối cùng
$maybay_query = mysqli_query($mysqli, "
    SELECT m.MAMAYBAY, m.TENMAYBAY,
        (SELECT sb.TENSANBAY FROM chuyenbay cb 
         JOIN tuyenbay t ON cb.MATUYEN = t.MATUYEN 
         JOIN sanbay sb ON t.SANBAYDEN = sb.MASANBAY 
         WHERE cb.MAMAYBAY = m.MAMAYBAY ORDER BY cb.THOIGIANDEN DESC LIMIT 1) as LAST_AIRPORT,
        (SELECT t.SANBAYDEN FROM chuyenbay cb 
         JOIN tuyenbay t ON cb.MATUYEN = t.MATUYEN 
         WHERE cb.MAMAYBAY = m.MAMAYBAY ORDER BY cb.THOIGIANDEN DESC LIMIT 1) as LAST_AIRPORT_ID,
        (SELECT cb.THOIGIANDEN FROM chuyenbay cb 
         WHERE cb.MAMAYBAY = m.MAMAYBAY ORDER BY cb.THOIGIANDEN DESC LIMIT 1) as LAST_TIME
    FROM maybay m
");
?>

<style>
:root {
    --primary: #1a73e8;
    --danger: #d93025;
    --warning: #f9ab00;
    --success: #1e8e3e;
    --bg-card: #ffffff;
    --text-main: #202124;
    --border: #dadce0;
}

.admin-wrap { max-width: 900px; margin: 30px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.card { background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
.card-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid var(--border); color: var(--text-main); }
.card-header h3 { margin: 0; font-size: 18px; font-weight: 500; }
.card-body { padding: 30px; }

.form-group { margin-bottom: 24px; }
.form-group label { font-size: 13px; font-weight: 600; color: #5f6368; display: block; margin-bottom: 8px; }
.form-group input, .form-group select { 
    width: 100%; padding: 10px 12px; border-radius: 4px; border: 1px solid var(--border); 
    font-size: 14px; transition: 0.2s; box-sizing: border-box;
}
.form-group input:focus, .form-group select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 2px rgba(26,115,232,0.1); }

.row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

/* Route Log & Alert */
#alert-box { display: none; padding: 12px 16px; border-radius: 4px; color: #fff; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }
#route-log { 
    display: none; margin-bottom: 25px; padding: 20px; border-radius: 6px; 
    background: #fdfdfd; border: 1px solid var(--border); border-left: 4px solid var(--primary);
}

.btn-submit { 
    background: var(--primary); color: #fff; padding: 12px 24px; border: none; 
    border-radius: 4px; font-weight: 600; cursor: pointer; transition: 0.2s; width: 100%;
    font-size: 15px; letter-spacing: 0.5px;
}
.btn-submit:disabled { background: #dadce0; cursor: not-allowed; }
.btn-submit:hover:not(:disabled) { background: #1557b0; }

/* Lộ trình trực quan */
.route-viz { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
.viz-point { text-align: center; flex: 1; }
.viz-line { flex: 2; height: 2px; border-top: 2px dashed #dadce0; position: relative; margin: 0 15px; }
.viz-icon { position: absolute; top: -11px; left: 45%; background: white; padding: 0 5px; }
</style>

<div class="admin-wrap">
    <div class="card">
        <div class="card-header"><h3>Thiết lập chuyến bay mới</h3></div>
        <div class="card-body">
            
            <!-- Thông báo lỗi tổng quát -->
            <div id="alert-box"></div>

            <!-- Nhật ký lộ trình chi tiết -->
            <div id="route-log">
                <h4 style="margin:0 0 15px 0; font-size:13px; color:#5f6368; text-transform:uppercase;">Phân tích lộ trình bạn cần xem lỗi phía bên dưới</h4>
                <div id="log-content"></div>
            </div>

            <form method="POST" action="modules/quanlychuyenbay/xuly.php" id="flightForm">
                <div class="row">
                    <div class="form-group">
                        <label>Số hiệu chuyến bay *</label>
                        <input type="text" name="MACHUYENBAY" required placeholder="VD: VJ123" style="text-transform: uppercase;">
                    </div>

                    <div class="form-group">
                        <label>Máy bay vận hành *</label>
                        <select name="MAMAYBAY" id="select_maybay" required onchange="validateFlight()">
                            <option value="">-- Chọn máy bay --</option>
                            <?php while($r = mysqli_fetch_assoc($maybay_query)): ?>
                                <option value="<?= $r['MAMAYBAY'] ?>" 
                                        data-lastid="<?= $r['LAST_AIRPORT_ID'] ?? 'NEW' ?>" 
                                        data-lastname="<?= $r['LAST_AIRPORT'] ?? 'Xưởng (Mới)' ?>"
                                        data-lasttime="<?= $r['LAST_TIME'] ?? '' ?>">
                                    <?= $r['TENMAYBAY'] ?> (Đang ở: <?= $r['LAST_AIRPORT'] ?? 'Mới' ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tuyến bay *</label>
                    <select name="MATUYEN" id="tuyenbay" required onchange="validateFlight()">
                        <option value="">-- Chọn lộ trình --</option>
                        <?php mysqli_data_seek($tuyenbay_query, 0); 
                              while($tb = mysqli_fetch_assoc($tuyenbay_query)): ?>
                            <option value="<?= $tb['MATUYEN'] ?>"
                                    data-time="<?= $tb['THOIGIANBAY'] ?>"
                                    data-msbdi="<?= $tb['SANBAYDI'] ?>"
                                    data-sbdi="<?= $tb['SANBAY_DI'] ?>">
                                <?= $tb['DI'] ?> (<?= $tb['SANBAY_DI'] ?>) ➔ <?= $tb['DEN'] ?> (<?= $tb['SANBAY_DEN'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Ngày đi *</label>
                        <input type="date" name="NGAYDI" id="ngaydi" required onchange="validateFlight()">
                    </div>
                    <div class="form-group">
                        <label>Giờ đi *</label>
                        <input type="time" name="GIODI" id="giodi" required onchange="validateFlight()">
                    </div>
                </div>

                <div class="form-group">
                    <label>Dự kiến hạ cánh</label>
                    <input type="text" id="arrival_preview" readonly style="background: #f8f9fa; color: var(--primary); font-weight: bold; border-color: #eee;">
                    <input type="hidden" name="THOIGIANDEN" id="thoigianden_hidden">
                </div>

                <button type="submit" name="themchuyenbay" class="btn-submit" id="submitBtn">TẠO CHUYẾN BAY</button>
            </form>
        </div>
    </div>
</div>

<script>
const CONFIG = { MIN_REST_HOURS: 3 };

function validateFlight() {
    const els = {
        mb: document.getElementById('select_maybay'),
        tb: document.getElementById('tuyenbay'),
        ngay: document.getElementById('ngaydi'),
        gio: document.getElementById('giodi'),
        alert: document.getElementById('alert-box'),
        btn: document.getElementById('submitBtn'),
        preview: document.getElementById('arrival_preview'),
        hidden: document.getElementById('thoigianden_hidden'),
        routeLog: document.getElementById('route-log'),
        logContent: document.getElementById('log-content')
    };

    // Reset UI
    [els.mb, els.tb, els.ngay, els.gio].forEach(el => el.style.borderColor = "#dadce0");
    els.alert.style.display = 'none';
    els.routeLog.style.display = 'none';
    els.btn.disabled = false;
    els.btn.style.background = "var(--primary)";
    els.btn.innerText = "TẠO CHUYẾN BAY";

    if (!els.mb.value || !els.tb.value) return;

    const mbOpt = els.mb.selectedOptions[0].dataset;
    const tbOpt = els.tb.selectedOptions[0].dataset;
    let hasError = false;
    let htmlLog = "";

    // 1. KIỂM TRA ĐỊA ĐIỂM (NOTE LỖI TRỰC QUAN)
    if (mbOpt.lastid !== 'NEW' && mbOpt.lastid !== tbOpt.msbdi) {
        hasError = true;
        els.mb.style.borderColor = "var(--danger)";
        els.tb.style.borderColor = "var(--danger)";
        htmlLog += `
            <div class="route-viz">
                <div class="viz-point"><small>Vị trí hiện tại</small><br><strong style="color:var(--success)">📍 ${mbOpt.lastname}</strong></div>
                <div class="viz-line"><span class="viz-icon">❌</span></div>
                <div class="viz-point"><small>Điểm đi yêu cầu</small><br><strong style="color:var(--danger)">🛫 ${tbOpt.sbdi}</strong></div>
            </div>
            <p style="color:var(--danger); font-size:13px; margin-top:10px;">
                <b>Lỗi:</b> Máy bay không thể thực hiện chuyến bay vì đang không có mặt tại sân bay khởi hành.
            </p>
        `;
    }

    // 2. KIỂM TRA THỜI GIAN NGHỈ
    if (els.ngay.value && els.gio.value && mbOpt.lasttime) {
        const lastDate = new Date(mbOpt.lasttime.replace(/-/g, "/"));
        const startDate = new Date(`${els.ngay.value}T${els.gio.value}`);
        const diffHours = (startDate - lastDate) / (1000 * 60 * 60);

        if (diffHours < CONFIG.MIN_REST_HOURS) {
            hasError = true;
            const minDate = new Date(lastDate.getTime() + CONFIG.MIN_REST_HOURS * 3600000);
            
            htmlLog += `
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <p style="color:var(--danger); font-weight:bold; margin:0 0 8px 0;">Lỗi thời gian nghỉ máy bay</p>
                    <table style="width:100%; font-size:13px; color:#5f6368;">
                        <tr><td>Lịch trình cuối:</td><td>${lastDate.toLocaleString('vi-VN')}</td></tr>
                        <tr><td>Dự kiến bay:</td><td>${startDate.toLocaleString('vi-VN')}</td></tr>
                        <tr><td>Thời gian nghỉ:</td><td style="color:var(--danger)">${diffHours.toFixed(1)}h / tối thiểu ${CONFIG.MIN_REST_HOURS}h</td></tr>
                    </table>
                    <p style="color:var(--primary); font-size:13px; margin-top:8px;">
                        Gợi ý: Sớm nhất có thể cất cánh vào <b>${minDate.toLocaleString('vi-VN')}</b> và không được phép chèn chuyến vào giữa hành trình chỉ được phép thêm chuyến sau thời gian đáp cuối cùng
                    </p>
                </div>
            `;
            els.ngay.style.borderColor = "var(--danger)";
            els.gio.style.borderColor = "var(--danger)";
        }
    }

    // HIỂN THỊ LOG & XỬ LÝ NÚT
    if (hasError) {
        els.routeLog.style.display = 'block';
        els.logContent.innerHTML = htmlLog;
        els.btn.disabled = true;
        els.btn.innerText = "DỮ LIỆU KHÔNG HỢP LỆ";
        els.btn.style.background = "#9aa0a6";
    } else {
        calculateArrival(els, tbOpt);
    }
}

function calculateArrival(els, tbOpt) {
    if (!els.ngay.value || !els.gio.value) return;
    
    const start = new Date(`${els.ngay.value}T${els.gio.value}`);
    const end = new Date(start.getTime() + parseInt(tbOpt.time) * 60000);
    
    const timeStr = end.getHours().toString().padStart(2, '0') + ":" + end.getMinutes().toString().padStart(2, '0');
    const dateStr = end.toLocaleDateString('vi-VN');
    
    els.preview.value = `${timeStr} ngày ${dateStr}`;
    
    // Format chuẩn cho MySQL: YYYY-MM-DD HH:MM:SS
    const y = end.getFullYear();
    const m = (end.getMonth() + 1).toString().padStart(2, '0');
    const d = end.getDate().toString().padStart(2, '0');
    els.hidden.value = `${y}-${m}-${d} ${timeStr}:00`;
}
</script>