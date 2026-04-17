<?php
include(__DIR__ . '/set_trangthai.php');
require_once(__DIR__ . '/../../includes/dinhgia.php');

if (!function_exists('tinhGiaVeDayDu')) {
    echo "<p style='color:red; padding:20px; border:1px solid red;'>Lỗi: Không tìm thấy hàm định giá.</p>";
    return;
}
$ngay_loc = isset($_GET['ngay_loc']) ? $_GET['ngay_loc'] : date('Y-m-d');
$keyword  = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$keyword  = mysqli_real_escape_string($mysqli, $keyword);

$where = [];

if (!empty($keyword)) {
    $where[] = "cb.MACHUYENBAY LIKE '%$keyword%'";
} else {
    $where[] = "DATE(cb.THOIGIANDI) = '$ngay_loc'";
}

$where_sql = implode(" AND ", $where);
$sql = "SELECT cb.*, mb.TENMAYBAY, tb.GIACOBAN AS GIA_TUYEN, 
               sb_di.TENSANBAY AS TENSANBAY_DI, sb_den.TENSANBAY AS TENSANBAY_DEN,
               dd_di.TENDIADIEM AS DIADIEM_DI, dd_den.TENDIADIEM AS DIADIEM_DEN,
               IFNULL(ghe_data.tong_ghe, 0) AS tong_ghe_thiet_ke
        FROM chuyenbay cb
        LEFT JOIN maybay mb ON cb.MAMAYBAY = mb.MAMAYBAY
        LEFT JOIN tuyenbay tb ON cb.MATUYEN = tb.MATUYEN
        LEFT JOIN sanbay sb_di ON tb.SANBAYDI = sb_di.MASANBAY
        LEFT JOIN sanbay sb_den ON tb.SANBAYDEN = sb_den.MASANBAY
        LEFT JOIN diadiem dd_di ON sb_di.MADIADIEM = dd_di.MADIADIEM
        LEFT JOIN diadiem dd_den ON sb_den.MADIADIEM = dd_den.MADIADIEM
        LEFT JOIN (
            SELECT MAMAYBAY, COUNT(*) AS tong_ghe 
            FROM ghe GROUP BY MAMAYBAY
        ) ghe_data ON ghe_data.MAMAYBAY = cb.MAMAYBAY
        WHERE $where_sql
        ORDER BY cb.THOIGIANDI ASC";

$query = mysqli_query($mysqli, $sql);
$groups = ['truoc'=>[], 'dang'=>[], 'da_den'=>[], 'da_huy'=>[]];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $st = (int)$row['TRANGTHAI_CB'];
        if ($st == 1 || $st == 2) $groups['truoc'][] = $row;
        elseif ($st == 3) $groups['dang'][] = $row;
        elseif ($st == 4) $groups['da_den'][] = $row;
        elseif ($st == 0) $groups['da_huy'][] = $row;
    }
}
?>

<style>
.admin-wrap {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    color: #333;
    padding: 10px;
    
}

/* =========================
   2. TABLE
========================= */
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #ccc;
    margin-top: 10px;
}

.admin-table th,
.admin-table td {
    border: 1px solid #ddd;
    padding: 12px 10px;
    text-align: left;
    font-size: 13px;
}

.admin-table th {
    background: #f8f9fa;
    text-transform: uppercase;
    font-size: 11px;
    color: #555;
}

.admin-table tr:hover {
    background: #fcfcfc;
}

.tab-nav {
    margin: 20px 0 15px 0;
    border-bottom: 2px solid #2c3e50;
}

.tab-link {
    padding: 10px 20px;
    text-decoration: none;
    margin-right: 4px;
    display: inline-block;
    border-radius: 6px 6px 0 0;
    font-size: 13px;
    transition: 0.25s;
    background: #e9eef3;
    color: #555;
}


.tab-link.active {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
    font-weight: bold;
}


.tab-link:hover {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
}

.price-cell {
    color: #c0392b;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    font-size: 14px;
}

.price-sub {
    display: block;
    font-size: 10px;
    color: #888;
    margin-top: 2px;
}
.btn-action {
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 12px;
    color: #fff;
    border: none;
    text-decoration: none;
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    transition: all 0.25s ease;
    box-shadow: 0 2px 6px rgba(0,114,255,0.3);
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,114,255,0.4);
}



