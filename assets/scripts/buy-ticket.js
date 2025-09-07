import { displayInfo } from "./export.js";

$(document).ready(function () {
  //Prevent empty input fields
  $("#fullname, #email, #contact, #amount")
    .on("keyup", function () {
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $("#buy-ticket").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $("#buy-ticket").attr("disabled", true);
      }
    })
    .on("blur", function () {
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $("#buy-ticket").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $("#buy-ticket").attr("disabled", true);
      }
    });

  //Prevent zero values for amount
 $("#amount")
    .on("keyup blur", function () {
      const amount = parseFloat($(this).val());
      if (amount === 0 || amount < 1000) {
        $(this).css({ border: "2px solid red" });
        $("#buy-ticket").prop("disabled", true);
      } else {
        $(this).css({ border: "none" });
        $("#buy-ticket").prop("disabled", false);
      }
    });

  //Sign up
  $("#buy-ticket").on("click", function () {
    const name = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const amount = parseFloat($("#amount").val()) + 10; // Including processing charges
    const id = $("#ticketID").val();
    const code = "+234";
    const total = amount * 100;

    if (name === "" || email === "" || contact === "" || amount === "") {
      displayInfo("Please fill in all required fields");
      return;
    } else if (amount === 0 || amount < 1000) {
      displayInfo("Amount cannot be zero and must be greater than 1,000");
      return;
    } else {
      const request = new FormData();
      request.append("name", name);
      request.append("email", email);
      request.append("contact", contact);
      request.append("amount", total);
      request.append("code", code);
      request.append("id", id);
      //Make payment
      $.ajax({
        type: "POST",
        url: "./assets/server/buy-ticket.php",
        data: request,
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Redirecting to payment page") {
                displayInfo(content);
                setTimeout(function () {
                  window.location = response.details.link;
                }, 1500);
              } else {
                displayInfo(response.details.error);
              }
            }
          }
        },
        error: function (e) {
          displayInfo("Error connecting to server");
          //console.log(e.responseText);
        },
      });
    }
  });
});
