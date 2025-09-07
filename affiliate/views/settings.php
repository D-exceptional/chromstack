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

  $bank_array = array();
  
  $email = '';
  $fullname = '';
  $profile = '';

  $sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliateID = '$affiliateID'");
  if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $email = $row['email'];
    $profile = $row['affiliate_profile'];
    $fullname = $row['fullname'];
    $country = strtolower($row['country']);
    $account_number = $row['account_number'];
    $bank = $row['bank'];
    $bank_code = $row['bank_code'];
    $recipient_code = $row['recipient_code'];
  }

  global $country;

  function getCountryCurrency($countryName) {
    $countryName = urlencode($countryName); // URL encode the country name
    $url = "https://restcountries.com/v3.1/name/{$countryName}?fullText=true"; // Restcountries API endpoint

    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // For HTTPS
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // For HTTPS

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        curl_close($ch);
        return null;
    }
    
    // Close cURL session
    curl_close($ch);
    
    // Decode JSON response
    $data = json_decode($response, true);

    // Check if we got a result
    if (isset($data[0]['currencies'])) {
        // Extract currency information
        $currencies = $data[0]['currencies'];
        $currencyCode = array_key_first($currencies);
        $currencyName = $currencies[$currencyCode]['name'];
        return $currencyCode;
    } else {
        return null;
    }
  }

// Example usage
$currency = getCountryCurrency($country);