.btn-view {
    background: linear-gradient(135deg, #00c6ff, #0072ff);
}


.btn-danger {
    background: linear-gradient(135deg, #ff4d4d, #c0392b);
}
.btn-add {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    padding: 8px 15px;
}

.stt-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}
.toolbar {
    background: #fdfdfd;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
}
.text-muted {
    color: #888;
}

.text-small {
    font-size: 12px;
}
/* FORCE tất cả nút về gradient */
.btn-action {
    background: linear-gradient(135deg, #0072ff, #00c6ff) !important;
    color: #fff !important;
    border: none !important;
}


.btn-action:hover {
    opacity: 0.95;
    transform: translateY(-1px);
}

input[name="keyword"] {
    transition: 0.2s;
}

input[name="keyword"]:focus {
    border-color: #0072ff;
    box-shadow: 0 0 5px rgba(0,114,255,0.3);
}

.action-group {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    flex-wrap: nowrap;
}

</style>

<div class="admin-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; font-size: 20px;">QUẢN LÝ CHUYẾN BAY</h2>
        <a href="index.php?action=them" class="btn-action" style="background:#2c3e50; color:#fff; border:none; padding: 8px 15px; text-decoration: none; border-radius: 4px;">+ THÊM CHUYẾN MỚI</a>
    </div>

    <div class="admin-wrap">
   
    <div style="background: #fdfdfd; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <form method="GET" action="index.php" id="filterForm" 
      style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">

    <input type="hidden" name="action" value="<?=$_GET['action']?>">

  
    <div style="display: flex; align-items: center; gap: 10px;">
        <label style="font-weight: bold; font-size: 13px;">Ngày:</label>
        <input type="date" name="ngay_loc" value="<?=$ngay_loc?>" 
               onchange="this.form.submit()"
               style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px;">
    </div>

   
    <div style="display: flex; align-items: center; gap: 10px;">
        <input type="text" name="keyword" placeholder="🔍 Tìm bằng mã"
               value="<?= $_GET['keyword'] ?? '' ?>"
               style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 20px; width: 220px;">
    </div>


    <button type="submit" class="btn-action">Tìm</button>

    <!-- RESET -->
    <a href="index.php?action=<?=$_GET['action']?>"
       style="font-size: 12px; color: #e74c3c;">Reset</a>

</form>

        <div style="text-align: right;">
            <span style="font-size: 12px; color: #777;">Hiển thị: <strong><?=date('d/m/Y', strtotime($ngay_loc))?></strong></span>
        </div>
    </div>


    <div class="tab-nav">
        <a href="#" class="tab-link active" onclick="openTab(event, 't1')">Hoạt động (<?=count($groups['truoc'])?>)</a>
        <a href="#" class="tab-link" onclick="openTab(event, 't2')">Đang Bay (<?=count($groups['dang'])?>)</a>
        <a href="#" class="tab-link" onclick="openTab(event, 't3')">Hoàn Tất (<?=count($groups['da_den'])?>)</a>
        <a href="#" class="tab-link" onclick="openTab(event, 't4')">Đã Hủy (<?=count($groups['da_huy'])?>)</a>
    </div>

    <div id="t1" class="tab-content" style="display:block;"><?php renderSimpleTable($groups['truoc'], $mysqli); ?></div>
    <div id="t2" class="tab-content" style="display:none;"><?php renderSimpleTable($groups['dang'], $mysqli); ?></div>
    <div id="t3" class="tab-content" style="display:none;"><?php renderSimpleTable($groups['da_den'], $mysqli); ?></div>
    <div id="t4" class="tab-content" style="display:none;"><?php renderSimpleTable($groups['da_huy'], $mysqli); ?></div>
</div>

<?php
function renderSimpleTable($data, $mysqli) {
    if (empty($data)) { 
        echo "<div style='padding:40px; text-align:center; border:1px dashed #ccc; color:#999; background:#fff;'>Không có dữ liệu hiển thị.</div>"; 
        return; 
    }
?>
    <table class="admin-table">
        <thead>
            <tr>
                <th width="80">Mã Số</th>
                <th>Máy Bay</th>
                <th>Lộ Trình</th>
                <th width="110">Khởi Hành</th>
                <th width="140">Giá Vé</th>
                <th width="100">Ghế (Đặt/Tổng)</th>
                <th width="100">Trạng Thái</th>
                <th width="110">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row): 
                $id = $row['MACHUYENBAY'];
                $st = (int)$row['TRANGTHAI_CB'];
                
                $giaInfo = tinhGiaVeDayDu($mysqli, $id);
                $giaGoc = (int)$row['GIA_TUYEN'];
                

                $giaHienHanh = (int)($giaInfo['tong_gia'] ?? 0);
                
                if ($giaHienHanh <= 0) $giaHienHanh = $giaGoc;

                $tongGhe  = (int)($giaInfo['tong_ghe'] ?? $row['tong_ghe_thiet_ke']);
                $soGheCon = (int)($giaInfo['so_ghe_con'] ?? $tongGhe);
                $daDat    = $tongGhe - $soGheCon;
            ?>
            <tr>
                <td><strong><?=$id?></strong></td>
                <td>
                    <span style="font-size: 12px;"><?=$row['TENMAYBAY']?></span><br>
                    <small style="color: #888;"><?=$row['MAMAYBAY']?></small>
                </td>
                <td>
                    <div style="font-size: 13px;"><strong><?=$row['DIADIEM_DI']?></strong> → <strong><?=$row['DIADIEM_DEN']?></strong></div>
                    <small style="color: #999; font-size: 10px;"><?=$row['TENSANBAY_DI']?></small>
                </td>
                <td>
                    <span style="font-weight: bold;"><?=date('H:i', strtotime($row['THOIGIANDI']))?></span><br>
                    <small><?=date('d/m/y', strtotime($row['THOIGIANDI']))?></small>
                </td>
                
                <td class="price-cell">
                    <?= number_format($giaHienHanh) ?>đ
                    <?php if($giaHienHanh != $giaGoc && $giaGoc > 0): ?>
                        <span class="price-sub" style="color: <?=$giaHienHanh > $giaGoc ? '#e67e22' : '#2980b9'?>">
                            Gốc: <?=number_format($giaGoc)?>đ 
                            (<?= $giaHienHanh > $giaGoc ? '↑' : '↓' ?>)
                        </span>
                    <?php endif; ?>
                </td>

                <?php 
                    $tile_lap_day = ($tongGhe > 0) ? ($daDat / $tongGhe) * 100 : 0;
                    $color_ghe = '#333';
                    if($tile_lap_day >= 90) $color_ghe = '#e74c3c'; // Đỏ khi gần đầy
                    elseif($tile_lap_day >= 70) $color_ghe = '#e67e22'; // Cam
                ?>
                <td align="center">
                    <div style="font-weight:bold; color: <?=$color_ghe?>">
                        <?=$daDat?> / <?=$tongGhe?>
                    </div>
                    <div style="width: 100%; background: #eee; height: 4px; border-radius: 2px; margin-top: 4px;">
                        <div style="width: <?=$tile_lap_day?>%; background: <?=$color_ghe?>; height: 100%; border-radius: 2px;"></div>
                    </div>
                </td>
                
                <td>
                    <?php
                    $status_map = [
                        0 => ['Hủy', '#e74c3c'], 1 => ['Mở bán', '#2ecc71'], 
                        2 => ['Sắp bay', '#f1c40f'], 3 => ['Đang bay', '#3498db'], 4 => ['Hoàn tất', '#95a5a6']
                    ];
                    $label = $status_map[$st] ?? ['N/A', '#ccc'];
                    echo '<span class="stt-dot" style="background:'.$label[1].'"></span>' . $label[0];
                    ?>
                </td>
                <td>
    <div class="action-group">
        <?php if($st == 1): ?>
            <a href="index.php?action=sua&id=<?=$id?>&back=<?=urlencode($_SERVER['REQUEST_URI'])?>" class="btn-action">
    Sửa
</a>
            <a href="index.php?action=chitiet_chuyenbay&id=<?=$id?>" class="btn-action" style="background:#3498db;">Xem</a>
            <a href="javascript:void(0)"  
   onclick="confirmCancel('<?=$id?>', '<?=urlencode($_SERVER['REQUEST_URI'])?>')" 
   class="btn-action btn-danger" 
   style="background: linear-gradient(135deg, #ff4d4d, #c0392b) !important;">
   Hủy
</a>
        <?php else: ?>
            <a href="index.php?action=chitiet_chuyenbay&id=<?=$id?>" class="btn-action" style="background:#3498db;">Xem</a>
            <span style="color:#999; font-size:12px;">Đã khóa</span>
        <?php endif; ?>
    </div>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php } ?>

<script>
function openTab(evt, tabId) {
    let contents = document.getElementsByClassName("tab-content");
    for (let c of contents) c.style.display = "none";
    let links = document.getElementsByClassName("tab-link");
    for (let l of links) l.classList.remove("active");
    document.getElementById(tabId).style.display = "block";
    evt.currentTarget.classList.add("active");
    evt.preventDefault();
}

function confirmCancel(id) {
    if(confirm('Xác nhận HỦY chuyến ' + id + '?')) {
        window.location.href = 'modules/quanlychuyenbay/xuly.php?type=huy&machuyenbay=' + id;
    }
}
</script>
