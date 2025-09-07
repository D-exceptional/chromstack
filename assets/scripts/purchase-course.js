import { displaySuccess, displayInfo } from "./export.js";

// Payment processing charges
const courseTitle = $("#course-title").text();
const transactionReference = $("#payment-reference").text();
const courseAmount = parseFloat($("#raw-course-amount").text());
const charge = 200; // Payment processing charges
const totalAmount = (courseAmount + charge) * 100; // Convert to kobo or minor currency unit

//Store country code here
let isoCode = 0;
let dialingCode = "+234";
let currency = "NGN";
let countriesArray = [];
let countriesISOArray = [];

//Get country and dialing code details
function loadCountries() {
  $.getJSON("../countries-details.json", function (data) {
    for (const key in data) {
      if (Object.hasOwnProperty.call(data, key)) {
        const content = data[key];
        //Prepare object
        const countryObject = {
          name: content.country_name,
          code: content.phone_code,
          currency: content.currency_code,
        };
        //Save in array
        countriesArray.push(countryObject);
        //Get country names
        const countryName = `<option value='${content.country_name}'>${content.country_name}</option>`;
        $("#user-country").append(countryName);
      }
    }
  });
  //Append default option
  const defaultOption = `<option value=''>Select Your Country</option>`;
  $("#user-country").append(defaultOption);
}

loadCountries();

// Function to load country and dialing code details
function loadISO4217() {
  // Fetch the JSON data
  $.getJSON("../countries-iso-4217.json", function (data) {
    // Iterate over each country in the data
    $.each(data, function (index, content) {
      // Prepare the country object with name and currency numeric code
      const countryObject = {
        name: content.country,
        code: content.currency_numeric,
      };
      // Push the country object into the array
      countriesISOArray.push(countryObject);
    });
  });
}

// Call the function to load data
loadISO4217();

$("#course-review-carousel.owl-stage-outer.owl-stage.owl-item.cloned").each(
  function () {
    $(this).css({ opacity: 0 });
  }
);

function setDialingCode() {
  const selectedCountry = $("#user-country").val();
  //Get code associated with country
  $.each(countriesArray, function (index, country) {
    if (country.name === selectedCountry) {
      dialingCode = `+` + country.code;
      currency = country.currency;
    }
  });
  //Get currency code associated with country
  $.each(countriesISOArray, function (index, country) {
    if (country.name === selectedCountry) {
      isoCode = country.code;
    }
  });
}

//Track dialing code at select change event
$("#user-country").change(function () {
  setDialingCode();
});

function checkStatus(value) {
  $.ajax({
    type: "POST",
    url: "./assets/server/check-registration-status.php",
    data: { email: value },
    dataType: "json",
    success: function (response) {
      for (var key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (
            content !== "Email is not registered" &&
            content !== "Email field is empty"
          ) {
            displaySuccess(content);
            $(
              "#fullname-div, #contact-div, #password-div, #confirm-password-div"
            ).css({ display: "none" });
            $("#status").text("Registered");
          } else {
            displayInfo(content);
            $(
              "#fullname-div, #contact-div, #password-div, #confirm-password-div"
            ).css({ display: "block" });
            $("#status").text("Unregistered");
          }
        }
      }
    },
    error: function () {
      displayInfo("Error connecting to server");
    },
  });
}

$("#email").on("blur", function () {
  if ($(this).val() !== "") {
    checkStatus($(this).val());
  } else {
    displayInfo("No email supplied !");
    $("#fullname-div, #contact-div, #password-div, #confirm-password-div").css({
      display: "none",
    });
  }
});

//Prevent empty input fields
$("#fullname, #email, #contact")
  .on("keyup", function () {
    if ($(this).val() !== "") {
      $(this).css({ border: "none" });
      $("#Signup").attr("disabled", false);
    } else {
      $(this).css({ border: "2px solid red" });
      $("#Signup").attr("disabled", true);
    }
  })
  .on("blur", function () {
    if ($(this).val() !== "") {
      $(this).css({ border: "none" });
      $("#Signup").attr("disabled", false);
    } else {
      $(this).css({ border: "2px solid red" });
      $("#Signup").attr("disabled", true);
    }
  });

function makePayment() {
  const request = new FormData();
  const fullname = $("#fullname").val();
  const email = $("#email").val();
  const contact = $("#contact").val();
  const country = $("#user-country").val();
  const code = dialingCode;
  const reference = $("#payment-reference").text();
  const amount = $("#raw-course-amount").text();
  const type = $("#course-type").text();
  const sales = $("#course-sales-type").text();
  const narration = $("#course-sales-narration").text();
  const affiliate = $("#ref-id").text();
  const id = $("#courseID").text();

  //Prepare params
  request.append("fullname", fullname);
  request.append("email", email);
  request.append("contact", contact);
  request.append("code", code);
  request.append("country", country);
  request.append("id", id);
  request.append("type", type);
  request.append("sales", sales);
  request.append("narration", narration);
  request.append("affiliate", affiliate);
  request.append("reference", reference);
  request.append("amount", amount);
  request.append("currency", currency);

  //Send to server
  $.ajax({
    type: "POST",
    url: "./assets/server/purchase-course.php",
    data: request,
    dataType: "json",
    processData: false,
    contentType: false,
    cache: false,
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response.Info;
          if (content === "Payment confirmed successfully") {
            displaySuccess(
              `
                Your registration and purchase of the course ${courseTitle} was successful. 
                A confirmation email has been sent to your email address: ${email}
              `
            );
            setTimeout(function () {
              window.location.reload();
            }, 2000);
          } else {
            displayInfo(content);
          }
        }
      }
    },
    error: function (e) {
      displayInfo("Error connecting to server");
      console.log(e.responseText)
    },
  });
}

// Buy course
$("#pay").on("click", function () {
  if ($("#status").text() === "Registered") {
    displayInfo("You are already registered");
    return;
  } else {
    if (
      $("#fullname").val() === "" ||
      $("#email").val() === "" ||
      $("#contact").val() === ""
    ) {
      displayInfo("Some fields are empty");
      return;
    } else {
      // Sample payment request
      const request = {
        merchant_code: "MX115942",
        site_redirect_url:
          "https://chromstack.com/main-course-purchase.php?ref=1&id=1&type=Affiliate&sales=Admin&narration=Regular",
        pay_item_id: "Default_Payable_MX115942",
        pay_item_name: `Digital Course: ${courseTitle}`,
        cust_email: $("#email").val(),
        txn_ref: transactionReference,
        amount: totalAmount,
        currency: isoCode, // ISO 4217 numeric code of the currency used
        onComplete: function (response) {
          // Ensure the response is valid and contains the necessary information
          if (response && response.resp === "00") {
            // Payment successful
            makePayment() // Call the register function
            // Save the payment response in localStorage
            //localStorage.setItem("paymentResponse", JSON.stringify(response));
          } else {
            // Payment failed or invalid response
            displayInfo("Payment failed or invalid response");
          }
        },
        onClose: function () {
          // Handle the case when the user closes the payment modal
          displayInfo("Payment modal closed");
        },
        mode: "LIVE", // Make sure you are in the right environment (LIVE or TEST)
      };

      // Call webpayCheckout to initiate the payment
      window.webpayCheckout(request);
    }
  }
});
