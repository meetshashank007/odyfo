  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
		 <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                
				<h3></h3>
                <p>Players(<?php echo $totalPlayers;?>)</p>
              </div>
              <div class="icon">
                <img src = "<?php echo SITE_PATH;?>img/users.png">
              </div>
              <a href="<?php echo SITE_PATH;?>admin/Players/manage_players" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                
				<h3></h3>
                <p>Organisers(<?php echo $totalOrganisers;?>)</p>
              </div>
              <div class="icon">
                <img src = "<?php echo SITE_PATH;?>img/organiser.png">
              </div>
              <a href="<?php echo SITE_PATH;?>admin/Organisers/manage_organisers" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3></h3>

                <p>Games(<?php echo $totalGames;?>)</p>
              </div>
              <div class="icon">
                <img src = "<?php echo SITE_PATH;?>img/game.png">
              </div>
              <a href="<?php echo SITE_PATH;?>admin/Games/manage_games" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
		 
		   <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3></h3>

                <p>News(<?php echo $totalNews;?>)</p>
              </div>
              <div class="icon">
                <img src = "<?php echo SITE_PATH;?>img/news.png">
              </div>
              <a href="<?php echo SITE_PATH;?>admin/News/manage_news" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3></h3>

                <p>Venue</p>
              </div>
              <div class="icon">
                <img src = "<?php echo SITE_PATH;?>img/venue.png">
              </div>
              <a href="<?php echo SITE_PATH;?>admin/Venues/manage_venues" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
          <!--<div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>150</h3>

                <p>New Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
         <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>-->
          <!-- ./col -->
        </div>
        <!-- /.row -->
        
        
            </div>
            <!-- /.card -->
          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  