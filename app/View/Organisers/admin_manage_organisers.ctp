<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		
        <div class="col-12">
        <?php //echo $this->Session->flash('successMsg');?>
		<center><div class="text-success message"><?php echo $this->Session->flash();?></div></center>
          <div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">
				
				<table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
				  
                  <th>Sr.No.</th>
				  <th>Name</th>
                  <th>Email</th>
				  <th>Gender</th>
				  <th>Phone</th>
				  <th>OTP</th>
				  <th>Admin Status</th>
				  <th>Action</th>
			    </tr>
                </thead>
                <tbody>
				<?php 
				if($result){
					$i=1;
					foreach($result as $res){
					?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php echo $res['User']['full_name'];?></td>
							<td><?php echo $res['User']['email'];?></td>
							<td><?php if($res['User']['gender']==1){echo 'Male';}elseif($res['User']['gender']==2){echo 'Female';}else{echo 'Other';}?></td>
							<td><?php echo $res['User']['mobile'];?></td>
							<td><?php if($res['User']['status']==1){?>
								<div id="status_<?php echo $res['User']['id'];?>" onclick="changeStatus(<?php echo $res['User']['id'];?>,0)">
									<span class="badge badge-success">Yes</span>
								</div><?php }else{?>
								<div id="status_<?php echo $res['User']['id'];?>" onclick="changeStatus(<?php echo $res['User']['id'];?>,1)">
									<span class="badge badge-danger">No</span>
								</div><?php }?>
							</td>
							<td><?php if($res['User']['admin_status']==1){?>
								<div id="admin_status_<?php echo $res['User']['id'];?>" onclick="changeAdminStatus(<?php echo $res['User']['id'];?>,0)">
									<span class="badge badge-success">Active</span>
								</div><?php }else{?>
								<div id="admin_status_<?php echo $res['User']['id'];?>" onclick="changeAdminStatus(<?php echo $res['User']['id'];?>,1)">
									<span class="badge badge-danger">Inactive</span>
								</div><?php }?>
							</td>
							<td>
								<a href="<?php echo SITE_PATH.'admin/Organisers/view_organiser/'.$res['User']['id'].'/1';?>" title = "View"><i class="fa fa-eye"></i></a>
								<a href="<?php echo SITE_PATH.'admin/Organisers/view_ratings/'.$res['User']['id'];?>" title = "Rating"><img src="<?php echo SITE_PATH."img/view.png"?>" width="20px" height ="20px"></a>
								<a href="<?php echo SITE_PATH.'admin/Organisers/view_organiser/'.$res['User']['id'];?>" title = "Edit"><i class="fa fa-edit"></i></a>
								<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/Organisers/delete/'.$res['User']['id'];?>')" title = "Delete"><i class="fa fa-trash"></i></a>
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
		
		
		
		function changeStatus(user_id,status){
			url="<?php echo SITE_PATH ?>admin/Organisers/updateStatus",
			$.ajax({
				url : url,
				type:'GET',
				async:false,
				data:{user_id:user_id,status:status},
				success:function(data){
					if(data !=1){
						$("#status_"+user_id).replaceWith('<div id="status_'+user_id+'" onclick="changeStatus('+user_id+',1)"><span class="badge badge-danger">No</span></div>');
					}else{
						$("#status_"+user_id).replaceWith('<div id="status_'+user_id+'" onclick="changeStatus('+user_id+',0)"><span class="badge badge-success">Yes</span></div>');
					}
					
				}
			});		
		}
		
		function changeAdminStatus(user_id,status){
			url="<?php echo SITE_PATH ?>admin/Organisers/verifyStatus",
			$.ajax({
				url : url,
				type:'GET',
				async:false,
				data:{user_id:user_id,admin_status:status},
				success:function(data){
					if(data !=1){
						$("#admin_status_"+user_id).replaceWith('<div id="admin_status_'+user_id+'" onclick="changeAdminStatus('+user_id+',1)"><span class="badge badge-danger">Inactive</span></div>');
					}else{
						$("#admin_status_"+user_id).replaceWith('<div id="admin_status_'+user_id+'" onclick="changeAdminStatus('+user_id+',0)"><span class="badge badge-success">Active</span></div>');
					}
					
				}
			});		
		}
	</script>
  
 
