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
  <title>Vendor | Orders</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/commission-overlay.css'); ?>">
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
            <h1><b>Orders</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Orders</li>
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
          <h3 class="card-title">Orders</h3>
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
                    <th>SN</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Tracking ID</th>
                    <th>Status</th>
                </tr>
              </thead>
              <tbody>

                <?php 

                    $button = '';

                    $sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE course_authors = '$fullname'");
                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)){
                          $courseID = $row['courseID'];
                          //Fetch all orders
                          $query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$courseID' AND course_type = 'External' ORDER BY purchase_date DESC");
                            if (mysqli_num_rows($query) > 0) {
                                while($result = mysqli_fetch_assoc($query)){
                                    $purchaseID = $result['purchaseID'];
                                    $email = $result['buyer_email'];
                                    $amount = $result['course_amount'];
                                    $date = $result['purchase_date'];
                                    $tracking_id = $result['trackingID'];
                                    $status = $result['purchase_status'];
                                    $formatted_amount = '$' . ($amount / 1000);

                                    if ($status == 'Pending') {
                                        $button = "<button class='btn btn-danger btn-sm'>$status</button>";
                                    }
                                    else {
                                        $button = "<button class='btn btn-success btn-sm'> $status</button>";
                                    }
                                    
                                    echo "
                                            <tr class='rows' id='$purchaseID'>
                                                <td>#</td>
                                                <td class='email'>$email</td>
                                                <td class='amount'>$formatted_amount</td>
                                                <td class='date'>$date</td>
                                                <td class='tracking'>$tracking_id</td>
                                                <td class='status'>$button</td>
                                            </tr>
                                            ";
                                }
                            }
                            else { 
                                    echo "<script>
                                            // Get the div element
                                            const myDiv = document.getElementByTagName('tbody');
            
                                            // Clear the existing content
                                            myDiv.innerHTML = '';
            
                                            //Set a style
                                            myDiv.style.textAlign = 'center';
            
                                            // Create a new text node
                                            const newText = document.createTextNode('No orders yet!');
            
                                            // Append the text node to the div
                                            myDiv.appendChild(newText);
                                            
                                          </script>
                                         ";
                                }
                        }
                    }
                    else{
                           echo "<script>
                                // Get the div element
                                const myDiv = document.getElementByTagName('tbody');

                                // Clear the existing content
                                myDiv.innerHTML = '';

                                //Set a style
                                myDiv.style.textAlign = 'center';

                                // Create a new text node
                                const newText = document.createTextNode('No course available');

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
