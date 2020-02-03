<!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
     </ul>
	<!-- Right navbar links -->
    <ul class="navbar-nav ml-auto"><a href="<?php echo SITE_PATH.'admin/Admins/logout';?>">Logout</a>
    </ul>
  </nav>
  <!-- /.navbar -->
  
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <!--<img src="<?php echo SITE_PATH; ?>img/AdminLTELogo.png"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">-->
		   <img src="<?php echo SITE_PATH; ?>img/admin.png"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
     <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/Admins/dashboard" class="nav-link">
              <i class="nav-icon fa fa-dashboard"></i>
              <p>
                Dashboard
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
          </li>
		 
          <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/Players/manage_players" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Players
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
			
          </li>
		   <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/Organisers/manage_organisers" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Organisers
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
			
          </li>
		   <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Venues
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo SITE_PATH;?>admin/Venues/manage_venues" class="nav-link">
                  <i class="fa fa-circle-o nav-icon"></i>
                  <p>Manage Venues</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo SITE_PATH;?>admin/Venues/add_venue" class="nav-link">
                  <i class="fa fa-circle-o nav-icon"></i>
                  <p>Add Venue</p>
                </a>
              </li>
              
            </ul>
          </li>
		  <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Games
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo SITE_PATH;?>admin/Games/manage_games" class="nav-link">
                  <i class="fa fa-circle-o nav-icon"></i>
                  <p>Manage Games</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo SITE_PATH;?>admin/Games/view_game" class="nav-link">
                  <i class="fa fa-circle-o nav-icon"></i>
                  <p>Add Game</p>
                </a>
              </li>
              
            </ul>
          </li>
		   
		   
		    <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/News/manage_news" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage News
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
			
          </li>
		  
		  <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/Payments/manage_payments/" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Transaction
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
			
          </li>
		   <li class="nav-item has-treeview">
            <a href="<?php echo SITE_PATH;?>admin/StaticPages/manage_pages" class="nav-link">
              <i class="nav-icon fa fa-pie-chart"></i>
              <p>
                Manage Pages
                <!--<i class="right fa fa-angle-left"></i>-->
              </p>
            </a>
			
          </li>
		</ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
