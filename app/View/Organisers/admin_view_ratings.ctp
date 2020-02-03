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
					<th>Sr. No.</th>
					<th>Game</th>
					<th>Rating By</th>
					<th>Rating</th>
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
							<td><?php echo $res['Game']['name'];?></td>
							<td><?php echo $res['User']['full_name'];?></td>
							<td><?php echo $res['UserRating']['rating']."*";?></td>
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
			url="<?php echo SITE_PATH ?>admin/Players/updateStatus",
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
	</script>
  
 
