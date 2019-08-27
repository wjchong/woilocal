<?php
include('config.php');
echo $id=$_POST['updatedid'];
echo $status=$_POST['upadtedstatus'];
$update=mysqli_query($conn,"UPDATE users SET `isLocked`='$status' WHERE id='$id' ");

?>