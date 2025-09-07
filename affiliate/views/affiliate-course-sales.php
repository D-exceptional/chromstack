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

$courseID = mysqli_real_escape_string($conn, $_GET['courseID']);
$type = mysqli_real_escape_string($conn, $_GET['type']);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affiliate | Sales</title>
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
  </style>
</head>
<body class="hold-transition sidebar-mini">
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
            <h1><b>Your Sales</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Your Sales</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <?php
              switch ($type) {
                    case 'External':
                    case 'Admin':
                       $sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE courseID = '$courseID'");
                        if(mysqli_num_rows($sql) > 0){
                            $result = mysqli_fetch_assoc($sql);
                            $course_title = $result['course_title'];
                        }
                    break;
                    case 'Affiliate':
                      $sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course WHERE courseID = '$courseID'");
                      if(mysqli_num_rows($sql) > 0){
                          $result = mysqli_fetch_assoc($sql);
                          $course_title = $result['course_title'];
                      }
                    break;
                }  
             
                echo "Sales of " . "<b>" . $course_title . "</b>";
            ?>
          </h3>

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
                      <th>Course</th>
                      <th>Fixed Commission</th> 
                      <th>Total Sales</th>
                      <th>Total Revenue</th>
                      <th>Affiliate Commision</th>
                  </tr>
              </thead>
              <tbody>
                <?php 
                  switch ($type) {
                    case 'External':
                    case 'Admin':
                       $sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE courseID = '$courseID'");
                        if(mysqli_num_rows($sql) > 0){
                            $result = mysqli_fetch_assoc($sql);
                            $course_title = $result['course_title'];
                            $affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                            //Get total individual affiliate revenue
                            $affiliate_revenue_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_revenue FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                            $affiliate_revenue_info = mysqli_fetch_assoc($affiliate_revenue_query);
                            $calculated_affiliate_revenue = $affiliate_revenue_info['total_affiliate_revenue'];
                            if ($calculated_affiliate_revenue > 0) {
                                $total_affiliate_revenue = number_format(($calculated_affiliate_revenue), 2, '.', ',') ;
                                $total_affiliate_revenue_in_usd = number_format(($calculated_affiliate_revenue / 1000), 2, '.', ',') ;
                            }
                            else{
                                $total_affiliate_revenue = number_format(0, 2, '.', ',');
                                $total_affiliate_revenue_in_usd = number_format(0, 2, '.', ',');
                            }
                        }
                        else{
                            echo 'Course details not available !';
                        }    
                        //Get total sales data
                        $affiliate_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                        $total_sales = mysqli_num_rows($affiliate_sales_query);
                        if ($total_sales > 0) {
                          //Continue with processing
                          $query_data = mysqli_fetch_assoc($affiliate_sales_query);
                          //Get fullname
                          $individual_affiliate_sales_query = mysqli_query($conn, "SELECT sellerID, SUM(sales_amount) AS affiliate_unique_commission FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                          $val = mysqli_fetch_assoc($individual_affiliate_sales_query);
                          $calculated_affiliate_commission = $val['affiliate_unique_commission'];
                          //Get total sales for each affiliate
                          $unique_affiliate_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                          $total_unique_affiliate_sales = mysqli_num_rows($unique_affiliate_sales_query);
                          //Get unique earnings
                          $total_unique_earning = number_format((($calculated_affiliate_commission) * ($affiliate_commission / 100)), 2, '.', ',');
                          $total_unique_earning_in_usd = number_format(((($calculated_affiliate_commission) * ($affiliate_commission / 100)) / 1000), 2, '.', ',');
                            
                          echo "<tr>
                                  <td>$course_title</td>
                                  <td>$affiliate_commission%</td>
                                  <td>$total_unique_affiliate_sales</td>
                                  <td>$$total_affiliate_revenue_in_usd</td>
                                  <td>$$total_unique_earning_in_usd</td>
                                </tr>
                              ";
                        }
                        else {
                            echo "<tr>
                                    <td>$course_title</td>
                                    <td>$affiliate_commission%</td>
                                    <td>0</td>
                                    <td>$$total_affiliate_revenue_in_usd</td>
                                    <td>$0.00</td>
                                  </tr>
                                ";
                        }
                    break;
                    case 'Affiliate':
                       $sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course WHERE courseID = '$courseID'");
                        if(mysqli_num_rows($sql) > 0){
                            $result = mysqli_fetch_assoc($sql);
                            $course_title = $result['course_title'];
                            $affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                            //Get total affiliate revenue
                            $affiliate_revenue_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_revenue FROM affiliate_course_sales_backup WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                            $affiliate_revenue_info = mysqli_fetch_assoc($affiliate_revenue_query);
                            $calculated_affiliate_revenue = $affiliate_revenue_info['total_affiliate_revenue'];
                            if ($calculated_affiliate_revenue > 0) {
                                $total_affiliate_revenue = number_format(($calculated_affiliate_revenue), 2, '.', ',') ;
                                $total_affiliate_revenue_in_usd = number_format(($calculated_affiliate_revenue / 1000), 2, '.', ',') ;
                            }
                            else{
                                $total_affiliate_revenue = number_format(0, 2, '.', ',');
                                $total_affiliate_revenue_in_usd = number_format(0, 2, '.', ',');
                            }
                        }
                        else{
                            echo 'Course details not available !';
                        }    
                        //Get total sales data
                        $affiliate_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup  WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                        $total_sales = mysqli_num_rows($affiliate_sales_query);
                        if ($total_sales > 0) {
                            //Continue with processing
                            $query_data = mysqli_fetch_assoc($affiliate_sales_query);
                            //AdminID
                            $currentID = $query_data['sellerID'];
                            //Get fixed affiliate commission
                            $commission_sql = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course");
                            $result = mysqli_fetch_assoc($commission_sql);
                            $fixed_affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                            //Get fullname
                            $individual_affiliate_sales_query = mysqli_query($conn, "SELECT sellerID, SUM(sales_amount) AS affiliate_unique_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                            $val = mysqli_fetch_assoc($individual_affiliate_sales_query);
                            $calculated_affiliate_commission = $val['affiliate_unique_commission'];
                            $calculated_commission = $calculated_affiliate_commission / 1000;
                            //Get total sales for each affiliate
                            $unique_affiliate_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                            $total_unique_affiliate_sales = mysqli_num_rows($unique_affiliate_sales_query);
                            //Get unique earnings
                            $total_unique_earning = number_format((($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100)), 2, '.', ',');
                            $total_unique_earning_in_usd = number_format(((($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100)) / 1000), 2, '.', ',');
                                   
                            echo "<tr>
                                    <td>$course_title</td>
                                    <td>$affiliate_commission%</td>
                                    <td>$total_unique_affiliate_sales</td>
                                    <td>$$calculated_commission</td>
                                    <td>$$total_unique_earning_in_usd</td>
                                  </tr>
                                ";
                        }
                        else {
                           echo "<tr>
                                  <td>$course_title</td>
                                  <td>$affiliate_commission%</td>
                                  <td>0</td>
                                  <td>$0.00</td>
                                  <td>$0.00</td>
                                </tr>
                              ";
                        }
                    break;
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
  <!-- /.content-wrapper 

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy;  <a href="#!">chromstack</a>.</strong> All rights reserved.
  </footer>-->

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
</body>
</html>
