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
  <title>Admin | Compose Message</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../../assets/css/mail-overlay.css'); ?>">
    <style>
      html, body{
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
            <h1><b>Compose</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Compose</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style="overflow-y: auto !important;box-sizing: border-box;">
      <div class="container-fluid"  style="height: 550px;">
        <div class="row">
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
                    <a href="./mailbox.php" class="nav-link">
                      <i class="fas fa-inbox"></i> Inbox
                      <span class="badge bg-primary float-right">
                        <?php 
                            $sql = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$email' OR mail_receiver = 'Admin'");
                            echo mysqli_num_rows($sql);
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
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Compose New Message</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="form-group">
                  <!--<input class="form-control" placeholder="Select recipients" id='email'>-->
                  <div class="form-control" id='choose-recipient'>Choose recipient</div>
                </div>
                <div class="form-group">
                  <input class="form-control" placeholder="Subject:" id='subject'>
                </div>
                <div class="form-group">
                    <textarea id="compose-textarea" class="form-control" style="height: 300px"></textarea>
                </div>
                <div class="form-group">
                  <div class="btn btn-default btn-file">
                    <i class="fas fa-paperclip"></i> Attachment
                    <input type="file" id='attachment'>
                  </div>
                  <p class="help-block">Max. 32MB</p>
                </div>
              </div>
               <!--/.card-body -->
              <div class="card-footer" style="margin-bottom: 50px !important;">
                <div class="float-right">
                     <input class="form-control" id="admin-email" value="<?php echo $email; ?>" hidden>
                     <input class="form-control" id="admin-name" value="<?php echo $fullname; ?>" hidden>
                  <!--<button type="button" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Draft</button>-->
                  <button type="button" class="btn btn-primary" id='send-email'><i class="far fa-envelope"></i> Send</button>
                </div>
                <button type="button" class="btn btn-default" id='discard-email'><i class="fas fa-times"></i> Discard</button>
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
      <b>Version</b> 1.1.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#!">chromstack</a>.</strong> All rights reserved.
  </footer>-->
  
  <div class='mail-overlay'>
      <div class='main-content'>
          <div class='main-content-select'>
              <div id='view-admins'>Admins</div>
              <div id='view-affiliates'>Affiliates</div>
              <div id='view-vendors'>Vendors</div>
              <div class='main-content-close' id='close-modal'>X</div>
          </div>
          <div class='main-content-header'>
              <div class='main-content-header-text'>
                  Send to everyone
              </div>
              <div class='main-content-header-icon'>
                  <input type='checkbox' id='send-to-all'>
              </div>
          </div>
          <div class='main-content-view'>
              <div class='main-content-inner'>
                <div class='user-view' id='admin-section-view'>
                    <div class='user-view-header'>
                      <div class='user-view-header-text'>Admins Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-admin'>
                      </div>
                    </div>
                  <div class='user-scroll' id='admin-section'>
                    <?php 
                      $display_image = '';
                      $sql = mysqli_query($conn, "SELECT adminID, admin_profile, fullname, email FROM admins");
                      if(mysqli_num_rows($sql) > 0){
                          while($row = mysqli_fetch_assoc($sql)){
                            $id = $row['adminID'];
                            $image = $row['admin_profile'];
                            $name = $row['fullname'];
                            $admin_email = $row['email'];
                            /*Get or set image
                            if ($image !== 'null') {
                                $display_image = "../../../uploads/$image";
                            }
                            else {
                              $display_image = "../../../assets/img/user.png";
                            }*/
                            
                            $display_image = "../../../assets/img/user.png";

                            echo " <div class='user-card'>
                                      <div class='user-card-image'>
                                          <img src='$display_image' alt='admin-image'>
                                      </div>
                                      <div class='user-card-name'>$name</div>
                                      <div class='user-card-select'>
                                          <input type='checkbox' class='add-user' id='admin$id'>
                                          <p class='email-address'>$admin_email</p>
                                      </div>
                                  </div>
                                ";
                           
                          }
                          
                      }
                      else{ echo "<center>No admin found</center>"; }
                    ?>
                  </div>
                </div>
                <div class='user-view' id='affiliate-section-view'>
                  <div class='user-view-header'>
                      <div class='user-view-header-text'>Affiliates Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-affiliate'>
                      </div>
                    </div>
                  <div class='user-scroll' id='affiliate-section'>
                    <?php 
                      $display_image = '';
                      $sql = mysqli_query($conn, "SELECT affiliateID, affiliate_profile, fullname, email FROM affiliates WHERE affiliate_status = 'Active' ORDER BY affiliateID ASC");
                      if(mysqli_num_rows($sql) > 0){
                          while($row = mysqli_fetch_assoc($sql)){
                            $id = $row['affiliateID'];
                            $image = $row['affiliate_profile'];
                            $name = $row['fullname'];
                            $affiliate_email = $row['email'];
                            /*Get or set image
                            if ($image !== 'null') {
                                $display_image = "../../../uploads/$image";
                            }
                            else {
                              $display_image = "../../../assets/img/user.png";
                            }*/
                            
                            $display_image = "../../../assets/img/user.png";

                            echo " <div class='user-card'>
                                      <div class='user-card-image'>
                                          <img src='$display_image' alt='affiliate-image'>
                                      </div>
                                      <div class='user-card-name'>$name</div>
                                      <div class='user-card-select'>
                                          <input type='checkbox' class='add-user' id='affiliate$id'>
                                          <p class='email-address'>$affiliate_email</p>
                                      </div>
                                  </div>
                                ";
                           
                          }
                          
                      }
                      else{ echo "<center>No affiliate found</center>"; }
                    ?>
                  </div>
                </div>
                <div class='user-view' id='vendor-section-view'>
                  <div class='user-view-header'>
                      <div class='user-view-header-text'>Vendors Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-vendor'>
                      </div>
                    </div>
                  <div class='user-scroll' id='vendor-section'>
                    <?php 
                      $display_image = '';
                      $counter = 1;
                      $sql = mysqli_query($conn, "SELECT vendorID, vendor_profile, fullname, email FROM vendors ORDER BY vendorID ASC");
                      if(mysqli_num_rows($sql) > 0){
                          while($row = mysqli_fetch_assoc($sql)){
                            $id = $row['vendorID'];
                            $image = $row['vendor_profile'];
                            $name = $row['fullname'];
                            $vendor_email = $row['email'];
                            /*Get or set image
                            if ($image !== 'null') {
                                $display_image = "../../../uploads/$image";
                            }
                            else {
                              $display_image = "../../../assets/img/user.png";
                            }*/
                            
                            $display_image = "../../../assets/img/user.png";

                            echo " <div class='user-card'>
                                      <div class='user-card-image'>
                                          <img src='$display_image' alt='affiliate-image'>
                                      </div>
                                      <div class='user-card-name'>$name</div>
                                      <div class='user-card-select'>
                                          <input type='checkbox' class='add-user' id='vendor$id'>
                                          <p class='email-address'>$vendor_email</p>
                                      </div>
                                  </div>
                                ";
                           
                          }
                          
                      }
                      else{ echo "<center>No vendor found</center>"; }
                    ?>
                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>
  
  

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
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    //Add text editor
    $('#compose-textarea').summernote()
  })
</script>
<!-- Core Script -->
<script src="../../../assets/js/sweetalert2.all.min.js"></script>
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/compose-mail.js'); ?>" type='module'></script>
</body>
</html>
