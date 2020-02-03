<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4>View Transaction</h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Payments/manage_payments"?>'" style="float:right">Manage Transactions</button>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="container-fluid">
			<div class="row">
				<!-- left column -->
				<div class="col-md-6">
					<!-- general form elements -->

					<div class="card card-primary">
						<!-- /.card-header -->
						<!-- form start -->
						<form role="form" id="addGame" method="post">
							<div class="card-body">
								<div class="form-group">
									<label>User Name</label>
									<input type="text" class="form-control required" name="" readonly  value="<?php $name = $this->My->getuserbyId($result['Payment']['user_id']);echo $name['full_name'];?>">
								</div>
								
								<div class="form-group">
									<label>Game Name</label>
									<input type="text" class="form-control required" name="" readonly  value="<?php $game = $this->My->getGameById($result['Payment']['game_id']);echo $game['Game']['name'];?>">
								</div>
								
								<div class="form-group">
									<label>Amount(in usd)</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['amount']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Token</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['token']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Charge Id</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['charge_id']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Transfer Id</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['transfer_id']:'');?>">
								</div>
								<div class="form-group">
									<label>Transaction Id</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['transaction_id']:'');?>">
								</div>
								<div class="form-group">
									<label>Account No</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['account_no']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Description</label>
									<textarea name = "description" readonly class="form-control" ><?php echo $result['Payment']['description'];?></textarea>
								</div>
								
								
								<div class="form-group">
									<label>Payment Mode</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ($result['Payment']['pay_mode'] == 0)?'Card':"Cash";?>">
								</div>
								<div class="form-group">
									<label>Date</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$result['Payment']['created']:'');?>">
								</div>
								<div class="form-group">
									<label>Status</label>
									<input type="text" class="form-control required" name="" readonly value="<?php echo ((isset($result) && !empty($result))?$this->My->getPaymentStatus($result['Payment']['status']):'');?>">
								</div>
			
								
						</form>
						
					</div>
				</div>
			</div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
	