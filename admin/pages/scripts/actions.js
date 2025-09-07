import { displayInfo, displaySuccess } from "../scripts/export.js";

function updatePayment(id, name, data) {
    const verify = confirm(`Are you sure to update ${name}'s payment ?`);
    if (verify === true) {
         $.ajax({
           type: "POST",
           url: "../server/update-payment.php",
           data: data,
           dataType: "json",
           success: function (response) {
             for (var key in response) {
               if (Object.hasOwnProperty.call(response, key)) {
                 const content = response[key];
                 if (content === "Payment updated successfully") {
                   $(".rows").each(function () {
                     if ($(this).attr("id") === id) {
                       //Update status
                       $(this)
                         .find(".status")
                         .children("button")
                         .removeClass("btn btn-danger btn-sm")
                         .addClass("btn btn-success btn-sm")
                         .text("Completed");
                       //Update text
                       $(this).find(".action").children("button").text("View");
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

$(".action button").each(function (index, el) {
    $(el).on("click", function () {
        const paymentID = $(el).parent().parent().attr("id");
        const name = $(el).parent().parent().find(".fullname").text();
        const email = $(el).parent().parent().find(".email").text();
        const type = $(el).parent().parent().find(".type").text();
        const track = $(el).parent().parent().find(".track").text();
        const reference = $(el).parent().parent().find(".reference").text();
        const payload = {
          fullname: name,
          email: email,
          type: type,
          track: track,
          reference: reference
        };
        if ($(el).text() === "Update") {
            updatePayment(paymentID, name, payload);
        }
    });
});

 //Indent all inner child navs
$('.nav-sidebar').addClass('nav-child-indent');

//Search function
$('#page-search').on('keyup', function () { 
    const searchValue = $(this).val();
    if (searchValue !== "") {
        $('.rows').each(function (index, el) {
            if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
              $(el).css({ display: "table-row" });
            } else {
              $(el).css({ display: "none" });
            }
        });
    }
    else{
        $('.rows').each(function (index, el) {
            if($(el).css('display') === 'none'){
                $(el).css({'display':'table-row'});
            }
        });
    }
});

// Append rows with pending status on top
$('.rows').each(function (index, el) {
    if ($(el).find(".status").text() === 'Pending') {
      $('tbody').prepend($(el));
    } 
});
