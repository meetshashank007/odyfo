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
            <h4>Manage Pages</h4>
          </div>
          <div class="col-sm-6">
            <!--<button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/StaticPages/add_pages"?>'" style="float:right">Add Pages</button>-->
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
					<th>Title</th>
					<th>Language</th>
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
							
							<td><?php echo $res['StaticPage']['title'];?></td>
							<td><?php echo $this->My->getLanguage($res['StaticPage']['language']);?></td>
							
							<td><?php if($res['StaticPage']['status']==1){?>
								<div id="status_<?php echo $res['StaticPage']['id'];?>" onclick="changeStatus(<?php echo $res['StaticPage']['id'];?>,0)">
									<span class="badge badge-success">Active</span>
								</div><?php }else{?>
								<div id="status_<?php echo $res['StaticPage']['id'];?>" onclick="changeStatus(<?php echo $res['StaticPage']['id'];?>,1)">
									<span class="badge badge-danger">Inactive</span>
								</div><?php }?>
							</td>
							</td>
							<td>
								<a href="<?php echo SITE_PATH.'admin/StaticPages/add_pages/'.$res['StaticPage']['id'].'/1';?>" title = "View"><i class="fa fa-eye"></i></a>
							
								<a href="<?php echo SITE_PATH.'admin/StaticPages/add_pages/'.$res['StaticPage']['id'];?>" title = "Edit"><i class="fa fa-edit"></i></a>
								<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/StaticPages/delete/'.$res['StaticPage']['id'];?>')" title = "Delete"><i class="fa fa-trash"></i></a>
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
		function changeStatus(page_id,status){
			url="<?php echo SITE_PATH ?>admin/StaticPages/updateStatus",
			$.ajax({
				url : url,
				type:'GET',
				async:false,
				data:{page_id:page_id,status:status},
				success:function(data){
					if(data !=1){
						$("#status_"+page_id).replaceWith('<div id="status_'+page_id+'" onclick="changeStatus('+page_id+',1)"><span class="badge badge-danger">Inactive</span></div>');
					}else{
						$("#status_"+page_id).replaceWith('<div id="status_'+page_id+'" onclick="changeStatus('+page_id+',0)"><span class="badge badge-success">Active</span></div>');
					}
					
				}
			});		
		}
		
	</script>
  
 
