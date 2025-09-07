<?php 

    require 'assets/server/conn.php';
    mysqli_set_charset($conn, 'utf8');
    
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

    //Get details
    $ref = $_GET['ref'];
    $id = $_GET['id'];
    $type = $_GET['type'];
    $sales = $_GET['sales'];
    $narration = $_GET['narration'];

    $title = '';
    $amount = '';
    $image = '';
    $folder = '';
    $course_description = '';
    $raw_amount = 0;
    $author = 'Chromstack';

    //Get details
    $sql = mysqli_query($conn, "SELECT course_title, course_cover_page, course_amount, course_description, course_narration, folder_path FROM affiliate_program_course WHERE courseID = '$id'");
    $row = mysqli_fetch_assoc($sql);
    $title = $row['course_title'];
    $amount = '$' . ($row['course_amount'] / 1000);
    $folder = $row['folder_path'];
    $image = './courses/' . $folder . '/' . $row['course_cover_page'];
    $course_description = $row['course_description'];
    $raw_amount = $row['course_amount'];
    $course_narration = $row['course_narration'];
    
    // Function to generate a random alphanumeric code
    function generateReference($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return 'TXN' . $code . 'mcp'; // Reference pattern for main course purchase. Tables involved are `affiliate_course_sales` and `affiliate_course_sales_backup`
    }
    
    $payment_reference = generateReference();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chromstack | Buy Product</title>
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
  <link href="<?php echo getCacheBustedUrl('assets/css/style.css'); ?>" rel="stylesheet">
  <link href="<?php echo getCacheBustedUrl('assets/css/overlay.css'); ?>" rel="stylesheet">
  <style>
    html, body{
        overflow-x: hidden !important;
    }
    ::-webkit-scrollbar {
        width: 5px;
        border-radius: 20px;
        background: #094e41;
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
            <h2 class="m-0 text-primary">Product | Buy</h2>
        </a>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header" style="background-image: url(<?php echo $image; ?>);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown" id="course-title" style="color: transparent !important;"><?php echo $title; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <!--<li class="breadcrumb-item"><a class="text-white" href="#"></a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#"></a></li>-->
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="row g-4" style='display:flex;flex-direction:row;align-items:center;justify-content:center;'>
        <div class="container">
            <div class="text-center">
                <h1 class="mb-5" style="color: #181d38 !important;">Buy <br> <?php echo $title; ?></h1>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
            <form id="course-purchase-form">
                <div class="row g-3" style='padding: 0% 2%;'>
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-12" id="fullname-div">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Your Name">
                            <label for="name">Fullname</label>
                        </div>
                    </div>
                    <div class="col-md-12" id="country-div">
                        <div class="form-floating">
                            <select name="user_country" id="user-country" class="form-control" style='border: 1px solid #ced4da !important;background: white;'></select>
                            <label for="user_country">Country</label>
                        </div>
                    </div>
                    <div class="col-12" id="contact-div">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                            <label for="subject">Contact</label>
                        </div>
                    </div>
                    <p>By signing up, I agree to the <a href="terms-of-service">Terms</a> and <a href="privacy-policy">Privacy Policy</a></p>
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="button" id="proceed" style="margin-bottom: 20px;">Get Started</button>
                        <!--<button class="btn btn-primary w-100 py-3" type="button" id="pay" style="margin-bottom: 20px;">Get Started</button>-->
                    </div>
                </div>
                <p id="status" style="display: none;"></p>
                <p id="payment-reference" style="display: none;"><?php echo $payment_reference; ?></p>
                <p id="raw-course-amount" style="display: none;"><?php echo $raw_amount; ?></p>
                <p id="course-sales-type" style="display: none;"><?php echo $sales; ?></p>
                <p id="course-sales-narration" style="display: none;"><?php echo $narration; ?></p>
                <p id="course-type" style="display: none;"><?php echo $type; ?></p>
                <p id="ref-id" style="display: none;"><?php echo $ref; ?></p>
                <p id="courseID" style="display: none;"><?php echo $id; ?></p>
            </form>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div id='payment-modal-overlay'>
        <div class="row g-4">
            <div class="container">
                <div class="text-center">
                    <h1 class="mb-5">Make Payment</h1>
                    <div>
                        <img src='' alt='logo' id='bankLogo'>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 wow fadeInUp" data-wow-delay="0.5s" id="paymentDetails">
                <form id="payment-form">
                    <div class="row g-3" style='padding: 0% 2%;'>
                        <center>
                            <p id='pay-text' style='width: 100%;padding: 2%;'></p>  
                        </center>
                        <div class="col-md-12" id='ref-div' style='display: none'>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="reference" name="reference" placeholder="Enter Payment Reference">
                                <label for="reference">Payment Reference</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="button" id="show-continue" style="color: white;">I have paid</button>
                            <button class="btn btn-primary w-100 py-3" type="button" id="hide-continue" style="color: white;display: none;">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id='payment-overlay-close'>X</div>
    </div>

    <!-- Payment Modal End -->

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
    <!--Use this script while we sort out a payment processor ---->
    <script src="<?php echo getCacheBustedUrl('assets/scripts/buy-main-course.js'); ?>" type="module"></script>
    <!-- Interswitch Payment --
    <script src="https://newwebpay.interswitchng.com/inline-checkout.js"></script>
    <!--<script src="assets/scripts/purchase-course.js" type="module"></script>-->
    <!-- Completely disable autofill using these two files -->
    <script src="https://cdn.jsdelivr.net/npm/disableautofill/src/jquery.disableAutoFill.min.js"></script> 
    <script>
        $(document).ready(function () {
            $("input").attr("autocomplete", "new-password");
            $('#course-purchase-form').disableAutoFill();
        });
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LJMVMMV3RP"></script>
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