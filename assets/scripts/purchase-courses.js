import { displaySuccess, displayInfo } from "./export.js";

// Product details
const courseTitle = $("#course-title").text();
const type = $("#course-type").text();
const sales = $("#course-sales-type").text();
const narration = $("#course-sales-narration").text();
const affiliate = $("#ref-id").text();
const id = $("#courseID").text();

// Payment processing charges
const transactionReference = $("#payment-reference").text();
const courseAmount = parseFloat($("#raw-course-amount").text());
const charge = 200; // Payment processing charge
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

// Update dialing code based on selected country
function updateDialingCode() {
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

// Event handlers
$("#user-country").change(updateDialingCode);

$("#course-review-carousel.owl-stage-outer.owl-stage.owl-item.cloned").css({
  opacity: 0,
});

// Check user credentials
function checkCredentials(user, type) {
  if (!user) {
    displayInfo("No email supplied!");
    return;
  }

  $.ajax({
    type: "GET",
    url: "./assets/server/check-user-credential.php",
    data: { email: user, type },
    dataType: "json",
    success(response) {
      const content = response[Object.keys(response)[0]];
      $("#status").text(content);
      if (
        ["User exists", "Affiliate exists", "Vendor exists"].includes(content)
      ) {
        handlePositiveResponse(content);
        // Update details
        $("#fullname").val(response.details.fullname);
        $("#contact").val(response.details.contact);
      } else {
        handleNegativeResponse(content);
      }
    },
    error() {
      displayInfo("Error connecting to server");
    },
  });
}

function handlePositiveResponse(content) {
  displaySuccess(content);
  $("#fullname-div, #contact-div, #password-div, #confirm-password-div").hide();
  $("#description-overlay").hide();
  $("#description-text p").html("");
}

function handleNegativeResponse(content) {
  const nonExistentResponses = {
    "User does not exist": () =>
      $(
        "#fullname-div, #contact-div, #password-div, #confirm-password-div"
      ).show(),
    "Affiliate does not exist": () => {
      $(
        "#fullname-div, #country-div, #contact-div, #password-div, #confirm-password-div"
      ).hide();
      $("#description-overlay").show();
      $("#description-text p").html(
        `To purchase a course as a promoter, you must first get a promoter account. The system has detected that you are not yet registered as a promoter. Register now by visiting <a href="https://chromstack.com/membership-signup?type=affiliate">this link</a>.`
      );
    },
    "Vendor does not exist": () => {
      $(
        "#fullname-div, #country-div, #contact-div, #password-div, #confirm-password-div"
      ).hide();
      $("#description-overlay").show();
      $("#description-text p").html(
        `To purchase a course as a vendor, you must first get a vendor account. The system has detected that you are not yet registered as a vendor. Register now by visiting <a href="https://chromstack.com/membership-signup?type=vendor">this link</a>.`
      );
    },
  };

  if (nonExistentResponses[content]) {
    nonExistentResponses[content]();
  } else {
    displayInfo(content);
  }
}

$("#membership-type").change(function () {
  checkCredentials($("#email").val(), $(this).val());
});

$("#email").blur(function () {
  checkCredentials($(this).val(), $("#membership-type").val());
});

// Validate input fields
$("#fullname, #email, #contact").on("keyup blur", function () {
  const isValid = $(this).val() !== "";
  $(this).css({ border: isValid ? "none" : "2px solid red" });
  $("#Signup").prop("disabled", !isValid);
});

// Checkout process
function makePayment() {
  const data = {
    membership: $("#status").text() === "User does not exist" ? "New" : "Old",
    fullname: $("#fullname").val(),
    email: $("#email").val(),
    contact: $("#contact").val(),
    country: $("#user-country").val(),
    amount: $("#raw-course-amount").text(),
    type: $("#course-type").text(),
    sales: $("#course-sales-type").text(),
    narration: $("#course-sales-narration").text(),
    affiliate: $("#ref-id").text(),
    id: $("#courseID").text(),
    status: $("#status").text(),
    user: $("#membership-type").val(),
    reference: transactionReference,
    code: dialingCode,
    currency,
  };

  if (data.status === "User does not exist") {
    if (!data.fullname || !data.email || !data.contact) {
      displayInfo("Some fields are empty");
      return;
    }
  } else if (!data.email) {
    displayInfo("Email field is empty");
    return;
  }

  $.ajax({
    type: "POST",
    url: "./assets/server/purchase-courses.php",
    data: data,
    dataType: "json",
    success(response) {
      const content = response.Info;
      if (
        content === "Purchase confirmed successfully" ||
        content === "You have registered successfully"
      ) {
        displaySuccess(`
          ${
            content === "Purchase confirmed successfully"
              ? "Your course purchase request has been received successfully."
              : "Your registration and course purchase request has been received successfully."
          }
          A confirmation email has been sent to your email address: ${
            data.email
          }
        `);
        $("#course-purchase-form")[0].reset();
        setTimeout(() => window.location.reload(), 2000);
      } else {
        displayInfo(content);
      }
    },
    error(e) {
      displayInfo("Error connecting to server");
      console.log(e.responseText);
    },
  });
}

$("#pay").click(function () {
  if ($("#email").val() === "") {
    displayInfo("Some fields are empty");
    return;
  }
  else{
    // Sample payment request
    const request = {
      merchant_code: "MX115942",
      site_redirect_url: `https://chromstack.com/course-purchase.php?ref=${affiliate}&id=${id}&type=${type}&sales=${sales}&narration=${narration}`,
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
          makePayment(); // Call the makePayment function
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
});

$("#close-decsription-view").on("click", function () {
  $("#description-overlay").css({ display: "none" });
  $("#description-text p").html("");
});
