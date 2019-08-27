<?php 
include("config.php");

if(!isset($_SESSION['login']))
{
	header("location:login.php");
}

if(isset($_GET['page']))
{
	$page = $_GET['page'];
}
else
{
	$page = 1;
}

$limit = 25;

$total_rows = mysqli_num_rows(mysqli_query($conn, "(SELECT tranfer.invoice_no,tranfer.id,users.name,users.email,tranfer.amount,tranfer.wallet,tranfer.created_on,'Send' AS tx_type FROM tranfer,users WHERE tranfer.sender_id=".$_SESSION['login']." AND users.id=tranfer.receiver_id) UNION (SELECT tranfer.invoice_no,tranfer.id,users.name,users.email,tranfer.amount,tranfer.wallet,tranfer.created_on,'Receives' AS tx_type FROM tranfer,users WHERE tranfer.receiver_id=".$_SESSION['login']." AND users.id=tranfer.sender_id)"));
$total_page_num = ceil($total_rows / $limit);
$start = ($page - 1) * $limit;
$end = $page * $limit;

$tx_history_data = mysqli_query($conn, "(SELECT tranfer.invoice_no,tranfer.id,users.name,users.email,tranfer.amount,tranfer.wallet,tranfer.created_on,'Send' AS tx_type FROM tranfer,users WHERE tranfer.sender_id=".$_SESSION['login']." AND users.id=tranfer.receiver_id) UNION (SELECT tranfer.invoice_no,tranfer.id,users.name,users.email,tranfer.amount,tranfer.wallet,tranfer.created_on,'Receives' AS tx_type FROM tranfer,users WHERE tranfer.receiver_id=".$_SESSION['login']." AND users.id=tranfer.sender_id) ORDER BY id DESC LIMIT $start,$end")


?>
<!DOCTYPE html>
<html lang="en" style="" class="js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths">

<head>
    <?php include("includes1/head.php"); ?>
	<style>
	.well
	{
		min-height: 20px;
		padding: 19px;
		margin-bottom: 20px;
		background-color: #fff;
		border: 1px solid #e3e3e3;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
		box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
	}
	
	.pagination {
		display: inline-block;
		padding-left: 0;
		margin: 20px 0;
		border-radius: 4px;
	}
	.pagination>li {
		display: inline;
	}
	.pagination>li:first-child>a, .pagination>li:first-child>span {
		margin-left: 0;
		border-top-left-radius: 4px;
		border-bottom-left-radius: 4px;
	}
	.pagination>li:last-child>a, .pagination>li:last-child>span {
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	.pagination>li>a, .pagination>li>span {
		position: relative;
		float: left;
		padding: 6px 12px;
		margin-left: -1px;
		line-height: 1.42857143;
		color: #337ab7;
		text-decoration: none;
		background-color: #fff;
		border: 1px solid #ddd;
	}
	.pagination a {
		text-decoration: none !important;
	}
	.pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
		z-index: 3;
		color: #fff;
		cursor: default;
		background-color: #337ab7;
		border-color: #337ab7;
	}
	</style>
</head>

<body class="header-light sidebar-dark sidebar-expand pace-done">

    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div id="wrapper" class="wrapper">
        <!-- HEADER & TOP NAVIGATION -->
        <?php include("includes1/navbar.php"); ?>
        <!-- /.navbar -->
        <div class="content-wrapper">
            <!-- SIDEBAR -->
            <?php include("includes1/sidebar.php"); ?>
            <!-- /.site-sidebar -->


            <main class="main-wrapper clearfix" style="min-height: 522px;">
                <div class="row" id="main-content" style="padding-top:25px">
					<div class="well" style="width:100%">
						<h3>Transaction History</h3>
						<table class="table table-striped">
							<tr>
								<th>Transaction ID</th>
								<th>Invoice No</th>
								<th>Name</th>
								<th>Email</th>
								<th>Amount</th>
								<th>Wallet</th>
								<th>Transaction Type</th>
								<th>Transaction On</th>
							</tr>
							<?php
					
							while($tx_history_row = mysqli_fetch_assoc($tx_history_data))
							
							{
								 $invoice_id=$tx_history_row['invoice_no'];
								if($invoice_id)
								{
									// echo "SELECT invoice_no FROM order_list WHERE id in($invoice_id)";
									// die;
									$invo = mysqli_fetch_all(mysqli_query($conn, "SELECT invoice_no FROM order_list WHERE id in($invoice_id)"));
									
									
								}
								?>
								<tr>
									<?php 
									if($tx_history_row['wallet'] == "INR")
							{
								$wat = "CNY";
							}
							else
							{
								$wat = $tx_history_row['wallet']; 
							}
							
							?>
									<td><?php echo $tx_history_row['id']; ?></td>
									<td><?php if($tx_history_row['invoice_no']){ foreach($invo as $si){ echo $si[0].",";}} ?></td>
									<td><?php echo $tx_history_row['name']; ?></td>
									<td><?php echo $tx_history_row['email']; ?></td>
									<td><?php echo number_format($tx_history_row['amount'],2); ?></td>
									<td><?php echo $wat; ?></td>
									<td><?php echo $tx_history_row['tx_type']; ?></td>
									<td><?php echo date("d-m-Y, H:i A", $tx_history_row['created_on']); ?></td>
								</tr>
								<?php
							}
							?>
						</table>
					</div>
					<div style="margin:0px auto;">
						<ul class="pagination">
						<?php
						  for($i = 1; $i <= $total_page_num; $i++)
						  {
							  if($i == $page)
							  {
								  $active = "class='active'";
							  }
							  else
							  {
								  $active = "";
							  }
							  echo "<li $active><a href='?page=$i'>$i</a></li>";
						  }
						?>
						</ul>
					</div>
				</div>
			</main>
        </div>
        <!-- /.widget-body badge -->
    </div>
    <!-- /.widget-bg -->

    <!-- /.content-wrapper -->
    <?php include("includes1/footer.php"); ?>
</body>

</html>