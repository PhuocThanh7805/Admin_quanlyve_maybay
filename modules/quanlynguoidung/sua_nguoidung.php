<?php
$sql = "SELECT * FROM users WHERE id='".$_GET['id']."' LIMIT 1";
$query = mysqli_query($mysqli,$sql);
$row = mysqli_fetch_array($query);
?>

<form method="POST" action="modules/quanlynguoidung/xuly_nguoidung.php?id=<?php echo $row['id']; ?>">

<table border="1" width="50%" style="border-collapse:collapse">

<tr>
<td>ID</td>
<td>
<input type="text" value="<?php echo $row['id']; ?>" readonly>
</td>
</tr>

<tr>
<td>Username</td>
<td>
<input type="text" name="username" value="<?php echo $row['username']; ?>">
</td>
</tr>

<tr>
<td>Email</td>
<td>
<input type="email" name="email" value="<?php echo $row['email']; ?>">
</td>
</tr>

<tr>
<td>Role</td>
<td>
<select name="role">
<option value="0" <?php if($row['role']==0) echo "selected"; ?>>User</option>
<option value="1" <?php if($row['role']==1) echo "selected"; ?>>Admin</option>
</select>
</td>
</tr>

<tr>
<td colspan="2">
<input type="submit" name="sua_nguoidung" value="Xác nhận sửa">
</td>
</tr>

</table>

</form>