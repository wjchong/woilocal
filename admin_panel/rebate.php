<?php 
include("config.php");

$users = mysqli_query($conn, "SELECT * FROM users WHERE user_roles = '1' order by id desc");
$merchants = mysqli_query($conn, "SELECT * FROM users where user_roles='2'");

$merchant = "";
$user = "";
if(!isset($_SESSION['admin']))
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
$filter='';
if($_POST['filter']){
	
    $merchant = $_POST['merchant'];
    $user = $_POST['member'];
    $rebate_status = $_POST['rebate_status'];
    if($merchant)
	$filter.=" and o.merchant_id='$merchant'";
     if($user)
	$filter.=" and o.user_id='$user'";	
	if($rebate_status!="all")
	{
		$filter.=" and o.rebate_credited='$rebate_status'";	
	}
   
} 


   $query="SELECT m.name as merchant_name,o.*,u.name,u.id as u_id,u.otp_verified as user_verified,u.balance_usd,u.balance_inr,u.balance_myr from order_list
 as o inner join users as u on u.id=o.user_id inner join users as m on m.id=o.merchant_id Where  u.user_roles = 1 and u.isLocked=0 and total_rebate_amount>0 $filter ORDER BY o.id DESC";

$user = mysqli_query($conn,$query);
$total_rows = mysqli_num_rows($user);
$total_page_num = ceil($total_rows / $limit);

$start = ($page - 1) * $limit;
$end = $page * $limit;
?>
<!DOCTYPE html>
<html lang="en" style="" class="js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths">

<head>
    <?php include("includes1/head.php"); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="/css/dropzone.css" type="text/css" /> 
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
	
	.wallet_h{
	    font-size: 30px;
        color: #213669;

	}
	.kType_table{
	    border: 1px #aeaeae solid !important;
	}
	.kType_table th, .kType_table td{
	    border: 1px #aeaeae solid !important;
	}
	.kType_table thead th{
	    border-bottom: 1px  #aeaeae solid !important;
	} 
	.kType_table tbody .complain{
	    color: red;
	    text-decoration: underline;
	}
	.sort{
	    margin-bottom: 10px;
	}
	/*kType_table tbody tr.k_normal{
	    background: #ececec;
	}*/
	#kType_table tbody tr.k_user{
	    background: #bcbcbc;
	}
	#kType_table tbody tr.k_merchant{
	    background: #dcdcdc;
	}
	.select2-container--bootstrap{
	    width: 175px;
	    display: inline-block !important;
	    margin-bottom: 10px;
	}
	@media  (max-width: 750px) and (min-width: 300px)  {
	    .select2-container--bootstrap{
	        width: 300px;
	    }
	}
	</style>
</head>

