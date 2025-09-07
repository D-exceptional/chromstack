<?php 
    $access_type = $_GET['access'];
    $access_id = $_GET['accessID'];
    require 'assets/server/conn.php'; 
    require 'check.php';
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="author" content="www.frebsite.nl" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Chromstack | eLearning</title>
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
        <div id="main-wrapper"  data-spy="scroll" data-target=".navbar" data-offset="60">

            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->
            <!-- Start Navigation -->
			<div class="header header-light head-shadow" style="box-shadow: none !important;border-bottom: 1px solid gray;">
				<div class="container">
					<nav id="navigation" class="navigation navigation-landscape">
						<div class="nav-header">
							<a class="nav-brand" href="#">
								<img src="/assets/img/chromstack-logo.jpg" class="logo" alt="" style="width: 50px;height: 50px;border-radius: 10px;position: relative;top: 0;left: 0;" />
								e-Learning 
							</a>
							<div class="nav-toggle"></div>
							<p style='display: none;' id='access-id-holder'><?php echo $access_id; ?></p>
							<p style='display: none;' id='access-type-holder'><?php echo $access_type; ?></p>
						</div>
						<?php
                            if ($profile !== null && $profile !== '' && $profile !== 'null') {
                               $user_profile = './../uploads/' . $profile;
                            }
                            else {
                                $user_profile = './../assets/img/user.png';
                            }
                            echo "<img src='$user_profile' id='user-image' class='img-fluid avater' alt='User Image' style='width: 50px;height: 50px;border-radius: 50%;position: absolute;top: 20%;right: 0;cursor: pointer;'>";
                        ?>
					</nav>
				</div>
			</div>
			<!-- End Navigation -->
			<div class="clearfix"></div>
			<!-- ============================================================== -->
			<!-- Top header  -->
			<!-- ============================================================== -->		

			
			<!-- ============================ Dashboard: Dashboard Start ================================== -->
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
			
								<div class="d-navigation">
									<ul id="side-menu">
										<li class="active"><a href="#" style="background-color: #bbf9ff;color: #bbf9ff;"><i class="ti-dashboard"></i>Dashboard</a></li>
										<li><a href="all_course.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>"><i class="ti-book"></i>All Courses</a></li>
										<li><a href="order_history.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>"><i class="ti-shopping-cart-full"></i> Order History</a></li>
										<li><a href="profile.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>"><i class="ti-user"></i>My Profile</a></li>
										<li><a href="logout.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>"><i class="ti-power-off"></i>Log Out</a></li>
									</ul>
								</div>
							</div>
						</div>	
						
						<div class="col-lg-9 col-md-9 col-sm-12">
							
							<!-- Row -->
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 pb-4">
									<nav aria-label="breadcrumb">
										<ol class="breadcrumb">
											<!---<li class="breadcrumb-item"><a href="#">Home</a></li>-->
											<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
										</ol>
									</nav>
								</div>
							</div>
							<!-- /Row -->
							
							<!-- Row -->
							<div class="row">
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
									<div class="dashboard_stats_wrap">
										<div class="dashboard_stats_wrap_content"><h4 class="text-success"><?php echo $enrolled_courses; ?></h4> <span>Enrolled courses</span></div>
										<div class="dashboard_stats_wrap-icon"><i class="ti-location-pin"></i></div>
									</div>	
								</div>
						
								<!--<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
									<div class="dashboard_stats_wrap">
										<div class="dashboard_stats_wrap_content"><h4 class="text-success">4</h4> <span>Course in Progress</span></div>
										<div class="dashboard_stats_wrap-icon"><i class="ti-location-pin"></i></div>
									</div>	
								</div>
								
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
									<div class="dashboard_stats_wrap">
										<div class="dashboard_stats_wrap_content"><h4 class="text-warning">2</h4> <span>Completed Courses</span></div>
										<div class="dashboard_stats_wrap-icon"><i class="ti-pie-chart"></i></div>
									</div>	
								</div>-->
								
							</div>
							<!-- /Row -->
						</div>
			            <!-- End Modal -->
					    <a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
				    </div>
				</div>
			</section>
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
		<script src="assets/js/redirect.js"></script> 
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->
		<script src="assets/js/metisMenu.min.js"></script>	
		<script>
			$('#side-menu').metisMenu();
			$('.d-navigation ul li a ')
			.each(function () {
				$(this)
				.on('hover, mouseover, click', function () {
					$(this)
					.css({
						'background-color': '#bbf9ff !important',
						'background': '#bbf9ff !important'
					});
				});
			});
		</script>
		<script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'G-LJMVMMV3RP');
       </script>

	</body>
</html>