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

    if(!isset($_SESSION['vendorID'])){
      header("Location: /login");
    }

    $vendorID = $_SESSION['vendorID'];
  
    $email = '';
    $fullname = '';
    $profile = '';
    
  $sql = mysqli_query($conn, "SELECT * FROM vendors WHERE vendorID = '$vendorID'");
  if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $email = $row['email'];
    $fullname = $row['fullname'];
    $profile = $row['vendor_profile'];
  }

?>  


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vendor | Commissions</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/commission-overlay.css'); ?>">
   <style>
      html,body{
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
          <!--<select name="commission_filter" id="commission-filter" style="margin-left: 10px;border: none;">
            <option value="Daily">Daily</option>
            <option value="Weekly">Weekly</option>
            <option value="Monthly">Monthly</option>
            <option value="Yearly">Yearly</option>
            <option value="Custom">Custom</option>
          </select>-->
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
                  <th>Vendor Commission</th> 
                  <th>Weekly Sales</th>
                  <th>Weekly Revenue</th>
                  <th>Weekly Commision</th>
                  <th>Total Sales</th>
                  <th>Total Revenue</th>
                </tr>
              </thead>
              <tbody>

                <?php 

                    //Global variables
                    $total_weekly_earning = 0;
                    $total_weekly_earning_in_usd = 0;
                    $total_uploaded_course_earnings = 0;
                    $total_payout_amount = 0;
                    $total_payout_amount_in_usd = 0;
                    $overall_uploaded_course_sales = 0;
                    $image = '';

                    //Get vendor fullname
                    $name_sql = mysqli_query($conn, "SELECT fullname FROM vendors WHERE email = '$email'");
                    $name_sql_result = mysqli_fetch_assoc($name_sql);
                    $fullname = $name_sql_result['fullname'];

                    //Get course commissions details
                    $sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE course_authors = '$fullname' AND course_status = 'Approved'");
                    if(mysqli_num_rows($sql) > 0){
                        while($result = mysqli_fetch_assoc($sql)){
                          $courseID = $result['courseID'];
                          $course_title = $result['course_title'];
                          $cover_page = $result['course_cover_page'];
                          $course_amount = '$' . ($result['course_amount'] / 1000);
                          $vendor_commission = substr($result['vendor_percentage'], 0, -1);
                          $image = $result['folder_path'] === 'null' ? "../../assets/img/$cover_page" : "../../courses/$path/$cover_page";

                          //Get total sales data
                          $course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                          $weekly_sales = mysqli_num_rows($course_sales_query);
                            
                          //Get overall sales data
                          $overall_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                          $overall_uploaded_course_sales = mysqli_num_rows($overall_uploaded_course_sales_query);

                          //if ($total_sales > 0) {
                            $individual_course_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_course_commission FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                            $val = mysqli_fetch_assoc($individual_course_sales_query);
                            $calculated_weekly_commission = $val['total_course_commission'];
                            $total_weekly_commission = number_format((($calculated_weekly_commission / 1000)), 2, '.', ',');

                            //Get unique earnings
                            $total_weekly_earning = number_format((($vendor_commission / 100) * ($calculated_weekly_commission)), 2, '.', ',');
                            $total_weekly_earning_in_usd = number_format(((($vendor_commission / 100) * ($calculated_weekly_commission)) / 1000), 2, '.', ',');
                            
                            // Total revenue
                            $total_revenue_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_revenue FROM uploaded_course_sales_backup WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                            $value = mysqli_fetch_assoc($total_revenue_query);
                            $calculated_revenue = $value['total_revenue'];
                            $total_revenue = number_format((($calculated_revenue / 1000)), 2, '.', ',');
                            
                            echo 
                            "<tr>
                              <td>$course_title</td>
                              <td>
                                <img src='$image' style='width: 80px;height: 80px;border-radius: 5px;' alt='Cover Image'>
                              </td>
                              <td>$course_amount</td>
                              <td>$vendor_commission%</td>
                              <td>$weekly_sales</td>
                              <td>$$total_weekly_commission</td>
                              <td>$$total_weekly_earning_in_usd</td>
                              <td>$overall_uploaded_course_sales</td>
                              <td>$$total_revenue</td>
                              </tr>
                            ";
                          //}
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
    <input type="date" name="" id="from">
      <p>to</p> 
      <input type="date" name="" id="to">
      <button id="check-range-sales">Go</button>
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
<script src="<?php echo getCacheBustedUrl('../scripts/commissions.js'); ?>"></script>
</body>
</html>
