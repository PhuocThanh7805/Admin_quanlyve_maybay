<?php
// --- GIỮ NGUYÊN LOGIC PHP CỦA BẠN ---
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$sql = "
    SELECT tb.*, dd1.TENDIADIEM AS DI, dd2.TENDIADIEM AS DEN 
    FROM tuyenbay tb
    JOIN sanbay sb1 ON tb.SANBAYDI = sb1.MASANBAY
    JOIN sanbay sb2 ON tb.SANBAYDEN = sb2.MASANBAY
    JOIN diadiem dd1 ON sb1.MADIADIEM = dd1.MADIADIEM
    JOIN diadiem dd2 ON sb2.MADIADIEM = dd2.MADIADIEM
";

if ($keyword !== '') {
    $sql .= " WHERE 
        dd1.TENDIADIEM LIKE ? 
        OR dd2.TENDIADIEM LIKE ?
        OR tb.SANBAYDI LIKE ?
        OR tb.SANBAYDEN LIKE ?
        OR tb.MATUYEN LIKE ?
    ORDER BY tb.MATUYEN DESC";

    $stmt = mysqli_prepare($mysqli, $sql);
    $search = "%$keyword%";
    mysqli_stmt_bind_param($stmt, "sssss", $search, $search, $search, $search, $search);
    mysqli_stmt_execute($stmt);
    $query_lietke_tuyen = mysqli_stmt_get_result($stmt);
} else {
    $sql .= " ORDER BY tb.MATUYEN DESC";
    $query_lietke_tuyen = mysqli_query($mysqli, $sql);
}
?>

<!-- GOOGLE FONTS -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ===== CORE DESIGN ===== */
.admin-wrap {
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding: 25px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.03);
    margin: 10px;
}

/* TIÊU ĐỀ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #1a202c;
    letter-spacing: -0.5px;
}

/* TOOLBAR & SEARCH */
.toolbar {
    background: #f8fafc;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #f1f5f9;
}

.search-container {
    display: flex;
    align-items: center;
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 4px 8px;
    transition: 0.3s;
}

.search-container:focus-within {
    border-color: #0072ff;
    box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
}

.search-container input {
    border: none;
    padding: 8px 12px;
    width: 280px;
    outline: none;
    font-size: 14px;
}

/* NÚT BẤM HIỆN ĐẠI */
.btn-action {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #fff !important;
    text-decoration: none;
    border: none;
    cursor: pointer;
    background: #0072ff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-action:hover {
    background: #0056cc;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 114, 255, 0.2);
}

.btn-danger {
    background: #fff;
    color: #e11d48 !important;
    border: 1.5px solid #ffe4e6;
}

.btn-danger:hover {
    background: #e11d48;
    color: #fff !important;
    border-color: #e11d48;
}

/* TABLE MODERN STYLE */
.table-responsive {
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.admin-table th {
    background: #f8fafc;
    padding: 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    border-bottom: 1px solid #f1f5f9;
}

.admin-table td {
    padding: 20px 16px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.admin-table tr:last-child td { border-bottom: none; }
.admin-table tr:hover { background: #fcfdfe; }

/* ROUTE DESIGN */
.route-box {
    display: flex;
    align-items: center;
    gap: 15px;
}

.route-dot {
    width: 35px;
    height: 35px;
    background: #eff6ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0072ff;
    font-size: 18px;
}

.route-info .main-path {
    font-weight: 700;
    color: #1e293b;
    font-size: 15px;
    display: block;
    margin-bottom: 4px;
}

.route-info .sub-path {
    color: #94a3b8;
    font-size: 12px;
    font-weight: 500;
}

/* BADGES */
.badge-time {
    background: #f0fdf4;
    color: #166534;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #dcfce7;
}

.price-tag {
    font-weight: 800;
    color: #0f172a;
    font-size: 16px;
}

/* RESET LINK */
.reset-link {
    color: #94a3b8;
    font-size: 13px;
    text-decoration: none;
    margin-left: 10px;
    transition: 0.2s;
}
.reset-link:hover { color: #e11d48; }
</style>

<div class="admin-wrap">
    <!-- HEADER -->
    <div class="page-header">
        <h2>Quản lý Tuyến bay</h2>
        <a href="index.php?action=them_tuyenbay" class="btn-action">
            <span>+</span> Thêm Tuyến mới
        </a>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <form method="GET" action="index.php" style="display:flex; align-items:center;">
            <input type="hidden" name="action" value="lietke_tuyenbay">
            <div class="search-container">
                <input type="text" name="keyword" 
                       placeholder="Tìm mã số, thành phố đi, đến..." 
                       value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" style="background:none; border:none; cursor:pointer; padding: 0 10px;">🔍</button>
            </div>
            <?php if($keyword !== ''): ?>
                <a href="index.php?action=lietke_tuyenbay" class="reset-link">Làm mới</a>
            <?php endif; ?>
        </form>
        <div>
            <span style="font-size: 14px; color: #64748b;">Hiển thị <strong><?= mysqli_num_rows($query_lietke_tuyen) ?></strong> kết quả</span>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th width="120">Mã Tuyến</th>
                    <th>Lộ trình chi tiết</th>
                    <th>Thời lượng bay</th>
                    <th>Giá vé cơ bản</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if(mysqli_num_rows($query_lietke_tuyen) == 0){
                echo "<tr><td colspan='5' style='text-align:center; padding:80px; color:#94a3b8;'>
                        <div style='font-size: 40px; margin-bottom: 10px;'>📂</div>
                        Không tìm thấy tuyến bay nào phù hợp.
                      </td></tr>";
            }

            while($row = mysqli_fetch_array($query_lietke_tuyen)){ 
            ?>
                <tr>
                    <td>
                        <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-weight: 700; color: #475569; font-size: 13px;">
                            #<?= $row['MATUYEN'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="route-box">
                            <div class="route-dot">✈</div>
                            <div class="route-info">
                                <span class="main-path"><?= $row['DI'] ?> → <?= $row['DEN'] ?></span>
                                <span class="sub-path"><?= $row['SANBAYDI'] ?> — <?= $row['SANBAYDEN'] ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-time">
                            🕒 <?= $row['THOIGIANBAY'] ?> phút
                        </span>
                    </td>
                    <td>
                        <span class="price-tag"><?= number_format($row['GIACOBAN'], 0, ',', '.') ?>đ</span>
                    </td>
                    <td align="center">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="index.php?action=sua_tuyenbay&id=<?= $row['MATUYEN'] ?>" 
                               class="btn-action" style="padding: 8px 15px; background: #f1f5f9; color: #1e293b !important; border: 1px solid #e2e8f0;">
                                Sửa
                            </a>
                            <a href="modules/quanlytuyenbay/xuly_tuyen.php?id=<?= $row['MATUYEN'] ?>&type=xoa"
                               onclick="return confirm('Xác nhận xóa tuyến bay này?')"
                               class="btn-action btn-danger" style="padding: 8px 15px;">
                                Xóa
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>