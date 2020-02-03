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
        <center><div class="text-success message"><?php echo $this->Session->flash();?></div></center>
		
          <div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr. No.</th>
				  <th>Subject</th>
                  <th>Action</th>
			    </tr>
                </thead>
                <tbody>
				<?php 
				if($result){
					$i=1;
					foreach($result as $res){//echo "<pre>";print_r($res);die;?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php echo $res['EmailTemplate']['subject'];?></td>
							<td>
								<a href="<?php echo SITE_PATH.'admin/EmailTemplates/add_email/'.$res['EmailTemplate']['id'].'/1';?>" title="View"><i class="fa fa-eye"></i></a>
								<a href="<?php echo SITE_PATH.'admin/EmailTemplates/add_email/'.$res['EmailTemplate']['id'];?>" title="Edit"><i class="fa fa-edit"></i></a>
								<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/EmailTemplates/delete/'.$res['EmailTemplate']['id'];?>')" title="Delete"><i class="fa fa-trash"></i></a>
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
		function changeStatus(rank_id,status){
			url="<?php echo SITE_PATH ?>admin/RankingLevels/updateStatus",
			$.ajax({
				url : url,
				type:'GET',
				async:false,
				data:{rank_id:rank_id,status:status},
				success:function(data){
					if(data !=1){
						$("#status_"+rank_id).replaceWith('<div id="status_'+rank_id+'" onclick="changeStatus('+rank_id+',1)"><span class="badge badge-danger">Inactive</span></div>');
					}else{
						$("#status_"+rank_id).replaceWith('<div id="status_'+rank_id+'" onclick="changeStatus('+rank_id+',0)"><span class="badge badge-success">Active</span></div>');
					}
					
				}
			});		
		}
	</script>
  
 
