<?php
include('../../config/config.php');
$username = $_POST['username'];
$email = $_POST['email'];
$role = $_POST['role'];
if(isset($_POST['sua_nguoidung'])){
$sql = "UPDATE users SET
username='".$username."',
email='".$email."',
role='".$role."'
WHERE id='".$_GET['id']."'";

mysqli_query($mysqli,$sql);

header('Location:../../index.php?action=danhsach');
}
?>