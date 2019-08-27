<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
if(!isset($_SESSION))
{
 session_start();
}
error_reporting(0);
$conn = mysqli_connect("127.0.0.1", "root", "", "koofamilies");
if(!$conn)
{
	echo "database error"; die;
}
$site_url = "http://127.0.0.1/woi";   // Prod
if(!function_exists('redirectToUrl')) {
	function redirectToUrl($url) {
		echo '<script language="javascript">window.location.href ="'.$url.'"</script>';
		exit;
	}
}
?>
