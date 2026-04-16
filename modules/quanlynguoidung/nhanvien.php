<?php
// Truy vấn danh sách nhân viên
$sql = "SELECT * FROM NHANVIEN ORDER BY MANHANVIEN DESC";
$query = mysqli_query($mysqli, $sql);
?>

<style>
    .table-container { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .admin-table th { background: #f4f6f8; color: #637381; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; padding: 15px; border-bottom: 2px solid #edf2f7; }
    .admin-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #2d3748; }
    .admin-table tr:hover { background-color: #f8fafc; }
    
    /* Style cho giới tính */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
    .badge-male { background: #e0f2fe; color: #0369a1; } /* Xanh dương cho Nam */
    .badge-female { background: #fdf2f8; color: #be185d; } /* Hồng cho Nữ */

    .btn-edit { color: #3182ce; text-decoration: none; margin-right: 15px; }
    .btn-delete { color: #e53e3e; text-decoration: none; font-weight: 500; }
</style>

<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; color: #1a202c; font-size: 20px;">👨‍💼 Quản lý nhân viên</h2>
        <a href="index.php?action=quanlynhanvien&query=them" 
           style="background: #2d3e50; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px;">
           + Thêm nhân viên
        </a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th width="80">Mã NV</th>
                <th>Họ tên</th>
                <th width="120">Ngày sinh</th>
                <th width="100">Giới tính</th>
                <th width="150">CCCD</th>
                <th width="130">Thao tác</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if(mysqli_num_rows($query) == 0){
                echo "<tr><td colspan='6' style='text-align:center; padding: 40px; color: #a0aec0;'>Hiện chưa có nhân viên nào trong hệ thống.</td></tr>";
            }

            while($row = mysqli_fetch_assoc($query)){ 
                // Định dạng lại ngày sinh
                $ngaySinh = date('d/m/Y', strtotime($row['NGAYSINH']));
                $isNam = ((int)$row['GIOITINH'] == 1);
            ?>
                <tr>
                    <td><strong><?php echo $row['MANHANVIEN']; ?></strong></td>
                    <td style="font-weight: 500; color: #1a202c;"><?php echo $row['HOTEN']; ?></td>
                    <td><?php echo $ngaySinh; ?></td>
                    <td>
                        <span class="badge <?php echo $isNam ? 'badge-male' : 'badge-female'; ?>">
                            <?php echo $isNam ? "♂ Nam" : "♀ Nữ"; ?>
                        </span>
                    </td>
                    <td><code style="background: #f1f5f9; padding: 2px 5px; border-radius: 4px;"><?php echo $row['SOCCCD']; ?></code></td>
                    <td>
                        <a href="index.php?action=quanlynhanvien&query=sua&id=<?php echo $row['MANHANVIEN']; ?>" class="btn-edit">Sửa</a>
                        <a href="modules/quanlynhanvien/xuly.php?id=<?php echo $row['MANHANVIEN']; ?>&type=xoa" 
                           onclick="return confirm('Xác nhận xóa nhân viên này khỏi hệ thống?');" 
                           class="btn-delete">Xóa</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>