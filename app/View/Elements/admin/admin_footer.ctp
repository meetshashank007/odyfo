<!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
    </div>
    <strong>Copyright &copy; <?php echo (date('Y').'-'.(date('Y')+1));?> <a href="#">Admin</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<!--<script src="<?php echo SITE_PATH;?>js/admin/jquery.min.js"></script>-->
<!-- Bootstrap 4 -->
<script src="<?php echo SITE_PATH;?>js/admin/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="<?php echo SITE_PATH;?>js/admin/jquery.dataTables.js"></script>
<script src="<?php echo SITE_PATH;?>js/admin/dataTables.bootstrap4.js"></script>
<!-- SlimScroll -->
<script src="<?php echo SITE_PATH;?>js/admin/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo SITE_PATH;?>js/admin/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo SITE_PATH;?>js/admin/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo SITE_PATH;?>js/admin/demo.js"></script>
<!--- Add on 12Mar19--->
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<!---End--->
  
<!-- page script -->
<script>
	$(window).on('load', function () {
		$('#example1').parent().addClass('table-responsive');
	});
		
  $(function () {
    $("#example1").DataTable({
	});
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('.message').fadeOut() }, 5000);
    });
</script>
</body>
</html>