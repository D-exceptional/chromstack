<?php

    require 'assets/server/conn.php'; 
    
    $icon = '';
    $title = '';
    $message = '';

    $name = $_GET['name'];
    $amount = $_GET['amount'];
    $status = $_GET['status'];

    switch ($status) {
        case 'success':
            $icon = "<i class='fas fa-check' style='color: green;'></i>";
            $title = "Payment Successful";
            $message = "<p>
                            Hi <b>$name</b>, 
                            <br>
                            Your payment of $amount was successful. <br>
                            Check your email for more information. <br>
                            Warm regards from the Chromstack team!
                        </p>
                    ";
        break;
        case 'failed':
            $icon = "<i class='fas fa-exclamation' style='color: red;'></i>";
            $title = "Payment Failed";
            $message = "
                        Hi <b>$name</b>, 
                        <br>
                        Your payment of $formatted_amount failed. <br>
                        Some factors may have led to this. <br>
                        Kindly retry the payment shortly.
                    ";
        break;
        case 'error':
            $icon = "<i class='fas fa-exclamation' style='color: red;'></i>";
            $title = "Payment Error";
            $message = "
                        Hi <b>$name</b>, 
                        <br>
                        Your payment of $formatted_amount could not be processed due to some error. <br>
                        Some factors may have led to this. <br>
                        Kindly retry the payment shortly.
                    ";
        break;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chromstack | Payment Status</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <!-- Favicon -->
     <link type="image/x-icon" rel="icon" href="assets/img/short-logo.png">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
    <link href="assets/css/font.css" rel="stylesheet">
    <style>
        #start{
            width: 180px;
            height: 180px;
            border-radius: 50%;
            text-align: center;
            font-size: 8rem;
            /*border-radius: 10px;*/ 
            box-shadow: 0 8px 8px 0 rgba(0, 0, 0, 0.2), 0 8px 20px 0 rgba(0, 0, 0, 0.19);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0" style="box-shadow: none !important;">
        <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
             <img src="assets/img/chromstack-logo.jpg" style="width: 60px;height: 60px;border-radius: 10%;">
            <!--<h2 class="m-0 text-primary">Chromstack</h2>-->
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="/" class="nav-item nav-link active"><b>Home</b></a>
                <a href="login" class="nav-item nav-link"><b style="border: 2px solid gray;padding: 8px;border-radius: 6px;">Login</b></a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Message Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4" style="display: flex;flex-direction: column;align-items: center;justify-content: center;">
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.3s" id="start">
                    <?php echo $icon; ?>
                </div>
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h5 style="text-align: center;"><?php echo $title; ?></h5>
                    <p class="mb-4" style="text-align: center;"> 
                        <?php echo $message; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!--  Message End -->
        
       
    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-white mb-3">Quick Link</h4>
                        <a class="btn btn-link" href="/">Home</a>
                        <a class="btn btn-link" href="#!">Tickets</a>
                        <a class="btn btn-link" href="pricing">Pricing</a>
                        <a class="btn btn-link" href="products">Products</a>
                        <a class="btn btn-link" href="about">About Us</a>
                        <a class="btn btn-link" href="contact">Contact Us</a>
                        <a class="btn btn-link" href="privacy-policy">Privacy Policy</a>
                        <a class="btn btn-link" href="terms-of-service">Terms & Condition</a>
                        <a class="btn btn-link" href="faq">FAQs & Help</a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-white mb-3">Contact</h4>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Lagos Island, Lagos, Nigeria.</p>
                        <p class="mb-2"><i class="fa fa-phone me-3" style="transform: rotate(90deg);"></i>+234 816 869 4765</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>chromstack@gmail.com</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href="https://instagram.com/chromstack?igshid=MzRIODBiNWFIZA=="><i
                                    class="fab fa-instagram"></i></a>
                            <a class="btn btn-outline-light btn-social" href="https://twitter.com/Chromstack?t=xINvz_rUVKawFvOaAjuWng&s=09"><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href="https://facebook.com/profile.php?id=61556804134821"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href="https://www.youtube.com/@Chromstack"><i
                                    class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-white mb-3">Newsletter</h4>
                        <p>Follow up with our developments by adding your email address.</p>
                        <div class="position-relative mx-auto" style="max-width: 400px;">
                            <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email" id="subscriptionEmail">
                            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2" id="addEmail">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-12 text-center mb-3 mb-md-0">
                            All Right Reserved. 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->


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
    <script src="assets/scripts/check-subscription.js" type="module"></script>
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