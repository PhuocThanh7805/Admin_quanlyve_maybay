<?php
$ma = $_GET['ma'] ?? '';
$ma = mysqli_real_escape_string($mysqli, $ma);

// Lấy tên khách
$ten_kh = '';
if(!empty($ma)){
    $getName = mysqli_query($mysqli, "
        SELECT HOTEN 
        FROM HANHKHACH 
        WHERE MAHANHKHACH = '$ma' 
        LIMIT 1
    ");

    if($getName && $rowName = mysqli_fetch_assoc($getName)){
        $ten_kh = $rowName['HOTEN'];
    }
}

// Query lịch sử vé
$sql = "
SELECT 
    v.MAVE,
    v.MACHUYENBAY,
    v.MAGHE,
    v.GIAVE,
    v.NGAYDATVE,
    v.HOTEN,
    v.GIOITINH,
    v.QUOCTICH,
    v.NGAYSINH,
    v.SDT,
    v.LOAI_HANH_KHACH
FROM VE v
WHERE v.MAHANHKHACH = '$ma'
ORDER BY v.NGAYDATVE DESC
";

$result = mysqli_query($mysqli, $sql);
if(!$result){
    die("Lỗi SQL: " . mysqli_error($mysqli));
}
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
    padding: 18px 25px;
    background: var(--primary-gradient);
    color: #fff;
}

.container-header h3{
    margin: 0;
    font-size: 18px;
}

/* TABLE */
.table-custom{
    width: 100%;
    border-collapse: collapse;
}

.table-custom thead{
    background: #f1f5f9;
}

.table-custom th{
    padding: 12px;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-color);
}

.table-custom td{
    padding: 14px 12px;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
    text-align: center;
}

.table-custom tbody tr:hover{
    background: #f8fbff;
}

/* BADGE */
.badge{
    background: #e2e8f0;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
}

/* PRICE */
.price{
    color: #e11d48;
    font-weight: 700;
}

/* EMPTY */
.empty{
    padding: 50px;
    text-align: center;
    color: #94a3b8;
}

/* BACK BUTTON */
.btn-back{
    display:inline-block;
    margin:15px 25px;
    padding:6px 12px;
    font-size:12px;
    border-radius:6px;
    background:#f1f5f9;
    text-decoration:none;
    color:#333;
}

.btn-back:hover{
    background:#e2e8f0;
}
</style>

<div class="table-container">

    <!-- HEADER -->
    <div class="container-header">
        <h3>
            📄 Lịch sử đặt vé 
            <?php if($ten_kh != ''): ?>
                - <b><?=htmlspecialchars($ten_kh)?></b>
            <?php endif; ?>
        </h3>
    </div>

    <!-- BACK -->
    <a href="javascript:history.back()" class="btn-back">← Quay lại</a>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Mã vé</th>
                <th>Chuyến bay</th>
                <th>Ghế</th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Quốc tịch</th>
                <th>Ngày sinh</th>
                <th>SĐT</th>
                <th>Loại</th>
                <th>Giá</th>
                <th>Ngày đặt</th>
            </tr>
        </thead>

        <tbody>
        <?php if(mysqli_num_rows($result)==0): ?>
            <tr>
                <td colspan="11" class="empty">
                    Không có lịch sử đặt vé
                </td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><span class="badge"><?=$row['MAVE']?></span></td>

                <td><?=$row['MACHUYENBAY']?></td>

                <td><?=$row['MAGHE'] ?? '—'?></td>

                <td style="font-weight:600;">
                    <?=htmlspecialchars($row['HOTEN'])?>
                </td>

                <td><?=$row['GIOITINH']?></td>

                <td><?=$row['QUOCTICH']?></td>

                <td><?=date('d/m/Y', strtotime($row['NGAYSINH']))?></td>

                <td><?=$row['SDT']?></td>

                <td><?=$row['LOAI_HANH_KHACH'] ?? '—'?></td>

                <td class="price">
                    <?=number_format($row['GIAVE'])?>đ
                </td>

                <td>
                    <?=date('d/m/Y H:i', strtotime($row['NGAYDATVE']))?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>