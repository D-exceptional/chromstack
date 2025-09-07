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
    <title>Chromstack | Orders</title>
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
							<p style='display: none;' id='access-id-holder'><?php echo $access_id; ?></p>
							<p style='display: none;' id='access-type-holder'><?php echo $access_type; ?></p>
						</div>
						<div class="nav-menus-wrapper" style="transition-property: none;"></div>
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

             <!-- ============================ Page Title Start================================== -->
			<section class="page-title" style="height: 100px;padding: 20px 0px 10px 0px;">
				<div class="container">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="breadcrumbs-wrap">
								<h1 class="breadcrumb-title">My Orders</h1>
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="index.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">My Orders</li>
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
					<div class="row" style="display: flex;flex-direction: column;align-items: center;justify-content: center;">
						<div class="col-lg-9 col-md-9 col-sm-9">
							<!-- Row -->
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div class="dashboard_container">
										<div class="dashboard_container_header">
											<div class="dashboard_fl_1">
												<h4>All Orders</h4>
											</div>
										</div>
										<div class="dashboard_container_body">
											<div class="table-responsive">
												<table class="table">
													<thead class="thead-dark">
														<tr>
															<th scope="col">OrderID</th>
															<th scope="col">Course</th>
															<th scope="col">Cover Page</th>
															<th scope="col">Amount</th>
															<th scope="col">Author</th>
															<th scope="col">Date</th>
															<th scope="col">Status</th>
														</tr>
													</thead>
													<tbody id='order-lists'>

														<?php

															$status_button = '';
															$author_name = '';

															//Check if this person has bought a course
															$all_course_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE buyer_email = '$email' AND purchase_status = 'Completed' GROUP BY courseID");
															$enrolled_courses_count = mysqli_num_rows($all_course_query);
															if($enrolled_courses_count > 0){
																while ($result = mysqli_fetch_assoc($all_course_query)) {
																	$course_id = $result['courseID'];
																	$course_type = $result['course_type'];
																	$course_amount = '$' . ($result['course_amount'] / 1000);
																	$course_purchase_date = timeAgo($result['purchase_date']);
																	$course_purchase_status = $result['purchase_status'];
																	$trackingID = $result['trackingID'];
																	//Get course details
																	switch ($course_type) {
																		case 'Affiliate':
																			$course_list_query = mysqli_query($conn, "SELECT course_title, course_cover_page, course_authors, folder_path FROM affiliate_program_course WHERE courseID = '$course_id'");
																			while($course_list_result = mysqli_fetch_assoc($course_list_query)){
																				$cover_page = $course_list_result['course_cover_page'];
																				$course_title = $course_list_result['course_title'];
																				$folder_path = $course_list_result['folder_path'];
																				$author_name = $course_list_result['course_authors'];
																			}
																		break;
																		case 'Admin':
																		case 'External':
																			$course_list_query = mysqli_query($conn, "SELECT course_title, course_cover_page, course_authors, folder_path FROM uploaded_courses WHERE courseID = '$course_id'");
																			while($course_list_result = mysqli_fetch_assoc($course_list_query)){
																				$cover_page = $course_list_result['course_cover_page'];
																				$course_title = $course_list_result['course_title'];
																				$folder_path = $course_list_result['folder_path'];
																				$author_name = $course_list_result['course_authors'];
																			}
																		break;
																	}

																	if ($course_purchase_status == 'Pending') {
																		$status_button = "<span class='payment_status inprogress'>Pending</span>";
																	} else {
																		$status_button = "<span class='payment_status complete'>Completed</span>";
																	}

																	echo "<tr>
																			<td>$trackingID</td>
																			<td>$course_title</td>
																			<td>
																			   <img src='./../courses/$folder_path/$cover_page' alt='' style='width: 100px;height: 100px;'>
																			</td>
																			<td>$course_amount</td>
																			<td>$author_name</td>
																			<td>$course_purchase_date</td>
																			<td>$status_button</td>
																		 </tr>
																		";
																}
															}
															else{
																echo "<script>
																		// Get the div element
																		const myDiv = document.getElementById('order-lists');

																		// Clear the existing content
																		myDiv.innerHTML = '';

																		//Set a style
																		myDiv.style.textAlign = 'center';

																		// Create a new text node
																		const newText = document.createTextNode('No purchased course!');

																		// Append the text node to the div
																		myDiv.appendChild(newText);
																		
																	</script>
																	";
															}

															?>
												
													</tbody>
												</table>
											</div>
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
			
			
			<!-- End Modal -->
			
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
		 <script src="assets/js/redirect.js"></script> 
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->
		<script src="assets/js/metisMenu.min.js"></script>	
		<script>
			$('#side-menu').metisMenu();
		</script>

	</body>

</html>