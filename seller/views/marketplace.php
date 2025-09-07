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
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Vendor | Marketplace</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../admin/dist/css/overlay.css'); ?>">
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
            <h1><b>Marketplace</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Marketplace</li>
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
          <h3 class="card-title">Marketplace</h3>
          <div class="card-tools">
           <!--<button type="button" class="btn btn-tool" style='background: #199c37;color: white;'>
              <a href="../upload-course.php" style='text-decoration: none;color: white;'><i class="fas fa-upload" style="padding-right: 5px;"></i> Upload</a>
            </button>-->
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <input type="text" name="fullname" id="fullname" value="<?php echo $fullname; ?>" hidden>
            <input type="text" name="email" id="email" value="<?php echo $email; ?>" hidden>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th>Title</th>
                      <th>Cover Page</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Authors</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Affiliate</th>
                      <th>Vendor</th>
                      <th>Preview</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody></tbody>
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

<div id='full-details-overlay'>
  <iframe src="" frameborder="0"></iframe>
  <div id='close-view'>X</div>
</div>

<div id='description-overlay'>
  <div id='close-decsription-view'>X</div>
  <div id='description-text'></div>
  <div id='button-div'>
    <button>Edit</button>
  </div>
  <input type="text" id="courseID" value="" hidden>
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
<script src="<?php echo getCacheBustedUrl('../scripts/courses.js'); ?>" type='module'></script>
</body>
</html>
