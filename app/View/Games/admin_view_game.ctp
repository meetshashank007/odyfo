<style>
#datepicker{width:180px; margin: 0 20px 20px 20px;}
#datepicker > span:hover{cursor: pointer;}

.testdate{
	margin-left:0px !important;
	width:100% !important;
}
.ui-timepicker-standard a{
	text-align:left;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4><?php if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){echo "View Game";}elseif(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){echo "Edit Game";}else{echo 'Add Game';}?></h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Games/manage_games"?>'" style="float:right">Manage Games</button>
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
						<?php
						if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){
							$view	= 	$this->params['pass'][1];
							$url 	=	'';
							$edit	=	0;
						}elseif(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){
							$url 	=	SITE_PATH.'admin/Games/view_game/'.$this->params['pass'][0];
							//$view	= 	$this->params['pass'][0];
							$edit	=	1;
						}else{
							$url 	=	'';
							$edit	=	2;
						}
						if(isset($result) && !empty($result)){
							$date_time = explode(" ",$result['Game']['date_time']);
							$date	   = $date_time[0];
							$time	   = $date_time[1];
						}else{
							$date	   = "";
							$time	   = "";
						}
						
						
						?>
						<!-- /.card-header -->
						<!-- form start -->
						<form role="form" id="addGame" method="post" action ="<?php echo $url;?>" enctype="multipart/form-data">
							<div class="card-body">
								<div class="form-group">
									<label>Name</label>
									<input type="text" class="form-control required" name="name" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?>  value="<?php echo ((isset($result) && !empty($result))?$result['Game']['name']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Image</label>
									<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
									<input type="file" class="form-control image" name="image"><?php }else{?>
									
									<input type="hidden" name="game_image" value="<?php echo ((isset($result) && !empty($result))? $result['Game']['image']:'');?>"><?php }?>
									
									
								</div>
								<?php if(isset($result) && !empty($result)){?><img src="<?php echo SITE_PATH."img/game/".$result['Game']['image'];?>" width="50px" height="50px"><?php }?>
			
			
								<div class="form-group">
									<label>Date</label>
									<div id="datepicker" class="input-group date testdate" data-date-format="yyyy-mm-dd">
										<input name ="date" type="text" readonly class="form-control required" value="<?php echo $date;?>" />
										<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									</div>
								</div>
								
								<div class="form-group">
									<label>Time(in 24hr format)</label>
								<input type="text" id="" name="time" readonly class="timepicker form-control required" value="<?php echo $time;?>">
								<div id="timepicker-error"></div>
								</div>
								
								<div class="form-group">
									<label>Venue</label>
									<?php if(!empty($view)){?>
										<input type="text" class="form-control required" id="venue_id" placeholder="Enter Venue" name="type" value="<?php  $venue = $this->My->getVenueById($result['Game']['venue_id']); echo $venue['Venue']['name'];?>"readonly>
									<?php }else{?>
									<select class="form-control required venue" name="venue_id">
										<option value="">Select Venue</option>
										<?php 
										$getVenues = $this->My->getVenueByUserId($result['Game']['user_id']);
										foreach($getVenues as $getVenue){
											if(!empty($result)){
												if($getVenue['Venue']['id']==$result['Game']['venue_id']){
													$selected = 'selected';
												}else{
													$selected = '';
												}
											}else{
												$selected = '';
											}
										?>
										<option value="<?php echo $getVenue['Venue']['id'];?>"<?php echo $selected;?>><?php echo $getVenue['Venue']['name'];?></option>
										<?php }?>
									</select>
									<?php }?>
								</div>
								
								
								
								<div class="form-group">
									<label>Sport Type</label>
									<input type="hidden" class="form-control sportId"  name="sport_id"  value="<?php echo (!empty($result)?$result['Game']['sports_id']:'');?>">
									<input type="text" class="form-control sportName" name=""  value="<?php 
									if(!empty($result)){ 
										$sport = $this->My->getSportById($result['Game']['sports_id']) ;
										echo $sport['Sport']['name'];
									}else{
										echo '';
									}?>" readonly>
								</div>
								
								<div class="form-group">
									<label>Gender</label>
									<?php if(!empty($view)){?>
										<input type="text" class="form-control required" id="type" placeholder="Enter type" name="type" value="<?php if($result['Game']['gender']==1){echo 'Male';}elseif($result['Game']['gender']==2){echo "Female";}else{ echo 'Other';}?>"readonly>
									<?php }else{?>
									<select class="form-control required" name="gender">
										<option value="">Select Gender</option>
										<option value="1"<?php echo (!empty($result)?($result['Game']['gender']==1 ? 'selected':''):'');?>>Male</option>
										<option value="2"<?php echo (!empty($result)?($result['Game']['gender']==2 ? 'selected':''):'');?>>Female</option>
										<option value="3"<?php echo (!empty($result)?($result['Game']['gender']==3 ? 'selected':''):'');?>>Other</option>
									</select>
									<?php }?>
								</div>
								
								<div class="form-group">
									<label>City</label>
									<?php if(!empty($view)){?>
									<input type="text" class="form-control required" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="city"  value="<?php if(isset($result) && !empty($result)){$city = $this->My->getCityById($result['Game']['city_id']); echo $city['City']['name'];}else{echo '';}?>">
									<?php }else{?>
										<select class="form-control required" name="city_id">
										<option value="">Select City</option>
										<?php foreach($getCity as $city){?>
											<option value="<?php echo $city['City']['id'];?>" <?php echo (!empty($result)?($city['City']['id'] == $result['Game']['city_id'] ? 'selected':''):'');?>><?php echo $city['City']['name'];?></option>
										<?php }?>
									</select>
									<?php }?>
								</div>
								<div class="form-group">
									<label>Price</label>
									<input type="text" class="form-control required" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="price"  value="<?php echo ((isset($result) && !empty($result))? $result['Game']['price']:'');?>">
								</div>
								
								<div class="form-group">
									<label>Payment Mode</label>
									<?php 
										if(isset($result) && !empty($result)){
											$payment_mode = $result['Game']['payment_mode'];
										}else{
											$payment_mode = "";
										}
									
									if(!empty($view)){
										if(!empty($payment_mode)){
											if($payment_mode == 1){
												$mode	= "Cash";
											}elseif($payment_mode == 2){
												$mode	= "Card";
											}else{
												$mode	= "Both";
											}
										}
										
									?>
									<input type="text" class="form-control required" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="payment_mode"  value="<?php echo $mode;?>">
									<?php }else{
											
										
									?>
										<select class="form-control required" name="payment_mode">
											<option value="">Select Payment mode</option>
											<option value="1"<?php echo (!empty($payment_mode) ? (($payment_mode == 1)? "selected":""):"");?>>Cash</option>
											<option value="2" <?php echo (!empty($payment_mode) ? (($payment_mode == 2)? "selected":""):"");?>>Card</option>
											<option value="3" <?php echo (!empty($payment_mode) ? (($payment_mode == 3)? "selected":""):"");?>>Both</option>
										<?php //}?>
									</select>
									<?php }?>
								</div>
								
								<div class="form-group">
									<label>Description</label>
									<textarea name = "description" placeholder="Enter Description"  <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> class="form-control required"><?php echo ((isset($result) && !empty($result))? $result['Game']['description']:'');?></textarea>
								</div>
								
								<div class="form-group">
									<label>Number of Player</label>
									<input type="text" class="form-control required no_player" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="no_of_player"  value="<?php echo ((isset($result) && !empty($result))? $result['Game']['no_of_player']:'');?>">
								</div>
								<div class="form-group">
									<label>Minimum Player</label>
									<input type="text" class="form-control required min_player" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="min_player"  value="<?php echo ((isset($result) && !empty($result))? $result['Game']['min_player']:'');?>">
									<span class="error player"></span>
								</div>
								<div class="form-group">
									<label>Already Player</label>
									<?php if(!empty($view)){?>
										<input type="text" class="form-control required" id="type" value="<?php echo (($result['Game']['already_player']==1)?'Available':'Not Available');?>"readonly>
									<?php }else{?>
									<select class="form-control required already_player" name="already_player">
										<option value="">Select Player</option>
										<option value="1"<?php echo (!empty($result)?($result['Game']['already_player']==1 ? 'selected':''):'');?>>Available</option>
										<option value="0"<?php echo (!empty($result)?($result['Game']['already_player']==0 ? 'selected':''):'');?>>Not Available</option>
									</select>
									<?php }?>
								</div>
								<?php $style = (!empty($result)?(($result['Game']['already_player']==0)? "display:none" :""):"display:none"); ?>
								<div class="form-group already" style="<?php echo $style;?>">
									<label>Number of Already Player</label>
									<input type="text" class="form-control no_already_player" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="no_of_already_player"  value="<?php echo ((isset($result) && !empty($result))? $result['Game']['no_of_already_player'] :'');?>">
								</div>
								
								
								<?php if(isset($view)&& !empty($view)){?>
								<div class="form-group">
									<label>Organised By</label>
									<input type="text" class="form-control required" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="user_id"  value="<?php if(isset($result) && !empty($result)){if($result['Game']['user_id']== 0){echo "Odyfo";}else{$name = $this->My->getuserbyId($result['Game']['user_id']); echo $name['full_name'];}}?>">
								</div>
								<?php }?>
								<div class="form-group">
									<label>Status</label>
									<?php if(!empty($view)){?>
										<input type="text" class="form-control required" id="type" placeholder="Enter type" name="type" value="<?php echo (($result['Game']['status']==0)?'Inactive':'Active');?>"readonly>
									<?php }else{?>
									<select class="form-control required" name="status">
										<option value="1"<?php echo (!empty($result)?($result['Game']['status']==1 ? 'selected':''):'');?>>Active</option>
										<option value="0"<?php echo (!empty($result)?($result['Game']['status']==0 ? 'selected':''):'');?>>Inactive</option>
									</select>
									<?php }?>
								</div>
								
								
							</div>
							<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
								<div class="card-footer">
									<input type="submit" name="submit" class="btn btn-primary" value="Submit">
								</div>
							<?php }?>
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
	<script>
	$( document ).ready(function() {
		$("#addGame").validate();
	});
	$(".image").change(function(){
		var fileName = $(this).val();
		var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1); 
		if(fileExtension == 'jpeg' || fileExtension == 'jpg'|| fileExtension == 'png' || fileExtension == 'gif' || fileExtension == 'GIF'|| fileExtension == 'JPEG' || fileExtension == 'JPG'){
			//alert('ok');
		}else{
			alert('Please upload only image of type jpeg, jpg, png, gif');
			$(".image").val('');
			return false;
		}
		
	});
	
	/* $('.sport').change(function(){
		//alert('hello');
		var sport_id = $(this).val();
		url="<?php echo SITE_PATH ?>admin/Games/getSportVenue",
		$.ajax({
			url : url,
			type:'GET',
			async:false,
			data:{sport_id:sport_id},
			dataType: 'json',
			success:function(data){
			if(data){
				$('.venueName').val(data.venue_name);
				$('.venueId').val(data.venue_id);
			}
				
			}
		});	
	}) */
	
	$('.venue').change(function(){
		var venue_id = $(this).val();
		url="<?php echo SITE_PATH ?>admin/Games/getSportByVenueId",
		$.ajax({
			url : url,
			type:'GET',
			async:false,
			data:{venue_id:venue_id},
			dataType: 'json',
			success:function(data){
			if(data){//alert(data);exit;
				$('.sportName').val(data.sport_name);
				$('.sportId').val(data.sport_id);
			}
				
			}
		});	
	})
	
	$(".already_player").change(function(){
		var val = $(this).val();
		if(val == 1){
			$(".already").show();
			$(".no_already_player").addClass('required');
		}else{
			$(".already").hide();
			$(".no_already_player").removeClass('required');
		}
		
	})
	
	$(".min_player").change(function(){
		var min_player = $(this).val();
		var no_player  = $(".no_player").val();//alert(min_player);alert(no_player);exit;
		if(min_player > no_player){
			$(".min_player").val("");
			$(".player").text('Minimum player should be less than or equal to total number of player.');
		}else{
			$(".player").text('');
		}
	})
	
	</script>
	
	
<script>
	$(document).ready(function(){
		$('.timepicker').timepicker({
			timeFormat: 'HH:mm',
			interval: 5,
			minTime: '1',
			maxTime: '23:59',
			defaultTime: '00',
			startTime: '01:00',
			dynamic: false,
			dropdown: true,
			scrollbar: true
		});
	});
	$(function () {
		var date = new Date();
		 $("#datepicker").datepicker({ 
				autoclose: true, 
				todayHighlight: true
		 // }).datepicker('update', new Date()); 
		 });
	});
	
</script>

