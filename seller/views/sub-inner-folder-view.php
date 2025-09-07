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
  <title>Vendor | Course Preview</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <style>
  
    html,body{
        overflow: hidden;
    }
  
    .content-div{
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
      }

      p{
          text-align: left;
          white-space: normal;
          overflow-x: hidden;
      }
      
          /* Extra small devices (phones, 600px and down) */
    @media screen and (max-width: 600px) {
       .content-div
        {
            width: 50% !important;
        }
    }
    
    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media screen and (min-width: 600px) and (max-width: 992px) {
       .content-div
        {
            width: 50% !important;
        }
    }
    
    /* Medium devices (landscape tablets, 768px and up) */
    @media screen and (min-width: 768px) {
       .content-div
        {
            width: 50% !important;
        }
    }
    
    /* Large devices (laptops/desktops, 992px and up) */
    @media screen and (min-width: 992px) {
       .content-div
        {
            width: 20% !important;
        }
    }
    
    /* Extra large devices (large laptops and desktops, 1200px and up) 
    @media only screen and (min-width: 1200px) {
        .col-lg-3 .col-6 {
            width: 25% !important;
            max-width: 25% !important;
        }
    }*/
  </style>
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

<!-- Navbar -->

 <?php include '../navbar.php'; ?>

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
            <h1>Course Preview</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Course Preview</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-body p-0" style='display: flex;flex-direction: row;flex-wrap: wrap;padding: 10px !important;overflow-y: auto !important;box-sizing: border-box;height: auto;'>

        <?php 

        // Define a function to output files in a directory
          function viewDirectory($path){
            // Check directory exists or not
            if(is_dir($path)){
                // Search the files in this directory
                $files = glob($path ."/*");
                if(count($files) > 0){
                    // Loop through retuned array
                    foreach($files as $file){
                        if(is_file("$file")){
                          //Get file extension
                          $extension = pathinfo($file, PATHINFO_EXTENSION);
                          $filename = pathinfo($file, PATHINFO_BASENAME);
                          switch ($extension) {
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                              echo "
                                    <div class='content-div'>
                                      <img src='$file' style='width: 150px;height: 150px;'>
                                      <p>$filename</p>
                                    </div>
                                  ";
                            break;
                            case 'mp4':
                              echo "
                                    <div class='content-div'>
                                      <video src='$file' controls='true' style='width: 150px;height: 150px;'></video>
                                      <p>$filename</p>
                                    </div>
                                  ";
                            break;
                            case 'pdf':
                              echo "
                                    <div class='content-div'>
                                      <img src='../../assets/img/pdf-preview.jpg' style='width: 150px;height: 150px;'>
                                      <p>$filename</p>
                                    </div>
                                  ";
                            break;
                            case 'docx':
                              echo "
                                    <div class='content-div'>
                                      <img src='../../assets/img/word-preview.jpg' style='width: 150px;height: 150px;'>
                                      <p>$filename</p>
                                    </div>
                                  ";
                            break;
                          }
                        }
                        else if(is_dir("$file")){  
                          $filename = pathinfo($file, PATHINFO_BASENAME);
                           echo "
                              <div class='content-div'>
                                <img src='../../img/assets/folder.png' class='sub-inner-child-folder-image' style='width: 150px;height: 150px;'>
                                <p>$filename</p>
                              </div>
                            ";
                        }
                    }
                }
                else{
                    echo "No file found in the specified directory.";
                }
            }
            else {
                echo "The specified directory does not exist.";
            }
          }

          // Define the directory path

          $dir = mysqli_real_escape_string($conn, $_GET['filePath']);
          $parent = mysqli_real_escape_string($conn, $_GET['parentDirectory']);
          $fullPath = $parent.'/'.$dir;

          viewDirectory($fullPath);
           
        ?> 

        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper 

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
<script src="../../admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../admin/dist/js/demo.js"></script>
<script>

$('.sub-inner-child-folder-image').each(function (index, el) {
    $(el).on('click', function (e) {
        const filePath = $(el).parent().find('p').text();
        const parentFolder = '<?php echo $fullPath; ?>';
        window.location = `../views/sub-inner-child-folder-view.php?filePath=${filePath}&parentDirectory=${parentFolder}`;
    });
});

</script>
<script src="../../assets/scripts/tawkto.js"></script>
<script src="../../assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
