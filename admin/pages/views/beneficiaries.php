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
  <title>Admin | Beneficiaries</title>
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
            <h1>
              <b>Beneficiaries</b>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Beneficiaries</li>
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
          <h3 class="card-title">Beneficiaries</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;margin-left: 5px;' id='refresh'>Refresh</button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0"  style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Account Number</th>
                    <th>Bank</th>
                    <th>Currency</th>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='beneficiary-list'>

              <?php

                //List beneficiaries on Chromstack's Paystack Integration
                $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.paystack.co/transferrecipient",
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
                                const myDiv = document.getElementById('beneficiary-list');

                                // Clear the existing content
                                myDiv.innerHTML = '';

                                //Set a style
                                myDiv.style.textAlign = 'center';

                                // Create a new text node
                                const newText = document.createTextNode('No beneficiary added yet!');

                                // Append the text node to the div
                                myDiv.appendChild(newText);
                                
                              </script>
                             ";
                  //exit();
                }
                else {
                        $result = json_decode($response, true);
                        $result_status = $result['status'];
                        $result_message = $result['message'];
                        $result_data = $result['data'];
                        //Loop through each object in the array
                        foreach($result_data as $key => $value){
                          $id = $value['id'];
                          $type = $value['type'];
                          $fullname = $value['name'];
                          $details = $value['details'];
                          $account_number = $details['account_number'];   
                          $bank = $details['bank_name']; 
                          $currency = $value['currency'];  
                          $recipient_code = $value['recipient_code'];

                          echo "
                                <tr class='rows' id='$id'>
                                    <td class='name'>$fullname</td>
                                    <td class='account'>$account_number</td> 
                                    <td class='bank'>$bank</td>
                                    <td class='currency'>$currency</td>
                                    <td class='type'>$type</td>
                                    <td class='code'>$recipient_code</td>
                                    <td class='action'>
                                        <button class='btn btn-info btn-sm' style='margin-bottom: 10px;'>Edit</button>
                                    </td>
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
</body>
</html>