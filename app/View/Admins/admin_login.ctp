<script>
	$( document ).ready(function() {
		$("#loginForm").validate();
	});
</script>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="javascript:void(0);"><b>Login</b></a>
  </div>
  <img src="<?php echo SITE_PATH; ?>img/login.png" style="width:300px;margin: 0 auto;display: table; padding: 10px;">
  <!-- /.login-logo -->
  <?php echo $this->Session->flash('LoginError');?>
  
  <div class="card">
    <div class="card-body login-card-body">
		
		<form action="<?php echo SITE_PATH.'admin/Admins/login';?>" method="post" id="loginForm">
			<div class="form-group has-feedback">
			  <input type="text" class="form-control required" name="username" placeholder="Username">
			  <!--<span class="fa fa-envelope form-control-feedback"></span>-->
			</div>
			<div class="form-group has-feedback">
			  <input type="password" name="password" class="form-control required" placeholder="Password">
			  <!--<span class="fa fa-lock form-control-feedback"></span>-->
			</div>
			<div class="row">
			  
			  <!-- /.col -->
			  <div class="col-4">
			  </div>
			  <div class="col-4">
				<input type="submit" name="submit" value="LogIn" class="btn btn-primary btn-block btn-flat">
			  </div>
			  <!-- /.col -->
			</div>
		</form>
	</div>
    <!-- /.login-card-body -->
  </div>
</div>
