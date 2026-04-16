<?php
$sql = "SELECT * FROM HANHKHACH ORDER BY MAHANHKHACH ASC";
$query = mysqli_query($mysqli, $sql);
?>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #0072ff, #00c6ff);
    --primary-blue: #0072ff;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
}

/* CONTAINER */
.table-container{
    margin: 30px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* HEADER */
.container-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 25px;
    background: var(--primary-gradient);
}

.container-header h3{
    margin: 0;
    color: #fff;
    font-size: 18px;
    font-weight: 700;
}

/* TABLE */
.admin-table{
    width: 100%;
    border-collapse: collapse;
}

/* HEADER TABLE */
.admin-table thead{
    background: #f1f5f9;
}

.admin-table th{
    padding: 12px;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-color);
}

/* BODY */
.admin-table td{
    padding: 14px 12px;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
}

/* HOVER */
.admin-table tbody tr:hover{
    background: #f8fbff;
}

/* BADGE */
.id-badge{
    background: #e2e8f0;
    padding: 4px 8px;
    border-radius: 6px;
    font-family: monospace;
    font-weight: 600;
}

/* PHONE */
.phone-link{
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 500;
}

/* BUTTON */
.btn-action{
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    text-decoration: none;
    color: #fff;
    background: var(--primary-gradient);
    transition: 0.2s;
    display: inline-block;
}

.btn-action:hover{
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,114,255,0.3);
}

/* DELETE */
.btn-delete{
    background: linear-gradient(135deg, #ff4d4d, #c0392b);
}

/* EMPTY */
.empty-box{
    text-align:center;
    padding:50px;
    color:#94a3b8;
}
</style>

<div class="table-container">

    <!-- HEADER -->
    <div class="container-header">
        <h3>👤 Danh sách khách hàng</h3>
        <div style="color:#fff; font-size:13px;">
            Tổng: <b><?=mysqli_num_rows($query)?></b>
        </div>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Họ tên</th>
                <th>SĐT</th>
                <th>CCCD</th>
                <th>Địa chỉ</th>
                <th style="text-align:center;">Hành động</th>
            </tr>
        </thead>

        <tbody>
        <?php if(mysqli_num_rows($query)==0): ?>
            <tr>
                <td colspan="6" class="empty-box">
                    Không có dữ liệu khách hàng
                </td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><span class="id-badge"><?=$row['MAHANHKHACH']?></span></td>

                <td style="font-weight:600;">
                    <?=htmlspecialchars($row['HOTEN'])?>
                </td>

                <td>
                    <a href="tel:<?=$row['SDT']?>" class="phone-link">
                        📞 <?=$row['SDT']?>
                    </a>
                </td>

                <td>
                    <code><?=$row['SOCCCD']?></code>
                </td>

                <td>
                    <?= !empty($row['DIACHI']) 
                        ? htmlspecialchars($row['DIACHI']) 
                        : '<i style="color:#94a3b8">Chưa có</i>' ?>
                </td>

                <td style="text-align:center;">
                    <a href="index.php?action=lichsu_hanhkhach&ma=<?=$row['MAHANHKHACH']?>" class="btn-action">
                        Lịch sử
                    </a>

                    <a href="modules/quanlykhachhang/xoa.php?ma=<?=$row['MAHANHKHACH']?>"
                       onclick="return confirm('Xóa khách hàng?');"
                       class="btn-action btn-delete">
                       Xóa
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>