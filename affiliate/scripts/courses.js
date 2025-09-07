import { displayInfo, displaySuccess } from "../scripts/export.js";

//URL shortening
function shortenLink(link) {
  const textInput = document.createElement("input");
  textInput.setAttribute("type", "text");
  textInput.setAttribute("value", link);
  textInput.setAttribute("hidden", true);
  textInput.select();
  textInput.setSelectionRange(0, 99999);
  const shareLink = textInput.value;
  navigator.clipboard.writeText(shareLink);
  displaySuccess("Link copied");
}

const affiliateID = $("body").attr("id");

function fetchCourses() {
  $.ajax({
    type: "GET",
    url: "../server/courses.php",
    data: { id: affiliateID, type: "Affiliate" },
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content !== "No course found") {
            let USDAmount = content.course_amount / 1000;
            let coverPage = "";

            if (content.folder_path !== "null") {
              coverPage = `<img src='../../courses/${content.folder_path}/${content.course_cover_page}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>`;
            } else {
              coverPage = `<img src='../../assets/img/${content.course_cover_page}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>`;
            }

            let courseLists = `  <tr class='uploaded-course-list' id='${content.courseID}'>
                                    <td class='course-title'>${content.course_title}</td>
                                    <td>
                                        ${coverPage}
                                    </td>
                                    <td class='course-description'>${content.course_description}</td>
                                    <td class='author'>${content.course_authors}</td>
                                    <td class='amount'>$${USDAmount}</td>
                                    <td>${content.affiliate_percentage}</td>
                                    <td class='sales-type-row'>
                                        <button class="btn btn-info btn-sm" style='width: 80px !important;margin-bottom: 8px;'>Sales</button>
                                        <button class="btn btn-success btn-sm share" style='width: 80px !important;margin-bottom: 8px;'>Link</button> 
                                        <button class="btn btn-info btn-sm" style='width: 60px !important;margin-bottom: 8px;'>Details</button>
                                        <input type='text' class='course-type' value='${content.course_type}' hidden />
                                        <input type='text' class='course-image-data' value='courses/${content.folder_path}/${content.course_cover_page}' hidden />
                                        <input type='text' class='short-link' value='${content.short_link}' hidden />
                                    </td>
                                </tr>
                            `;

            $("tbody").append(courseLists);
          } else {
            //displayInfo(content);
            $("tbody").css({ opacity: 1 });
          }
        }
      }

      $(".sales-type-row")
        .children("button")
        .each(function (index, el) {
          $(el).on("click", function () {
            const courseID = $(el).parent().parent().attr("id");
            const courseType = $(el).parent().find(".course-type").val();
            const buttonText = $(el).text();
            switch (buttonText) {
              case "Sales":
                window.location = `../views/affiliate-course-sales.php?courseID=${courseID}&type=${courseType}&sales-type=Affiliate`;
                break;
              case "Link":
                const link = $(el).parent().find(".short-link").val();
                shortenLink(link);
                break;
              case "Details":
                $("#full-details-overlay").css({ display: "block" });
                $("#full-details-overlay iframe").attr(
                  "src",
                  `../../details.php?id=${courseID}&type=${courseType}`
                );
                break;
            }
          });
        });
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

fetchCourses();

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search function
$("#page-search").on("keyup", function () {
  let searchValue = $(this).val();
  if (searchValue !== "") {
    $(".uploaded-course-list, .main-course-list").each(function (index, el) {
      if (
        $(el).find(".course-title").text().toLowerCase().includes(searchValue)
      ) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".uploaded-course-list, .main-course-list").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});

