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
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Courses</title>
   <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../dist/css/overlay.css'); ?>">
    <style>
      html, body{
          overflow: hidden !important;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini" id='<?php echo $_SESSION['adminID']; ?>'>
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
              <?php 
                $sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course");
                $count = mysqli_num_rows($sql);
                $query = mysqli_query($conn, "SELECT * FROM uploaded_courses");
                $result = mysqli_num_rows($query);
                $total = $count + $result;
                echo "<b>Courses ($total) </b>";
              ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Courses</li>
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
          <h3 class="card-title">Courses</h3>
          <div class="card-tools">
            <!--<button type="button" class="btn btn-tool" style='background: #199c37;color: white;'>
              <a href="../upload-main-course.php" style='text-decoration: none;color: white;'>
                  <i class="fas fa-upload" style="padding-right: 5px;"></i> Main</a>
            </button>-->
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;'>
              <a href="../upload-admin-course.php" style='text-decoration: none;color: white;'>
                  <i class="fas fa-upload" style="padding-right: 5px;"></i> Personal</a>
            </button>
            <button type="button" class="btn btn-tool" style='background: #199c37;color: white;'>
              <a href="../upload-vendor-course.php" style='text-decoration: none;color: white;'>
                  <i class="fas fa-upload" style="padding-right: 5px;"></i> Others</a>
            </button>
             <!--<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
           <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>--->
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
                      <th>Admin</th>
                      <th>Affiliate</th>
                      <th>Vendor</th>
                      <th>Preview</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody id='lists'>
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
  <input type="text" id="courseType" value="" hidden>
</div>

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
<script src="<?php echo getCacheBustedUrl('../scripts/main-course.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/courses.js'); ?>" type='module'></script>
</body>
</html>
