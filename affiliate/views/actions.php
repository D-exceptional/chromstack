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

  require '../server/conn.php';

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
    $profile = $row['affiliate_profile'];
    $fullname = $row['fullname'];
  }

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affiliate | Analytics</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <style>
   html, body{
          overflow: hidden;
      }
    /* Extra small devices (phones, 600px and down) */
    @media screen and (max-width: 600px) {
        .col-lg-3.col-6
        {
            min-width: 100% !important;
        }
    }
    
    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media screen and (min-width: 600px) and (max-width: 992px) {
        .col-lg-3.col-6
        {
            width: 50% !important;
        }
    }
    
    /* Medium devices (landscape tablets, 768px and up) */
    @media screen and (min-width: 768px) {
        .col-lg-3.col-6
        {
            width: 50% !important;
        }
    }
    
    /* Large devices (laptops/desktops, 992px and up) */
    @media screen and (min-width: 992px) {
        .col-lg-3.col-6
        {
            width: 25% !important;
        }
    }
    
    /* Extra large devices (large laptops and desktops, 1200px and up) 
    @media only screen and (min-width: 1200px) {
        .col-lg-3 .col-6 {
            width: 25% !important;
            max-width: 25% !important;
        }
    }*/
    .inner{
        background-image: url(../../assets/img/dashboard-bg.jpeg);
        background-position: center;
        background-size: cover;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <input type="text" name="affiliateID" id="affiliateID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
<div class="wrapper">

  <!-- Navbar -->

  <?php include '../navbar.php'; ?>

    <!-- /.navbar -->
 
  <!-- Main Sidebar Container -->

  <?php include '../sidenav.php'; ?>

  <!-- / Main Sidebar Container -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><b>Analytics</b></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Analytics</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row" id="content-overview">

        <?php

            //Count completed sales
            $completed_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
            $completed_uploaded_course_sales_count = mysqli_num_rows($completed_uploaded_course_sales_query);
            //Get total affiliate program course sold
            $completed_affiliate_course_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
            $completed_affiliate_course_sales_count = mysqli_num_rows($completed_affiliate_course_sales_query);
            //Total courses sold
            $total_completed_course_sales = $completed_uploaded_course_sales_count + $completed_affiliate_course_sales_count;

            //Count pending sales
            $pending_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Pending'");
            $pending_uploaded_course_sales_count = mysqli_num_rows($pending_uploaded_course_sales_query);
            //Get total affiliate program course sold
            $pending_affiliate_course_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Pending'");
            $pending_affiliate_course_sales_count = mysqli_num_rows($pending_affiliate_course_sales_query);
            //Total courses sold
            $total_pending_course_sales = $pending_uploaded_course_sales_count + $pending_affiliate_course_sales_count;

            //Get total affiliate program course sold
            $total_clicks = $total_completed_course_sales + $total_pending_course_sales; 
           
            echo "
                 <div class='col-lg-3 col-6'>
                    <!-- small box -->
                    <div class='small-box bg-info' style='background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;'>
                        <div class='inner' style='display: flex;flex-direction: row;'>
                            <div class='icon-div' style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                               <i class='fas fa-hand-point-up'></i>
                            </div>
                            <div class='info-div' style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                <h3>$total_clicks</h3>
                                <p>Clicks</p>
                            </div>
                        </div>
                        <a href='#' class='small-box-footer' style='background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;'></a>
                    </div>
                </div>
                ";

                echo "
                     <div class='col-lg-3 col-6'>
                      <!-- small box -->
                      <div class='small-box bg-info' style='background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;'>
                          <div class='inner' style='display: flex;flex-direction: row;'>
                              <div class='icon-div' style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                  <!--<i class='fas fa-chart-line'></i>-->
                                  <i class='fas fa-hourglass-start'></i>
                              </div>
                              <div class='info-div' style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                   <h3>$total_pending_course_sales</h3>
                                    <p>Pending Sales</p>
                              </div>
                          </div>
                          <a href='#' class='small-box-footer' style='background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;'></a>
                      </div>
                  </div>
                    ";

            echo "
                 <div class='col-lg-3 col-6'>
                    <!-- small box -->
                    <div class='small-box bg-info' style='background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;'>
                        <div class='inner' style='display: flex;flex-direction: row;'>
                            <div class='icon-div' style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                <i class='fas fa-chart-line'></i>
                            </div>
                            <div class='info-div' style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                <h3>$total_completed_course_sales</h3>
                                <p>Completed Sales</p>
                            </div>
                        </div>
                        <a href='#' class='small-box-footer' style='background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;'></a>
                    </div>
                </div>
                ";

        ?>

        </div>
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
<!-- jQuery -->
<script src="../../admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../admin/dist/js/demo.js"></script>
<!-- Core Script -->
<script src="../../assets/js/sweetalert2.all.min.js"></script>
<script src="../scripts/events.js" type='module'></script>
<script src="../../assets/scripts/tawkto.js"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<!-- Admin main functional files -->
</body>
</html>
