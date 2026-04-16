<?php

$loai_thong_ke = isset($_GET['type']) ? mysqli_real_escape_string($mysqli, $_GET['type']) : 'month';
$thang_chon = isset($_GET['month']) ? mysqli_real_escape_string($mysqli, $_GET['month']) : date('Y-m');
$nam_chon = isset($_GET['year']) ? mysqli_real_escape_string($mysqli, $_GET['year']) : date('Y');



if ($loai_thong_ke == 'month') {
    $dk_hoa_don = "DATE_FORMAT(NGAYLAPHOADON,'%Y-%m') = '$thang_chon'";
    $dk_ve = "DATE_FORMAT(NGAYDATVE,'%Y-%m') = '$thang_chon'";
    $nhan_thoi_gian = "Tháng " . date('m/Y', strtotime($thang_chon));
    
    // kỳ truoc so sanh thang 
    $ky_truoc = date('Y-m', strtotime($thang_chon . " -1 month"));
    $dk_hoa_don_ky_truoc = "DATE_FORMAT(NGAYLAPHOADON,'%Y-%m') = '$ky_truoc'";
} else {
    $dk_hoa_don = "YEAR(NGAYLAPHOADON) = '$nam_chon'";
    $dk_ve = "YEAR(NGAYDATVE) = '$nam_chon'";
    $nhan_thoi_gian = "Năm " . $nam_chon;
    
    $ky_truoc = $nam_chon - 1;
    $dk_hoa_don_ky_truoc = "YEAR(NGAYLAPHOADON) = '$ky_truoc'";
}

// teuy van full
$truy_van_kpi = "SELECT SUM(TONGTIEN) as doanh_thu, COUNT(MAHOADON) as so_luong FROM hoadon WHERE $dk_hoa_don";
$ket_qua_kpi = mysqli_query($mysqli, $truy_van_kpi);
$du_lieu_kpi = mysqli_fetch_assoc($ket_qua_kpi);
$tong_doanh_thu = $du_lieu_kpi['doanh_thu'] ?? 0;

//lay doanh thu tinh tăng
$truy_van_ky_truoc = "SELECT SUM(TONGTIEN) as doanh_thu FROM hoadon WHERE $dk_hoa_don_ky_truoc";
$ket_qua_ky_truoc = mysqli_query($mysqli, $truy_van_ky_truoc);
$du_lieu_ky_truoc = mysqli_fetch_assoc($ket_qua_ky_truoc);
$doanh_thu_ky_truoc = $du_lieu_ky_truoc['doanh_thu'] ?? 0;

//tinh du bao 
$ty_le_tang_truong = 0;
$so_tien_chenh_lech = $tong_doanh_thu - $doanh_thu_ky_truoc;
if ($doanh_thu_ky_truoc > 0) {
    $ty_le_tang_truong = ($so_tien_chenh_lech / $doanh_thu_ky_truoc) * 100;
}

// ve + hieu suat
$truy_van_hieu_suat = "SELECT 
                COUNT(v.MAVE) as tong_ve,
                SUM(COALESCE(mb.SOGHE_EC,0) + COALESCE(mb.SOGHE_FC,0) + COALESCE(mb.SOGHE_BC,0)) as tong_suc_chua
            FROM ve v
            JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
            JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
            WHERE $dk_ve";
$ket_qua_hieu_suat = mysqli_fetch_assoc(mysqli_query($mysqli, $truy_van_hieu_suat));
$tong_so_ve = $ket_qua_hieu_suat['tong_ve'] ?? 0;
$tong_suc_chua = $ket_qua_hieu_suat['tong_suc_chua'] ?? 0;

$ty_le_lap_day = ($tong_suc_chua > 0) ? ($tong_so_ve / $tong_suc_chua) * 100 : 0;
$gia_ve_trung_binh = ($tong_so_ve > 0) ? $tong_doanh_thu / $tong_so_ve : 0;


// Top 5 Tuyến bay doanh thu
$truy_van_tuyen_bay = "SELECT tb.SANBAYDI, tb.SANBAYDEN, SUM(v.GIAVE) as doanh_thu
             FROM ve v
             JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
             JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
             WHERE $dk_ve
             GROUP BY tb.MATUYEN ORDER BY doanh_thu DESC LIMIT 5";
$ds_tuyen_bay = mysqli_query($mysqli, $truy_van_tuyen_bay);

// full ghe
$truy_van_lap_day = "SELECT tb.SANBAYDI, tb.SANBAYDEN,
                (COUNT(v.MAVE) / NULLIF(SUM(COALESCE(mb.SOGHE_EC,0) + COALESCE(mb.SOGHE_FC,0) + COALESCE(mb.SOGHE_BC,0)), 0)) * 100 as ty_le
            FROM ve v
            JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
            JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
            JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
            WHERE $dk_ve
            GROUP BY tb.MATUYEN ORDER BY ty_le DESC LIMIT 5";
