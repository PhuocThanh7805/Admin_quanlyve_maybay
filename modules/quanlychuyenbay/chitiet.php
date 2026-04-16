<?php
include(__DIR__ . '/set_trangthai.php');
require_once(__DIR__ . '/../../includes/dinhgia.php');

$id = $_GET['id'] ?? '';
if (!$id) {
    die("<div style='padding:20px; color:red;'>Lỗi: Thiếu mã số chuyến bay.</div>");
}

$id = mysqli_real_escape_string($mysqli, $id);

/* ========================================================
   1. TRUY VẤN TOÀN BỘ DỮ LIỆU LIÊN QUAN (FULL JOIN)
   ======================================================== */
$sql = "SELECT cb.*, 
               mb.TENMAYBAY, mb.MAHANG, mb.SOGHE_BC, mb.SOGHE_FC, mb.SOGHE_EC,
               tb.GIACOBAN, tb.THOIGIANBAY,
               sb_di.TENSANBAY AS SB_DI, sb_den.TENSANBAY AS SB_DEN,
               dd_di.TENDIADIEM AS DI_DI, dd_den.TENDIADIEM AS DI_DEN,
               dl.DANSO, dl.DIENTICH, dl.MUIGIO, dl.NGONNGU, dl.QUOCGIA, dl.TIENTE, dl.MOTA1, dl.MOTA2
        FROM chuyenbay cb
        LEFT JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
        LEFT JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
        LEFT JOIN sanbay sb_di ON tb.SANBAYDI = sb_di.MASANBAY
        LEFT JOIN sanbay sb_den ON tb.SANBAYDEN = sb_den.MASANBAY
        LEFT JOIN diadiem dd_di ON sb_di.MADIADIEM = dd_di.MADIADIEM
        LEFT JOIN diadiem dd_den ON sb_den.MADIADIEM = dd_den.MADIADIEM
        LEFT JOIN diemdulich dl ON dd_den.MADIADIEM = dl.MADIADIEM
        WHERE cb.MACHUYENBAY = '$id'";

$result = mysqli_query($mysqli, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) die("<div style='padding:20px;'>Hệ thống: Không tìm thấy bản ghi này trong cơ sở dữ liệu.</div>");

$tongGheThietKe = (int)($data['SOGHE_BC'] + $data['SOGHE_FC'] + $data['SOGHE_EC']);
$giaInfo = tinhGiaVeDayDu($mysqli, $id);

$giaHienTai = $giaInfo['tong_gia'] ?? $data['GIACOBAN'];
$soGheCon = $giaInfo['so_ghe_con'] ?? $tongGheThietKe;
$daDat = $tongGheThietKe - $soGheCon;
$st = (int)$data['TRANGTHAI_CB'];
?>

<style>

.admin-container {
    font-family: 'Segoe UI', Arial, sans-serif;
    padding: 20px;
    background: #eef5ff; /* nền xanh nhẹ */
    color: #333;
}

/* CARD */
.detail-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,114,255,0.15);
    max-width: 1100px;
    margin: 0 auto;
    overflow: hidden;
}

/* HEADER */
.card-header {
    padding: 18px 25px;
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
}

/* BODY */
.card-body { padding: 25px; }


.data-section { margin-bottom: 30px; }

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: #0072ff;
    text-transform: uppercase;
    margin-bottom: 12px;
    border-left: 4px solid #00c6ff;
    padding-left: 10px;
}


.table-info {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
    overflow: hidden;
    border-radius: 8px;
}

.table-info th {
    width: 200px;
    background: #f0f6ff;
    color: #555;
    font-weight: 600;
    padding: 12px 15px;
    border: 1px solid #e3ecff;
    text-align: left;
    font-size: 13px;
}

.table-info td {
    padding: 12px 15px;
    border: 1px solid #e3ecff;
    font-size: 13px;
}


.table-info b {
    color: #0072ff;
}


.table-info td[style*="color: #c0392b"] {
    color: #0072ff !important;
}


.btn-group {
    display: flex;
    justify-content: space-between;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn-base {
    padding: 9px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.25s;
    border: none;
    cursor: pointer;
}

/* NÚT CHÍNH */
.btn-gradient {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff !important;
}

.btn-gradient:hover {
    opacity: 0.9;
    box-shadow: 0 4px 12px rgba(0,114,255,0.4);
}

/* NÚT PHỤ */
.btn-secondary {
    background: #64748b;
    color: #fff !important;
}

.btn-secondary:hover {
    background: #475569;
}


.stt-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}


.table-info small {
    color: #64748b;
}

@media (max-width: 768px) {
    .table-info th {
        width: 120px;
    }
}
</style>

