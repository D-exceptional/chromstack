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
  <title>Affiliate | Commissions</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/commission-overlay.css'); ?>">
   <style>
       html, body{
          overflow: hidden;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini" id="<?php echo $affiliateID; ?>">
<!-- Site wrapper -->
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
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><b>Commissions</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Commissions</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>

      <!-- Default box -->
      <div class="card" style="height: 550px;">
        <div class="card-header">
          <h3 class="card-title">Commission filter</h3>
          <select name="commission_filter" id="commission-filter" style="margin-left: 10px;border: none;">
            <option value="Select">Select</option>
            <option value="Daily">Yesterday</option>
            <option value="Weekly">Last Week</option>
            <option value="Monthly">Last Month</option>
            <option value="Yearly">Last Year</option>
            <option value="Custom">Custom</option>
          </select>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th>Course Title</th>
                      <th>Cover Page</th>
                      <th>Course Amount</th>
                      <th>Fixed Commission</th> 
                      <th>Weekly Sales</th>
                      <th>Weekly Revenue</th>
                      <th>Weekly Commision</th>
                      <th>Total Sales</th>
                      <th>Total Commision</th>
                  </tr>
              </thead>
              <tbody>

                <?php 
                    //Global variables
                    $course_title = '';
                    $total_sales = 0;
                    $total_affiliate_revenue = 0;
                    $total_affiliate_revenue_in_usd = 0;
                    $total_affiliate_commission = 0;
                    $affiliate_commission = 0;
                    $fixed_affiliate_commission = 0;
                    $total_unique_earning_in_usd = 0;
                    $total_unique_earning = 0;
                    $total_uploaded_course_earnings = 0;
                    $total_affiliate_course_earnings = 0;
                    $calculated_affiliate_commission = 0;
                ?>

                <?php

                  $sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course");
                  if(mysqli_num_rows($sql) > 0){
                      $result = mysqli_fetch_assoc($sql);
                      $course_title = $result['course_title'];
                      $cover_page = $result['course_cover_page'];
                      $course_amount = '$' . ($result['course_amount'] / 1000);
                      $path = $result['folder_path'];
                      $affiliate_commission = $result['affiliate_percentage'];
                      $formatted_num = substr($affiliate_commission, 0, -1);

                      //Get weekly sales for each affiliate
                      $unique_affiliate_weekly_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                      $weekly_affiliate_sales = mysqli_num_rows($unique_affiliate_weekly_sales_query);

                      //Get weekly affiliate revenue
                      $affiliate_revenue_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_revenue FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                      $affiliate_revenue_info = mysqli_fetch_assoc($affiliate_revenue_query);
                      $calculated_affiliate_revenue = $affiliate_revenue_info['total_affiliate_revenue'];
                      if ($calculated_affiliate_revenue > 0) {
                          $total_affiliate_revenue_in_usd = number_format(($calculated_affiliate_revenue / 1000), 2, '.', ',') ;
                      }
                      else{
                          $total_affiliate_revenue_in_usd = number_format(0, 2, '.', ',');
                      }

                      //Get weekly commission
                      $individual_affiliate_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM affiliate_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                      while($val = mysqli_fetch_assoc($individual_affiliate_sales_query)){
                        $calculated_affiliate_commission += $val['affiliate_unique_commission'];
                      }
                      $total_unique_earning_in_usd = number_format(((($formatted_num / 100) * ($calculated_affiliate_commission)) / 1000), 2, '.', ',');

                      //Get overall sales data
                      $overall_affiliate_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                      $overall_sales = mysqli_num_rows($overall_affiliate_sales_query);

                      //Total affiliate course commisions
                      $query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS earning_amount FROM affiliate_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                      if(mysqli_num_rows($query) > 0){ 
                        while($val = mysqli_fetch_assoc($query)){
                          $total_affiliate_course_earnings += $val['earning_amount'];
                        }
                      }
                      else{
                          $total_affiliate_course_earnings = 0;
                      }

                      $total_course_commission = '$' . number_format(($total_affiliate_course_earnings / 1000), 2, '.', ',');

                      echo "<tr>
                              <td>$course_title</td>
                              <td>
                                  <img src='../../courses/$path/$cover_page' style='width: 80px;height: 80px;border-radius: 5px;' alt='Cover Image'>
                              </td>
                              <td>$course_amount</td>
                              <td>$affiliate_commission</td>
                              <td>$weekly_affiliate_sales</td>
                              <td>$$total_affiliate_revenue_in_usd</td>
                              <td>$$total_unique_earning_in_usd</td>
                              <td>$overall_sales</td>
                              <td>$total_course_commission</td>
                          </tr>
                          ";
                  }

                ?>

                <?php 

                    $sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE course_status = 'Approved'");
                    if(mysqli_num_rows($sql) > 0){
                      while($result = mysqli_fetch_assoc($sql)){
                        $courseID = $result['courseID'];
                        $course_title = $result['course_title'];
                        $cover_page = $result['course_cover_page'];
                        $course_amount = '$' . ($result['course_amount'] / 1000);
                        $path = $result['folder_path'];
                        $affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                        $coverPage = '';
                        
                        if($path !== "null"){
                            $coverPage = "<img src='../../courses/$path/$cover_page' style='width: 80px;height: 80px;border-radius: 5px;' alt='Cover Image'>";
                        }else{
                            $coverPage = "<img src='../../assets/img/$cover_page' style='width: 80px;height: 80px;border-radius: 5px;' alt='Cover Image'>";
                        }

                        //Get weekly sales for each affiliate
                        $unique_affiliate_weekly_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sellerID = '$affiliateID' AND courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                        $weekly_affiliate_sales = mysqli_num_rows($unique_affiliate_weekly_sales_query);

                        //Get weekly revenue
                        $affiliate_revenue_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_revenue FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                        $affiliate_revenue_info = mysqli_fetch_assoc($affiliate_revenue_query);
                        $calculated_affiliate_revenue = $affiliate_revenue_info['total_affiliate_revenue'];
                        if ($calculated_affiliate_revenue > 0) {
                            $total_affiliate_revenue_in_usd = number_format(($calculated_affiliate_revenue / 1000), 2, '.', ',') ;
                        }
                        else{
                            $total_affiliate_revenue_in_usd = number_format(0, 2, '.', ',');
                        }
                        
                        //Get weekly commission
                        $individual_affiliate_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM uploaded_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                        $val = mysqli_fetch_assoc($individual_affiliate_sales_query);
                        $calculated_affiliate_commission = $val['affiliate_unique_commission'];
                        $total_unique_earning_in_usd = number_format(((($affiliate_commission / 100) * ($calculated_affiliate_commission)) / 1000), 2, '.', ',');
                       
                        //Get overall sales data
                        $overall_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                        $overall_uploaded_course_sales = mysqli_num_rows($overall_uploaded_course_sales_query);

                        //Get total commission
                        $query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS earning_amount FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                        if(mysqli_num_rows($query) > 0){ 
                          while($val = mysqli_fetch_assoc($query)){
                            $total_uploaded_course_earnings += $val['earning_amount'];
                          }
                        }
                        else{
                          $total_uploaded_course_earnings = 0;
                        }

                       $total_course_commission = '$' . number_format(( $total_uploaded_course_earnings / 1000), 2, '.', ',');
                        
                        echo "<tr>
                                <td>$course_title</td>
                                <td>
                                    $coverPage
                                </td>
                                <td>$course_amount</td>
                                <td>$affiliate_commission%</td>
                                <td>$weekly_affiliate_sales</td>
                                <td>$$total_affiliate_revenue_in_usd</td>
                                <td>$$total_unique_earning_in_usd</td>
                                <td>$overall_uploaded_course_sales</td>
                                <td>$total_course_commission</td>
                            </tr>
                            ";
                      }
                    }
                   
                ?>

              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

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
<script src="<?php echo getCacheBustedUrl('../scripts/commissions.js'); ?>" type='module'></script>
</body>
</html>