$ds_lap_day = mysqli_query($mysqli, $truy_van_lap_day);

// Hiệu suất theo Hãng hàng không
$truy_van_hang = "SELECT h.TENHANG, COUNT(v.MAVE) as sl_ve, SUM(v.GIAVE) as doanh_thu
             FROM ve v
             JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
             JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
             JOIN hangmaybay h ON mb.MAHANG = h.MAHANG
             WHERE $dk_ve
             GROUP BY h.MAHANG ORDER BY doanh_thu DESC";
$ds_hang_bay = mysqli_query($mysqli, $truy_van_hang);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    /* Tổng thể: Dùng màu trung tính, chữ rõ nét */
    :root { 
        --primary: #1a73e8; 
        --success: #1db954; 
        --danger: #d93025; 
        --nen-xam: #f8f9fa;
        --chu-chinh: #202124;
        --chu-phu: #5f6368;
        --duong-ke: #dadce0;
    }

    .dashboard-container { 
        padding: 30px; 
        font-family: 'Segoe UI', Tahoma, sans-serif; 
        background: #fff; 
        color: var(--chu-chinh);
    }

    /* Tiêu đề trang */
    .header-flex { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--duong-ke);
    }

    .header-flex h1 { font-size: 24px; font-weight: bold; margin: 0; }

    /* Ô lọc thời gian */
    .filter-section { 
        display: flex; 
        align-items: center;
        background: var(--nen-xam); 
        padding: 8px 15px; 
        border-radius: 5px; 
        border: 1px solid var(--duong-ke); 
    }

    .filter-section select, .filter-section input {
        border: none; background: transparent; font-size: 14px; font-weight: 600; outline: none; cursor: pointer;
    }

    /* Bốn ô số liệu quan trọng */
    .grid-kpi { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 20px; 
        margin-bottom: 30px; 
    }

    .card-kpi { 
        background: var(--nen-xam); 
        padding: 20px; 
        border-radius: 8px; 
        border: 1px solid var(--duong-ke);
    }

    .card-kpi h3 { 
        font-size: 13px; 
        color: var(--chu-phu); 
        margin: 0 0 10px 0; 
        text-transform: uppercase;
    }

    .card-kpi .value { 
        font-size: 26px; 
        font-weight: bold; 
        margin-bottom: 5px;
        display: block;
    }

    /* Bảng và Biểu đồ */
    .bi-grid { 
        display: grid; 
        grid-template-columns: 1.6fr 1fr; 
        gap: 25px; 
    }

    .chart-box { 
        border: 1px solid var(--duong-ke); 
        padding: 20px; 
        border-radius: 8px; 
    }

    .chart-box h3 {
        font-size: 16px; margin: 0 0 20px 0; padding-left: 10px;
        border-left: 4px solid var(--primary);
    }

    /* Bảng danh sách hãng bay */
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { 
        text-align: left; font-size: 12px; color: var(--chu-phu); 
        padding: 12px; border-bottom: 2px solid var(--duong-ke);
    }
    .custom-table td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
    .custom-table tr:hover { background: #f1f3f4; }

    /* Màu sắc tăng giảm */
    .up { color: var(--success); font-weight: bold; }
    .down { color: var(--danger); font-weight: bold; }
    .btn-analysis {
    display: inline-flex;
    align-items: center;
    padding: 10px 20px;
    background-color: #1a73e8; /* Màu xanh đậm nghiêm túc */
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-analysis:hover {
    background-color: #1557b0;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>

<div class="dashboard-container">
    <div class="header-flex">
        <div>
            <h1>Kết quả bán vé và Doanh thu</h1>
            <p style="color: var(--chu-phu); margin-top: 5px;">Số liệu tính cho: <strong><?=$nhan_thoi_gian?></strong></p>
        </div>
        
        <form method="GET" class="filter-section">
            <select name="type" onchange="this.form.submit()">
                <option value="month" <?=$loai_thong_ke=='month'?'selected':''?>>Xem theo Tháng</option>
                <option value="year" <?=$loai_thong_ke=='year'?'selected':''?>>Xem theo Năm</option>
            </select>
            <span style="margin: 0 10px; color: #ccc;">|</span>
            <?php if($loai_thong_ke == 'month'): ?>
                <input type="month" name="month" value="<?=$thang_chon?>" onchange="this.form.submit()">
            <?php else: ?>
                <select name="year" onchange="this.form.submit()">
                    <?php for($i=date('Y'); $i>=2020; $i--) echo "<option value='$i' ".($nam_chon==$i?'selected':'').">$i</option>"; ?>
                </select>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid-kpi">
        <div class="card-kpi">
            <h3>Tổng tiền thu về</h3>
            <span class="value"><?= number_format($tong_doanh_thu) ?> đ</span>
            <small style="color: var(--chu-phu);">Tiền bán vé thực tế</small>
        </div>

        <div class="card-kpi">
            <h3>Số lượng vé bán ra</h3>
            <span class="value"><?= number_format($tong_so_ve) ?> vé</span>
            <small>Ghế có khách: <strong><?= number_format($ty_le_lap_day, 1) ?>%</strong></small>
        </div>

        <div class="card-kpi">
            <h3>Trung bình mỗi vé</h3>
            <span class="value"><?= number_format($gia_ve_trung_binh) ?> đ</span>
            <small>Số tiền thu được trên 1 vé</small>
        </div>

        <div class="card-kpi">
            <h3>So với kỳ trước</h3>
            <span class="value <?= ($ty_le_tang_truong >= 0) ? 'up' : 'down' ?>">
                <?= ($ty_le_tang_truong >= 0) ? 'Tăng' : 'Giảm' ?> <?= number_format(abs($ty_le_tang_truong), 1) ?>%
            </span>
            <small>Lệch: <?= number_format(abs($so_tien_chenh_lech)) ?> đ</small>
        </div>
    </div>

    <div class="bi-grid">
        <div class="chart-box">
            <h3>Xếp hạng tiền thu được theo Tuyến bay</h3>
            <div style="height: 380px;"><canvas id="bieuDoDoanhThu"></canvas></div>
        </div>

        <div class="chart-box">
            <h3>Doanh thu theo các Hãng bay</h3>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tên hãng</th>
                        <th style="text-align:center">Số vé</th>
                        <th style="text-align:right">Tiền thu về</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($ds_hang_bay) > 0):
                        mysqli_data_seek($ds_hang_bay, 0);
                        while($hang = mysqli_fetch_assoc($ds_hang_bay)): 
                    ?>
                    <tr>
                        <td><strong><?=$hang['TENHANG']?></strong></td>
                        <td style="text-align:center"><?=number_format($hang['sl_ve'])?></td>
                        <td style="text-align:right; font-weight:bold;"><?=number_format($hang['doanh_thu'])?> đ</td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" style="text-align:center; padding: 40px; color: #999;">Không có dữ liệu bán vé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3 style="margin-top: 30px;">Tỉ lệ ghế có khách (%)</h3>
            <div style="height: 200px;"><canvas id="bieuDoLapDay"></canvas></div>
        </div>
        <div style="display: flex; gap: 10px;">
    <a href="index.php?action=phantich&month=<?=$thang_chon?>&type=<?=$loai_thong_ke?>" class="btn-analysis">
         Xem phân tích & Đề xuất
    </a>
</div>
    </div>
</div>

<script>
const nhanTuyenBay = [<?php 
    if(mysqli_num_rows($ds_tuyen_bay) > 0) {
        mysqli_data_seek($ds_tuyen_bay, 0);
        while($tuyen = mysqli_fetch_assoc($ds_tuyen_bay)) echo "'".$tuyen['SANBAYDI']."-".$tuyen['SANBAYDEN']."',"; 
    }
?>];
const duLieuDoanhThu = [<?php 
    if(mysqli_num_rows($ds_tuyen_bay) > 0) {
        mysqli_data_seek($ds_tuyen_bay, 0);
        while($tuyen = mysqli_fetch_assoc($ds_tuyen_bay)) echo $tuyen['doanh_thu'].","; 
    }
?>];

const nhanLapDay = [<?php 
    if(mysqli_num_rows($ds_lap_day) > 0) {
        mysqli_data_seek($ds_lap_day, 0);
        while($ld = mysqli_fetch_assoc($ds_lap_day)) echo "'".$ld['SANBAYDI']."-".$ld['SANBAYDEN']."',"; 
    }
?>];
const duLieuLapDay = [<?php 
    if(mysqli_num_rows($ds_lap_day) > 0) {
        mysqli_data_seek($ds_lap_day, 0);
        while($ld = mysqli_fetch_assoc($ds_lap_day)) echo round($ld['ty_le'], 1).","; 
    }
?>];

// Biểu đồ Cột Doanh Thu
new Chart(document.getElementById('bieuDoDoanhThu'), {
    type: 'bar',
    data: {
        labels: nhanTuyenBay,
        datasets: [{
            label: 'Doanh thu (₫)',
            data: duLieuDoanhThu,
            backgroundColor: '#1a73e8',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
    }
});


new Chart(document.getElementById('bieuDoLapDay'), {
    type: 'bar',
    data: {
        labels: nhanLapDay,
        datasets: [{
            data: duLieuLapDay,
            backgroundColor: '#1db954',
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: { callbacks: { label: (context) => ' ' + context.raw + '%' } }
        },
        scales: { x: { max: 100, beginAtZero: true }, y: { grid: { display: false } } }
    }
});
</script>