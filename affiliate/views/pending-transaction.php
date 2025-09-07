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
  <title>Affiliate | Weekly Payout</title>
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
            <h1 id='payCounter'><b>Weekly Payout</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Weekly Payout</li>
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
          <h3 class="card-title">Beneficiary Details</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0"  style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Sales</th>
                    <th>Amount</th>
                    <th>Account</th>
                    <th>Bank</th>
                    <th>Code</th>
                </tr>
            </thead>
            <tbody>

                <!-- Final affiliate payout transaction here -->

                <?php 
                 
                    //Global variables
                    $fullname = '';
                    $account_number = '';
                    $bank = '';
                    $role = '';
                    $total_course_sold = 0;
                    //Earnings variables
                    $total_external_uploaded_course_commission = 0;
                    $total_personal_course_commission = 0;
                    $sales_type_commission = 0;
                    $total_affiliate_course_commission = 0;
                    $total_uploaded_course_earnings = 0;
                    $total_affiliate_course_earnings = 0;
                    $total_payout_amount = 0;
                    $total_payout_amount_in_usd = 0;

                    //Step (1) ---- Get sales details from affiliates table
                    $name_sql = mysqli_query($conn, "SELECT fullname, contact, account_number, bank, bank_code FROM affiliates WHERE affiliateID = '$affiliateID'");
                    $name_sql_result = mysqli_fetch_assoc($name_sql);
                    $fullname = $name_sql_result['fullname'];
                    $contact = $name_sql_result['contact'];
                    $account_number = $name_sql_result['account_number'];
                    $bank = $name_sql_result['bank'];
                    $bank_code = $name_sql_result['bank_code'];
                    $role = 'Affiliate';
                    //Get total uploaded courses sold
                    $uploaded_course_sales_count = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate'");
                    $uploaded_courses_sold = mysqli_num_rows($uploaded_course_sales_count);
                    //Get total affiliate program course sold
                    $affiliate_course_sales_count = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate'");
                    $affiliate_course_sold = mysqli_num_rows($affiliate_course_sales_count);
                    //Total courses sold
                    $total_course_sold = $uploaded_courses_sold + $affiliate_course_sold;

                    //Earnings variable
                    $total_uploaded_course_earnings = 0;
                    $total_affiliate_course_earnings = 0;

                    //Get commission from affiliate course sales
                    $main_course_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                    if(mysqli_num_rows($main_course_query) > 0){ 
                        $value = mysqli_fetch_assoc($main_course_query);
                        //Get fixed affiliate commission
                        $commission_sql = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course");
                        $result = mysqli_fetch_assoc($commission_sql);
                        $fixed_affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                        //Get unique sales amount
                        $main_course_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM affiliate_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
                        $val = mysqli_fetch_assoc($main_course_sales_query);
                        $calculated_affiliate_commission = $val['affiliate_unique_commission'];
                        //Get unique earnings
                        $total_affiliate_course_earnings = ($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100);
                    }
                    else{
                        $total_affiliate_course_earnings = 0;
                    }

                    //Get weekly sales details
                    $all_course_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed' GROUP BY courseID");
                    if(mysqli_num_rows($all_course_query) > 0){ 
                        while ($value = mysqli_fetch_assoc($all_course_query)) {
                            $courseID = $value['courseID'];
                            //Get fixed affiliate commission
                            $commission_sql = mysqli_query($conn, "SELECT affiliate_percentage FROM uploaded_courses WHERE courseID = '$courseID'");
                            $result = mysqli_fetch_assoc($commission_sql);
                            $fixed_affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
                            //Get unique sales amount
                            $other_course_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM uploaded_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
                            $val = mysqli_fetch_assoc($other_course_sales_query);
                            $calculated_affiliate_commission = $val['affiliate_unique_commission'];
                            //Get unique earnings
                            $total_uploaded_course_earnings += ($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100);
                        }
                    }
                    else{
                        $total_uploaded_course_earnings = 0;
                    }

                    //Total payout amount
                    $total_payout_amount = number_format(($total_uploaded_course_earnings + $total_affiliate_course_earnings), 2, '.', ',');
                    $total_payout_amount_in_usd = number_format(((($total_uploaded_course_earnings + $total_affiliate_course_earnings) / (1000))), 2, '.', ',');

                     echo "
                            <tr class='rows'>
                              <td class='fullname'>$fullname</td>
                              <td class='email'>$email</td>
                              <td class='contact'>$contact</td>
                              <td class='role'>$role</td>
                              <td>$total_course_sold</td>
                              <td class='amount-row'>$$total_payout_amount_in_usd</td>
                              <td class='account'>$account_number</td>
                              <td class='bank'>$bank</td>
                              <td class='code'>$bank_code</td>
                            </tr>
                          ";
     
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
    <strong>Copyright &copy; <a href="#!">chromstack</a>.</strong> All rights reserved.
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