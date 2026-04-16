<?php

$loai_thong_ke = isset($_GET['type']) ? mysqli_real_escape_string($mysqli, $_GET['type']) : 'month';
$thang_chon = isset($_GET['month']) ? mysqli_real_escape_string($mysqli, $_GET['month']) : date('Y-m');
$nam_chon = isset($_GET['year']) ? mysqli_real_escape_string($mysqli, $_GET['year']) : date('Y');


if ($loai_thong_ke == 'month') {
    $dk_ve = "DATE_FORMAT(NGAYDATVE,'%Y-%m') = '$thang_chon'";
    $dk_hd = "DATE_FORMAT(NGAYLAPHOADON,'%Y-%m') = '$thang_chon'";
    $nhan_thoi_gian = "Tháng " . date('m/Y', strtotime($thang_chon));
    $ky_truoc = date('Y-m', strtotime($thang_chon . " -1 month"));
    $dk_hd_ky_truoc = "DATE_FORMAT(NGAYLAPHOADON,'%Y-%m') = '$ky_truoc'";
} else {
    $dk_ve = "YEAR(NGAYDATVE) = '$nam_chon'";
    $dk_hd = "YEAR(NGAYLAPHOADON) = '$nam_chon'";
    $nhan_thoi_gian = "Năm " . $nam_chon;
    $ky_truoc = $nam_chon - 1;
    $dk_hd_ky_truoc = "YEAR(NGAYLAPHOADON) = '$ky_truoc'";
}

// doanh thu + ve
$kpi = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(TONGTIEN) as dt, COUNT(MAHOADON) as sl FROM hoadon WHERE $dk_hd"));
$tong_doanh_thu = $kpi['dt'] ?? 0;

// doanh thu trc tăng
$kpi_old = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT SUM(TONGTIEN) as dt FROM hoadon WHERE $dk_hd_ky_truoc"));
$doanh_thu_ky_truoc = $kpi_old['dt'] ?? 0;
$so_tien_chenh_lech = $tong_doanh_thu - $doanh_thu_ky_truoc;
$ty_le_tang_truong = ($doanh_thu_ky_truoc > 0) ? ($so_tien_chenh_lech / $doanh_thu_ky_truoc) * 100 : 0;

// ghe full
$hieu_suat = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT 
    COUNT(v.MAVE) as tong_ve,
    SUM(COALESCE(mb.SOGHE_EC,0) + COALESCE(mb.SOGHE_FC,0) + COALESCE(mb.SOGHE_BC,0)) as tong_suc_chua
    FROM ve v
    JOIN chuyenbay cb ON v.MACHUYENBAY = cb.MACHUYENBAY
    JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
    WHERE $dk_ve"));
$tong_so_ve = $hieu_suat['tong_ve'] ?? 0;
$tong_suc_chua = $hieu_suat['tong_suc_chua'] ?? 0;
$ty_le_lap_day = ($tong_suc_chua > 0) ? ($tong_so_ve / $tong_suc_chua) * 100 : 0;
$gia_ve_trung_binh = ($tong_so_ve > 0) ? $tong_doanh_thu / $tong_so_ve : 0;
?>

