<?php 

    require '../server/conn.php';

    //Get incoming id
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    //Global variables
    $title = '';
    $course_id = '';
    $course_type = '';
    $start_date = '';
    $end_date = '';

    //Get sales statistics
    $sql = mysqli_query($conn, "SELECT contest_title, courseID, course_type, contest_start_date, contest_end_date FROM contest WHERE contestID = '$id'");
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
        $title = $row['contest_title'];
        $course_id = $row['courseID'];
        $course_type = $row['course_type'];
        $start_date = $row['contest_start_date'];
        $end_date = $row['contest_end_date'];
    }
    
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chromstack | Leaderboard</title>
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
  <link href="../../assets/resources/font-awesome-5.1.0.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="../../assets/lib/animate/animate.min.css" rel="stylesheet">
  <link href="../../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <style>
   html{
       overflow-x: hidden !important;
   }

    ::-webkit-scrollbar {
        width: 5px;
        border-radius: 20px;
        background: #094e41;
    }
  </style>
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0" style="box-shadow: none !important;">
        <a href="#" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary">
                <!--<i class="fa fa-arrow-left me-3" id='close-view'></i>--> Contest | Leaderboard
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
                            <li class="breadcrumb-item"><a class="text-white" href="#">Contest</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Leaderboard</a></li>
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
                <h6 class="section-title bg-white text-center text-primary px-3">Sales Statistics</h6>
                <h1 class="mb-5"><?php echo $title; ?></h1>
            </div>
            <!--<div class="row g-4 justify-content-center">-->
                <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
                    <table class="table table-striped projects" style='opacity: 1 !important;'>
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Fullname</th>
                                <th>Country</th>
                                <th>Sales</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                //Earnings variables 
                                $total_earnings = 0;
                                $total_payout_amount = 0;
                                $total_payout_amount_in_usd = 0;

                                switch ($course_type) {
                                    case 'Affiliate':
                                        $query = mysqli_query($conn, "SELECT sellerID, SUM(sales_amount) AS affiliate_unique_commission FROM affiliate_course_sales_backup WHERE courseID = '$course_id' AND sales_narration = 'Contest' AND sales_type = 'Affiliate' AND sales_status = 'Completed' AND sales_date BETWEEN '$start_date' AND '$end_date' GROUP BY sellerID ORDER BY affiliate_unique_commission DESC");
                                        if(mysqli_num_rows($query) > 0){ 
                                            while ($row = mysqli_fetch_assoc($query)) {
                                                $sellerID = $row['sellerID'];
                                                //Calculate earnings
                                                $calculated_affiliate_commission = intval($row['affiliate_unique_commission']);
                                                //Get fixed affiliate commission
                                                $sql = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course WHERE courseID = '$course_id'");
                                                $result = mysqli_fetch_assoc($sql);
                                                $fixed_affiliate_commission = intval(substr($result['affiliate_percentage'], 0, -1));
                                                $total_earnings = $calculated_affiliate_commission;
                                                //Total sales
                                                $sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE courseID = '$course_id' AND sellerID = '$sellerID' AND sales_narration = 'Contest' AND sales_type = 'Affiliate' AND sales_status = 'Completed' AND sales_date BETWEEN '$start_date' AND '$end_date'");
                                                //Total payout amount
                                                $total_payout_amount = number_format(($total_earnings), 2, '.', ',');
                                                $total_payout_amount_in_usd = number_format(((($total_earnings) / (1000))), 2, '.', ',');
                                                //Get details
                                                $name_sql = mysqli_query($conn, "SELECT affiliate_profile, fullname, email, country FROM affiliates WHERE affiliateID = '$sellerID'");
                                                $name_sql_result = mysqli_fetch_assoc($name_sql);
                                                $profile = $name_sql_result['affiliate_profile'];
                                                $fullname = $name_sql_result['fullname'];
                                                $country = $name_sql_result['country'];
                                                $total_sales = mysqli_num_rows($sales_query);
                                                //Get profile image
                                                if ($profile !== 'null' && !empty($profile)) {
                                                    $image = '../../uploads/' . $profile;
                                                }
                                                else {
                                                    $image = '../../assets/img/user.png';
                                                }
                                                //Display results
                                                echo "
                                                    <tr class='rows'>
                                                        <td>
                                                            <img src='$image' style='width: 50px;height: 50px;border-radius: 50%;'>
                                                        </td>
                                                        <td class='fullname'>$fullname</td>
                                                        <td class='country'>$country</td>
                                                        <td class='total'>$total_sales</td>
                                                        <td class='amount'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                                                    </tr>
                                                ";
                                            }
                                        }
                                        else{
                                            echo "<center><p>Sales statistics not available</p></center>";
                                        }  
                                    break;
                                    case 'Admin':
                                    case 'External':
                                        $query = mysqli_query($conn, "SELECT sellerID, SUM(sales_amount) AS affiliate_unique_commission FROM uploaded_course_sales_backup WHERE courseID = '$course_id' AND sales_narration = 'Contest' AND sales_type = 'Affiliate' AND sales_status = 'Completed' AND sales_date BETWEEN '$start_date' AND '$end_date' GROUP BY sellerID ORDER BY affiliate_unique_commission DESC");
                                        if(mysqli_num_rows($query) > 0){ 
                                            while ($row = mysqli_fetch_assoc($query)) {
                                                $sellerID = $row['sellerID'];
                                                //Calculate earnings
                                                $calculated_affiliate_commission = intval($row['affiliate_unique_commission']);
                                                //Get fixed affiliate commission
                                                $sql = mysqli_query($conn, "SELECT affiliate_percentage FROM uploaded_courses WHERE courseID = '$course_id'");
                                                $result = mysqli_fetch_assoc($sql);
                                                $fixed_affiliate_commission = intval(substr($result['affiliate_percentage'], 0, -1));
                                                $total_earnings = $calculated_affiliate_commission;
                                                //Total sales
                                                $sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE courseID = '$course_id' AND sellerID = '$sellerID' AND sales_narration = 'Contest' AND sales_type = 'Affiliate' AND sales_status = 'Completed' AND sales_date BETWEEN '$start_date' AND '$end_date'");
                                                //Total payout amount
                                                $total_payout_amount = number_format(($total_earnings), 2, '.', ',');
                                                $total_payout_amount_in_usd = number_format(((($total_earnings) / (1000))), 2, '.', ',');
                                                //Get details
                                                $name_sql = mysqli_query($conn, "SELECT affiliate_profile, fullname, email, country FROM affiliates WHERE affiliateID = '$sellerID'");
                                                $name_sql_result = mysqli_fetch_assoc($name_sql);
                                                $profile = $name_sql_result['affiliate_profile'];
                                                $fullname = $name_sql_result['fullname'];
                                                $country = $name_sql_result['country'];
                                                $total_sales = mysqli_num_rows($sales_query);
                                                //Get profile image
                                                if ($profile !== 'null' && !empty($profile)) {
                                                    $image = '../../uploads/' . $profile;
                                                }
                                                else {
                                                    $image = '../../assets/img/user.png';
                                                }
                                                //Display results
                                                echo "
                                                    <tr class='rows'>
                                                        <td>
                                                           <img src='$image' style='width: 50px;height: 50px;border-radius: 50%;'>
                                                        </td>
                                                        <td class='fullname'>$fullname</td>
                                                        <td class='country'>$country</td>
                                                        <td class='total'>$total_sales</td>
                                                        <td class='amount'>&#x20A6 $total_payout_amount / $$total_payout_amount_in_usd</td>
                                                    </tr>
                                                ";
                                            }
                                        }
                                        else{
                                            echo "<center><p>Sales statistics not available</p></center>";
                                        }  
                                    break;
                                }

                            ?>
                        </tbody>
                    </table>
                </div>
            <!--</div>-->
        </div>
    </div>
    <!-- Courses End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="../../assets/scripts/jquery-1.11.1.min.js"></script>
    <script src="../../assets/scripts/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/wow/wow.min.js"></script>
    <script src="../../assets/lib/easing/easing.min.js"></script>
    <script src="../../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../../assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="../../assets/js/main.js"></script>
</body>

</html>