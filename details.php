<?php 
    require 'assets/server/conn.php'; 
    mysqli_set_charset($conn, 'utf8');

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $type = mysqli_real_escape_string($conn, $_GET['type']);
   
    $title = '';
    $amount = '';
    $author = '';
    $cover_page = '';
    $course_description = '';

    switch ($type) {
        case 'Affiliate':
            $sql = mysqli_query($conn, "SELECT course_cover_page, course_title, course_authors, course_amount, course_description, folder_path FROM affiliate_program_course");
            $row = mysqli_fetch_assoc($sql);
            $image = $row['course_cover_page'];
            $title = $row['course_title'];
            $author = $row['course_authors'];
            $amount = '$' . ($row['course_amount'] / 1000);
            $course_description = $row['course_description'];
            $folder_path = $row['folder_path'];
            $cover_page = '../../courses/' . $folder_path . '/' . $image;
        break;
        case 'Admin':
        case 'External':
            $sql = mysqli_query($conn, "SELECT course_cover_page, course_title, course_authors, course_amount, course_description, folder_path FROM uploaded_courses WHERE courseID = '$id'");
            $row = mysqli_fetch_assoc($sql);
            $image = $row['course_cover_page'];
            $title = $row['course_title'];
            $author = $row['course_authors'];
            $amount = '$' . ($row['course_amount'] / 1000);
            $course_description = $row['course_description'];
            $folder_path = $row['folder_path'];
            if($folder_path !== 'null'){
               $cover_page = '../../courses/' . $folder_path . '/' . $image;
            }
            else{
                 $cover_page = 'assets/img/' . $image;
            }
        break;
    }
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Product | Details</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
     <link type="image/x-icon" rel="icon" href="assets/img/short-logo.png">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="assets/resources/font-awesome-5.1.0.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="assets/lib/animate/animate.min.css" rel="stylesheet">
  <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
   html{
       overflow-x: hidden !important;
   }

    ::-webkit-scrollbar {
        width: 10px;
        border-radius: 20px;
        background: #094e41;
    }
    .img-fluid {
        width: 100vw;
    }
    #course-description{
        text-align: left !important;
        background: transparent;
        color: #181d38;
        font-size: 16px;
        /*font-family: 'Outfit', sans-serif;*/
        margin: 0;
        overflow-x: hidden !important;
        font-weight: 400;
        padding: 2%;
    }
  </style>
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0" style="box-shadow: none !important;">
        <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary">
            Product | Details
            </h2>
        </a>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown"><?php echo $title; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Product</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Details</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Courses Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Product Details</h6>
                <h1 class="mb-5"><?php echo $title; ?></h1>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-9 col-md-9 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="course-item bg-light">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="<?php echo $cover_page; ?>" alt="" style="width: 100% !important;">
                        </div>
                        <div class="text-center p-4 pb-0">
                            <h3 class="mb-0"><?php echo $amount; ?></h3>
                            <div class="mb-3">
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                            </div>
                            <h5 class="mb-4"><?php echo $title; ?></h5>
                            <p id='course-description'><?php echo $course_description; ?></p>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2">
                                <i class="fa fa-user text-primary me-2"></i>
                                <?php echo $author; ?>
                            </small>
                            <small class="flex-fill text-center py-2">
                                 <i class="fa fa-shopping-cart text-primary me-2"></i>
                                <?php
                                     $query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$id' AND course_type = '$type' AND purchase_status = 'Completed'");
                                     $salesCount = mysqli_num_rows($query);
                                     echo $salesCount;
                                 ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Courses End -->

    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonial</h6>
                <h1 class="mb-5">Our Students Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative" id="reviewsList">
                <?php 
                    $query = mysqli_query($conn, "SELECT * FROM reviews WHERE courseID = '$id' AND course_type = '$type'");
                    if(mysqli_num_rows($query) > 0){ 
                        while ($row = mysqli_fetch_assoc($query)) {
                            //Display review carousel
                            $profile = $row['profile'];
                            $name = $row['fullname']; 
                            $review = $row['review_comment'];
                            $type = 'Student';

                            if ($profile !== 'null' && $profile !== null && $profile !== '') {
                                $profile_image = 'uploads/'. $profile;
                            } else {
                                 $profile_image = 'assets/img/user.png';
                            }
                            
                             echo " <div class='testimonial-item text-center'>
                                        <img class='border rounded-circle p-2 mx-auto mb-3' src='$profile_image' style='width: 80px; height: 80px;'>
                                        <h5 class='mb-0'>$name</h5>
                                        <p>$type</p>
                                        <div class='testimonial-text bg-light text-center p-4'>
                                            <p class='mb-0' style='color: #181d38 !important;'>$review</p>
                                        </div>
                                    </div>
                                ";
                        }
                    }
                    else{
                        echo "<script>
                                // Get the div element
                                const myDiv = document.getElementById('reviewsList');

                                // Clear the existing content
                                myDiv.innerHTML = '';

                                //Set a style
                                myDiv.style.textAlign = 'center';

                                // Create a new text node
                                const newText = document.createTextNode('No reviews yet!');

                                // Append the text node to the div
                                myDiv.appendChild(newText);
                                
                              </script>
                             ";
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="assets/scripts/jquery-1.11.1.min.js"></script>
    <script src="assets/scripts/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/wow/wow.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
</body>

</html>