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

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Approvals</title>
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
            <h1><b>Approvals</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Approvals</li>
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
          <h3 class="card-title">Approvals</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>TrackingID</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 

                  //Display all affiliate course payments for the week
                  $sql = mysqli_query($conn, "SELECT * FROM affiliate_course_sales ORDER BY salesID DESC");
                  if (mysqli_num_rows($sql) > 0) {
                    while($result = mysqli_fetch_assoc($sql)){
                      $salesID = $result['salesID'];
                      $email = $result['sales_email'];
                      $amount = '$' .( $result['sales_amount'] / 1000);
                      $date = $result['sales_date'];
                      $type = 'Affiliate Membership';
                      $tracking_id = $result['trackingID'];
                      $reference = $result['sales_txref'];
                      $status = $result['sales_status'];
                      $status_button = '';
                      $action_button = '';
                      //Filter buttons
                      if ($status == 'Pending') {
                          $status_button = "<button class='btn btn-danger btn-sm'>Pending</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>Update</button>";
                      }
                      else {
                          $status_button = "<button class='btn btn-success btn-sm'>Completed</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>View</button>";
                      }
                      //Get fullname
                      $name_query = mysqli_query($conn, "SELECT fullname FROM affiliates WHERE email = '$email'");
                      $name_result = mysqli_fetch_assoc($name_query);
                      $fullname = $name_result['fullname'];
                     
                      echo "
                              <tr class='rows' id='$salesID'>
                                <td class='fullname'>$fullname</td>
                                <td class='email'>$email</td>
                                <td class='amount'>$amount</td>
                                <td class='date'>$date</td>
                                <td class='type'>$type</td>
                                <td class='track'>$tracking_id</td>
                                 <td class='reference'>$reference</td>
                                <td class='status'>$status_button</td>
                                <td class='action'>$action_button</td>
                              </tr>
                            ";
                    }
                  }

                  //Display all direct affilaite payments for the week
                  $sql = mysqli_query($conn, "SELECT * FROM membership_payment ORDER BY paymentID DESC");
                  if (mysqli_num_rows($sql) > 0) {
                    while($result = mysqli_fetch_assoc($sql)){
                      $paymentID = $result['paymentID'];
                      $email = $result['payment_email'];
                      $amount = '$' .( $result['paid_amount'] / 1000);
                      $date = $result['payment_date'];
                      $type = 'Regular Membership';
                      $tracking_id = 'Null';
                      $reference = $result['payment_ref'];
                      $status = $result['payment_status'];
                      $status_button = '';
                      $action_button = '';
                      //Filter buttons
                      if ($status === 'Pending') {
                          $status_button = "<button class='btn btn-danger btn-sm'>Pending</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>Update</button>";
                      }
                      else {
                          $status_button = "<button class='btn btn-success btn-sm'>Completed</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>View</button>";
                      }
                      //Get fullname
                      $name_query = mysqli_query($conn, "SELECT fullname FROM affiliates WHERE email = '$email'");
                      $name_result = mysqli_fetch_assoc($name_query);
                      $fullname = $name_result['fullname'];
                     
                      echo "
                              <tr class='rows' id='$paymentID'>
                                <td class='fullname'>$fullname</td>
                                <td class='email'>$email</td>
                                <td class='amount'>$amount</td>
                                <td class='date'>$date</td>
                                <td class='type'>$type</td>
                                <td class='track'>$tracking_id</td>
                                 <td class='reference'>$reference</td>
                                <td class='status'>$status_button</td>
                                <td class='action'>$action_button</td>
                              </tr>
                            ";
                    }
                  }

                  //Display all direct affilaite payments for the week
                  $sql = mysqli_query($conn, "SELECT * FROM uploaded_course_sales ORDER BY salesID DESC");
                  if (mysqli_num_rows($sql) > 0) {
                    while($result = mysqli_fetch_assoc($sql)){
                      $salesID = $result['salesID'];
                      $email = $result['sales_email'];
                      $amount = '$' .( $result['sales_amount'] / 1000);
                      $date = $result['sales_date'];
                      $buyer = $result['buyer_type'];
                      $type = 'Course Purchase';
                      $tracking_id = $result['trackingID'];
                      $reference = $result['sales_txref'];
                      $status = $result['sales_status'];
                      $status_button = '';
                      $action_button = '';
                      $fullname = '';
                      //Filter buttons
                      if ($status === 'Pending') {
                          $status_button = "<button class='btn btn-danger btn-sm'>Pending</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>Update</button>";
                      }
                      else {
                          $status_button = "<button class='btn btn-success btn-sm'>Completed</button>";
                          $action_button = "<button class='btn btn-info btn-sm'>View</button>";
                      }

                      switch ($buyer) {
                        case 'Affiliate':
                         //Get fullname
                          $name_query = mysqli_query($conn, "SELECT fullname FROM affiliates WHERE email = '$email'");
                          $name_result = mysqli_fetch_assoc($name_query);
                          $fullname = $name_result['fullname'];
                        break;
                        case 'User':
                          //Get fullname
                          $name_query = mysqli_query($conn, "SELECT fullname FROM users WHERE email = '$email'");
                          $name_result = mysqli_fetch_assoc($name_query);
                          $fullname = $name_result['fullname'];
                        break;
                        case 'Vendor':
                          //Get fullname
                          $name_query = mysqli_query($conn, "SELECT fullname FROM vendors WHERE email = '$email'");
                          $name_result = mysqli_fetch_assoc($name_query);
                          $fullname = $name_result['fullname'];
                        break;
                      }
                     
                      echo "
                              <tr class='rows' id='$salesID'>
                                <td class='fullname'>$fullname</td>
                                <td class='email'>$email</td>
                                <td class='amount'>$amount</td>
                                <td class='date'>$date</td>
                                <td class='type'>$type</td>
                                <td class='track'>$tracking_id</td>
                                 <td class='reference'>$reference</td>
                                <td class='status'>$status_button</td>
                                <td class='action'>$action_button</td>
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
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/actions.js'); ?>" type="module"></script>
</body>
</html>