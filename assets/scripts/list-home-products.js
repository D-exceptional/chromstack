import { displayInfo } from "../scripts/export.js";

function listCatgories() {
  $.ajax({
    type: "GET",
    url: "./assets/server/list-categories.php",
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content !== "No category found") {
            const category = `<option value='${content.category}'>${content.category}</option> `;
            $("#category-filter").append(category);
          }
        }
      }
      //Append `All` to show all products again
      const allFilter = `<option value='All'>All</option>`;
      $("#category-filter").prepend(allFilter);
    },
    error: function () {
      displayInfo("Error connecting to server");
    },
  });
}

listCatgories();


function listProducts() {
  $("tbody").empty();
  $.ajax({
    type: "GET",
    url: "./assets/server/list-home-products.php",
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content) {
            const mainCourse = content.main;
            const vendorCourses = content.vendor;

            //List main course
            $.each(mainCourse, function (index, course) {
              let detailsLink = "";
              if (course.sales_page !== "null") {
                detailsLink = course.sales_page;
              } else {
                detailsLink = `details.php?id=${course.courseID}&type=${course.course_type}`;
              }
              const price = course.course_amount / 1000;
              const image = `../../courses/${course.folder_path}/${course.course_cover_page}`;
              const purchaseLink = course.short_link;
              let productCard = `<div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="course-item bg-light">
                                                    <div class="position-relative overflow-hidden">
                                                        <img class="img-fluid" src="${image}" alt="">
                                                        <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                                            <!--<a href="${detailsLink}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 30px;color: #181d38;background: white !important;border: 2px solid #181d38;">Learn More</a>-->
                                                        </div>
                                                    </div>
                                                    <div class="text-center p-4 pb-0">
                                                        <h3 class="mb-0">$${price}</h3>
                                                        <div class="mb-3">
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small>(${course.reviews})</small>
                                                        </div>
                                                        <h5 class="mb-4">${course.course_title}</h5>
                                                    </div>
                                                    <div class="d-flex border-top">
                                                        <small class="flex-fill text-center border-end py-2" style="color: red !important;"><i class="fa fa-file text-primary me-2"></i>${course.course_narration}</small>
                                                        <span class='product-category' style='display: none;'>${course.course_category}</span>
                                                        <span class='product-price' style='display: none;'>${course.course_amount}</span>
                                                        <!--<small class="flex-fill text-center border-end py-2"><i class="fa fa-bars text-primary me-2"></i>${course.course_category}</small>
                                                        <small class="flex-fill text-center border-end py-2">
                                                            <i class="fa fa-shopping-cart text-primary me-2"></i>
                                                            ${course.buyers}
                                                        </small>-->
                                                        <small class="flex-fill text-center py-2">
                                                            <button class="btn btn-success btn-sm">
                                                                 <i class="fa fa-shopping-cart text-primary me-2" style='color: white;'></i>
                                                                <a href='${purchaseLink}' style='color: white;text-decoration: none;'>Get Started</a>
                                                            </button>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

              $("#product-list").append(productCard);
            });

            //List other courses
            $.each(vendorCourses, function (index, course) {
              let coverPage = "";
              let iconClass = "";
              let detailsLink = "";

              //Define links
              const purchaseLink = course.short_link;
              const price = course.course_amount / 1000;

              if (course.folder_path !== "null") {
                coverPage = `../../courses/${course.folder_path}/${course.course_cover_page}`;
              } else {
                coverPage = `../../assets/img/${course.course_cover_page}`;
              }

              if (course.sales_page !== "null") {
                detailsLink = course.sales_page;
              } else {
                detailsLink = `details.php?id=${course.courseID}&type=${course.course_type}`;
              }

              if (course.course_category == "Digital Course") {
                iconClass = `<i class="fa fa-file text-primary me-2"></i>`;
              } else {
                iconClass = `<i class="fa fa-bars text-primary me-2"></i>`;
              }

              //List courses
              let productCard = `<div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="course-item bg-light">
                                                    <div class="position-relative overflow-hidden">
                                                        <img class="img-fluid" src="${coverPage}" alt="">
                                                        <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                                            <!--<a href="${detailsLink}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 30px;color: #181d38;background: white !important;border: 2px solid #181d38;">Learn More</a>-->
                                                        </div>
                                                    </div>
                                                    <div class="text-center p-4 pb-0">
                                                        <h3 class="mb-0">$${price}</h3>
                                                        <div class="mb-3">
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small>(${course.reviews})</small>
                                                        </div>
                                                        <h5 class="mb-4">${course.course_title}</h5>
                                                    </div>
                                                    <div class="d-flex border-top">
                                                        <small class="flex-fill text-center border-end py-2" style="color: red !important;">${iconClass} ${course.course_narration}</small>
                                                        <span class='product-category' style='display: none;'>${course.course_category}</span>
                                                        <span class='product-price' style='display: none;'>${course.course_amount}</span>
                                                        <!--<small class="flex-fill text-center border-end py-2"><i class="fa fa-bars text-primary me-2"></i>${course.course_category}</small>
                                                        <small class="flex-fill text-center border-end py-2">
                                                            <i class="fa fa-shopping-cart text-primary me-2"></i>
                                                            ${course.buyers}
                                                        </small>-->
                                                        <small class="flex-fill text-center py-2">
                                                            <button class="btn btn-success btn-sm">
                                                                <i class="fa fa-shopping-cart text-primary me-2" style='color: white;'></i>
                                                                <a href='${purchaseLink}' style='color: white;text-decoration: none;'>Buy Now</a>
                                                            </button>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

              $("#product-list").append(productCard);
            });
          }
        }
      }

      $(".flex-shrink-0").each(function (index, el) {
        $(el).on("click", function (e) {
          e.preventDefault();
          const detailsUrl = $(el).attr("href");
          $("#full-details-overlay").css({ display: "block" });
          $("#full-details-overlay iframe").attr("src", detailsUrl);
        });
      });
    },
    error: function () {
      displayInfo("Error connecting to server");
    },
  });
}

listProducts();

//Close overlay
$("#close-view").on("click", function () {
  $("#full-details-overlay").css({ display: "none" });
  $("#full-details-overlay iframe").attr("src", "");
});

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search product
$("#product-search").on("keyup", function () {
  let searchValue = $(this).val().toLowerCase();
  if (searchValue !== "") {
    $(".col-lg-4.col-md-6.wow.fadeInUp").each(function (index, el) {
      if ($(el).find(".mb-4").text().toLowerCase().includes(searchValue)) {
        $(el).css({ display: "flex" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".col-lg-4.col-md-6.wow.fadeInUp").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "flex" });
      }
    });
  }
});

//Filter by category
$("#category-filter").on("change", function () {
  const category = $(this).val();
  if (category === 'All') {
    $(".col-lg-4.col-md-6.wow.fadeInUp").each(function (index, el) {
      $(el).css({ display: "flex" });
    });
  } 
  else {
    $(".col-lg-4.col-md-6.wow.fadeInUp").each(function (index, el) {
      if ($(el).find(".product-category").text() === category) {
        $(el).css({ display: "flex" });
      } else {
        $(el).css({ display: "none" });
        if ($("#product-list").children('div').length === 0) {
          $("#product-list").html(
            '<center><h6 class="section-title bg-white text-center text-primary px-3" style="color: black !important;">No product is available for this category</h6></center>'
          );
        }
      }
    });
  }
});

