<?php 
    require 'assets/server/conn.php'; 
    //Request Variables
    $folderName = $_GET['folder'];
	$courseID = $_GET['course_id'];
	$course_type = $_GET['course_type'];
	$access_type = $_GET['access'];
	$access_id = $_GET['accessID'];
	require 'check.php';
	mysqli_set_charset($conn, 'utf8');

	//Global Variables
	$course_title = '';
	$course_description = '';
	$course_narration = '';
	$course_cover_page = '';
	$course_author = '';
	$author_profile = '';
	$folder_path = '';
	$time = '';
	$instagram_link = '#';
	$tiktok_link = '#';
	$twitter_link = '#';
	$facebook_link = '#';
	$telegram_link = '#';
	$number_of_enrolled_students = 0;
	$authors_total_courses = 0;

	switch ($course_type) {
		case 'Affiliate':
			$sql = mysqli_query($conn, "SELECT * FROM affiliate_program_course WHERE courseID = '$courseID'");
			if(mysqli_num_rows($sql) > 0){
				$result = mysqli_fetch_assoc($sql);
				$course_title = $result['course_title'];
				$course_description = $result['course_description'];
				$folder_path = $result['folder_path'];
				$course_cover_page = "./../courses/$folder_path/" . $result['course_cover_page'];
				$course_author = $result['course_authors'];
				$folder_path = $result['folder_path'];
				$time = timeAgo($result['uploaded_on']);
				$course_narration = strtolower($result['course_narration']);
				//Enrolled students count
				$enrolled_students_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$courseID' AND course_type = '$course_type' AND purchase_status = 'Completed'");
				$enrolled_students_count = mysqli_num_rows($enrolled_students_query);
				$number_of_enrolled_students = $enrolled_students_count + count($default_allowed_emails); //Based on start-off affiliates and admins that have access to the main course
				$author_profile = './../assets/img/chromstack-logo.jpg';
				//Get links
				$instagram_link = 'https://instagram.com/chromstack?igshid=MzRIODBiNWFIZA==';
				$tiktok_link = 'https://www.tiktok.com/@chromstack';
				$twitter_link = 'https://x.com/chromstack?s=21';
				$facebook_link = 'https://facebook.com/profile.php?id=61556804134821';
				$telegram_link = 'https://t.me/+gc9Fr20Y70A0NTdk';
				//Get authors total courses
				$authors_course_query = mysqli_query($conn, "SELECT * FROM affiliate_program_course WHERE course_authors = '$course_author' GROUP BY courseID");
				$authors_total_courses = mysqli_num_rows($authors_course_query);
			}
		break;
		case 'Admin':
		case 'External':
			$sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE courseID = '$courseID'");
			if(mysqli_num_rows($sql) > 0){
				$result = mysqli_fetch_assoc($sql);
				$course_title = $result['course_title'];
				$course_description = $result['course_description'];
				$folder_path = $result['folder_path'];
				$course_author = $result['course_authors'];
				$time = timeAgo($result['uploaded_on']);
				$course_narration = strtolower($result['course_narration']);
				//Enrolled students count
				$enrolled_students_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$courseID' AND course_type = '$course_type' AND purchase_status = 'Completed'");
				$enrolled_students_count = mysqli_num_rows($enrolled_students_query);
				$number_of_enrolled_students = $enrolled_students_count;
				//Get author profile
                if($course_type === 'Admin'){
                    $sql = mysqli_query($conn, "SELECT * FROM admins WHERE fullname = '$course_author'");
    				$row = mysqli_fetch_assoc($sql);
    				$db_profile = $row['admin_profile'];
    				//Get links
    				$link_one = $row['instagram_link'];
    				$link_two = $row['tiktok_link'];
    				$link_three = $row['twitter_link'];
    				$link_four = $row['facebook_link'];
                }
                else{
                    $sql = mysqli_query($conn, "SELECT * FROM vendors WHERE fullname = '$course_author'");
    				$row = mysqli_fetch_assoc($sql);
    				$db_profile = $row['vendor_profile'];
    				//Get links
    				$link_one = $row['instagram_link'];
    				$link_two = $row['tiktok_link'];
    				$link_three = $row['twitter_link'];
    				$link_four = $row['facebook_link'];
                }
			
				if ($db_profile !== null && $db_profile !== '' && $db_profile !== 'null') {
					$author_profile = './../uploads/' . $db_profile;
				}
				else {
					$author_profile = './../assets/img/user.png';
				}
				if ($folder_path === "null") {
					$course_cover_page = "./../assets/img/" . $result['course_cover_page'];
				}
				else{
					$course_cover_page = "./../courses/$folder_path/" . $result['course_cover_page'];
				}
		
				//Process links
				if ($link_one !== null && $link_one !== '' && $link_one !== 'null') {
					$instagram_link = $link_one;
				}
					
				if ($link_two !== null && $link_two !== '' && $link_two !== 'null') {
					$tiktok_link = $link_two;
				}
					
				if ($link_three !== null && $link_three !== '' && $link_three !== 'null') {
					$twitter_link = $link_three;
				}
				
				if ($link_four !== null && $link_four !== '' && $link_four !== 'null') {
					$facebook_link = $link_four;
				}
				$telegram_link = '#';
				//Get authors total courses
				$authors_course_query = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE course_authors = '$course_author' GROUP BY courseID");
				$authors_total_courses = mysqli_num_rows($authors_course_query);
			}
		break;
	}
	
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
    <title>Chromstack | Course Details</title>
    <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <!-- Custom Color Option -->
    <link href="assets/css/colors.css" rel="stylesheet">
	<link href="<?php echo getCacheBustedUrl('assets/css/overlay.css'); ?>" rel="stylesheet">
	<style>
		html, body{
			overflow: hidden;
		}
		
		.bg-light{
		    padding-top: 30px !important;
		}
		
		page-title{
		    padding-top: 0px !important;
		}
        
        .file-title {
           padding-right: 1.5rem !important;
           width: 70% !important;
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
							<p style='display: none;' id='course-parent-holder'><?php echo $folderName; ?></p>
							<p style='display: none;' id='access-id-holder'><?php echo $access_id; ?></p>
							<p style='display: none;' id='access-type-holder'><?php echo $access_type; ?></p>
							<input type="text" id="course-id-input" value="<?php echo $courseID; ?>" hidden>
							<input type="text" id="course-type-input" value="<?php echo $course_type; ?>" hidden>
							<input type="text" id="course-title-input" value="<?php echo $course_title; ?>" hidden>
							<input type="text" id="fullname-container" value="<?php echo $fullname; ?>" hidden>
							<input type="text" id="profile-container" value="<?php echo $profile; ?>" hidden>
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
									<h1 class="breadcrumb-title">Course details</h1>
									<nav aria-label="breadcrumb">
										<ol class="breadcrumb">
											<li class="breadcrumb-item"><a href="index.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>">Dashboard</a></li>
											<li class="breadcrumb-item active" aria-current="page">Course details</li>
										</ol>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- ============================ Page Title End ================================== -->			
	

			<!-- ============================ Course Detail ================================== -->
			<section class="bg-light" style="overflow-y: auto !important;padding-bottom: 3rem !important;height: 500px;">
				<div class="container">
					<div class="row" style="display: flex;flex-direction: column;align-items: center;justify-content: center;">
						<div class="col-lg-12 col-md-12">
						
							<div class="inline_edu_wrap">
								<div class="inline_edu_first">
									<h4><?php echo $course_title; ?></h4>
									<ul class="edu_inline_info">
										<li><i class="ti-calendar"></i><?php echo $time; ?></li>
										<!--<li><i class="ti-control-forward"></i>102 Lectures</li>-->
										<li><i class="ti-user"></i><?php echo $number_of_enrolled_students; ?> Students Enrolled</li>
									</ul>
								</div>	
							</div>
							
							<div class="property_video xl">
								<div class="thumb">
									<img class="pro_img img-fluid w100" src="<?php echo $course_cover_page; ?>" alt="Cover Page">
									<div class="overlay_icon">
										<!-- <div class="bb-video-box">
											<div class="bb-video-box-inner">
												<div class="bb-video-box-innerup">
													<a href="https://www.youtube.com/watch?v=A8EI6JaFbv4" data-toggle="modal" data-target="#popup-video" class="theme-cl"><i class="ti-control-play"></i></a>
												</div>
											</div>
										</div> -->
									</div>
								</div>
								
								<div class="instructor_over_info">
									<ul>
										<li>
											<div class="ins_info" href="dashboard.php">
												<div class="ins_info_thumb">
													<img src="<?php echo $author_profile; ?>"  class="img-fluid" alt="" />
												</div>
												<div class="ins_info_caption">
													<span>Author</span>
													<h4><?php echo $course_author; ?></h4>
												</div>
											</div>
										</li>
										<!-- <li>
											<span>Category</span>
											Software
										</li> --
										<li>
											<span>Reviews</span>
											<div class="eds_rate">
												4.2
												<div class="eds_rating">
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star filled"></i>
													<i class="fas fa-star"></i>
												</div>
											</div>
										</li>-
									-->
									</ul>
								</div>
								
							</div>
							
							<!-- All Info Show in Tab -->
							<div class="tab_box_info mt-4">
								<ul class="nav nav-pills mb-3 light" id="pills-tab" role="tablist" style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
									<li class="nav-item" style="width: 25%;text-align: center;">
										<a class="nav-link active" id="overview-tab" data-toggle="pill" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Summary</a>
									</li>
									<li class="nav-item" style="width: 25%;text-align: center;">
										<a class="nav-link" id="curriculum-tab" data-toggle="pill" href="#curriculum" role="tab" aria-controls="curriculum" aria-selected="false">Modules</a>
									</li>
									<li class="nav-item" style="width: 25%;text-align: center;">
										<a class="nav-link" id="instructor-tab" data-toggle="pill" href="#instructor" role="tab" aria-controls="instructor" aria-selected="false">Author</a>
									</li>
									<li class="nav-item" style="width: 25%;text-align: center;">
										<a class="nav-link" id="reviews-tab" data-toggle="pill" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews</a>
									</li>
								</ul>
							
								<div class="tab-content" id="pills-tabContent">
									
									<!-- Overview Detail -->
									<div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
										<!-- Overview -->
										<div class="edu_wraper">
											<h4 class="edu_title">Course Overview</h4>
											<p>
												<?php echo $course_description; ?>
											</p>		
										</div>
									</div>
									
									<!-- Curriculum Detail -->
									<div class="tab-pane fade" id="curriculum" role="tabpanel" aria-labelledby="curriculum-tab">
										<div class="edu_wraper">
											<h4 class="edu_title">Course Curriculum</h4>
											<div id="accordionExample" class="accordion shadow circullum">

											    <?php

													function displayContents($directory, $dirname) {
														// Ensure the directory exists
														if (!is_dir($directory)) {
															echo "The directory does not exist.";
															return;
														}

														// Initialize an integer for collapsible sections
														$integer = 0;

														// List all items in the directory, including files and subdirectories
														$items = glob($directory . '/*');

														foreach ($items as $item) {
															// Get the item name and path
															$title = ucwords(pathinfo($item, PATHINFO_FILENAME));
															$fullPath = $item;
															$integer++;

															if (is_dir($item)) {
																// Handle subdirectories
																//$integer++;
																echo "<div class='card'>";
																echo   "<div id='heading$integer' class='card-header bg-white shadow-sm border-0'>";
																echo       "
																			<h6 class='mb-0 accordion_title'>
																				<a href='#' data-toggle='collapse' data-target='#collapse$integer' aria-expanded='false' aria-controls='collapse$integer' class='d-block position-relative text-dark collapsible-link py-2'>
																					<p style='width: 90%;'>$title</p>
																				</a>
																			</h6>
																		";
																echo   "</div>";
																echo   "<div id='collapse$integer' aria-labelledby='heading$integer' data-parent='#accordionExample' class='collapse'>";
																echo       "<div class='card-body pl-3 pr-3'>";
																echo           "<ul class='lectures_lists'>";

																// List files in the subdirectory
																$subfiles = glob($item . '/*');
																foreach ($subfiles as $file) {
																	if (is_file($file)) {
																		$filename = ucwords(pathinfo($file, PATHINFO_FILENAME));
																		$extension = pathinfo($file, PATHINFO_EXTENSION);
																		$counter = $counter ?? 0; // Initialize counter if not set
																		$counter++;

																		switch ($extension) {
																			case 'jpg':
																			case 'jpeg':
																			case 'png':
																			case 'webp':
																				echo "
																						<li class='image_content'>
																							<div class='lectures_lists_title'>Item: $counter</div>
																							<p class='file-title'>$filename</p>
																							<p class='file-path' style='display: none;'>$file</p>
																						</li>
																						";
																				break;
																			case 'mp4':
																				echo "
																						<li class='video_content'>
																							<div class='lectures_lists_title'>
																								<i class='ti-control-play'></i>Item: $counter
																								<p class='video-duration' style='font-size: 12px;padding-left: 12px;'></p>
																							</div>
																							<p class='file-title'>$filename</p>
																							<p class='file-path' style='display: none;'>$file</p>
																						</li>
																						";
																				break;
																			case 'pdf':
																				echo "
																						<li class='pdf_content'>
																							<div class='lectures_lists_title'>Item: $counter</div>
																							<p class='file-title'>$filename</p>
																							<p class='file-path' style='display: none;'>$file</p>
																						</li>
																						";
																				break;
																			case 'docx':
																				echo "
																						<li class='word_content'>
																							<div class='lectures_lists_title'>Item: $counter</div>
																							<p class='file-title'>$filename</p>
																							<p class='file-path' style='display: none;'>$file</p>
																						</li>
																						";
																				break;
																		}
																	}
																}

																echo           "</ul>";
																echo       "</div>";
																echo   "</div>";
																echo "</div>";

															} elseif (is_file($item)) {
																// Handle files directly in the parent directory
																$filename = ucwords(pathinfo($item, PATHINFO_FILENAME));
																$extension = pathinfo($item, PATHINFO_EXTENSION);
																$num = $num ?? 0; // Initialize counter if not set
																$num++;


																echo "<div class='card'>";
																echo   "<div id='heading$integer' class='card-header bg-white shadow-sm border-0'>";
																echo       "
																			<h6 class='mb-0 accordion_title'>
																				<a href='#' data-toggle='collapse' data-target='#collapse$integer' aria-expanded='false' aria-controls='collapse$integer' class='d-block position-relative text-dark collapsible-link py-2'>
																					<p style='width: 90%;'>$title</p>
																				</a>
																			</h6>
																		";
																echo   "</div>";
																echo   "<div id='collapse$integer' aria-labelledby='heading$integer' data-parent='#accordionExample' class='collapse'>";
																echo       "<div class='card-body pl-3 pr-3'>";
																echo           "<ul class='lectures_lists'>";

																switch ($extension) {
																	case 'jpg':
																	case 'jpeg':
																	case 'png':
																	case 'webp':
																		echo "
																				<li class='image_content'>
																					<div class='lectures_lists_title'>Item: $num</div>
																					<p class='file-title'>$filename</p>
																					<p class='file-path' style='display: none;'>$item</p>
																				</li>
																				";
																		break;
																	case 'mp4':
																		echo "
																				<li class='video_content'>
																					<div class='lectures_lists_title'>
																						<i class='ti-control-play'></i>Item: $num
																						<p class='video-duration' style='font-size: 12px;padding-left: 12px;'></p>
																					</div>
																					<p class='file-title'>$filename</p>
																					<p class='file-path' style='display: none;'>$item</p>
																				</li>
																				";
																		break;
																	case 'pdf':
																		echo "
																				<li class='pdf_content'>
																					<div class='lectures_lists_title'>Item: $num</div>
																					<p class='file-title'>$filename</p>
																					<p class='file-path' style='display: none;'>$item</p>
																				</li>
																				";
																		break;
																	case 'docx':
																		echo "
																				<li class='word_content'>
																					<div class='lectures_lists_title'>Item: $num</div>
																					<p class='file-title'>$filename</p>
																					<p class='file-path' style='display: none;'>$item</p>
																				</li>
																				";
																	break;
																}

																echo           "</ul>";
																echo       "</div>";
																echo   "</div>";
																echo "</div>";
								
															}
														}
													}

													// Define the root directory
													$rootDirectory = "./../courses/$folder_path";

													// Display the contents of the parent directory and its subfolders
													displayContents($rootDirectory, $folder_path);

													?>


											</div>
										</div>
									</div>
									
									<!-- Instructor Detail -->
									<div class="tab-pane fade" id="instructor" role="tabpanel" aria-labelledby="instructor-tab">
										<div class="single_instructor">
											<div class="single_instructor_thumb">
												<a href="#"><img src="<?php echo $author_profile; ?>" class="img-fluid" alt=""></a>
											</div>
											<div class="single_instructor_caption">
												<h4><a href="#"><?php echo $course_author; ?></a></h4>
												<ul class="instructor_info">
													<li>
													    <i class="ti-video-camera"></i>
													    <?php 
													        if($authors_total_courses < 2){
													            echo $authors_total_courses . ' Course'; 
													        }else{
													            echo $authors_total_courses . ' Courses'; 
													        }
													    ?>
													</li>
												</ul>
												<p>
													This section contains the details of <b><?php echo $course_author; ?></b>,
												    the author of this <?php echo $course_narration; ?>. Below are the author's social media links.
													Feel free to connect with the author across their social media handles.
												</p>
												<ul class="social_info">
													<li><a href="<?php echo $facebook_link; ?>"><i class="ti-facebook"></i></a></li>
													<li><a href="<?php echo $twitter_link; ?>"><i class="ti-twitter"></i></a></li>
													<li><a href="<?php echo $instagram_link; ?>"><i class="ti-instagram"></i></a></li>
													<li><a href="<?php echo $telegram_link; ?>"><i class="fa fa-paper-plane"></i></a></li>
												</ul>
											</div>
										</div>
									</div>
									
									<!-- Reviews Detail -->
									<div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
										
										<!-- Overall Reviews --
										<div class="rating-overview">
											<div class="rating-overview-box">
												<span class="rating-overview-box-total">4.2</span>
												<span class="rating-overview-box-percent">out of 5.0</span>
												<div class="star-rating" data-rating="5"><i class="ti-star"></i><i class="ti-star"></i><i class="ti-star"></i><i class="ti-star"></i><i class="ti-star"></i>
												</div>
											</div>
										</div>
									-->
							
										<!-- Reviews -->
										<div class="list-single-main-item fl-wrap">
											<div class="list-single-main-item-title fl-wrap">
												<h3>Course Reviews - 
												  <span>
													 <?php 
														$reviews_count = mysqli_query($conn, "SELECT * FROM reviews WHERE courseID = '$courseID' AND course_type = '$course_type'");
														$total_reviews = mysqli_num_rows($reviews_count);
														echo $total_reviews;
													 ?>
												</span>
											</h3>
											</div>
											<div class="reviews-comments-wrap" id="reviews-list">

											   <?php
											   
												$reviews_sql = mysqli_query($conn, "SELECT * FROM reviews WHERE courseID = '$courseID' AND course_type = '$course_type' ORDER BY reviewID DESC");
													if(mysqli_num_rows($reviews_sql) > 0){
														while($review_item = mysqli_fetch_assoc($reviews_sql)){
															$review_id = $review_item['reviewID'];
															$review_profile = $review_item['profile'];
															$review_fullname = $review_item['fullname'];
															$review_comment = $review_item['review_comment'];
															$review_time = $review_item['review_time'];
															//Check profile
															if ($review_fullname === $fullname) {
																if ($profile !== null && $profile !== '' && $profile !== 'null') {
																	$user_profile = './../uploads/' . $profile;
																}
																else {
																	$user_profile = './../assets/img/user.png';
																}
															}
															else {
																if ($review_profile !== null && $review_profile !== '' && $review_profile !== 'null') {
																	$user_profile = './../uploads/' . $review_profile;
																}
																else {
																	$user_profile = './../assets/img/user.png';
																}
															}

															echo "<div class='reviews-comments-item'>
																		<div class='review-comments-avatar'>
																			<img src='$user_profile' class='img-fluid' alt='Profile'> 
																		</div>
																		<div class='reviews-comments-item-text'>
																			<h4>
																				<a href='#!' style='padding-right: 5px;'>$review_fullname</a>
																				<span class='reviews-comments-item-date'>
																					<!--<i class='ti-calendar theme-cl'></i>-->
																					$review_time
																				</span>
																		    </h4>
																			<!--<div class='listing-rating high' data-starrating2='5'>
																				<i class='ti-star active'></i>
																				<i class='ti-star active'></i>
																				<i class='ti-star active'></i>
																				<i class='ti-star active'></i>
																				<i class='ti-star active'></i>
																				<span class='review-count'>4.9</span>
																			</div>-->
																			<div class='clearfix'></div>
																			<p>
																				$review_comment
																			</p>
																			<div class='pull-left reviews-reaction'>
																				<a href='#' class='comment-like active'><i class='ti-thumb-up'></i> 0</a>
																				<a href='#' class='comment-dislike active'><i class='ti-thumb-down'></i> 0</a>
																				<a href='#' class='comment-love active'><i class='ti-heart'></i> 0</a>
																			</div>
																		</div>
																	</div>
																";
														}
													}
													else{
														 echo "<script>
																// Get the div element
																const myDiv = document.getElementById('reviews-list');

																// Clear the existing content
																myDiv.innerHTML = '';

																//Set a style
																myDiv.setAttribute('style', 'text-align: center;');
																
																//Create p tag
																const paragraph = document.createElement('p');
																
																//Set styles
																paragraph.setAttribute('id', 'reviewsParagraph');
																
																//Set text node
																paragraph.textContent = 'No reviews yet. Be the first to drop a review about this course!';
																
																// Append the text node to the div
																myDiv.appendChild(paragraph);
																
															</script>
															";
														}
												?>
												
											</div>
										</div>
										
										<!-- Submit Reviews -->
										<div class="edu_wraper">
											<h4 class="edu_title">Submit Reviews</h4>
											<div class="review-form-box form-submit">
												<form id="review-form">
													<div class="row">
														
														<div class="col-lg-6 col-md-6 col-sm-12">
															<div class="form-group">
																<label>Name</label>
																<input class="form-control" type="text" id="fullname" placeholder="Your Name" value="<?php echo $fullname; ?>" disabled>
															</div>
														</div>
														
														<div class="col-lg-6 col-md-6 col-sm-12">
															<div class="form-group">
																<label>Email</label>
																<input class="form-control" type="email" id="email" placeholder="Your Email" value="<?php echo $email; ?>" disabled>
															</div>
														</div>
														
														<div class="col-lg-12 col-md-12 col-sm-12">
															<div class="form-group">
																<label>Review</label>
																<textarea class="form-control ht-140" id="comment" placeholder="Review"></textarea>
															</div>
														</div>
														
														<div class="col-lg-12 col-md-12 col-sm-12">
															<div class="form-group">
																<button type="submit" class="btn btn-theme">Submit Review</button>
															</div>
														</div>

													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<div class="col-lg-4 col-md-4">
			<!-- End Modal -->
			<!-- End Video Modal -->
			<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>
		</div>

		<!-- ============================================================== -->
		<!-- Overlays for viewing course contents -->
		<!-- ============================================================== -->
		
		<!-- Video Overlay -->
		<div class='content-overlay' id='video-content-overlay'>
			<div class='overlay-close' id='close-video'>X</div>
			<video src="" id="course-video"></video>
			<div class='video-controls'> 
				<div class='first-row'>
					<div class='icon-div'>
						<i class='fas fa-play'></i>
					</div>
				</div>
				<div class='second-row'>
					<input type="range" name="" min="0" max="100" value="0" id="rangeSelector" class="slider">
				</div>
				<div class='third-row'>
					<div class='icon-div' id='video-playtime'>
					    00:00:00
					</div>
					<div class='icon-div' id='video-speedtime'>
					    <select name='video_speed' id='video-speed'>
            		        <option value='0.5' name='speed'>0.5x</option>
            		        <option value='1.0' name='speed'>1.0x</option>
            		        <option value='1.5' name='speed'>1.5x</option>
            		    </select>
					</div>
				</div>
			</div>
		</div>
        
        <!-- Image Overlay -->
		<div class='content-overlay' id='image-content-overlay'>
			<div class='overlay-close' id='close-image'>X</div>
			<img src="" alt="Course image" id="course-image">
		</div>
		
		 <!-- DOCX Overlay -->
		<div class='content-overlay' id='word-content-overlay'>
			<div class='overlay-close' id='close-word'>X</div>
			<iframe src=""></iframe>
			 <!--<div id="word_container"></div>-->
		</div>
        
        <!-- PDF Overlay -->
		<div class='content-overlay' id='pdf-content-overlay'>
			<div class='overlay-close' id='close-pdf'>X</div>
			<iframe src=""></iframe>
			 <!--<div class="canvas_container">
                <canvas id="pdf_renderer"></canvas>
            </div>
			<div class='bottom-row'>
			    <div class="navigation_controls">
                    <button id="go_previous">Prev</button>
                    <input id="current_page" value="1" type="number"/>
                    <button id="go_next">Next</button>
                </div>
                <div class="zoom_controls">  
                    <button id="zoom_in">+</button>
                    <button id="zoom_out">-</button>
                </div>
			</div>-->
		</div>

		<!-- ============================================================== -->
		<!-- Overlays end -->
		<!-- ============================================================== -->

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
		<script src="../assets/js/sweetalert2.all.min.js"></script>
		<!-- PDF Support -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>
		<!-- DOCX Support -->
		<script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.js"></script>
        <!--<script src="Scripts/docx-preview.js"></script>-->
		<script src="<?php echo getCacheBustedUrl('assets/js/reviews.js'); ?>" type="module"></script>
		<script src="<?php echo getCacheBustedUrl('assets/js/controls.js'); ?>" type="module"></script>
		<!-- <script src="assets/js/custom.js"></script> -->
		<script src="assets/js/redirect.js"></script> 
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->

	</body>

<!-- </html> -->