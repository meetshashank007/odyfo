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
            <h4>Manage Games</h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Games/view_game"?>'" style="float:right">Add Game</button>
          </div>
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
					<th>Image</th>
					<th>Name</th>
					<th>Date</th>
					<th>Type</th>
					<th>City</th>
					<th>Organised By</th>
					<th>Game Status</th>
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
							<?php $dummyImage = SITE_PATH.'img/no-image.png';?>
							<img src="<?php echo !empty($res['Game']['image'])?SITE_PATH."img/game/".$res['Game']['image']:$dummyImage;?>" width="50px" height="50px"></td>
							<td><?php echo $res['Game']['name'];?></td>
							<td><?php echo $res['Game']['date_time'];?></td>
							<td><?php $sport 	=  	$this->My->getSportById($res['Game']['sports_id']); echo $sport['Sport']['name'];?></td>
							<td><?php $city 	= 	$this->My->getCityById($res['Game']['city_id']); echo $city['City']['name'];?></td>
							
							<td><?php if($res['Game']['user_id']== 0){echo "Odyfo";}else{$name = $this->My->getuserbyId($res['Game']['user_id']); echo $name['full_name'];}?></td>
							<td><?php if($res['Game']['cancel_status']== 1){echo "Cancel";}else{if($res['Game']['cancel_status']== 0){echo "Upcoming";}else{echo "Completed";}}?></td>
							
							
							<td><?php if($res['Game']['status']==1){?>
								<div id="status_<?php echo $res['Game']['id'];?>" onclick="changeStatus(<?php echo $res['Game']['id'];?>,0)">
									<span class="badge badge-success">Active</span>
								</div><?php }else{?>
								<div id="status_<?php echo $res['Game']['id'];?>" onclick="changeStatus(<?php echo $res['Game']['id'];?>,1)">
									<span class="badge badge-danger">Inactive</span>
								</div><?php }?>
							</td>
							<td>
								<a href="<?php echo SITE_PATH.'admin/Games/view_game/'.$res['Game']['id'].'/1';?>" title = "View"><i class="fa fa-eye"></i></a>
								<a href="<?php echo SITE_PATH.'admin/Games/game_team/'.$res['Game']['id'];?>" title = "Teams"><img src="<?php echo SITE_PATH."img/view.png"?>" width="20px" height ="20px"></a>
								<a href="<?php echo SITE_PATH.'admin/Games/view_game/'.$res['Game']['id'];?>" title = "Edit"><i class="fa fa-edit"></i></a>
								<!--<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/Games/delete/'.$res['Game']['id'];?>')" title = "Delete"><i class="fa fa-trash"></i></a>-->
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
		function changeStatus(game_id,status){
			url="<?php echo SITE_PATH ?>admin/Games/updateStatus",
			$.ajax({
				url : url,
				type:'GET',
				async:false,
				data:{game_id:game_id,status:status},
				success:function(data){
					if(data !=1){
						$("#status_"+game_id).replaceWith('<div id="status_'+game_id+'" onclick="changeStatus('+game_id+',1)"><span class="badge badge-danger">Inactive</span></div>');
					}else{
						$("#status_"+game_id).replaceWith('<div id="status_'+game_id+'" onclick="changeStatus('+game_id+',0)"><span class="badge badge-success">Active</span></div>');
					}
					
				}
			});		
		}
	</script>
  
 
