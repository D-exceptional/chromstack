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
  $bank = '';

  $sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliateID = '$affiliateID'");
  if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $email = $row['email'];
    $profile = $row['affiliate_profile'];
    $fullname = $row['fullname'];
    $bank = $row['bank'];
  }

  //Earnings variables
  $total_savings = 0;

  //Get savings if any
  $savings_query = mysqli_query($conn, "SELECT SUM(wallet_amount) AS total_savings FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Affiliate'");
  if(mysqli_num_rows($savings_query) > 0){ 
    $value = mysqli_fetch_assoc($savings_query);
    $total_savings = $value['total_savings'];
  }

  // Total amount
  $total_payout_amount = $total_savings;
  $total_payout_amount_in_usd = ($total_savings / 1000);


?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affiliate | Weekly Withdrawal</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../admin/dist/css/overlay.css'); ?>">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/overlay.css'); ?>">
   <style>
       html, body{
          overflow: hidden;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini" id='<?php echo $_SESSION['affiliateID']; ?>'>
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
            <h1 style='color: #212529'><b>Weekly Withdrawal</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Weekly Withdrawal</li>
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
          <h3 class="card-title">Weekly Withdrawal</h3>
          <div class="card-tools">
            <?php
              // Get the current day of the week
              $currentDay = date('l'); // 'l' (lowercase 'L') returns the full textual representation of the day
              $currentHour = date('H');

              // Check if the current day is Thursday
              if ($currentDay === 'Thursday' && $currentHour < 23) { //Withdrawal should be done before 11pm
                //Check if this person has placed withdrawal
                $check = mysqli_query($conn, "SELECT * FROM withdrawals WHERE withdrawal_email = '$email'");
                if(mysqli_num_rows($check) === 0){
                  echo "<button class='btn btn-danger btn-sm' id='withdraw'>
                          Place Withdrawal
                        </button> 
                      ";
                }
              } 
            ?>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th>SN</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Bank</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                <?php 
                  $history_query = mysqli_query($conn, "SELECT * FROM withdrawals WHERE withdrawal_email = '$email' ORDER BY withdrawalID DESC");
                  if(mysqli_num_rows($history_query) > 0){
                    while($value = mysqli_fetch_assoc($history_query)){
                      $button = '';
                      $id = $value['withdrawalID'];
                      $amount = $value['withdrawal_amount'];
                      $bank = $value['withdrawal_bank'];
                      $date = $value['withdrawal_date'];
                      $status = $value['withdrawal_status'];
                      $narration = $value['withdrawal_narration'];
                      //Format amount
                      $payout_amount = '$' . number_format(($amount), 2, '.', ',');
                      //Format status
                      if ($status === "Completed") {
                        $button = "<button class='btn btn-success btn-sm'>
                                    Completed
                                   </button> 
                                  ";
                      } else {
                         $button = "<button class='btn btn-danger btn-sm'>
                                    Pending
                                   </button> 
                                  ";
                      }
                      //Format action button
                      $action = "<button class='btn btn-info btn-sm'>
                                    View
                                 </button> 
                                ";

                      
                      //Append to tbody
                      echo "
                          <tr id='$id'>
                              <td>#</td>
                              <td>$date</td>
                              <td>$payout_amount</td>
                              <td>$bank</td>
                              <td>$narration</td>
                              <td>$button</td>
                              <td>$action</td>
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

<div id='withdraw-overlay'>
  <form class="form-horizontal" id='withdraw-form'>
      <center>
          <h3>Withdraw Funds</h3>
      </center>
    <div class="form-group">
        <div class='form-text'>
            <label for="facebook" class="col-sm-2 col-form-label">Available Amount ($)</label>
        </div>
      <div class="col-sm-10">
        <input type="number" class="form-control" id='availableAmount' name="available_amount" value="<?php echo $total_payout_amount_in_usd; ?>" disabled>
        <input type="text" id='session_Name' value="<?php echo $fullname; ?>" hidden>
        <input type="text" id='session_Email' value="<?php echo $email; ?>" hidden>
      </div>
    </div>
    <div class="form-group">
        <div class='form-text'>
            <label for="facebook" class="col-sm-2 col-form-label">Withdrawal Amount ($)</label>
        </div>
      <div class="col-sm-10">
        <input type="number" step="any" class="form-control" id='withdrawalAmount' name="withdrawal_amount" placeholder="Amount to withdraw">
      </div>
    </div>
    <div class="form-group">
      <div class="offset-sm-2 col-sm-10">
          <center>
            <?php
                if ($total_payout_amount === 0) {
                   echo "<button type='button' class='btn btn-danger' disabled>Insufficient Balance</button>";
                } else {
                    echo "<button type='submit' class='btn btn-success' id='request-withdrawal'>Place Withdrawal</button>";
                }
            ?>
        </center>
      </div>
    </div>
    <center>
        <span id='info-span'></span>
    </center>
  </form>
  <div id='close-view'>X</div>
  <p id='bankName' style='display: none;'><?php echo $bank; ?></p>
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
<!--<script src="../../assets/scripts/tawkto.js"></script>-->
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/withdrawals.js'); ?>" type='module'></script>
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
