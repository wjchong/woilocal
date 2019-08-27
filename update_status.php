<?php
include("config.php");
// $conn2 = mysqli_connect("52.8.48.18", "koofamil_B277", "rSFihHas];1P", "koofamil_B277");   
if( isset( $_POST['method']) && ( $_POST['method'] == "updatePrinted" )  ) {
    $id= $_POST['id'];
    $printed = $_POST['printed'];
    mysqli_query($conn, "UPDATE order_list SET printed='$printed' WHERE id='$id'");
    echo('update printed.');
} else {
    $id= $_POST['id'];
    $oid= $_POST['oid'];
    $orid= $_POST['orid'];
    $status= $_POST['status'];
    $_SESSION['mm_id'] = $id;
    $_SESSION['o_id'] = $oid;
    $_SESSION['orid'] = $orid;
    $merchant_id = $_SESSION['login'];
    $invoice = mysqli_fetch_assoc(mysqli_query($conn, "SELECT max(invoice_no) no FROM order_list WHERE merchant_id='$merchant_id'"));
    $invoice_no += $invoice['no'] + 1;
    mysqli_query($conn, "UPDATE order_list SET status='$status', status_change_date = CURDATE() WHERE id='$id'");
    // mysqli_query($conn2, "UPDATE order_list SET status='$status', status_change_date = CURDATE() WHERE id='$id'");
    // echo $status;
    //die;
    if($status==1)
    {
        
    }
}
?>
