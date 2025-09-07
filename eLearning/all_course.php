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
    <title>Chromstack | Enrolled Courses</title>
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
								<h1 class="breadcrumb-title">My Courses</h1>
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb">
										<li class="breadcrumb-item"><a href="index.php?access=<?php echo $access_type; ?>&accessID=<?php echo $access_id; ?>">Dashboard</a></li>
										<li class="breadcrumb-item active" aria-current="page">My Courses</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- ============================ Page Title End ================================== -->			

			
			<!-- ============================ Full Width Courses  ================================== -->
			
			<section class="pt-0" style="overflow-y: auto;height: 550px;padding-bottom: 5rem !important;">
				<div class="container">
					<!-- Row -->
					<div class="row">	
						<div class="col-lg-12 col-md-12 col-sm-12">
							<!-- Row -->
							<div class="row align-items-center mb-3">
								<div class="col-lg-4 col-md-6 col-sm-12"></div>
								<div class="col-lg-4 col-md-6 col-sm-12 ordering"></div>
							</div>
							<!-- /Row -->
							
							<div class="row">

                                <?php

                                    $cover_page = '';
                                    $image = '';
                                    $course_title = '';
                                    $course_amount = '';
                                    $folder_path = '';
                                    $author_name = '';
                                    $author_profile = '';
                                    $time_elapsed = '';
                                    $number_of_purchases = '';
									$enrolled_courses_count = '';

                                    switch ($access_type) {
                                        case 'Admin':
                                            //Display the main course by default
                                            $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM affiliate_program_course");
                                            if(mysqli_num_rows($course_list_query) > 0){
                                                //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                    $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                    $course_id = $course_list_result['courseID'];
                                                    $course_type = $course_list_result['course_type'];
                                                    $cover_page = $course_list_result['course_cover_page'];
                                                    $course_title = $course_list_result['course_title'];
                                                    $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                    $course_narration = $course_list_result['course_narration'];
                                                    $folder_path = $course_list_result['folder_path'];
                                                    $author_name = $course_list_result['course_authors'];
                                                    $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                    //Get sales count
                                                    $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                    $course_sales_count = mysqli_num_rows($course_sales_query);
                                                    if ($course_sales_count > 0) {
                                                        $number_of_purchases = $course_sales_count . ' Purchases';
                                                    }
                                                    else {
                                                        $number_of_purchases = 0 . ' Purchases';
                                                    }

                                                    //Output result
                                                    
                                                    echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                            <div class='education_block_grid style_2'>
                                                                <div class='education_block_thumb n-shadow'>
                                                                    <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                </div>
                                                                
                                                                <div class='education_block_body'>
                                                                    <h4 class='bl-title'>
                                                                        <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                    </h4>
                                                                </div>
                                                                
                                                                <div class='cources_info'>
                                                                    <div class='cources_info_first'>
                                                                        <ul>
                                                                            <li><strong>$number_of_purchases</strong></li>
                                                                            <li class='theme-cl'>$course_narration</li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class='cources_info_last'>
                                                                        <h3>$course_amount</h3>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class='education_block_footer'>
                                                                    <div class='education_block_author'>
                                                                        <div class='path-img'>
                                                                            <a href='#'>
                                                                                <img src='./../assets/img/chromstack-logo.jpg' class='img-fluid' alt=''>
                                                                            </a>
                                                                        </div>
                                                                        <h5><a href='#'>$author_name</a></h5>
                                                                    </div>
                                                                    <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                </div>
                                                            </div>	
                                                        </div>
                                                    ";
                                                //}
                                            }
                                            //List other courses bought by this person
                                            $all_course_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE buyer_email = '$email' AND purchase_status = 'Completed'");
                                            $enrolled_courses_count = mysqli_num_rows($all_course_query);
                                            if($enrolled_courses_count > 0){
                                                while ($result = mysqli_fetch_assoc($all_course_query)) {
                                                    $course_id = $result['courseID'];
                                                    $course_type = $result['course_type'];
                                                    //Get course details
                                                    switch ($course_type) {
                                                        case 'Affiliate':
                                                            //Skip and continue to other courses
                                                        break;
                                                        case 'Admin':
                                                        case 'External':
                                                            $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM uploaded_courses WHERE courseID = '$course_id'");
                                                            if(mysqli_num_rows($course_list_query) > 0){
                                                                //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                    $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                    $cover_page = $course_list_result['course_cover_page'];
                                                                    $course_title = $course_list_result['course_title'];
                                                                    $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                    $course_narration = $course_list_result['course_narration'];
                                                                    $folder_path = $course_list_result['folder_path'];
                                                                    $author_name = $course_list_result['course_authors'];
                                                                    $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                    //Get author profile
                                                                    if($course_type === 'Admin'){
                                                                        $author_profile_query = mysqli_query($conn, "SELECT admin_profile FROM admins WHERE fullname = '$author_name'");
                                                                        $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                        $image = $profile_data['admin_profile'];
                                                                    }
                                                                    else{
                                                                        $author_profile_query = mysqli_query($conn, "SELECT vendor_profile FROM vendors WHERE fullname = '$author_name'");
                                                                        $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                        $image = $profile_data['vendor_profile'];
                                                                    }
                                                                    //Get sales count
                                                                    $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                    $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                    if ($course_sales_count > 0) {
                                                                        $number_of_purchases = $course_sales_count . ' Purchases';
                                                                    }
                                                                    else {
                                                                        $number_of_purchases = 0 . ' Purchases';
                                                                    }
                                                                    //Check image
                                                                    if ($image !== null && $image !== '' && $image !== 'null') {
                                                                        $author_profile = './../uploads/' . $image;
                                                                    }
                                                                    else {
                                                                        $author_profile = './../assets/img/user.png';
                                                                    }

                                                                    //Output result

                                                                    if ($folder_path === "null") {
                                                                        echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                            <div class='education_block_grid style_2'>
                                                                                <div class='education_block_thumb n-shadow'>
                                                                                    <a href='#'><img src='./../assets/img/$cover_page' class='img-fluid' alt=''></a>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_body'>
                                                                                    <h4 class='bl-title'>
                                                                                        <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                    </h4>
                                                                                </div>
                                                                                
                                                                                <div class='cources_info'>
                                                                                    <div class='cources_info_first'>
                                                                                        <ul>
                                                                                            <li><strong>$number_of_purchases</strong></li>
                                                                                            <li class='theme-cl'>$course_narration</li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class='cources_info_last'>
                                                                                        <h3>$course_amount</h3>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_footer'>
                                                                                    <div class='education_block_author'>
                                                                                        <div class='path-img'>
                                                                                            <a href='#'>
                                                                                                <img src='$author_profile' class='img-fluid' alt=''>
                                                                                            </a>
                                                                                        </div>
                                                                                        <h5><a href='#'>$author_name</a></h5>
                                                                                    </div>
                                                                                    <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                </div>
                                                                            </div>	
                                                                        </div>
                                                                        ";
                                                                    } else {
                                                                        echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                            <div class='education_block_grid style_2'>
                                                                                <div class='education_block_thumb n-shadow'>
                                                                                    <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_body'>
                                                                                    <h4 class='bl-title'>
                                                                                        <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                    </h4>
                                                                                </div>
                                                                                
                                                                                <div class='cources_info'>
                                                                                    <div class='cources_info_first'>
                                                                                        <ul>
                                                                                            <li><strong>$number_of_purchases</strong></li>
                                                                                            <li class='theme-cl'>$course_narration</li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class='cources_info_last'>
                                                                                        <h3>$course_amount</h3>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_footer'>
                                                                                    <div class='education_block_author'>
                                                                                        <div class='path-img'>
                                                                                            <a href='#'>
                                                                                                <img src='$author_profile' class='img-fluid' alt=''>
                                                                                            </a>
                                                                                        </div>
                                                                                        <h5><a href='#'>$author_name</a></h5>
                                                                                    </div>
                                                                                    <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                </div>
                                                                            </div>	
                                                                        </div>
                                                                        ";
                                                                    }
                                                                //}
                                                            }
                                                        break;
                                                    }
                                                }
                                            }
                                        break;
                                        case 'Affiliate':
                                             //Check if this person is assigned the main course by default
                                            if(in_array($email, $default_allowed_emails)){ 
                                                //Display the main course by default
                                                $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM affiliate_program_course");
                                                if(mysqli_num_rows($course_list_query) > 0){
                                                    //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                        $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                        $course_id = $course_list_result['courseID'];
                                                        $course_type = $course_list_result['course_type'];
                                                        $cover_page = $course_list_result['course_cover_page'];
                                                        $course_title = $course_list_result['course_title'];
                                                        $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                        $course_narration = $course_list_result['course_narration'];
                                                        $folder_path = $course_list_result['folder_path'];
                                                        $author_name = $course_list_result['course_authors'];
                                                        $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                        //Get sales count
                                                        $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                        $course_sales_count = mysqli_num_rows($course_sales_query);
                                                        if ($course_sales_count > 0) {
                                                            $number_of_purchases = $course_sales_count . ' Purchases';
                                                        }
                                                        else {
                                                            $number_of_purchases = 0 . ' Purchases';
                                                        }

                                                        //Output result

                                                        echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                <div class='education_block_grid style_2'>
                                                                    <div class='education_block_thumb n-shadow'>
                                                                        <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                    </div>
                                                                    
                                                                    <div class='education_block_body'>
                                                                        <h4 class='bl-title'>
                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                        </h4>
                                                                    </div>
                                                                    
                                                                    <div class='cources_info'>
                                                                        <div class='cources_info_first'>
                                                                            <ul>
                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                <li class='theme-cl'>$course_narration</li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class='cources_info_last'>
                                                                            <h3>$course_amount</h3>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class='education_block_footer'>
                                                                        <div class='education_block_author'>
                                                                            <div class='path-img'>
                                                                                <a href='#'>
                                                                                    <img src='./../assets/img/chromstack-logo.jpg' class='img-fluid' alt=''>
                                                                                </a>
                                                                            </div>
                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                        </div>
                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                        ";
                                                    //}
                                                }
                                                //List other courses bought by this person
                                                $all_course_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE buyer_email = '$email' AND purchase_status = 'Completed'");
                                                $enrolled_courses_count = mysqli_num_rows($all_course_query);
                                                if($enrolled_courses_count > 0){
                                                    while ($result = mysqli_fetch_assoc($all_course_query)) {
                                                        $course_id = $result['courseID'];
                                                        $course_type = $result['course_type'];
                                                        //Get course details
                                                        switch ($course_type) {
                                                            case 'Affiliate':
                                                                //Skip and continue to other courses
                                                            break;
                                                            case 'Admin':
                                                            case 'External':
                                                                $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM uploaded_courses WHERE courseID = '$course_id'");
                                                                if(mysqli_num_rows($course_list_query) > 0){
                                                                    //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                        $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                        $cover_page = $course_list_result['course_cover_page'];
                                                                        $course_title = $course_list_result['course_title'];
                                                                        $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                        $course_narration = $course_list_result['course_narration'];
                                                                        $folder_path = $course_list_result['folder_path'];
                                                                        $author_name = $course_list_result['course_authors'];
                                                                        $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                        //Get author profile
                                                                        if($course_type === 'Admin'){
                                                                            $author_profile_query = mysqli_query($conn, "SELECT admin_profile FROM admins WHERE fullname = '$author_name'");
                                                                            $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                            $image = $profile_data['admin_profile'];
                                                                        }
                                                                        else{
                                                                            $author_profile_query = mysqli_query($conn, "SELECT vendor_profile FROM vendors WHERE fullname = '$author_name'");
                                                                            $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                            $image = $profile_data['vendor_profile'];
                                                                        }
                                                                        //Get sales count
                                                                        $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                        $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                        if ($course_sales_count > 0) {
                                                                            $number_of_purchases = $course_sales_count . ' Purchases';
                                                                        }
                                                                        else {
                                                                            $number_of_purchases = 0 . ' Purchases';
                                                                        }
                                                                        //Check image
                                                                        if ($image !== null && $image !== '' && $image !== 'null') {
                                                                            $author_profile = './../uploads/' . $image;
                                                                        }
                                                                        else {
                                                                            $author_profile = './../assets/img/user.png';
                                                                        }

                                                                        //Output result

                                                                        if ($folder_path  === 'null') {
                                                                            echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../assets/img/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                        } else {
                                                                            echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                        }
                                                                    //}
                                                                }
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            else{
                                                //Check if this person has bought a course
                                                $all_course_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE buyer_email = '$email' AND purchase_status = 'Completed'");
                                                $enrolled_courses_count = mysqli_num_rows($all_course_query);
                                                if($enrolled_courses_count > 0){
                                                    while ($result = mysqli_fetch_assoc($all_course_query)) {
                                                        $course_id = $result['courseID'];
                                                        $course_type = $result['course_type'];
                                                        //Get course details
                                                        switch ($course_type) {
                                                            case 'Affiliate':
                                                                $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM affiliate_program_course WHERE courseID = '$course_id'");
                                                                if(mysqli_num_rows($course_list_query) > 0){
                                                                    //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                        $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                        $cover_page = $course_list_result['course_cover_page'];
                                                                        $course_title = $course_list_result['course_title'];
                                                                        $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                        $course_narration = $course_list_result['course_narration'];
                                                                        $folder_path = $course_list_result['folder_path'];
                                                                        $author_name = $course_list_result['course_authors'];
                                                                        $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                        //Get sales count
                                                                        $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                        $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                        if ($course_sales_count > 0) {
                                                                            $number_of_purchases = $course_sales_count . ' Purchases';
                                                                        }
                                                                        else {
                                                                            $number_of_purchases = 0 . ' Purchases';
                                                                        }

                                                                        //Output result

                                                                        echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='./../assets/img/chromstack-logo.jpg' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                        ";
                                                                    //}
                                                                }
                                                            break;
                                                            case 'Admin':
                                                            case 'External':
                                                                $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM uploaded_courses WHERE courseID = '$course_id'");
                                                                if(mysqli_num_rows($course_list_query) > 0){
                                                                    //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                        $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                        $cover_page = $course_list_result['course_cover_page'];
                                                                        $course_title = $course_list_result['course_title'];
                                                                        $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                        $course_narration = $course_list_result['course_narration'];
                                                                        $folder_path = $course_list_result['folder_path'];
                                                                        $author_name = $course_list_result['course_authors'];
                                                                        $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                        //Get author profile
                                                                        if($course_type === 'Admin'){
                                                                            $author_profile_query = mysqli_query($conn, "SELECT admin_profile FROM admins WHERE fullname = '$author_name'");
                                                                            $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                            $image = $profile_data['admin_profile'];
                                                                        }
                                                                        else{
                                                                            $author_profile_query = mysqli_query($conn, "SELECT vendor_profile FROM vendors WHERE fullname = '$author_name'");
                                                                            $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                            $image = $profile_data['vendor_profile'];
                                                                        }
                                                                        //Get sales count
                                                                        $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                        $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                        if ($course_sales_count > 0) {
                                                                            $number_of_purchases = $course_sales_count . ' Purchases';
                                                                        }
                                                                        else {
                                                                            $number_of_purchases = 0 . ' Purchases';
                                                                        }
                                                                        //Check image
                                                                        if ($image !== null && $image !== '' && $image !== 'null') {
                                                                            $author_profile = './../uploads/' . $image;
                                                                        }
                                                                        else {
                                                                            $author_profile = './../assets/img/user.png';
                                                                        }

                                                                        //Output result

                                                                        if ($folder_path  === 'null') {
                                                                            echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../assets/img/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                        } else {
                                                                            echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                        }
                                                                    //}
                                                                }
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        break;
                                        case 'User':
                                            //Check if this person has bought a course
                                            $all_course_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE buyer_email = '$email' AND purchase_status = 'Completed'");
                                            $enrolled_courses_count = mysqli_num_rows($all_course_query);
                                            if($enrolled_courses_count > 0){
                                                while ($result = mysqli_fetch_assoc($all_course_query)) {
                                                    $course_id = $result['courseID'];
                                                    $course_type = $result['course_type'];
                                                    //Get course details
                                                    switch ($course_type) {
                                                        case 'Affiliate':
                                                            $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM affiliate_program_course WHERE courseID = '$course_id'");
                                                            if(mysqli_num_rows($course_list_query) > 0){
                                                                //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                    $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                    $cover_page = $course_list_result['course_cover_page'];
                                                                    $course_title = $course_list_result['course_title'];
                                                                    $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                    $course_narration = $course_list_result['course_narration'];
                                                                    $folder_path = $course_list_result['folder_path'];
                                                                    $author_name = $course_list_result['course_authors'];
                                                                    $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                    //Get sales count
                                                                    $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                    $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                    if ($course_sales_count > 0) {
                                                                        $number_of_purchases = $course_sales_count . ' Purchases';
                                                                    }
                                                                    else {
                                                                        $number_of_purchases = 0 . ' Purchases';
                                                                    }

                                                                    //Output result

                                                                    echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                            <div class='education_block_grid style_2'>
                                                                                <div class='education_block_thumb n-shadow'>
                                                                                    <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_body'>
                                                                                    <h4 class='bl-title'>
                                                                                        <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                    </h4>
                                                                                </div>
                                                                                
                                                                                <div class='cources_info'>
                                                                                    <div class='cources_info_first'>
                                                                                        <ul>
                                                                                            <li><strong>$number_of_purchases</strong></li>
                                                                                            <li class='theme-cl'>$course_narration</li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class='cources_info_last'>
                                                                                        <h3>$course_amount</h3>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class='education_block_footer'>
                                                                                    <div class='education_block_author'>
                                                                                        <div class='path-img'>
                                                                                            <a href='#'>
                                                                                                <img src='./../assets/img/chromstack-logo.jpg' class='img-fluid' alt=''>
                                                                                            </a>
                                                                                        </div>
                                                                                        <h5><a href='#'>$author_name</a></h5>
                                                                                    </div>
                                                                                    <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                </div>
                                                                            </div>	
                                                                        </div>
                                                                    ";
                                                                //}
                                                            }
                                                        break;
                                                        case 'Admin':
                                                        case 'External':
                                                            $course_list_query = mysqli_query($conn, "SELECT courseID, course_type, course_title, course_cover_page, course_authors, course_amount, course_narration, uploaded_on, folder_path FROM uploaded_courses WHERE courseID = '$course_id'");
                                                            if(mysqli_num_rows($course_list_query) > 0){
                                                                //while($course_list_result = mysqli_fetch_assoc($course_list_query)){
                                                                    $course_list_result = mysqli_fetch_assoc($course_list_query);
                                                                    $cover_page = $course_list_result['course_cover_page'];
                                                                    $course_title = $course_list_result['course_title'];
                                                                    $course_amount = '$' . ($course_list_result['course_amount'] / 1000);
                                                                    $course_narration = $course_list_result['course_narration'];
                                                                    $folder_path = $course_list_result['folder_path'];
                                                                    $author_name = $course_list_result['course_authors'];
                                                                    $time_elapsed = timeAgo($course_list_result['uploaded_on']);
                                                                    //Get author profile
                                                                    if($course_type === 'Admin'){
                                                                        $author_profile_query = mysqli_query($conn, "SELECT admin_profile FROM admins WHERE fullname = '$author_name'");
                                                                        $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                        $image = $profile_data['admin_profile'];
                                                                    }
                                                                    else{
                                                                        $author_profile_query = mysqli_query($conn, "SELECT vendor_profile FROM vendors WHERE fullname = '$author_name'");
                                                                        $profile_data = mysqli_fetch_assoc($author_profile_query);
                                                                        $image = $profile_data['vendor_profile'];
                                                                    }
                                                                    //Get sales count
                                                                    $course_sales_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$course_id' AND course_type = '$course_type' AND purchase_status = 'Completed'");
                                                                    $course_sales_count = mysqli_num_rows($course_sales_query);
                                                                    if ($course_sales_count > 0) {
                                                                        $number_of_purchases = $course_sales_count . ' Purchases';
                                                                    }
                                                                    else {
                                                                        $number_of_purchases = 0 . ' Purchases';
                                                                    }
                                                                    //Check image
                                                                    if ($image !== null && $image !== '' && $image !== 'null') {
                                                                        $author_profile = './../uploads/' . $image;
                                                                    }
                                                                    else {
                                                                        $author_profile = './../assets/img/user.png';
                                                                    }

                                                                    //Output result

                                                                    if ($folder_path  === 'null') {
                                                                        echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../assets/img/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                    } else {
                                                                            echo "<div class='col-lg-4 col-md-6' style='padding-bottom: 1rem !important;'>
                                                                                <div class='education_block_grid style_2'>
                                                                                    <div class='education_block_thumb n-shadow'>
                                                                                        <a href='#'><img src='./../courses/$folder_path/$cover_page' class='img-fluid' alt=''></a>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_body'>
                                                                                        <h4 class='bl-title'>
                                                                                            <a href='course_detail.php?course_id=$course_id&course_type=$course_type&access=$access_type&accessID=$access_id&folder=$folder_path'>$course_title</a>
                                                                                        </h4>
                                                                                    </div>
                                                                                    
                                                                                    <div class='cources_info'>
                                                                                        <div class='cources_info_first'>
                                                                                            <ul>
                                                                                                <li><strong>$number_of_purchases</strong></li>
                                                                                                <li class='theme-cl'>$course_narration</li>
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class='cources_info_last'>
                                                                                            <h3>$course_amount</h3>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class='education_block_footer'>
                                                                                        <div class='education_block_author'>
                                                                                            <div class='path-img'>
                                                                                                <a href='#'>
                                                                                                    <img src='$author_profile' class='img-fluid' alt=''>
                                                                                                </a>
                                                                                            </div>
                                                                                            <h5><a href='#'>$author_name</a></h5>
                                                                                        </div>
                                                                                        <span class='education_block_time'><i class='ti-calendar mr-1'></i>$time_elapsed</span>
                                                                                    </div>
                                                                                </div>	
                                                                            </div>
                                                                            ";
                                                                        }
                                                                //}
                                                            }
                                                        break;
                                                    }
                                                }
                                            }
                                        break;
                                    }

                                ?>

							</div>
							
							<!-- Row -->

                            <!--<div class='row'>
                                <div class='col-lg-12 col-md-12 col-sm-12'>
                                    <div class='row'>
                                        <div class='col-lg-12 col-md-12 col-sm-12 text-center'>
                                            <button type='button' class='btn btn-loader'>Load More<i class='ti-reload ml-3'></i></button>
                                        </div>
                                    </div> 
                                </div>
                            </div>-->

						 <!--/Row -->
						</div>
					</div>
					<!-- Row -->
				</div>
			</section>
			<!-- ============================ Full Width Courses End ================================== -->
			
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

	</body>

</html>