<div class="admin-container">
    <div class="detail-card">
        <div class="card-header">
            <h2 style="margin:0; font-size: 18px; color: #ffffff;">HỒ SƠ CHI TIẾT CHUYẾN BAY</h2>
            <span style="font-size: 18px; color: #ffffff;">ID CHUYẾN BAY: <strong><?=$id?></strong></span>
        </div>

        <div class="card-body">
            
            <div class="data-section">
                <div class="section-title">1. Vận hành & Lộ trình</div>
                <table class="table-info">
                    <tr>
                        <th>Mã chuyến bay</th>
                        <td><b style="font-size: 16px; color: #0072ff;"><?=$data['MACHUYENBAY']?></b></td>
                        <th>Máy bay vận hành</th>
                        <td><?=$data['TENMAYBAY']?> (Mã: <?=$data['MAMAYBAY']?>)</td>
                    </tr>
                    <tr>
                        <th>Tuyến bay</th>
                        <td><b><?=$data['DI_DI']?></b> (<?=$data['SB_DI']?>) &rarr; <b><?=$data['DI_DEN']?></b> (<?=$data['SB_DEN']?>)</td>
                        <th>Thời gian bay dự kiến</th>
                        <td><?=$data['THOIGIANBAY']?> Phút</td>
                    </tr>
                    <tr>
                        <th>Thời gian khởi hành</th>
                        <td><?=date('H:i | d/m/Y', strtotime($data['THOIGIANDI']))?></td>
                        <th>Thời gian hạ cánh</th>
                        <td><?=date('H:i | d/m/Y', strtotime($data['THOIGIANDEN']))?></td>
                    </tr>
                </table>
            </div>

            <div class="data-section">
                <div class="section-title">2. Giá vé & Tình trạng ghế</div>
                <table class="table-info">
                    <tr>
                        <th>Giá vé hiện hành</th>
                        <td style="color: #c0392b; font-weight: bold; font-size: 16px;">
                            <?=number_format($giaHienTai)?> VND
                        </td>
                        <th>Trạng thái chuyến bay</th>
                        <td>
                            <?php
                            $status_map = [
                                0 => ['Đã hủy', '#e74c3c'], 1 => ['Đang mở bán', '#2ecc71'], 
                                2 => ['Chờ bay', '#f1c40f'], 3 => ['Đang bay', '#3498db'], 4 => ['Hoàn tất', '#95a5a6']
                            ];
                            $label = $status_map[$st] ?? ['N/A', '#ccc'];
                            ?>
                            <span class="stt-dot" style="background:<?=$label[1]?>"></span>
                            <span style="color:<?=$label[1]?>; font-weight:bold;"><?=$label[0]?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tổng số ghế thiết kế</th>
                        <td><?=$tongGheThietKe?> ghế</td>
                        <th>Tình trạng đặt chỗ</th>
                        <td>
                            <b><?=$daDat?></b> đã đặt / <b><?=$soGheCon?></b> còn trống
                            <div style="font-size: 11px; color: #888; margin-top: 5px;">
                                (Thương gia: <?=$data['SOGHE_BC']?> | Nhất: <?=$data['SOGHE_FC']?> | Phổ thông: <?=$data['SOGHE_EC']?>)
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if($data['QUOCGIA']): ?>
            <div class="data-section">
                <div class="section-title">3. Thông tin địa lý & Điểm đến</div>
                <table class="table-info">
                    <tr>
                        <th>Quốc gia</th>
                        <td><?=$data['QUOCGIA']?></td>
                        <th>Tiền tệ</th>
                        <td><?=$data['TIENTE']?></td>
                    </tr>
                    <tr>
                        <th>Diện tích</th>
                        <td><?=number_format($data['DIENTICH'])?> km²</td>
                        <th>Dân số</th>
                        <td><?=$data['DANSO']?> Triệu người</td>
                    </tr>
                    <tr>
                        <th>Múi giờ</th>
                        <td><?=$data['MUIGIO']?></td>
                        <th>Ngôn ngữ chính</th>
                        <td><?=$data['NGONNGU']?></td>
                    </tr>
                    <tr>
                        <th>Mô tả tổng quan</th>
                        <td colspan="3" style="line-height: 1.6; color: #555;">
                            <i><?=$data['MOTA1']?></i>
                        </td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>

            <div class="btn-group">
                <a href="#" onclick="history.back(); return false;" class="btn-base btn-secondary">
    ⬅ QUAY LẠI
</a>
                
                <div>
                    <?php if($st == 1): ?>
                        <a href="index.php?action=sua&id=<?=$id?>" class="btn-base btn-gradient">✏️ CHỈNH SỬA THÔNG TIN</a>
                    <?php else: ?>
                        <span style="font-size: 12px; color: #999; font-style: italic;">Chuyến bay đã ở trạng thái khóa, không thể chỉnh sửa.</span>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>