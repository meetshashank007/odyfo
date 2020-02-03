<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
     <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4>Manage Transactions</h4>
          </div>
          <!--<div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Games/view_game"?>'" style="float:right">View News</button>
          </div>-->
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		
        <div class="col-12">
        <center><div class="text-success message"><?php echo $this->Session->flash();?></div></center>
          <div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                   <th>Sr.No.</th>
					<th>UserName</th>
					<th>Game</th>
					<th>Amount(EUR)</th>
					<th>TransactionId</th>
					<th>PayMode</th>
					<th>Date</th>
					<th>Status</th>
					<th>Action</th>
			    </tr>
                </thead>
                <tbody>
				<?php 
				if($result){
					$i=1;
					foreach($result as $res){?>
						<tr>
							<td><?php echo $i;?></td>
							<td>
							<?php $name = $this->My->getuserbyId($res['Payment']['user_id']);echo $name['full_name'];?></td>
							<td><?php $game = $this->My->getGameById($res['Payment']['game_id']);echo $game['Game']['name'];?></td>
							<td><?php echo $res['Payment']['amount'];?></td>
							<td><?php echo $res['Payment']['transaction_id'];?></td>
							<td><?php echo ($res['Payment']['pay_mode'] == 0)?'Card':"Cash";?></td>
							
							<td><?php echo $res['Payment']['created'];?></td>
							
							
							
							<td><?php echo $this->My->getPaymentStatus($res['Payment']['status']);?>
							</td>
							<td>
								<a href="<?php echo SITE_PATH.'admin/Payments/view_payment/'.$res['Payment']['id'].'/1';?>" title = "View"><i class="fa fa-eye"></i></a>
								<!--<a href="<?php echo SITE_PATH.'admin/Games/game_team/'.$res['Game']['id'];?>" title = "Teams"><img src="<?php echo SITE_PATH."img/view.png"?>" width="20px" height ="20px"></a>
								<a href="<?php echo SITE_PATH.'admin/Games/view_game/'.$res['Game']['id'];?>" title = "Edit"><i class="fa fa-edit"></i></a>-->
								<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/Payments/delete/'.$res['Payment']['id'];?>')" title = "Delete"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
				<?php	$i++;
						
					}
				}?>
               
                </tfoot>
              </table>
			
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
	<script>
		function del_confirm(msg,url){
			if(confirm(msg)){
				window.location.href=url
			}
			else{
				false;
			}
		}
		
	</script>
  
 
