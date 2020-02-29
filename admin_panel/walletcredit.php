<?php
include("config.php");
// wallet credit 
   if( isset( $_POST['method']) && ( $_POST['method'] == "walletcredit" )) {
	   // print_R($_POST);
	   // die;
	   extract($_POST);
	   $profile_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='".$user_id."'"));
	   // print_R($profile_data);
	
	    $myr_bal=$profile_data['balance_myr'];
	    $usb_bal=$profile_data['balance_usd'];
	    $inr_bal=$profile_data['balance_inr'];
	   // if($w_type=="myr_bal")
	   // {
		  // $myr_bal=$profile_data['balance_myr']+$rebate;
	   // }
		// else if($w_type=="usd_bal")
		// {
			// $usb_bal=$profile_data['balance_usd']+$rebate;
		// }
		// else if($w_type=="inr_bal")
		// {
			// $inr_bal=$profile_data['balance_inr']+$rebate;
		// }
		if($rebate)
		{
			$inr_bal=$profile_data['balance_inr']+$rebate;
		} 
		$_SESSION['new_order']='';
	  	$query="UPDATE users SET balance_inr='$inr_bal' WHERE `users`.`id`='$user_id'";
		   
		$insert=mysqli_query($conn,$query);
		if($insert)
		{  
			$date=date('Y-m-d h:i');
			 mysqli_query($conn,"UPDATE `order_list` SET `rebate_credited` = 'y',rebate_credited_date='$date' WHERE `order_list`.`id` ='$order_id'");
			 mysqli_query($conn,"INSERT INTO transactions (`sender_id`, `amount`, `receiver_id`, `wallet`) VALUES ('$user_id', '$rebate', 'rebate', '$w_type')");
			// if($rebate_amount>0)
			// mysqli_query($conn,"INSERT INTO transactions (`sender_id`, `amount`, `receiver_id`, `wallet`) VALUES ('$user_id', '$payable_amount', '$merchant_id', '$w_type')");
			    
			$item = array('msg'=>"Inserted",'status'=>true);
		}
		else
		{
			$item = array('msg'=>"fail",'status'=>false);
		}
		echo json_encode($item);
		die;   
		 
   }
   if( isset( $_POST['method']) && ( $_POST['method'] == "walletdeduct" )) {
	   // print_R($_POST);
	   // die;
	   extract($_POST);
	   $profile_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='".$user_id."'"));
	   // print_R($profile_data);
	
	    $myr_bal=$profile_data['balance_myr'];
	    $usb_bal=$profile_data['balance_usd'];
	    $inr_bal=$profile_data['balance_inr'];
	   // if($w_type=="myr_bal")
	   // {
		  // $myr_bal=$profile_data['balance_myr']+$rebate;
	   // }
		// else if($w_type=="usd_bal")
		// {
			// $usb_bal=$profile_data['balance_usd']+$rebate;
		// }
		// else if($w_type=="inr_bal")
		// {
			// $inr_bal=$profile_data['balance_inr']+$rebate;
		// }
		if($rebate)
		{
			$inr_bal=$profile_data['balance_inr']-$rebate;
		} 
		$_SESSION['new_order']='';
	  	$query="UPDATE users SET balance_inr='$inr_bal' WHERE `users`.`id`='$user_id'";
		   
		$insert=mysqli_query($conn,$query);
		if($insert)
		{  
			$date=date('Y-m-d h:i');
			 mysqli_query($conn,"UPDATE `order_list` SET `rebate_credited` = 'n',rebate_credited_date='$date' WHERE `order_list`.`id` ='$order_id'");
			 // mysqli_query($conn,"INSERT INTO transactions (`sender_id`, `amount`, `receiver_id`, `wallet`) VALUES ('$user_id', '$rebate', 'rebate', '$w_type')");
			// if($rebate_amount>0)
			// mysqli_query($conn,"INSERT INTO transactions (`sender_id`, `amount`, `receiver_id`, `wallet`) VALUES ('$user_id', '$payable_amount', '$merchant_id', '$w_type')");
			    
			$item = array('msg'=>"Updated",'status'=>true);
		}
		else
		{
			$item = array('msg'=>"fail",'status'=>false);
		}
		echo json_encode($item);
		die;
   }
?>