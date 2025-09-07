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
  <title>Admin | Payout History</title>
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
            <h1><b>Payout History</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Payout History</li>
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
          <h3 class="card-title">Transaction History</h3>

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
                      <th>Fullname</th>
                      <th>Account</th>
                      <th>Bank</th>
                      <th>Amount</th>
                      <th>Currency</th>
                      <th>Status</th>
                      <th>Recipient Code</th>
                      <th>Transfer Code</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody id='transfer-lists'>

                <?php 

                  //List beneficiaries on Chromstack's Paystack Integration
                  $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transfer",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                      "Authorization: Bearer $secret_key",
                      "Cache-Control: no-cache",
                    ),
                  ));
                  
                  $response = curl_exec($curl);
                  $err = curl_error($curl);
                  curl_close($curl);
                
                  if ($err) {
                    //echo "cURL Error #:" . $err;
                    echo "<script>
                                  // Get the div element
                                  const myDiv = document.getElementById('transfer-lists');

                                  // Clear the existing content
                                  myDiv.innerHTML = '';

                                  //Set a style
                                  myDiv.style.textAlign = 'center';

                                  // Create a new text node
                                  const newText = document.createTextNode('Could not fetch transfers !');

                                  // Append the text node to the div
                                  myDiv.appendChild(newText);
                                  
                                </script>
                              ";
                    //exit();
                  }
                  else {
                    $status_button = '';
                    $result = json_decode($response, true);
                    $result_data = $result['data'];
                    if (count($result_data) === 0 ) {
                      //echo "cURL Error #:" . $err;
                      echo "<script>
                                    // Get the div element
                                    const myDiv = document.getElementById('transfer-lists');

                                    // Clear the existing content
                                    myDiv.innerHTML = '';

                                    //Set a style
                                    myDiv.style.textAlign = 'center';

                                    // Create a new text node
                                    const newText = document.createTextNode('No transfers yet!');

                                    // Append the text node to the div
                                    myDiv.appendChild(newText);
                                    
                                  </script>
                                ";
                      //exit();
                    } else {
                     //Loop through each object in the array
                      foreach($result_data as $key => $value){
                        $id = $value['id'];
                        $recipient = $value['recipient'];
                        $fullname = $recipient['name'];
                        $account_number = $recipient['details']['account_number']; 
                        $bank = $recipient['details']['bank_name']; 
                        $amount = number_format(($value['amount'] / 100), 2, '.', ',');
                        $currency = $recipient['currency'];  
                        $status = $value['status'];
                        $display_value = ucfirst($status);
                        $recipient_code = $recipient['recipient_code'];
                        $transfer_code = $value['transfer_code'];

                        if ($status === 'success' || $status === 'otp') {
                          $status_button = "<button class='btn btn-success btn-sm' style='margin-bottom: 10px;'>$display_value</button>";
                        } else {
                          $status_button = "<button class='btn btn-danger btn-sm' style='margin-bottom: 10px;'>$display_value</button>";
                        }

                        echo "
                              <tr id='$id'>
                                  <td>$fullname</td>
                                  <td>$account_number</td> 
                                  <td>$bank</td>
                                  <td>$amount</td>
                                  <td>$currency</td>
                                  <td class='status'>$status_button</td>
                                  <td>$recipient_code</td>
                                  <td class='code'>$transfer_code</td>
                                  <td class='action'>
                                      <button class='btn btn-info btn-sm' style='margin-bottom: 10px;'>Verify</button>
                                  </td>
                              </tr>
                            ";
                        }
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
<script src="<?php echo getCacheBustedUrl('../scripts/transaction-status-actions.js'); ?>" type="module"></script>
</body>
</html>
