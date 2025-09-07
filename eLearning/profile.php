<?php 
    $access_type = $_GET['access'];
    $access_id = $_GET['accessID'];
    require 'assets/server/conn.php'; 
    require 'check.php';
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
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="www.frebsite.nl" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Chromstack | Profile</title>
    <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <!-- Custom Color Option -->
    <link href="assets/css/colors.css" rel="stylesheet">
    <style>
        html, body{
            overflow: hidden;
        }
    </style>
</head>
<body class="red-skin">
	
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- <div id="preloader"><div class="preloader"><span></span><span></span></div></div> -->
		
		
        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <div id="main-wrapper">
		
            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->
            <!-- Start Navigation -->
			<div class="header header-light head-shadow" style="box-shadow: none !important;border-bottom: 1px solid gray;">
				<div class="container">
					<nav id="navigation" class="navigation navigation-landscape">
						<div class="nav-header">
							<a class="nav-brand" href="#">
								<img src="/assets/img/chromstack-logo.jpg" class="logo" alt="" style="width: 50px;height: 50px;border-radius: 10px;" />
								e-Learning
							</a>
							<div class="nav-toggle"></div>
						</div>
						<div class="nav-menus-wrapper" style="transition-property: none;">
						
						</div>
					</nav>
				</div>
			</div>
			<!-- End Navigation -->
			<div class="clearfix"></div>
			<!-- ============================================================== -->
			<!-- Top header  -->
			<!-- ============================================================== -->	

            <!-- ============================ Page Title Start================================== -->
			<section class="page-title" style="height: 100px;padding: 20px 0px 10px 0px;">
				<div class="container">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="breadcrumbs-wrap">
								<h1 class="breadcrumb-title">My Profile</h1>
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="index.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">My Profile</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</section>

			
			<!-- ============================ Dashboard: My Order Start ================================== -->
			<section class="gray pt-5" style="overflow-y: auto;height: 550px;">
				<div class="container-fluid">		
					<div class="row">
					
						<div class="col-lg-3 col-md-3">
							<div class="dashboard-navbar">
								<div class="d-user-avater">
									<?php
                                        if ($profile !== null && $profile !== '' && $profile !== 'null') {
                                           $user_profile = './../uploads/' . $profile;
                                        }
                                        else {
                                            $user_profile = './../assets/img/user.png';
                                        }
                                    ?>
									<img src="<?php echo $user_profile; ?>" class="img-fluid avater" alt="" style="width: 100px;height: 100px;border-radius: 50%;">
									<h4><?php echo $fullname; ?></h4>
									<span><?php echo $country; ?></span>
								</div>
							</div>
						</div>	
						
						<div class="col-lg-9 col-md-9 col-sm-12">
							
							<!-- Row -->
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div class="dashboard_container">
										<div class="dashboard_container_header">
											<div class="dashboard_fl_1">
												<h4>Personal Details</h4>
											</div>
										</div>
										<div class="dashboard_container_body p-4">
											<!-- Basic info -->
											<div class="submit-section">
												<div class="form-row">

													<?php
														if ($membership_type == 'User') {
															echo "
																<div class='form-group col-md-6'>
																	<label>Change Profile</label>
																	<input type='file' class='form-control' id='profile'>
																	<input type='text' class='form-control' id='userID' value='$user_id' hidden>
																</div>
															";
														}
													?>
												
													<div class="form-group col-md-6">
														<label>Full Name</label>
														<input type="text" class="form-control" value="<?php echo $fullname; ?>" disabled>
													</div>
													
													<div class="form-group col-md-6">
														<label>Email</label>
														<input type="email" class="form-control" value="<?php echo $email; ?>" disabled>
													</div>
													
													<div class="form-group col-md-6">
														<label>Membership Type</label>
														<input type="text" class="form-control" value="<?php echo $membership_type; ?>" disabled>
													</div>
													
													<div class="form-group col-md-6">
														<label>Phone</label>
														<input type="text" class="form-control" value="<?php echo $contact; ?>" disabled>
													</div>
													
												</div>
											</div>
											<!-- Basic info -->
											
											<!-- Social Account info -->
											<div class="form-submit">	
												<?php
													if ($membership_type !== 'User') {
														echo "<h4 class='pl-2 mt-2'>Social Accounts</h4>";
													}
												?>
												<div class="submit-section">
													<div class="form-row">
													
														<?php
														
														    if ($membership_type !== 'User') {
																echo "<div class='form-group col-md-6'>
																		<label>Facebook</label>
																		<input type='text' class='form-control' value='$facebook_link'>
																	</div>
																	
																	<div class='form-group col-md-6'>
																		<label>Twitter</label>
																		<input type='email' class='form-control' value='$twitter_link'>
																	</div>
																	
																	<div class='form-group col-md-6'>
																		<label>Instagram</label>
																		<input type='text' class='form-control' value='$instagram_link'>
																	</div>
																	
																	<div class='form-group col-md-6'>
																		<label>TikTok</label>
																		<input type='text' class='form-control' value='$tiktok_link'>
																	</div>
																";
															}
														
														?>
														
														<?php
															if ($membership_type == 'User') {
																echo "<div class='form-group col-lg-12 col-md-12'>
																		<button class='btn btn-theme' type='submit' id='save'>Save Changes</button>
																	 </div>
																	";
															}
														?>
														
													</div>
												</div>
											</div>
											<!-- / Social Account info -->
											
										</div>
										
									</div>
								</div>
							</div>
							<!-- /Row -->
							
						</div>
					
					</div>
					<!-- Row -->
					
				</div>
			</section>
			<!-- ============================ Dashboard: My Order Start End ================================== -->
			
			
			<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
			

		</div>
		<!-- ============================================================== -->
		<!-- End Wrapper -->
		<!-- ============================================================== -->

		<!-- ============================================================== -->
		<!-- All Jquery -->
		<!-- ============================================================== -->
		<script src="assets/js/jquery.min.js"></script>
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<!-- <script src="assets/js/select2.min.js"></script> -->
		<script src="assets/js/slick.js"></script>
		<script src="assets/js/jquery.counterup.min.js"></script>
		<script src="assets/js/counterup.min.js"></script>
		<!-- <script src="assets/js/custom.js"></script> -->
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->
		<script src="assets/js/metisMenu.min.js"></script>	
		<script src="../assets/js/sweetalert2.all.min.js"></script>
		<script src="<?php echo getCacheBustedUrl('assets/js/profile.js'); ?>" type="module"></script>
		<script>
			$('#side-menu').metisMenu();
		</script>
	</body>
</html>