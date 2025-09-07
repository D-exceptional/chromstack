<?php 

  session_start();
  
    // Function to generate cache-busted URL
    function getCacheBustedUrl($filePath) {
        // Check if the file exists
        if (file_exists($filePath)) {
            // Get the last modified time of the file
            $fileModificationTime = filemtime($filePath);
            // Return the URL with a query parameter for cache busting
            return $filePath . '?v=' . $fileModificationTime;
        }
        return $filePath; // Return original path if file doesn't exist
    }

  require './server/conn.php';

  if(!isset($_SESSION['affiliateID'])){
    header("Location: /login");
  }

  $affiliateID = $_SESSION['affiliateID'];

  $email = '';
  $fullname = '';
  $profile = '';

  $sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliateID = '$affiliateID'");
  if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $email = $row['email'];
    $fullname = $row['fullname'];
    $profile = $row['affiliate_profile'];

  }

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affiliate | Dashboard</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../admin/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../admin/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../admin/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../admin/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../admin/dist/css/dashboard.css'); ?>">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/commission-overlay.css'); ?>">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <input type="text" name="affiliateID" id="affiliateID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
<div class="wrapper">
  <!-- Preloader --
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../admin/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>-->

  <!-- Navbar -->

   <?php include 'home-notification.php'; ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;overflow-y: auto;overflow-x: hidden !important;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../assets/img/short-logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="width: 35px;height: 40px;border-radius: 50%;opacity: .9;">
      <span class="brand-text font-weight-light">Chromstack</span>
       <p id="user-email" style="display: none"><?php echo $email; ?></p>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="height: 630px;overflow-x: hidden !important;">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <?php
            if ($profile !== 'null') {
                 echo "<img src='../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;border: 2px solid #c2c7d0;' alt='User Image'>";
            }
            else {
              echo "<img src='../assets/img/user.png' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;border: 2px solid #c2c7d0;' alt='User Image'>";
            }
          ?>
        </div>
        <div class="info">
        <?php
            if ($fullname !== 'null') {
                echo "<a href='./views/settings.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
              }
               else {
                echo "<a href='./views/settings.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>Admin</a>";
              }
          ?>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <p id='sessionEmail' style='display: none;'><?php echo $email; ?></p>
        <p id='sessionName' style='display: none;'><?php echo $fullname; ?></p>
          <!-- All Courses -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/marketplace.php" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Marketplace
              </p>
            </a>
          </li>
           <li class="nav-item" id="eLearning">
            <a href="https://www.chromstack.com/eLearning/index.php?access=Affiliate&accessID=<?php echo $affiliateID; ?>" class="nav-link">
              <i class="fas fa-university" style='font-size: 20px;padding-right: 4px;'></i>
              <p>e-Learning</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/commissions.php" class="nav-link">
              <i class="nav-icon fas fa-donate"></i>
              <p>
                Commissions
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="views/actions.php" class="nav-link">
              <i class="nav-icon fas fa-tags"></i>
              <p>
                Analytics
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-medal"></i>
              <p>
                Contest
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="views/active-challenge.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Active</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="views/completed-challenge.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Completed</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hand-holding-usd"></i>
              <p>
                Withdrawal
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="views/withdrawal.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Weekly</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="views/withdrawal-history.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>History</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- Transactions -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
              Payout
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/pending-transaction.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Weekly</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/transaction-history.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>History</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-envelope-open"></i>
              <p>
                Mail
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/mailbox.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inbox</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/compose-mail.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Compose</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Community
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="https://x.com/chromstack?s=21" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>X</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://chat.whatsapp.com/LjgB5DhGbh9KCrHgvNtQ5z" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WhatsApp</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://t.me/+gc9Fr20Y70A0NTdk" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Telegram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://facebook.com/profile.php?id=61556804134821" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Facebook</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://instagram.com/chromstack?igshid=MzRIODBiNWFIZA==" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Instagram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.tiktok.com/@chromstack" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tiktok</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.youtube.com/@Chromstack" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>YouTube</p>
                </a>
              </li>
            </ul>
          </li>
          <!--<li class="nav-item">
            <a href="./views/timeline.php" class="nav-link">
              <i class="nav-icon fas fa-history"></i>
              <p>
                Timeline
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-book nav-icon"></i>
              <p>e-Manual</p>
            </a>
          </li>--->
          <li class="nav-item">
            <a href="./views/manual.php" class="nav-link">
              <i class="fas fa-book nav-icon"></i>
              <p>e-Manual</p>
            </a>
          </li>
          <!--<li class="nav-item">
            <a href="views/membership-renewal.php" class="nav-link">
              <i class="fas fa-retweet nav-icon"></i>
              <p>Renewal</p>
            </a>
          </li>-->
          <li class="nav-item">
            <a href="views/settings.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="server/logout.php" class="nav-link">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0"><b>Dashboard</b></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
        <div class="row mb-2" style="flex-wrap: nowrap;">
          <div class="col-sm-6" id='item-one'>
            <select name="commission_filter" id="commission-filter">
                <option value="Select">Check Sales By:</option>
                <option value="Daily">Yesterday</option>
                <option value="Weekly">Last Week</option>
                <option value="Monthly">Last Month</option>
                <option value="Yearly">Last Year</option>
                <option value="Custom">Choose Date</option>
            </select>
          </div><!-- /.col -->
          <div class="col-sm-6" id='item-two'>
            <p style="margin-top: 10px;"><b>Currency:</b></p>
            <select name="commission_filter" id="currency-filter">
                <option value="Dollar">&#x24;</option>
                <option value="Naira">&#x20A6;</option>
                <option value="Cedis">&#x20B5;</option>
                <option value="Shillings">&#83;</option>
                <option value="Cefa">&#x20A3;</option>
                <option value="Rand">&#82;</option>
            </select>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content" style="overflow-y: scroll;box-sizing: border-box;">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row" id="content-overview" style="height: 500px;"></div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" style="background-color: #181d38 !important;">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!--- Commission Overlay -->
<div id="commission-overlay">
    <div id="amount-div">Total amount: <b></b></div>
    <div id="sales-div">Total sales: <b></b></div>
     <p id="info">Search sales between date intervals</p>
    <div id="range-div">
       <p>From</p> 
      <input type="date" name="" id="from">
       <p>To</p> 
       <input type="date" name="" id="to">
       <center><button id="check-range-sales">Go</button></center>
    </div>
    <div id="close-div">X</div>
</div>

<!-- jQuery -->
<script src="../admin/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../admin/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../admin/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../admin/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../admin/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../admin/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../admin/plugins/moment/moment.min.js"></script>
<script src="../admin/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../admin/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../admin/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../admin/dist/js/demo.js"></script>
<!-- Admin main functional files -->
<script src="./scripts/events.js" type='module'></script>
<script src="../assets/scripts/tawkto.js"></script>
<script src="../assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo getCacheBustedUrl('./scripts/home-notification.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('./scripts/overview.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('./scripts/check.js'); ?>" type='module'></script>
 <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-LJMVMMV3RP');
</script>

</body>
</html>