?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Affiliate | Settings</title>
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
<div class="wrapper">
  <input type="number" name="affiliateID" id="affiliateID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
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
            <h1><b>Settings</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Affiliate Settings</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <div class="container-fluid">
        <div class="row" style="height: 550px;">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                <?php
                    if ($profile !== 'null') {
                        echo "<img src='../../uploads/$profile' class='profile-user-img img-fluid img-circle' style='width: 100px !important;height: 100px !important;' alt='Admin Image'>";
                    }
                    else {
                    echo "<img src='../../assets/img/user.png' class='profile-user-img img-fluid img-circle' style='width: 100px !important;height: 100px !important;' alt='Admin Image'>";
                    }
                ?>
                </div>

                <h3 class="profile-username text-center">
                <?php
                    if ($fullname !== 'null') {
                        echo "<a href='#' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;color: #181d38 !important;'>$fullname</a>";
                      }
                      else {
                        echo "<a href='#' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;color: #181d38 !important;'>Admin</a>";
                      }
                  ?>
                </h3>
                <p class="text-muted text-center">Affiliate</p>
                <a href="#" class="btn btn-primary btn-block" id='update-profile' style='background: transparent !important;color: #181d38 !important;border: 1px solid #181d38 !important;'><b>Update Profile</b></a>
                <input type="file" class="form-control" id="profile-image" hidden>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab" style='background: transparent !important;color: #181d38 !important;border: 1px solid #181d38 !important;margin-right: 5px;'><b>Details</b></a></li>
                  <li class="nav-item"><a class="nav-link" href="#bank" data-toggle="tab" style='background: transparent !important;color: #181d38 !important;border: 1px solid #181d38 !important;margin-right: 5px;'><b>Earnings</b></a></li>
                  <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab" style='background: transparent !important;color: #181d38 !important;border: 1px solid #181d38 !important;'><b>Security</b></a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="settings" style="display: block;">
                    <form class="form-horizontal" id='details-form'>
                    <input type="number" name="affiliateID" id="affiliateID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
                      <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="name" value='<?php echo $row['fullname']; ?>' placeholder="Name" disabled>
                          <input type="text" class="form-control" name="name" id='accountHolder' value='<?php echo $row['fullname']; ?>' hidden>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" name="email" value='<?php echo $row['email']; ?>' placeholder="Email" disabled>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="contact" class="col-sm-2 col-form-label">Contact</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="contact" value='<?php echo $row['contact']; ?>' placeholder="Contact" disabled>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="instagram" class="col-sm-2 col-form-label">Instagram Link</label>
                        <div class="col-sm-10">
                           <input type="text" class="form-control" name="instagram" value='<?php echo $row['instagram_link']; ?>' placeholder="Instagram">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="tiktok" class="col-sm-2 col-form-label">Tiktok Link</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="tiktok" value='<?php echo $row['tiktok_link']; ?>' placeholder="Tiktok">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="twitter" class="col-sm-2 col-form-label">Twitter Link</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="twitter" value='<?php echo $row['twitter_link']; ?>' placeholder="Twitter">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="facebook" class="col-sm-2 col-form-label">Facebook Link</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="facebook" value='<?php echo $row['facebook_link']; ?>' placeholder="Facebook">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="bank">
                    <form class="form-horizontal" id='bank-form'>
                         <input type="number" id="uniqueID" name="adminID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
                         <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Currency</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='Currency' name="currency" value="<?php echo $currency; ?>" placeholder="Currency" disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Bank Name</label>
                          <div class="col-sm-10">
                           <?php 
                                echo '<select class="form-control" name="bank" id="bankName">';

                                $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://api.paystack.co/bank?country=$country",
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
                                    echo '<option>Error loading banks</option>';
                                } else {
                                    global $bank;
                                    //Append current bank
                                    if ($bank !== 'null') {
                                      echo "<option value='$bank'>$bank</option>";
                                    }
                                    $result = json_decode($response, true);
                                    $result_data = $result['data'];
                                    // Loop through each object in the array
                                    foreach ($result_data as $value) {
                                        $bank_name = $value['name']; 
                                        $bank_code = $value['code']; 
                                        $bank_info = array(
                                          'name' => $bank_name,
                                          'code' => $bank_code
                                        );
                                        //Update array
                                        array_push($bank_array, $bank_info);
                                        //Display banks
                                        echo "<option value='$bank_name'>$bank_name</option>";
                                    }
                                }

                                echo '</select>';

                                // Convert PHP array to JSON
                                $json_data = json_encode($bank_array);
                                // Echo JSON data
                                echo '<script>';
                                echo "const jsonData = $json_data;";
                                echo '</script>';
                            ?>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Account Number</label>
                          <div class="col-sm-10">
                            <?php 
                               echo "<input type='number' class='form-control' name='account_number' value='$account_number' id='accountNumber' placeholder='Account Number'>";
                            ?>
                          </div>
                        </div>
                        <div class="form-group row" id='accountNameDiv' style='display: none;'>
                          <label for="facebook" class="col-sm-2 col-form-label">Account Name</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="account_name" id="accountName" placeholder="Account Name" disabled>
                          </div>
                        </div>
                        <div class="form-group row" style='display: none;'>
                          <label for="facebook" class="col-sm-2 col-form-label">Unique Code</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="unique_code" id="uniqueCode" value="<?php echo $row['recipient_code']; ?>" placeholder="Unique Code" disabled>
                          </div>
                        </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10" id="bank-button-action">
                          <?php 
                             if($bank === 'null'){
                              echo "<button type='submit' id='add-details' class='btn btn-success'>Add</button>";
                             }
                             else{
                               echo "<button type='submit' id='add-details' class='btn btn-success'>Update</button>";
                             }
                          ?>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- Security section -->
                  <div class="tab-pane" id="security">
                    <form class="form-horizontal" id='security-form'>
                    <input type="number" name="affiliateID" id="affiliateID" value="<?php echo $_SESSION['affiliateID']; ?>" hidden>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Current Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='currentPassword' name="current_password" placeholder="Current password">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">New Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='changePassword' name="new_password" placeholder="New password">
                          </div>
                        </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper --
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="#">chromstack</a>.</strong> All rights reserved.
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
<!-- Your JavaScript code -->
<script>
    // Access jsonData variable
    //console.log(jsonData);  // This will log the array of arrays to the console
</script>
<script src="../scripts/events.js" type='module'></script>
<script src="../../assets/scripts/tawkto.js"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/profile.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/bank-details.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/security.js'); ?>" type='module'></script>
</body>
</html>
