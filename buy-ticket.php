<?php 

    require 'assets/server/conn.php'; 
    mysqli_set_charset($conn, 'utf8');

    //Get details
    $ticketID = $_GET['ticketID'];
    
    $banner = '';
    $title = '';
    $description = '';
    $organizer = '';
    
    $sql = mysqli_query($conn, "SELECT *  FROM tickets WHERE ticketID = '$ticketID'");
    $row = mysqli_fetch_assoc($sql);
    $banner = $row['banner'];
    $title = ucwords($row['title']);
    $description = $row['event_description'];
    $ownerID = $row['ownerID'];
    //Get organizer's name
    $query = mysqli_query($conn, "SELECT fullname FROM ticket_owners WHERE ownerID = '$ownerID'");
    $result = mysqli_fetch_assoc($query);
    $organizer = $result['fullname'];
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Chromstack | Buy Ticket</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link type="image/x-icon" rel="icon" href="assets/img/short-logo.png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap"
        rel="stylesheet">

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
        html,
        body {
            overflow-x: hidden !important;
        }

        ::-webkit-scrollbar {
            width: 5px;
            border-radius: 20px;
            background: #094e41;
        }

        #course-description {
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
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0"
        style="box-shadow: none !important;">
        <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary">Chromstack | Buy Ticket</h2>
        </a>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown" id="course-title">
                        <?php echo $title; ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Ticket</a></li>
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
                <h6 class="section-title bg-white text-center text-primary px-3">Ticket Details</h6>
                <h1 class="mb-5"><b id="quoted-course-title">
                        <?php echo $title; ?>
                    </b></h1>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="course-item bg-light">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="../tickets/<?php echo $banner; ?>" alt="ticket image"
                                style='width: 100% !important;'>
                        </div>
                        <div class="text-center p-4 pb-0">
                            <h1 class="mb-4">
                                <?php echo $title; ?>
                            </h1>
                            <h4 class="mb-4">
                                (<?php echo $organizer; ?>)
                            </h4>
                            <div class="mb-3">
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                            </div>
                            <p id='description'>
                                <?php echo $description; ?>
                            </p>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2">
                                <i class="fa fa-user text-primary me-2"></i>
                                <?php echo $organizer; ?>
                            </small>
                            <small class="flex-fill text-center py-2">
                                <i class="fa fa-shopping-cart text-primary me-2"></i>
                                <?php
                                    $query = mysqli_query($conn, "SELECT * FROM ticket_sales WHERE ticketID = '$ticketID' AND sales_status = 'Completed'");
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


    <div class="row g-4" style='display:flex;flex-direction:row;align-items:center;justify-content:center;'>
        <div class="container">
            <div class="text-center">
                <h1 class="mb-5">Buy Ticket</h1>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
            <form id="ticket-purchase-form">
                <div class="row g-3" style='padding: 0% 2%;'>
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                placeholder="Your Name">
                            <label for="name">Fullname</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating">
                            <select name="country" id="country" class="form-control"
                                style='border: 1px solid #ced4da !important;background: white;'>
                                <option value="Nigeria" selected>Nigeria</option>
                            </select>
                            <label for="country">Country</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact">
                            <label for="subject">Contact</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount">
                            <label for="amount">Amount</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="button" id="buy-ticket"
                            style="margin-bottom: 20px;">Buy Now</button>
                            <input type="number" class="form-control" id="ticketID" value="<?php echo $ticketID; ?>" hidden>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
    <script src="assets/scripts/buy-ticket.js" type="module"></script>
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