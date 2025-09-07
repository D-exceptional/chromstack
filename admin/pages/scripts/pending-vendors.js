import { displayInfo } from "../scripts/export.js";

//Toggle status
function activateVendor(id) {
    const verify = confirm('Are you to activate this account ?');
    if (verify === true) {
         $.ajax({
           type: "POST",
           url: "../server/activate-vendor.php",
           data: { vendorID: id },
           dataType: "json",
           success: function (response) {
             for (var key in response) {
               if (Object.hasOwnProperty.call(response, key)) {
                 const content = response[key];
                 if (content === "Vendor status updated successfully") {
                   $(".rows").each(function () {
                     if ($(this).attr("id") == id) {
                       $(this).find(".status").text("Active");
                       $(this)
                         .find(".toggle-button button")
                         .text("Activated")
                         .attr("disabled", true);
                       //$(this).css({'display':'none'});
                     }
                   });
                   displaySuccess(content);
                 } else {
                   displayInfo(content);
                 }
               }
             }
           },
           error: function (e) {
             displayInfo(e.responseText);
           },
         });
    } else {
        $('body').css({ opacity: 1 });
    }
}

let imgCard = "";

function fetchActiveVendors() {
  $.ajax({
    type: "GET",
    url: "../server/fetch-pending-vendors.php",
    dataType: "json",
    success: function (response) {
      $("tbody").empty();
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content !== "No record found") {
            if (
              content.vendor_profile !== null &&
              content.vendor_profile !== "null"
            ) {
              imgCard = `<img alt="Avatar" class="table-avatar" src="../../../uploads/${content.vendor_profile}" style="width: 50px;height: 50px;border-radius: 50%;">`;
            } else {
              imgCard = `<img alt="Avatar" class="table-avatar" src="../../../assets/img/user.png" style="width: 50px;height: 50px;border-radius: 50%;">`;
            }

            let vendorCard = `  <tr class='rows' id='${content.vendorID}'>
                                    <td class='fullname'>${content.fullname}</td>
                                    <td>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                ${imgCard}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class='email'>${content.email}</td>
                                    <td>${content.contact}</td>
                                    <td>${content.country}</td>
                                    <td>${content.created_on}</td>
                                    <td class='status'>${content.vendor_status}</td>
                                    <td class='toggle-button'>
                                        <button class="btn btn-success btn-sm">Activate</button>
                                    </td>
                                </tr> 
                                `;

            $("tbody").append(vendorCard);
          } else {
            displayInfo(content);
          }
        }
      }

      const counter = Number($("tbody").children("tr").length);
      $(".col-sm-6 h1")
        .empty()
        .html("<b>Pending Vendors " + "(" + counter + ")");

      $("tr button").each(function (index, el) {
        $(el).on("click", function () {
          const id = $(el).parent().parent().attr("id");
          activateVendor(id);
        });
      });
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

fetchActiveVendors();

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search function
$("#page-search").on("keyup", function () {
  let searchValue = $(this).val();
  if (searchValue !== "") {
    $(".rows").each(function (index, el) {
      if (
        $(el).find(".fullname").text().toLowerCase().includes(searchValue) ||
        $(el).find(".email").text().toLowerCase().includes(searchValue)
      ) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".rows").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});
