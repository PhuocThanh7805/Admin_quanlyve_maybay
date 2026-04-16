<?php
$sql_lietke_maybay = "
    SELECT 
        mb.MAMAYBAY,
        mb.TENMAYBAY,
        mb.SOGHE_FC,
        mb.SOGHE_BC,
        mb.SOGHE_EC,
        hmb.tenhang AS TENHANG
    FROM maybay mb
    LEFT JOIN hangmaybay hmb ON mb.MAHANG = hmb.MAHANG
    ORDER BY mb.MAMAYBAY ASC
";

$query = mysqli_query($mysqli, $sql_lietke_maybay);

if(!$query){
    die("Lỗi SQL: " . mysqli_error($mysqli));
}
?>
<style>
/* ===== CONTAINER ===== */
.table-container{
    margin: 20px;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #ddd;
    overflow: hidden;
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

/* ===== HEADER ===== */
.container-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.container-header h3{
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

/* NÚT THÊM */
.btn-them{
    text-decoration: none;
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: #fff;
    padding: 7px 14px;
    border-radius: 5px;
    font-size: 13px;
    transition: 0.25s;
}

.btn-them:hover{
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,114,255,0.3);
}

/* ===== TABLE ===== */
.flight_table{
    width: 100%;
    border-collapse: collapse;
}

/* HEADER TABLE */
.flight_table th{
    background: #f8f9fa;
    padding: 12px;
    font-size: 11px;
    text-transform: uppercase;
    color: #555;
    border: 1px solid #ddd;
}

/* BODY */
.flight_table td{
    padding: 12px 10px;
    font-size: 13px;
    border: 1px solid #ddd;
}

/* HOVER */
.flight_table tbody tr:hover{
    background: #fcfcfc;
}

/* TEXT PHỤ */
.flight_table small{
    color: #888;
    font-size: 11px;
}

/* ===== BUTTON ===== */
.btn-action{
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

.btn-action:hover{
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,114,255,0.4);
}

/* NÚT XÓA */
.btn-delete{
    background: linear-gradient(135deg, #ff4d4d, #c0392b);
}
</style>
<div class="table-container">
    <div class="container-header">
        <h3>Danh Sách Máy Bay</h3>
        <a href="index.php?action=them_maybay" class="btn-them">Thêm mới</a>
    </div>

    <table class="flight_table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã</th>
                <th>Tên máy bay</th>
                <th>Hãng</th>
                <th>Ghế</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            $i++;

            $h1 = (int)$row['SOGHE_FC'];
            $tg = (int)$row['SOGHE_BC'];
            $pt = (int)$row['SOGHE_EC'];

            $total = $h1 + $tg + $pt;
        ?>
            <tr>
                <td><?=$i?></td>

                <td><?=htmlspecialchars($row['MAMAYBAY'])?></td>

                <td>
                    <b><?=htmlspecialchars($row['TENMAYBAY'])?></b><br>
                    <small>Tổng: <?=$total?> ghế</small>
                </td>

                <td><?= $row['TENHANG'] ?? '---' ?></td>

                <td>
                    H1: <?=$h1?> | TG: <?=$tg?> | PT: <?=$pt?>
                </td>

                <td>
                    <a href="index.php?action=sua_maybay&MAMAYBAY=<?=$row['MAMAYBAY']?>" class="btn-action">Sửa</a>
                    <a href="modules/quanlymaybay/xuly_maybay.php?type=xoa&MAMAYBAY=<?=$row['MAMAYBAY']?>" 
   onclick="return confirm('Bạn có chắc chắn muốn xóa máy bay này?')" 
   class="btn-action btn-delete">Xóa</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>