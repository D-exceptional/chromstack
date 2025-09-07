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

  $mailID = mysqli_real_escape_string($conn, $_GET['mailID']);
  $sql = mysqli_query($conn, "SELECT * FROM mailbox WHERE mailID = '$mailID'");
  if(mysqli_num_rows($sql) > 0){
    $result = mysqli_fetch_assoc($sql);
  }

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Vendor | Read Mail</title>
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
  </style>
</head>
<body class="hold-transition sidebar-mini">
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
            <h1><b>Read Mail</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Read Mail</li>
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
            <a href="mailbox.php" class="btn btn-primary btn-block mb-3">Back to Inbox</a>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Folders</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item active">
                    <a href="mailbox.php" class="nav-link">
                      <i class="fas fa-inbox"></i> Inbox
                      <span class="badge bg-primary float-right">
                        <?php 
                              $query = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$email'");
                              echo mysqli_num_rows($query);
                        ?>  
                      </span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./sent-mail.php" class="nav-link">
                      <i class="far fa-envelope"></i> Sent
                      <span class="badge bg-primary float-right">
                      <?php 
                          $sql = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_sender = '$fullname'");
                          echo mysqli_num_rows($sql);
                      ?>  
                    </span>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
          <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Read Mail</h3>

              <div class="card-tools">
                <a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-read-info">
                <h6>From: <b><?php echo $result['mail_sender'] ?></b>
                  <span class="mailbox-read-time float-right"><?php echo $result['mail_date'].'  '.$result['mail_time'] ?></span></h6>
              </div>
              <!-- /.mailbox-read-info -->
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <p><?php echo $result['mail_message'] ?></p>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-white">
              <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                  <?php 
                  
                         $sql = mysqli_query($conn, "SELECT mail_filename, mail_extension FROM mailbox WHERE mailID = '$mailID'");
                         
                        if(mysqli_num_rows($sql) > 0){

                          while($result = mysqli_fetch_assoc($sql)){

                                $filename = $result['mail_filename'];
                                $extension = $result['mail_extension'];

                                switch ($extension) {
                                  
                                  case 'pdf':
                                    echo " <li>
                                            <span class='mailbox-attachment-icon'><i class='far fa-file-pdf'></i></span>
                                            <div class='mailbox-attachment-info'>
                                              <a href='../../attachments/$filename' class='mailbox-attachment-name'><i class='fas fa-paperclip'></i> $filename</a>
                                                  <span class='mailbox-attachment-size clearfix mt-1'>
                                                    <span>1,245 KB</span>
                                                    <a href='../../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                                  </span>
                                            </div>
                                          </li>
                                        ";
                                  break;
                                  case 'docx':
                                    echo "<li>
                                            <span class='mailbox-attachment-icon'><i class='far fa-file-word'></i></span>
                                            <div class='mailbox-attachment-info'>
                                              <a href='../../attachments/$filename' class='mailbox-attachment-name'><i class='fas fa-paperclip'></i> $filename</a>
                                                  <span class='mailbox-attachment-size clearfix mt-1'>
                                                    <span>1,245 KB</span>
                                                    <a href='../../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                                  </span>
                                            </div>
                                          </li>
                                          ";
                                  break;
                                  case 'jpg':
                                  case 'jpeg':
                                  case 'png':
                                    echo " <li>
                                              <span class='mailbox-attachment-icon has-img'><img src='../../attachments/$filename' alt='Image'></span>
                                              <div class='mailbox-attachment-info'>
                                                <a href='#' class='mailbox-attachment-name'><i class='fas fa-camera'></i> $filename</a>
                                                    <span class='mailbox-attachment-size clearfix mt-1'>
                                                      <span>2.67 MB</span>
                                                      <a href='../../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                                    </span>
                                              </div>
                                            </li>
                                        ";
                                  break;
                                  case 'mp4':
                                    echo " <li>
                                              <span class='mailbox-attachment-icon has-img'><video src='../../attachments/$filename' controls='true' style='max-height: 133px;'></video></span>
                                              <div class='mailbox-attachment-info'>
                                                <a href='#' class='mailbox-attachment-name'><i class='fas fa-camera'></i> $filename</a>
                                                    <span class='mailbox-attachment-size clearfix mt-1'>
                                                      <span>2.67 MB</span>
                                                      <a href='../../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                                    </span>
                                              </div>
                                            </li>
                                        ";
                                  break;
                                }
                            }
                        }
                        else{
                          echo "<h3>No attachment available for this email</p>";
                        }

                        
                    ?>  
              </ul>
            </div>
            <!-- /.card-footer -->
            <div class="card-footer">
              <div class="float-right">
                <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button>
                <button type="button" class="btn btn-default"><i class="fas fa-share"></i> Forward</button>
              </div>
              <!--<button type="button" class="btn btn-default"><i class="far fa-trash-alt"></i> Delete</button>
              <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button>-->
            </div>
            <!-- /.card-footer -->
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
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">chromstack</a>.</strong> All rights reserved.
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
<script src="../scripts/events.js" type='module'></script>
<script src="../../assets/scripts/tawkto.js"></script>
<script src="../../assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
