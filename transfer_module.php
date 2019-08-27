
<?php

session_start();
include("config.php");

if (isset($_POST['mobile_number'])) {
	$mobile_number = $_POST['mobile_number'];
	$sender_id = $_POST['sender_id'];
	$tmp = mysqli_query($conn, "SELECT id FROM users WHERE mobile_number = '$mobile_number'");
	if (mysqli_num_rows($tmp) > 0) {
		$buf = mysqli_fetch_assoc($tmp);
		$data->id = $buf['id'];
		$sender = mysqli_query($conn, "SELECT id, balance_inr, balance_myr, balance_usd FROM users WHERE id = ".$sender_id);
		if (mysqli_num_rows($sender) > 0) {
			$sender = mysqli_fetch_assoc($sender);
			if ($sender['balance_myr'] == '') {
				$data->MYR = 0;
			}
			else {
				$data->MYR = $sender['balance_myr'];
			}
			if ($sender['balance_usd'] == '') {
				$data->CF = 0;
			} else {
				$data->CF = $sender['balance_usd'];
			}
			if ($sender['balance_inr'] == '') {
				$data->CF = 0;
			} else {
				$data->INR = $sender['balance_inr'];
			}
			
			echo json_encode($data);
		}else{
			echo -1;
		}
		// print_r($tmp);
	} else {
		echo -1;
	}

} else {
	$sender_name = $_POST['sender_name'];
	$created_on = $_POST['created'] / 1000;
	$sender_id = $_POST['sender_id'];
	$receiver_id = $_POST['receiver_id'];
	$amount = $_POST['amount'];
	$wallet_type = $_POST['wallet_type'];

	$sql = 'INSERT INTO tranfer (sender_id, amount, receiver_id, wallet, created_on) VALUES ("'.$sender_id.'", "'.$amount.'", "'.$receiver_id.'", "'.$wallet_type.'", "'.$created_on.'")';
	$transfer = mysqli_query($conn, $sql);

	$sql_for_transaction = 'INSERT INTO transaction (sender_id, amount, receiver_id, wallet, created_on) VALUES ("'.$sender_id.'", "'.$amount.'", "'.$receiver_id.'", "'.$wallet_type.'", "'.$created_on.'")';
	$transaction = mysqli_query($conn, $sql_for_transaction);

	$noti_string = 'You Successfully Received '.$amount.' '.$wallet_type.' from '.$sender_name;
	$noti = 'INSERT into notifications (user_id, notification , type, created_on, readStatus) VALUES ("'.$receiver_id.'", "'.$noti_string.'", "receive", "'.$created_on.'", "0")';
	$notification = mysqli_query($conn, $noti);

	$tmp = 'SELECT balance_usd, balance_myr, balance_inr FROM users WHERE id="'.$sender_id.'"';
	$sender = mysqli_fetch_assoc(mysqli_query($conn, $tmp));
	$sender_myr = $sender['balance_myr'];
	$sender_inr = $sender['balance_inr'];
	$sender_usd = $sender['balance_usd'];

	$tmp = 'SELECT balance_usd, balance_myr, balance_inr FROM users WHERE id="'.$receiver_id.'"';
	$receiver = mysqli_fetch_assoc(mysqli_query($conn, $tmp));
	$receiver_myr = $receiver['balance_myr'];
	$receiver_inr = $receiver['balance_inr'];
	$receiver_usd = $receiver['balance_usd'];
	$sender_bal = 'UPDATE users SET ';
	$receiver_bal = 'UPDATE users SET ';
	if ($wallet_type == 'MYR') {
		$sender_myr = floatval($sender_myr) - floatval($amount);
		$receiver_myr = floatval($receiver_myr) + floatval($amount);
		$sender_bal = $sender_bal.'balance_myr="'.$sender_myr.'" ';
		$receiver_bal = $receiver_bal.'balance_myr="'.$receiver_myr.'" ';
	}
	if ($wallet_type == 'INR') {
		$sender_inr = floatval($sender_inr) - floatval($amount);
		$receiver_inr = floatval($receiver_inr) + floatval($amount);
		$sender_bal = $sender_bal.'balance_inr="'.$sender_inr.'" ';
		$receiver_bal = $receiver_bal.'balance_inr="'.$receiver_inr.'" ';
	}
	if ($wallet_type == 'CF') {
		$sender_usd = floatval($sender_usd) - floatval($amount);
		$receiver_usd = floatval($receiver_usd) + floatval($amount);
		$sender_bal = $sender_bal.'balance_usd="'.$sender_usd.'" ';
		$receiver_bal = $receiver_bal.'balance_usd="'.$receiver_usd.'" ';
	}
	$sender_bal = $sender_bal.' WHERE id='.$sender_id;
	mysqli_query($conn, $sender_bal);
	$receiver_bal = $receiver_bal.' WHERE id='.$receiver_id;
	mysqli_query($conn, $receiver_bal);

	// echo $noti;
	echo $sender_bal;
	echo $receiver_bal;
}
