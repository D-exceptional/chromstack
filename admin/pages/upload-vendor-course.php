<?php 
  require './server/conn.php';
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
    <meta charset="utf-8">
    <title>Chromstack | Upload Product</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <!-- Favicon -->
     <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">

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
</head>

<body>
    <!-- Spinner Start --
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>-->
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0" style='box-shadow: none !important;'>
        <a href="./index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary">Chromstack | Upload Product</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
             <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="./index.php" class="nav-item nav-link">Home</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Upload Product</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Product</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Upload Product</h6>
                <h1 class="mb-5">Product Details</h1>
            </div>
            <div class="row g-4">
            <!--<div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s"> </div>-->
                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <img class="position-relative rounded w-100 h-100" src="../../assets/img/about-1.jpg" style="min-height: 300px; border:0;overflow: hidden !important;" />
                </div>
                <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
                    <form id="course-upload-form">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="fullname" id='fullname' placeholder="Fullname">
                                    <label for="fullname">Fullname</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id='email' placeholder="Email">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="contact" id='contact' placeholder="Contact">
                                    <label for="contact">Contact</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="title" id='title' placeholder="Title">
                                    <label for="title">Product Title</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description" id='description' placeholder="Description"></textarea>
                                    <label for="description">Product Description</label>
                                </div>
                            </div>
                               
                            <div class="col-12">
                                <div class="form-floating" id='file-click' style='height: 50px;padding-top: 10px;padding-left: 10px;border: 1px solid #ced4da;color: #52565b;font-size: 1rem;font-weight: 400;line-height: 1.5;cursor: pointer;'>
                                    <p>Attach file (Images Only)</p>
                                </div>
                                <input type="file" class="form-control" name="cover_image" id='cover-image' hidden>
                            </div>

                            <p class="mb-4" style="margin-top: 20px;display: none;" id='course-file-name'></p>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="amount" id='amount' placeholder="Amount">
                                    <label for="amount">Product Amount</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="page" id='page' placeholder="Sales Page URL">
                                    <label for="page">Sales Page URL</label>
                                </div>
                            </div>
                             <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="product_input" placeholder="Product Type" disabled>
                                    <label for="product_input">Product Type</label>
                                    <select class="col-12" style='height: 40px;margin-top: 20px;' name='product_type' id='product-type'>
                                        <option value='Digital Course'>Digital Course</option>
                                        <option value='e-Book'>e-Book</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="category_input" placeholder="Category" disabled>
                                    <label for="category_input">Product Category</label>
                                    <select class="col-12" style='height: 40px;margin-top: 20px;' name='category' id='category'>
                                        <?php
                                             $query = mysqli_query($conn, "SELECT * FROM course_category ORDER BY category_name ASC");
                                                if(mysqli_num_rows($query) > 0){ 
                                                    while ($row = mysqli_fetch_assoc($query)) {
                                                        $category = $row['category_name'];
                                                        echo "<option value='$category'>$category</option>";
                                                    }
                                                }
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="commission_input" placeholder="Commission" disabled>
                                    <label for="commission">Affiliate Commission</label>
                                    <select class="col-12" style='height: 40px;margin-top: 20px;' name='affiliate_commission' id='commission'></select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit" id="upload">Upload Product</button>
                            </div>

                            <div class="col-12" id="uploadBar" style="display: none;height: 40px;background: #eff1f3;">
                                <div id="progressBar"
                                    style="background: green;color: white;text-align: center;transition: 0.5s;width: 0%;height: 100%;display: flex;flex-direction: row;align-items: center;justify-content: center;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

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
    <script src="<?php echo getCacheBustedUrl('scripts/upload-vendor-course.js'); ?>" type="module"></script>
</body>

</html>