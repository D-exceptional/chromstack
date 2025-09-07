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
  
  $total_withdrawal_amount = 0;
  
  //Get total amount generated from direct registrations
  $query = mysqli_query($conn, "SELECT SUM(withdrawal_amount) AS total_amount FROM withdrawals WHERE withdrawal_status = 'Pending'");
  if (mysqli_num_rows($query) > 0) {
    $result = mysqli_fetch_assoc($query);
    $total_withdrawal_amount = $result['total_amount'];
  }
  // Total 
  $total_generated_amount_in_naira = number_format(($total_withdrawal_amount * 1000), 2, '.', ',');

?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Payouts</title>
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
            <h1><b>Payouts</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Payouts</li>
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
          <h3 class="card-title">Payouts</h3>

          <div class="card-tools">
            <input type='text' id='total-amount-value' value='<?php echo $total_generated_amount_in_naira; ?>' hidden>
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;margin-left: 5px;' id='pay-all'>Pay All</button>
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;margin-left: 5px;' id='backup'>Back Up</button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th>Fullname</th>
                      <th>Email</th>
                      <th>Recipient</th>
                      <th>Country</th>
                      <th>Role</th>
                      <th>Amount</th>
                      <th>Currency</th>
                      <th>Account</th>
                      <th>Bank</th>
                      <th>Code</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Narration</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody id='transfer-lists'>
                <?php 
                  $admin_emails = ['izuchukwuokuzu@gmail.com', 'chukwuebukaokeke09@gmail.com', 'mrwisdom8086@gmail.com', 'mmasichukwudominic@gmail.com'];
                  //Get sales details from uploaded_course_sales table
                  $query = mysqli_query($conn, "SELECT * FROM withdrawals");
                  if(mysqli_num_rows($query) > 0 ){ 
                    while ($row = mysqli_fetch_assoc($query)) {
                      $withdrawal_email = $row['withdrawal_email'];
                      $withdrawal_amount = number_format((intval($row['withdrawal_amount']) * 1000), 2, '.', ',');
                      $withdrawal_bank = $row['withdrawal_bank'];
                      $withdrawal_date = $row['withdrawal_date'];
                      $withdrawal_status = $row['withdrawal_status'];
                      $withdrawal_narration = $row['withdrawal_narration'];
                      $dollar_amount = '$' . $row['withdrawal_amount'];
                      //Get fullanme
                      if(in_array($withdrawal_email, $admin_emails)){
                        $sql = mysqli_query($conn, "SELECT * FROM admins WHERE email = '$withdrawal_email'");
                        $result = mysqli_fetch_assoc($sql);
                        $fullname = $result['fullname'];
                        $recipient_code = $result['recipient_code'];
                        $country = $result['country'];
                        $account_number = $result['account_number'];
                        $bank_code = $result['bank_code'];
                        $role = 'Admin';
                      }
                      else{
                        $sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE email = '$withdrawal_email'");
                        $result = mysqli_fetch_assoc($sql);
                        $fullname = $result['fullname'];
                        $recipient_code = $result['recipient_code'];
                        $country = $result['country'];
                        $account_number = $result['account_number'];
                        $bank_code = $result['bank_code'];
                        $role = 'Affiliate'; 
                      }
                      $status_button = "";
                      //Check
                      if($withdrawal_status === "Pending"){
                        $status_button = "<button class='btn btn-danger btn-sm'>Pending</button>";
                      }
                      else{
                        $status_button = "<button class='btn btn-success btn-sm'>Completed</button>";
                      }
                      
                     //Display
                      echo "
                          <tr class='rows'>
                            <td class='fullname'>$fullname</td>
                            <td class='email'>$withdrawal_email</td>
                            <td class='recipient'>$recipient_code</td>
                            <td class='country'>$country</td>
                            <td class='role'>$role</td>
                            <td class='amount-row'>&#x20A6 $withdrawal_amount</td>
                            <td class='currency'>Null</td>
                            <td class='account'>$account_number</td>
                            <td class='bank'>$withdrawal_bank</td>
                            <td class='code'>$bank_code</td>
                            <td class='time'>$withdrawal_date</td>
                            <td class='status'> $status_button</td>
                            <td class='narration'>$withdrawal_narration</td>
                            <td class='action'><button class='btn btn-info btn-sm'>Pay</button></td>
                          </tr>
                        ";
                    }
                  }
                  /*else{
                    echo "No withdrawals available";
                  }*/
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
  <!-- /.content-wrapper --

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="#!">chromstack</a>.</strong> All rights reserved.
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
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/withdrawal.js'); ?>" type="module"></script>
</body>
</html>
