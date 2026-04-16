<?php
$mysqli = new mysqli("localhost","root","","quan_ly_ve_may_bay_new");

// kiemta connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}
?>