<body class="header-light sidebar-dark sidebar-expand pace-done">

    <div id="wrapper" class="wrapper">
        <!-- HEADER & TOP NAVIGATION -->
        <?php include("includes1/navbar.php"); ?>
        <!-- /.navbar -->
        <div class="content-wrapper">
            <!-- SIDEBAR -->
            <?php include("includes1/sidebar.php"); ?>
            <!-- /.site-sidebar -->


            <main class="main-wrapper clearfix" style="min-height: 522px;">
                <div class="container-fluid" id="main-content" style="padding-top:25px">
					<h2 class="text-center wallet_h">Rebate List</h2>
					<form class="sort row" method="post" action="">
					    <input type="hidden" name="filter" value="filter">
					    
					    <div class="col-sm-12">
					        <label class="control-label col-sm-1">User Name: </label>
				            <select class="form-control col-sm-2 select2" name="member" style="display: inline-block;">
				                <option value=""></option>
				                <?php while($row = mysqli_fetch_assoc($users)){?>
				                    <option <?php if($row['id'] == $user) echo 'selected';?> value="<?php echo $row[id];?>"><?php echo $row['name']."/".$row['mobile_number']?></option>
				                <?php }?>
				            </select>
					        <label class="control-label col-sm-1" style="display:inline-block;">Merchant Name: </label>
					        <select class="form-control col-sm-2 select2" name="merchant" style="display: inline-block;">
					            <option></option>
				                <?php while($row = mysqli_fetch_assoc($merchants)){?>
				                    <option <?php if($row['id'] == $merchant) echo 'selected';?> value="<?php echo $row[id];?>"><?php echo $row['name']?></option>
				                <?php }?>
				            </select>
							<label class="control-label col-sm-1" style="display:inline-block;">Rebate Status: </label>
					        <select class="form-control col-sm-2" name="rebate_status" style="display: inline-block;">
					            <option value="all">All</option>
					            <option value="n">Requested</option>
					            <option value="y">Credited</option>
				               
				            </select>
					    </div>
				         <button class="btn btn-default col-sm-1" type="submit">Filter</button>
					       <button type="button" class="btn btn-danger" onclick="window.location.href='./rebate.php'">Clear criteria</button>
							   
					</form>
					<input type="hidden" id="credit_amount_input"/>
				<input type="hidden" id="user_id"/>
				<input type="hidden" id="w_type_input"/>
				<input type="hidden" id="order_id"/>
					<table class="table table-striped kType_table" id="kType_table">
					    <thead>
					        <tr>
					          <th>Particular</th>
                        <th>Name</th>
						
                        <th>Mobile Number</th>
						 <th>Total Request</th>
						<th>Action</th>
						   <th>Rebate Amount</th>
						<th>Order Status</th>
                       
                        <th>Cur Wallet Amount</th>
                        <th>Id /Invoice No</th>
                        <th>Merchant Name</th>
                        <th>Order Amount</th>
                     
                		<th>Paid By wallet</th>
						<th>User Verified</th>
							<th>Date</th>
						<th>Credited Date</th>
                		<th>Status</th>
                		
					        </tr>
					    </thead>
					    <tbody>
					        <?php
                    	$i=1;
                    	while($row=mysqli_fetch_assoc($user)){
							$r_status=$row['rebate_credited'];
							$user_id=$row['u_id'];
							$user_mobile=$row['user_mobile'];
							$all_count = mysqli_fetch_assoc(mysqli_query($conn, "select count(id) as total_req from order_list where user_mobile='$user_mobile' and total_rebate_amount>0"))['total_req'];
							
							$otp_pre_verified=$row['user_verified'];
							$newuser=$row['newuser'];
							if($newuser=="y")
							{
								if($otp_pre_verified=="y")
								{
									$otp_verified="y";
									$otp_label="VERIFIED";
								}
								else
								{
									$otp_verified="n";
									$otp_label="NOT VERIFIED";
								}
							}
							else
							{
								$otp_verified="y";
								$otp_label="VERIFIED";
							}
							   if($r_status=="y")
								$r_label="Credited";
								 if($r_status=="rejected")
								$r_label="Rejected";
							   if($r_status=="n")
								$r_label="Requested";
							$order_status=$row['status'];
								if($order_status)
									$s_label="DONE";
								else 
									$s_label="PENDING";
							 ?>
                        	  <tr style="<?php  if($otp_verified=="n"){ echo "color:red;";} ?>">
                        		 <td> <?php echo $i; ?> </td>
                                <td class="name" data-id=<?php echo $row['id']; ?> style="cursor:pointer;"><?php echo $row['user_name'];  ?></td>
                                <td><?php echo $row['user_mobile'];  ?></td>
								 <td><?php echo $all_count;  ?></td>
								<td class="del">
								<?php if($order_status && $r_status=="n" && $otp_pre_verified=="y"){  ?>
								<i style="font-size:20px;" class="fa fa-check credit_amount" rebate="<?php echo $row['total_rebate_amount'];?>" user_id="<?php echo $row['user_id']?>" 
								order_id="<?php echo $row['id']?>" wallet="<?php echo $row['wallet']?>" user_mobile="<?php echo $row['user_mobile']?>"></i>
								<?php } ?>
								<?php if($order_status && $r_status=="y"){  ?>
								
							<i  style="font-size:20px;" class="fa fa-remove undo_confirm_amount" rebate="<?php echo $row['total_rebate_amount'];?>" user_id="<?php echo $row['user_id']?>" 
								order_id="<?php echo $row['id']?>" wallet="<?php echo $row['wallet']?>" user_mobile="<?php echo $row['user_mobile']?>"></i>
							
								<?php } ?>   
								</td>   
								<td><?php echo number_format($row['total_rebate_amount'],2);  ?></td>
                                <td><?php echo $s_label;  ?></td>
                               
                                <td><?php if($row['balance_inr']){echo number_format($row['balance_inr'],2);} else { echo "0.00";} ?></td>
                                <td><?php echo $row['id']."-".$row['invoice_no'];?></td>
                                <td><?php echo $row['merchant_name'];?></td>
								<td><?php echo number_format($row['total_cart_amount'],2);  ?></td>
								
								<td><?php echo number_format($row['wallet_paid_amount'],2);  ?></td>
                        		
                        		<td><?php echo $otp_label;  ?></td>
                        		
                        		
                        		

                        		<td><?php  $date=$row['created_on'];  
                        	        echo $joinigdate=date("Y-m-d h:i:sa",strtotime($date));  ?>
                        	    </td>
								
								<td><?php  $rebate_credited_date=$row['rebate_credited_date'];
										 if($r_status=="y")
										{
											echo $joinigdate=date("Y-m-d h:i:sa",strtotime($rebate_credited_date));
										}									
										else echo "--"; 
										?>
                        	    </td>
								<td><?php echo $r_label; ?></td>
								
								
                        	    
							  </tr>
                    	<?php
                            $i++;  
                    	}?>
					    </tbody>
					</table>
				</div>
			</main>
        </div>
      
        <!-- /.widget-body badge -->
    </div>
    <!-- /.widget-bg -->

    <!-- /.content-wrapper -->
	<?php include("includes1/footer.php"); ?>
	<script type="text/javascript" src="/js/dropzone.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	






	<script>
	    $(document).ready(function(){
	        jQuery(".dropzone").dropzone({
                sending : function(file, xhr, formData){
                },
                success : function(file, response) {
                    $(".complain_image").val(file.name);
                    
                }
            });
            $('#kType_table').DataTable({
				"bSort": false,
				"pageLength":50,
				dom: 'Bfrtip',
				 buttons: [
					'copy', 'csv', 'excel', 'pdf', 'print'
				]
				});
				$(document).on('click', '.credit_amount', function(e){
			 
				  e.preventDefault();
					 // alert(3);
					 $(this).hide();
					 var rebate=$(this).attr('rebate');
					 var user_id=$(this).attr('user_id');
					 var order_id=$(this).attr('order_id');
					 var wallet=$(this).attr('wallet');
					 var user_mobile=$(this).attr('user_mobile');
					 $('#order_id').val(order_id);
					 $('#w_type_input').val(wallet);
					 $('#credit_amount_input').val(rebate);
					 $('#user_id').val(user_id);
					 $('#credit_amount_label').html(rebate);
					 $('#credit_mobile_label').html(user_mobile);
					 var rebate=$('#credit_amount_input').val();
					 var wallet=$('#w_type_input').val();
					 var order_id=$('#order_id').val();
					 var user_id=$('#user_id').val();
					  // alert(user_id);
					  // alert(order_id);
					 var data = {order_id:order_id,user_id:user_id,method:"walletcredit",rebate:rebate,w_type:wallet};
						$.ajax({
							url :'walletcredit.php',
						  type:'POST',
						  dataType : 'json',
						  data:data,
						  success:function(response){
							  var data = JSON.parse(JSON.stringify(response));
							  if(data.status)
							  {
								 alert('Amount Credited');
							  }
							  else
							  {
								  alert('Failed to make payment From Wallet');
							  }
							  location.reload();
							}		  
							});
				});
				$(document).on('click', '.undo_confirm_amount', function(e){
			 
				  e.preventDefault();
				
					 // alert(3);
					 $(this).hide();
					 var rebate=$(this).attr('rebate');
					 var user_id=$(this).attr('user_id');
					 var order_id=$(this).attr('order_id');
					 var wallet=$(this).attr('wallet');
					 var user_mobile=$(this).attr('user_mobile');
					
					  // alert(user_id);
					  // alert(order_id);
					 var data = {order_id:order_id,user_id:user_id,method:"walletdeduct",rebate:rebate,w_type:wallet};
						$.ajax({
							url :'walletcredit.php',
						  type:'POST',
						  dataType : 'json',
						  data:data,
						  success:function(response){
							  var data = JSON.parse(JSON.stringify(response));
							  if(data.status)
							  {
								 alert('Amount Deducted');
							  }
							  else
							  {
								  alert('Failed to make payment From Wallet');
							  }
							  location.reload();
							}		  
							});
				});
	});
	  
	  
	</script>
	
</body>

</html>
