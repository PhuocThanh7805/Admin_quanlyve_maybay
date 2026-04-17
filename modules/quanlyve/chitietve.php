<?php
$mave = $_GET['mave'] ?? '';
$mave = mysqli_real_escape_string($mysqli, $mave);
if (empty($mave)) {
    die("<h2 style='text-align:center;color:red'>❌ Thiếu mã vé</h2>");
}
$sql = "
SELECT 
    v.*, cb.THOIGIANDI, cb.MAMAYBAY,
    tb.SANBAYDI, tb.SANBAYDEN,
    dd1.TENDIADIEM AS DIEMDI, 
    dd2.TENDIADIEM AS DIEMDEN,
    hd.TRANGTHAIHOADON, 
    g.MALOAIGHE
FROM ve v
JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
JOIN ghe g ON v.MAGHE = g.MAGHE
JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
LEFT JOIN chitiethoadon ct ON v.MAVE = ct.MAVE
LEFT JOIN hoadon hd ON ct.MAHOADON = hd.MAHOADON
WHERE v.MAVE = '$mave'
";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("<h2 style='text-align:center;color:red'>❌ Không tìm thấy vé</h2>");
}
$status_is_paid = isset($row['TRANGTHAIHOADON']) && $row['TRANGTHAIHOADON'] == 'DA_THANH_TOAN';
$boardingTime = date('H:i', strtotime($row['THOIGIANDI'] . ' -40 minutes'));
$secret = "SECRET_KEY_123";
$token = hash('sha256', $row['MAVE'] . $secret);
$qrData = json_encode([
    "mave" => $row['MAVE'],
    "ho_ten" => $row['HOTEN'],
    "chuyen_bay" => $row['MACHUYENBAY'],
    "ghe" => $row['MAGHE'],
    "diem_di" => $row['DIEMDI'],
    "diem_den" => $row['DIEMDEN'],
    "gio_di" => $row['THOIGIANDI'],
    "token" => $token,
    "created_at" => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
?>

<style>
    :root {
        --system-blue-dark: #004ecc;
        --system-blue-light: #0072ff;
        --system-gradient: linear-gradient(to right, #004ecc, #0072ff);
        --dark-bar: #1e293b;
        --gray-text: #64748b;
        --border-color: #cbd5e1;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f1f5f9;
        margin: 0;
        padding: 20px;
        color: #1e293b;
    }

    .itinerary-container {
        max-width: 850px;
        margin: 0 auto;
        background: white;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .top-header {
        background: var(--system-gradient);
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid var(--system-blue-dark);
    }

    .logo-area {
        color: white;
        font-size: 30px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -1px;
    }

    .pnr-box {
        background: white;
        padding: 5px 15px;
        text-align: center;
        min-width: 180px;
    }

    .pnr-box label { font-size: 11px; color: var(--gray-text); display: block; text-transform: uppercase; }
    .pnr-box strong { font-size: 24px; color: #000; letter-spacing: 1px; }

    .black-bar {
        background: var(--dark-bar);
        color: white;
        padding: 10px 30px;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .content-area { padding: 30px; }

    .main-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 2px solid var(--system-blue-light);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .main-title-row h2 {
        color: var(--system-blue-dark);
        margin: 0;
        font-size: 22px;
        text-transform: uppercase;
    }

    .info-bar {
        background: var(--system-blue-light);
        color: white;
        padding: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        font-size: 12px;
        font-weight: bold;
    }

    .flight-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--border-color);
    }

    .flight-table th {
        background: #94a3b8;
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 13px;
        text-transform: uppercase;
    }

    .flight-table td {
        padding: 20px 12px;
        border: 1px solid var(--border-color);
        vertical-align: top;
    }

    .city-label { font-weight: bold; font-size: 16px; color: #000; display: block; }
    .sub-info { font-size: 11px; color: var(--gray-text); margin-top: 5px; line-height: 1.4; }

    .footer-section {
        display: flex;
        border-top: 1px solid var(--border-color);
        margin-top: 30px;
    }

    .passenger-details { flex: 1; padding: 20px 0; }
    .qr-side {
        width: 180px;
        padding: 20px;
        text-align: center;
        border-left: 1px dashed var(--border-color);
    }

    .label-small { font-size: 11px; color: var(--gray-text); text-transform: uppercase; }
    .value-large { font-size: 18px; font-weight: bold; color: #000; margin-top: 5px; display: block; }

    .status-stamp {
        border: 3px solid #10b981;
        color: #10b981;
        padding: 5px 15px;
        font-weight: 900;
        display: inline-block;
        margin-top: 10px;
    }

    .btn {
        padding: 12px 25px;
        cursor: pointer;
        font-weight: bold;
        border: none;
        text-transform: uppercase;
        font-size: 12px;
        transition: 0.2s;
    }

    .btn-print { background: var(--system-blue-dark); color: white; }
    .btn-back { background: #cbd5e1; color: #1e293b; margin-right: 10px; text-decoration: none; }
    @media print {
        @page {
            margin: 0;
            size: auto;
        }

        html, body {
            height: 99%;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow: hidden;
        }
        body {
            visibility: hidden;
        }
        .itinerary-container {
            visibility: visible;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }
        .action-area, .main-title-row div:last-child {
            display: none !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
<div class="itinerary-container">
    <div class="top-header">
        <div class="logo-area">CAMEO SKY☁️</div>
        <div class="pnr-box">
            <label>Mã xác nhận đặt chỗ</label>
            <strong><?= $row['MAVE'] ?></strong>
        </div>
    </div>

    <div class="black-bar">
        
        <div style="color: #60a5fa; font-weight: bold;">Chú ý! Đây không phải là vé để lên máy bay</div>
    </div>

    <div class="content-area">
        

        <div class="info-bar">
            <div>Chi tiết chuyến bay</div>
            <div>Ngày đặt chỗ: <?= date('d M Y', strtotime($row['THOIGIANDI'])) ?></div>
            <div style="text-align: right;">Mã vé: <?= $row['MAVE'] ?></div>
        </div>

        <table class="flight-table">
            <thead>
                <tr>
                    <th width="15%">Ngày</th>
                    <th width="20%">Chuyến bay</th>
                    <th width="32%">Khởi hành</th>
                    <th width="33%">Đến</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= date('D d M', strtotime($row['THOIGIANDI'])) ?></strong><br>
                        <?= date('Y', strtotime($row['THOIGIANDI'])) ?>
                    </td>
                    <td>
                        <strong style="color: var(--system-blue-dark);"><?= $row['MACHUYENBAY'] ?></strong><br>
                        <div class="sub-info">
                            Máy bay: <?= $row['MAMAYBAY'] ?><br>
                            Hạng: <?= $row['MALOAIGHE'] ?>
                        </div>
                    </td>
                    <td>
                        <span class="city-label"><?= $row['DIEMDI'] ?></span>
                        <strong style="font-size: 18px;"><?= date('H:i', strtotime($row['THOIGIANDI'])) ?></strong><br>
                        <div class="sub-info">Sân bay <?= $row['SANBAYDI'] ?></div>
                    </td>
                    <td>
                        <span class="city-label"><?= $row['DIEMDEN'] ?></span>
                        <strong style="font-size: 18px;"><?= date('H:i', strtotime($row['THOIGIANDI'] . ' +2 hours')) ?></strong><br>
                        <div class="sub-info">Sân bay <?= $row['SANBAYDEN'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-section">
            <div class="passenger-details">
                <div class="label-small">Hành khách / Passenger</div>
                <span class="value-large" style="color: var(--system-blue-dark); font-size: 22px;">
                    <?= mb_strtoupper($row['HOTEN'],'UTF-8') ?>
                </span>

                <div style="display: flex; gap: 50px; margin-top: 20px;">
                    <div>
                        <div class="label-small">Số ghế / Seat</div>
                        <span class="value-large"><?= $row['MAGHE'] ?></span>
                    </div>
                    <div>
                        <div class="label-small">Giờ lên máy bay</div>
                        <span class="value-large" style="color: #ef4444;"><?= $boardingTime ?></span>
                    </div>
                    <div>
                        <div class="label-small">Tình trạng</div>
                        <?php if($status_is_paid): ?>
                            <div class="status-stamp">ĐÃ THANH TOÁN</div>
                        <?php else: ?>
                            <div class="status-stamp" style="border-color: #ef4444; color: #ef4444;">CHƯA THANH TOÁN</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="qr-side">
                <div class="label-small">QR Check-in</div>
                <div style="margin-top: 10px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($qrData) ?>" alt="QR">
                </div>
                <div style="font-size: 9px; color: var(--gray-text); margin-top: 5px;">Mã xác thực điện tử</div>
            </div>
        </div>
        <p style="font-size: 15px; color: var(--gray-text); border-top: 1px solid #eee; padding-top: 15px; margin-bottom: 0;">
            * Chú ý: Thời gian được tính theo giờ địa phương tại các sân bay tương ứng. Quý khách vui lòng có mặt tại quầy thủ tục trước 120 phút.
        </p>
    </div>
</div>
<div class="action-area" style="max-width: 850px; margin: 20px auto; text-align: right;">
   <a href="index.php?action=hienthive" class="btn btn-back">
    Quay lại
</a>
    <?php if($status_is_paid): ?>
        <a href="index.php?action=gui&mave=<?= $row['MAVE'] ?>" class="btn btn-back">Gửi vé</a>
    <?php endif; ?>
</div>
