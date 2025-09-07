<?php 

  session_start();
  if(!isset($_SESSION['adminID'])){
      header("Location: /admin/index");
  }

  require '../server/conn.php';
  mysqli_set_charset($conn, 'utf8');
  
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

?>                 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Transaction</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <style>
      html, body{
          overflow: hidden;
      }
      
      table tr:last-child {
      margin-bottom: 50px !important;
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
            <h1 id='payCounter'>Transaction</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Transaction</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;height: auto;'>

      <!-- Default box -->
      <div class="card" style="height: 550px;">
        <div class="card-header">
          <h3 class="card-title">Transaction</h3>
          <div class="card-tools">
            <!--<button type="button" class="btn btn-tool" style='background: #199c37;color: white;margin-left: 5px;' id='pay-all'>Pay All</button>
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;margin-left: 5px;' id='backup'>Back Up</button>-->
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0"  style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Country</th>
                    <th>Role</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='sales-list'>
              <?php
             
              //Global variables 
              $total_generated_amount = 0;
              $total_generated_amount_in_usd = 0;
              $total_external_generated_amount = 0;
              $total_affiliate_generated_amount = 0;
              $total_direct_registration_amount = 0;
              $sum_of_ten_percent_of_external_courses = 0;
              $amount_left = 0;

              //Get total amount generated from uploaded course sales
              $total_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_amount FROM uploaded_course_sales WHERE sales_status = 'Completed'");
              if(mysqli_num_rows($total_amount_query) > 0){ 
                $row = mysqli_fetch_assoc($total_amount_query);
                $total_external_generated_amount = $row['total_amount'];
              }

              //Get total amount generated from affiliate course sales
              $total_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_amount FROM affiliate_course_sales WHERE sales_status = 'Completed'");
              if(mysqli_num_rows($total_amount_query) > 0){ 
                $row = mysqli_fetch_assoc($total_amount_query);
                $total_affiliate_generated_amount = $row['total_amount'];
              }
              
              //Get total amount generated from direct registrations
              $query = mysqli_query($conn, "SELECT SUM(paid_amount) AS total_amount FROM membership_payment WHERE payment_status = 'Completed'");
              if (mysqli_num_rows($query) > 0) {
                $result = mysqli_fetch_assoc($query);
                $total_direct_registration_amount = $result['total_amount'];
              }

              //Total amount generated from weekly sales
              $total_generated_amount = $total_external_generated_amount + $total_affiliate_generated_amount + $total_direct_registration_amount;
              $total_generated_amount_in_naira = number_format(($total_generated_amount), 2, '.', ',');
              $total_generated_amount_in_usd = number_format(($total_generated_amount / 1000), 2, '.', ',');

              //Get payment bundle value { amount }
              $amount_left = $total_generated_amount / 1;

              if ($total_generated_amount > 0) {

                /*
                  * 
                  * PAY 
                  * ALL
                  * VENDORS 
                  * AT 
                  * THIS
                  * POINT
                  * 
                  */

                  $sql = $course_upload_check_query = mysqli_query($conn, "SELECT courseID, course_authors, vendor_percentage FROM uploaded_courses WHERE course_type = 'External' GROUP BY courseID");
                  if (mysqli_num_rows($sql) > 0) {
                    while($result = mysqli_fetch_assoc($sql)){
                      $courseID = $result['courseID'];
                      $vendor_commission = substr($result['vendor_percentage'], 0, -1);
                      $fullname = $result['course_authors'];
                      $role = 'Vendor';
                      $total_payout_amount = 0;
                      $total_payout_amount_in_usd = 0;
                      $balance = 0;
                      //Get email
                      $sql = mysqli_query($conn, "SELECT email, country, account_number, bank, bank_code, recipient_code FROM vendors WHERE fullname = '$fullname'");
                      if (mysqli_num_rows($sql) > 0) {
                        $row = mysqli_fetch_assoc($sql);
                        $email = $row['email'];
                        $country = $row['country'];
                        //Get wallet balance
                        $wallet = mysqli_query($conn, "SELECT wallet_amount FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Vendor'");
                        if (mysqli_num_rows($wallet) > 0) {
                          $wallet_result = mysqli_fetch_assoc($wallet);
                          $balance = $wallet_result['wallet_amount'];
                        }
                        $naira_balance = '₦' . number_format(($balance), 2, '.', ',');
                        $usd_balance = '$' . number_format(($balance / 1000), 2, '.', ',');
                        //Get total sales commission generated from owning course
                        $total_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_amount FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                        if(mysqli_num_rows($total_amount_query) > 0){ 
                          $details = mysqli_fetch_assoc($total_amount_query);
                          $total_sales_commission = $details['total_amount'];
                          //Get total respective commissions
                          $unique_vendor_commission = ($vendor_commission / 100) * ($total_sales_commission);
                          if ($unique_vendor_commission > 0) {
                            $total_payout_amount = number_format(($unique_vendor_commission), 2, '.', ',');
                            $total_payout_amount_in_usd = number_format(($unique_vendor_commission / 1000), 2, '.', ',');
                            //Amount left after all vendors are paid
                            $amount_left = $amount_left - $unique_vendor_commission;
                            //echo 'Amount left after all vendors are paid is <b>' . $amount_left . '</b><br>';
                            echo "
                              <tr class='rows'>
                                <td class='fullname'>$fullname</td>
                                <td class='country'>$country</td>
                                <td class='role'>$role</td>
                                <td class='amount-row'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                                <td class='balance'>$naira_balance / $usd_balance</td>
                                <td class='end'>
                                  <button class='btn btn-info btn-sm'>View</button>
                                </td>
                              </tr>
                            ";
                          }
                        }
                      }
                    }
                  }

                  /*
                   * 
                   * PAY 
                   * ALL
                   * AFFILIATES
                   * AT 
                   * THIS
                   * POINT
                   * 
                   */

                   //Get sales details from uploaded_course_sales table
                  $query = mysqli_query($conn, "SELECT * FROM affiliates GROUP BY affiliateID");
                  if(mysqli_num_rows($query) > 0 ){ 
                    while ($row = mysqli_fetch_assoc($query)) {
                      $affiliateID = $row['affiliateID'];
                      $fullname = $row['fullname'];
                      $email = $row['email'];
                      $country = $row['country'];
                      $role = 'Affiliate';
                      $total_uploaded_course_earnings = 0;
                      $total_affiliate_course_earnings = 0;
                      $total_payout_amount = 0;
                      $total_payout_amount_in_usd = 0;
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
                     
                      //Get commission from affiliate course sales
                      $main_course_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
                      if(mysqli_num_rows($main_course_query) > 0){ 
                        while($value = mysqli_fetch_assoc($main_course_query)){
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
                      }

                      $sum_amount = $total_uploaded_course_earnings + $total_affiliate_course_earnings;
                      if ($sum_amount > 0) {
                        //Get wallet balance
                        $wallet = mysqli_query($conn, "SELECT wallet_amount FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Affiliate'");
                        $wallet_result = mysqli_fetch_assoc($wallet);
                        $balance = $wallet_result['wallet_amount'];
                        $naira_balance = '₦' . number_format(($balance), 2, '.', ',');
                        $usd_balance = '$' . number_format(($balance / 1000), 2, '.', ',');
                        //Total payout amount
                        $total_payout_amount = number_format(($total_uploaded_course_earnings + $total_affiliate_course_earnings), 2, '.', ',');
                        $total_payout_amount_in_usd = number_format(((($total_uploaded_course_earnings + $total_affiliate_course_earnings) / (1000))), 2, '.', ',');
                        //Amount left after all affiliates are paid
                        $amount_left = $amount_left - ($total_uploaded_course_earnings + $total_affiliate_course_earnings);
                        //echo 'Amount left after all affiliates are paid is <b>' . $amount_left . '</b><br>';
                        echo "
                            <tr class='rows'>
                              <td class='fullname'>$fullname</td>
                              <td class='country'>$country</td>
                              <td class='role'>$role</td>
                              <td class='amount-row'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                              <td class='balance'>$naira_balance / $usd_balance</td>
                              <td class='end'>
                                <button class='btn btn-info btn-sm'>View</button>
                              </td>
                            </tr>
                          ";
                      }
                    }
                  }

                  /*
                  * 
                  * PAY 
                  * THE
                  * COMPANY
                  * AT 
                  * THIS
                  * POINT
                  * 
                  */

                  //Total payout amount
                  $percentage_company_savings = $amount_left * 0.40; //Currently 40%
                  $company_savings_amount = number_format(($percentage_company_savings), 2, '.', ',');
                  $company_savings_amount_in_usd = number_format(((($percentage_company_savings) / (1000))), 2, '.', ',');
                  //Amount left after all affiliates are paid
                  $amount_left = $amount_left - ($percentage_company_savings);
                  //echo 'Amount left after the company is paid is <b>' . $amount_left . '</b><br>';
                   echo "
                        <tr class='rows' style='margin-bottom: 50px !important;'>
                          <td class='fullname'>Company Savings</td>
                          <!--<td class='email'>admin@chromstack.com</td>-->
                          <td class='country'>Nigeria</td>
                          <td class='role'>Company</td>
                          <td class='amount-row'>&#x20A6 $company_savings_amount / $$company_savings_amount_in_usd</td>
                          <td class='balance'>Not available</td>
                        <td class='end'>
                          <button class='btn btn-info btn-sm'>View</button>
                        </td>
                      </tr>
                      ";

                  /*
                  * 
                  * PAY 
                  * THE
                  * ADMINS
                  * AT 
                  * THIS
                  * POINT
                  * 
                  *
                  */ 
                 
                  $admin_with_sales = [];

                  $query = mysqli_query($conn, "SELECT fullname, email, country, account_number, bank, bank_code, recipient_code FROM admins");
                  if(mysqli_num_rows($query) > 0){ 
                    while ($row = mysqli_fetch_assoc($query)) {
                      $fullname = $row['fullname'];
                      $email = $row['email'];
                      $country = $row['country'];
                      $role = 'Admin';
                      //Get wallet balance
                      $wallet = mysqli_query($conn, "SELECT wallet_amount FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Admin'");
                      $wallet_result = mysqli_fetch_assoc($wallet);
                      $balance = $wallet_result['wallet_amount'];
                      $naira_balance = '₦' . number_format(($balance), 2, '.', ',');
                      $usd_balance = '$' . number_format(($balance / 1000), 2, '.', ',');
                      $total_personal_course_commission = 0;
                      $total_payout_amount = 0;
                      $total_payout_amount_in_usd = 0;
                      //Check if admin's course was sold
                      $course_upload_check_query = mysqli_query($conn, "SELECT courseID, course_authors, vendor_percentage FROM uploaded_courses WHERE course_type = 'Admin' AND course_authors = '$fullname' GROUP BY courseID");
                      if (mysqli_num_rows($course_upload_check_query) > 0) {
                        while($result = mysqli_fetch_assoc($course_upload_check_query)){
                          $courseID = $result['courseID'];
                          $vendor_commission = substr($result['vendor_percentage'], 0, -1);
                          //Get total sales commission generated from owning course
                          $total_amount_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_amount FROM uploaded_course_sales WHERE courseID = '$courseID' AND sales_status = 'Completed'");
                          if(mysqli_num_rows($total_amount_query) > 0){ 
                            $details = mysqli_fetch_assoc($total_amount_query);
                            $total_sales_commission = $details['total_amount'];
                            //Get total respective commissions
                            $unique_vendor_commission = ($vendor_commission / 100) * ($total_sales_commission);
                            if ($unique_vendor_commission > 0) { 
                              //Add to array 
                              array_push($admin_with_sales, $fullname);
                              $total_payout_amount = number_format(($unique_vendor_commission), 2, '.', ',');
                              $total_payout_amount_in_usd = number_format(($unique_vendor_commission / 1000), 2, '.', ',');
                              //Amount left after all vendors are paid
                              $amount_left = $amount_left - $unique_vendor_commission;
                              //echo 'Amount left after all admin vendors are paid is <b>' . $amount_left . '</b><br>';
                              echo "
                                  <tr class='admin-rows'>
                                    <td class='fullname'>$fullname</td>
                                    <td class='country'>$country</td>
                                    <td class='role'>$role</td>
                                    <td class='amount-row'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                                    <td class='balance'>$naira_balance / $usd_balance</td>
                                    <td class='end'>
                                      <button class='btn btn-info btn-sm'>View</button>
                                    </td>
                                  </tr>
                                ";
                            }
                          }
                        }
                      } 
                      //List other admins without sales of their products
                      if(!in_array($fullname, $admin_with_sales)){
                        $total_payout_amount = number_format(($amount_left / 3), 2, '.', ',');
                        $total_payout_amount_in_usd = number_format((($amount_left / 1000) / 3), 2, '.', ',');
                        echo "
                          <tr class='admin-rows'>
                            <td class='fullname'>$fullname</td>
                            <td class='country'>$country</td>
                            <td class='role'>$role</td>
                            <td class='amount-row'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                            <td class='balance'>$naira_balance / $usd_balance</td>
                              <td class='end'>
                                <button class='btn btn-info btn-sm'>View</button>
                              </td>
                          </tr>
                        ";
                      }
                    }
                  }
                } 
                else {
                   echo "<script>
                    // Get the div element
                    const myDiv = document.getElementById('sales-list');

                    // Clear the existing content
                    myDiv.innerHTML = '';

                    //Set a style
                    myDiv.style.textAlign = 'center';

                    // Create a new text node
                    const newText = document.createTextNode('No sales yet!');

                    // Append the text node to the div
                    myDiv.appendChild(newText);
                    
                  </script>
                 ";
                }
                ?>
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
      <input type='text' id='total-amount-value' value='<?php echo $total_generated_amount_in_naira; ?>' hidden>
      <input type='text' id='amount-left-value' value='<?php echo $amount_left; ?>' hidden>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper --

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="#!">TecWelth</a>.</strong> All rights reserved.
  </footer>-->

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
 <!-- Step 4: Create a script to use the JSON data -->
<script src="<?php echo getCacheBustedUrl('../scripts/transaction.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>