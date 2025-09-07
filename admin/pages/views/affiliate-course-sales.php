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

  if(!isset($_SESSION['adminID'])){
    header("Location: /admin/index");
  }

  $adminID = $_SESSION['adminID'];

  $email = '';
  $fullname = '';
  $profile = '';

  $sql = mysqli_query($conn, "SELECT * FROM admins WHERE adminID = '$adminID'");
  if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $email = $row['email'];
    $fullname = $row['fullname'];
    $profile = $row['admin_profile'];
  }
  
  $courseID = mysqli_real_escape_string($conn, $_GET['courseID']);
  $type = mysqli_real_escape_string($conn, $_GET['type']);

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Affiliate Sales</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <style>
      html, body{
          overflow: hidden !important;
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
            <h1><b>Affiliate Sales</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Affiliate Sales</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style="overflow-y: auto !important;box-sizing: border-box;">

      <!-- Default box -->
      <div class="card" style="height: 550px;">
        <div class="card-header">
          <h3 class="card-title">Affiliate Sales</h3>

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
                      <th>Course Revenue</th>
                      <th>Affiliate Revenue</th>
                      <th>Fullname</th>
                      <th>Quantity Sold</th>
                      <th>Unique Revenue</th>
                      <th>Net Income</th>
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
                           //Get total amount generated
                          $amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_sales_amount FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                          if (mysqli_num_rows($amount_query) > 0) {
                            $amount_query_result = mysqli_fetch_assoc($amount_query);
                            $total_amount_generated = $amount_query_result['total_sales_amount'];
                            $total_sales_amount = $total_amount_generated;
                            $total_sales_amount_in_usd = $total_amount_generated / 1000;
                          }
                          else {
                            $total_sales_amount = 0;
                            $total_sales_amount_in_usd = 0;
                          }
                          //Total affiliate sales amount
                          $total_affiliate_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_sales_amount FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                          if (mysqli_num_rows($total_affiliate_amount_query) > 0) {
                            $total_affiliate_amount_query_result = mysqli_fetch_assoc($total_affiliate_amount_query);
                            $total_affiliate_sales_amount = $total_affiliate_amount_query_result['total_affiliate_sales_amount'];
                            $total_sales_amount_in_naira = $total_affiliate_sales_amount;
                            $total_affiliate_sales_amount_in_usd = $total_affiliate_sales_amount / 1000;
                          }
                          else {
                             $total_sales_amount_in_naira = 0;
                             $total_affiliate_sales_amount_in_usd = 0;
                          }
                          //Get affiliate details
                          $sales_details_query = mysqli_query($conn, "SELECT sellerID FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                          while($value = mysqli_fetch_assoc($sales_details_query)){
                            $affiliateID = $value['sellerID'];
                            //Get details
                            $affiliate_details_query = mysqli_query($conn, "SELECT fullname, email FROM affiliates WHERE affiliateID = '$affiliateID'");
                            $value = mysqli_fetch_assoc($affiliate_details_query);
                            $fullname = $value['fullname'];
                            $email = $value['email'];
                            //Get each affiliate sales count
                            $affiliate_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                            $total_sales = mysqli_num_rows($affiliate_sales_query);
                            //Get each affiliate sales amount
                            $unique_affiliate_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS unique_sales_commission FROM uploaded_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                            if (mysqli_num_rows($unique_affiliate_amount_query) > 0) {
                              $unique_affiliate_amount_query_result = mysqli_fetch_assoc($unique_affiliate_amount_query);
                              $unique_affiliate_total_amount = $unique_affiliate_amount_query_result['unique_sales_commission'];
                              $unique_affiliate_total_amount_in_naira = $unique_affiliate_total_amount;
                              $unique_affiliate_total_amount_in_usd = $unique_affiliate_total_amount / 1000;
                            }
                            else {
                              $unique_affiliate_total_amount_in_naira = 0;
                              $unique_affiliate_total_amount_in_usd = 0;
                            }

                             $net_income = $unique_affiliate_total_amount_in_naira * ($affiliate_commission / 100);
                             $net_income_in_usd = ($unique_affiliate_total_amount_in_naira * ($affiliate_commission / 100)) / 1000;
                          
                            echo "<tr>
                                    <td>$course_title</td>
                                    <td>&#x20A6 $total_sales_amount / $$total_sales_amount_in_usd</td>
                                    <td>&#x20A6 $total_sales_amount_in_naira / $$total_affiliate_sales_amount_in_usd</td>
                                    <td>$fullname</td>
                                    <td>$total_sales</td>
                                    <td>&#x20A6 $unique_affiliate_total_amount_in_naira / $$unique_affiliate_total_amount_in_usd</td>
                                    <td>&#x20A6 $net_income / $$net_income_in_usd</td>
                                </tr>
                                ";
                          } 
                      }
                      else{
                          echo 'Course details not available!';
                      } 
                    break;
                    case 'Affiliate':
                        $sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course WHERE courseID = '$courseID'");
                        if(mysqli_num_rows($sql) > 0){
                            $result = mysqli_fetch_assoc($sql);
                            $course_title = $result['course_title'];
                            $affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                            //Get total amount generated
                            $amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_sales_amount FROM affiliate_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                            if (mysqli_num_rows($amount_query) > 0) {
                              $amount_query_result = mysqli_fetch_assoc($amount_query);
                              $total_amount_generated = $amount_query_result['total_sales_amount'];
                              $total_sales_amount = $total_amount_generated;
                              $total_sales_amount_in_usd = $total_amount_generated / 1000;
                            }
                            else {
                              $total_sales_amount = 0;
                              $total_sales_amount_in_usd = 0;
                            }
                            //Total affiliate sales amount
                            $total_affiliate_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_affiliate_sales_amount FROM affiliate_course_sales WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                            if (mysqli_num_rows($total_affiliate_amount_query) > 0) {
                              $total_affiliate_amount_query_result = mysqli_fetch_assoc($total_affiliate_amount_query);
                              $total_affiliate_sales_amount = $total_affiliate_amount_query_result['total_affiliate_sales_amount'];
                              $total_sales_amount_in_naira = $total_affiliate_sales_amount;
                              $total_affiliate_sales_amount_in_usd = $total_affiliate_sales_amount / 1000;
                            }
                            else {
                              $total_sales_amount_in_naira = 0;
                              $total_affiliate_sales_amount_in_usd = 0;
                            }
                            //Get affiliate details
                            $sales_details_query = mysqli_query($conn, "SELECT sellerID FROM affiliate_course_sales WHERE courseID = '$courseID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                            while($value = mysqli_fetch_assoc($sales_details_query)){
                              $affiliateID = $value['sellerID'];
                              //Get details
                              $affiliate_details_query = mysqli_query($conn, "SELECT fullname, email FROM affiliates WHERE affiliateID = '$affiliateID'");
                              $value = mysqli_fetch_assoc($affiliate_details_query);
                              $fullname = $value['fullname'];
                              $email = $value['email'];
                              //Get each affiliate sales count
                              $affiliate_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                              $total_sales = mysqli_num_rows($affiliate_sales_query);
                              //Get each affiliate sales amount
                              $unique_affiliate_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS unique_sales_commission FROM affiliate_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                              if (mysqli_num_rows($unique_affiliate_amount_query) > 0) {
                                $unique_affiliate_amount_query_result = mysqli_fetch_assoc($unique_affiliate_amount_query);
                                $unique_affiliate_total_amount = $unique_affiliate_amount_query_result['unique_sales_commission'];
                                $unique_affiliate_total_amount_in_naira = $unique_affiliate_total_amount;
                                $unique_affiliate_total_amount_in_usd = $unique_affiliate_total_amount / 1000;
                              }
                              else {
                                $unique_affiliate_total_amount_in_naira = 0;
                                $unique_affiliate_total_amount_in_usd = 0;
                              }

                              $net_income = $unique_affiliate_total_amount_in_naira * ($affiliate_commission / 100);
                              $net_income_in_usd = (($unique_affiliate_total_amount_in_naira * ($affiliate_commission / 100)) / 1000);
                             
                              echo "<tr>
                                     <td>$course_title</td>
                                    <td>&#x20A6 $total_sales_amount / $$total_sales_amount_in_usd</td>
                                    <td>&#x20A6 $total_sales_amount_in_naira / $$total_affiliate_sales_amount_in_usd</td>
                                    <td>$fullname</td>
                                    <td>$total_sales</td>
                                    <td>&#x20A6 $unique_affiliate_total_amount_in_naira / $$unique_affiliate_total_amount_in_usd</td>
                                    <td>&#x20A6 $net_income / $$net_income_in_usd</td>
                                  </tr>
                                  ";
                            } 
                        }
                        else{
                            echo 'Course details not available!';
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
    <strong>Copyright &copy; <a href="#!">fiberearn</a>.</strong> All rights reserved.
  </footer> -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" style="background-color: #181d38 !important;">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Core Script -->
<script src="../../../assets/js/sweetalert2.all.min.js"></script>
<script src="../scripts/indent.js"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