<style>
    :root { 
        --neutral-100: #ffffff;
        --neutral-200: #f1f3f4;
        --neutral-300: #dadce0;
        --neutral-700: #5f6368;
        --neutral-900: #202124;
        --primary-blue: #1a73e8;
        --success-green: #137333;
        --success-bg: #e6f4ea;
        --error-red: #d93025;
        --error-bg: #fce8e6;
        --action-bg: #f8f9fa;
    }

    .analysis-body { 
        padding: 40px 20px; 
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
        background: var(--neutral-100); 
        color: var(--neutral-900); 
        max-width: 1200px; 
        margin: auto; 
    }
    
    /* Header: Thanh lịch và tối giản */
    .analysis-header { 
        border-bottom: 1px solid var(--neutral-300); 
        padding-bottom: 24px; 
        margin-bottom: 40px; 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
    }
    
    .analysis-header h1 { 
        font-size: 28px; 
        font-weight: 500; 
        margin: 0; 
        letter-spacing: -0.5px;
    }

    .btn-back { 
        padding: 8px 16px; 
        background: transparent; 
        color: var(--neutral-700); 
        text-decoration: none; 
        border-radius: 4px; 
        font-size: 14px; 
        font-weight: 500; 
        border: 1px solid var(--neutral-300);
        transition: all 0.2s;
    }
    .btn-back:hover { background: var(--neutral-200); color: var(--neutral-900); }

    /* Lưới nội dung: Khoảng cách rộng rãi */
    .grid-analysis { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 32px; 
    }

    /* Thẻ Card: Loại bỏ đổ bóng mè nheo, dùng border mảnh */
    .card { 
        border: 1px solid var(--neutral-300); 
        border-radius: 4px; 
        padding: 30px; 
        background: #fff; 
        display: flex;
        flex-direction: column;
    }

    .card h3 { 
        font-size: 16px; 
        font-weight: 600;
        color: var(--neutral-900); 
        margin: 0 0 20px 0; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Insight Box: Mạch lạc, dễ đọc */
    .insight-box { 
        padding: 16px; 
        border-radius: 4px; 
        font-size: 15px; 
        line-height: 1.6; 
        margin-bottom: 24px;
        border: 1px solid var(--neutral-300);
        background: var(--neutral-100);
    }

    /* Trạng thái Tốt/Xấu dựa trên màu chữ và nền nhạt */
    .insight-box.alert { 
        border-color: #f5c2c7; 
        background: var(--error-bg); 
        color: var(--error-red);
    }
    .insight-box.good { 
        border-color: #badbcc; 
        background: var(--success-bg); 
        color: var(--success-green);
    }

    /* Action Plan: Nhìn như một checklist công việc */
    .action-plan { 
        background: var(--action-bg); 
        padding: 20px; 
        border-radius: 4px; 
        border-top: 2px solid var(--primary-blue);
    }

    .action-plan h4 { 
        margin: 0 0 12px 0; 
        font-size: 13px; 
        color: var(--primary-blue); 
        font-weight: 700;
    }

    .action-plan ul { margin: 0; padding-left: 18px; }
    .action-plan li { 
        margin-bottom: 10px; 
        color: var(--neutral-700); 
        font-size: 14px; 
    }

    /* Badge: Nhỏ gọn, không chiếm diện tích */
    .badge { 
        padding: 2px 8px; 
        border-radius: 2px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase;
    }
    .badge-red { background: var(--error-red); color: white; }
    .badge-green { background: var(--success-green); color: white; }

    /* Loại bỏ màu sắc riêng cho card cuối, giữ tính đồng nhất */
    .card-special { border-color: var(--neutral-300) !important; background: #fff !important; }

</style>

<div class="analysis-body">
    <div class="analysis-header">
        <div>
            <h1 style="font-size: 24px; margin: 0;">Phuoc Thanh phân tích dữ liệu</h1>
            <p style="color: #718096; margin: 5px 0 0 0;">Dựa trên kết quả kinh doanh: <strong><?=$nhan_thoi_gian?></strong></p>
        </div>
        <a href="javascript:history.back()" class="btn-back">← Quay lại Thống kê</a>
    </div>

    <div class="grid-analysis">
        
        <div class="card">
            <h3>✈️ Tình trạng full ghế của chuyến bay</h3>
            <div class="insight-box <?=$ty_le_lap_day < 50 ? 'alert' : 'good'?>">
                Tỉ lệ ghế có khách đạt <strong><?=number_format($ty_le_lap_day, 1)?>%</strong>. 
                <?php if($ty_le_lap_day < 50): ?>
                    <span class="badge badge-red">Thấp</span>
                    <p>Hệ thống đang vận hành dưới công suất. Nhiều chuyến bay cất cánh với hơn nửa số ghế trống.</p>
                <?php else: ?>
                    <span class="badge badge-green">Tốt</span>
                    <p>Khả năng khai thác chuyến bay đang ở mức ổn định và hiệu quả.</p>
                <?php endif; ?>
            </div>
            <div class="action-plan">
                <h4>💡 Đề xuất tối ưu:</h4>
                <ul>
                    <li>Nếu tỉ lệ thấp: Chạy chương trình "Giờ vàng giá rẻ" để kích cầu người mua.</li>
                    <li>Xem xét gộp các chuyến bay có ít khách vào cùng một khung giờ để giảm chi phí nhiên liệu.</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>💰 Phân tích tăng trưởng</h3>
            <div class="insight-box <?=$ty_le_tang_truong < 0 ? 'alert' : 'good'?>">
                Doanh thu đạt <strong><?=number_format($tong_doanh_thu)?> đ</strong>. 
                Biến động so với kỳ trước: 
                <strong style="color: <?=$ty_le_tang_truong >= 0 ? 'var(--success)' : 'var(--danger)'?>">
                    <?=$ty_le_tang_truong >= 0 ? '+' : ''?><?=number_format($ty_le_tang_truong, 1)?>%
                </strong>
            </div>
            <div class="action-plan">
                <h4>💡 Đề xuất hành động:</h4>
                <ul>
                    <?php if($ty_le_tang_truong < 0): ?>
                        <li>Doanh thu đang sụt giảm, cần kiểm tra lại các tuyến bay chủ lực xem có bị cạnh tranh giá không.</li>
                    <?php else: ?>
                        <li>Đà tăng trưởng tốt, có thể cân nhắc tăng thêm 1-2 chuyến bay/tuần cho các tuyến đang "hot".</li>
                    <?php endif; ?>
                    <li>Duy trì mức giá vé trung bình khoảng <strong><?=number_format($gia_ve_trung_binh)?> đ</strong> để đảm bảo tính cạnh tranh.</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>🚩 Quản lý tuyến bay</h3>
            <div class="insight-box">
                Dựa trên số lượng <strong><?=number_format($tong_so_ve)?> vé</strong> đã bán, các tuyến bay đang có sự phân hóa rõ rệt về nhu cầu khách hàng.
            </div>
            <div class="action-plan">
                <h4>💡 Đề xuất hành động:</h4>
                <ul>
                    <li>Tập trung ngân sách quảng cáo vào các tuyến có doanh thu cao nhất để tối đa hóa lợi nhuận.</li>
                    <li>Rà soát lại các tuyến có số vé bán ra thấp (dưới 10 vé/tháng) để đánh giá khả năng duy trì.</li>
                </ul>
            </div>
        </div>

        <div class="card" style="background: #fffaf0; border-color: #fbd38d;">
            <h3 style="color: #c05621;">✈️ Chiến lược Hãng hàng không</h3>
            <div class="insight-box" style="border-left-color: #f6ad55;">
                Việc đa dạng hóa các hãng bay giúp khách hàng có nhiều lựa chọn, nhưng cần tập trung vào các đối tác mang lại doanh thu cao.
            </div>
            <div class="action-plan" style="background: #fffaf0; border-color: #f6ad55;">
                <h4 style="color: #c05621;">💡 Đề xuất hành động:</h4>
                <ul>
                    <li>Thỏa thuận lại hợp đồng chiết khấu với các hãng có lượng vé bán lớn nhất tháng này.</li>
                    <li>Ưu tiên hiển thị các hãng bay có tỉ lệ khách lấp đầy ghế cao để tăng uy tín dịch vụ.</li>
                </ul>
            </div>
        </div>

    </div>
</div>