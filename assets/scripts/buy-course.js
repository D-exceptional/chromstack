import { displaySuccess, displayInfo } from "./export.js";

const referrerID = $("#ref-id").text();
const salesType = $("#course-sales-type").text();

//Store country code here
let dialingCode = "+234";
let currency = "NGN";
let ratesArray = [];
let countriesArray = [];

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

function getExchangeRates() {
  const base = "NGN";
  const endpoint = "https://api.exchangerate-api.com/v4/latest";
  $.ajax({
    type: "GET",
    url: `${endpoint}/${base}`,
    dataType: "json",
    success: function (response) {
      const rates = response.rates;
      for (const [currency, rate] of Object.entries(rates)) {
        ratesArray.push({ currency, rate });
      }
    },
    error: function () {
      displayInfo("Error connecting to server");
    },
  });
}

getExchangeRates();

function formatCurrency(amount, currency) {
  let charge = 0;
  $.each(ratesArray, function (index, rate) {
    if (rate.currency === currency) {
      charge = amount * rate.rate;
    }
  });
  return Intl.NumberFormat("en-US", {
    style: "currency",
    currency,
  }).format(charge);
}

let supportedCountries = [
  "Benin",
  "Botswana",
  "Burkina Faso",
  "Burundi",
  "Cameroon",
  "Chad",
  "China", // Asia
  "Congo",
  "Congo (DRC)",
  "Côte d'Ivoire",
  "Ethiopia",
  "Ghana",
  "Guinea",
  "Guinea-Bissau",
  "India", // Asia
  "Ivory Coast",
  "Kenya",
  "Liberia",
  "Madagascar",
  "Malawi",
  "Mali",
  "Mozambique",
  "Niger",
  "Nigeria",
  "Philippines", // Asia
  "Rwanada",
  "Senegal",
  "Seychelles",
  "Sierra Leone",
  "Somalia",
  "South Africa",
  "South Sudan",
  "Swaziland",
  "Tanzania",
  "Togo",
  "Turkey", // Asia
  "Uganda",
  "Zambia",
  "Zimbabwe",
];

let eversendCountries = [
  "Benin",
  "Burkina Faso",
  "Cameroon",
  "Congo (DRC)",
  "Guinea-Bissau",
  "Ghana",
  "Ivory Coast",
  "Mali",
  "Niger",
  "Rwanada",
  "Senegal",
  "Uganda",
  "Kenya",
];

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
}

// Event handlers
$("#user-country").change(updateDialingCode);

$("#course-review-carousel.owl-stage-outer.owl-stage.owl-item.cloned").css({ opacity: 0 });

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
      
      if (["User exists", "Affiliate exists", "Vendor exists"].includes(content)) {
        handlePositiveResponse(content);
      } else {
        handleNegativeResponse(content);
      }
    },
    error() {
      displayInfo("Error connecting to server");
    }
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
    "User does not exist": () => $("#fullname-div, #contact-div, #password-div, #confirm-password-div").show(),
    "Affiliate does not exist": () => {
      $("#fullname-div, #country-div, #contact-div, #password-div, #confirm-password-div").hide();
      $("#description-overlay").show();
      $("#description-text p").html(`To purchase a course as a promoter, you must first get a promoter account. The system has detected that you are not yet registered as a promoter. Register now by visiting <a href="https://chromstack.com/membership-signup?type=affiliate">this link</a>.`);
    },
    "Vendor does not exist": () => {
      $("#fullname-div, #country-div, #contact-div, #password-div, #confirm-password-div").hide();
      $("#description-overlay").show();
      $("#description-text p").html(`To purchase a course as a vendor, you must first get a vendor account. The system has detected that you are not yet registered as a vendor. Register now by visiting <a href="https://chromstack.com/membership-signup?type=vendor">this link</a>.`);
    }
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
$("#username, #email, #contact").on("keyup blur", function () {
  const isValid = $(this).val() !== "";
  $(this).css({ border: isValid ? "none" : "2px solid red" });
  $("#Signup").prop("disabled", !isValid);
});

// Checkout process
function checkout(txn) {
  if (!txn) {
    $("#payment-modal-overlay").show();
    $("#show-continue").hide();
    $("#ref-div, #hide-continue").show();
    $("#reference").focus();
    return;
  }

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
    ref: $("#ref-id").text(),
    id: $("#courseID").text(),
    status: $("#status").text(),
    user: $("#membership-type").val(),
    txn,
    code: dialingCode,
    currency
  };
  
  if (!supportedCountries.includes(data.country)) {
    displayInfo("Country not supported");
    return;
  } 

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
    url: "./assets/server/buy-course.php",
    data: data,
    dataType: "json",
    success(response) {
      const content = response.Info;
      if (content === "Purchase request sent successfully" || content === "You have registered successfully") {
        displaySuccess(`
          ${content === "Purchase request sent successfully" ? "Your course purchase request has been received successfully." : "Your registration and course purchase request has been received successfully."}
          We are currently verifying your payment and will respond within the next one hour.
          A confirmation email has been sent to your email address: ${data.email}
        `);
        $("#course-purchase-form")[0].reset();
        setTimeout(() => window.location.reload(), 1500);
      } else {
        displayInfo(content);
      }
    },
    error(e) {
      displayInfo("Error connecting to server");
    }
  });
}

$("#proceed").click(function () {
  if (supportedCountries.includes($("#user-country").val())) {
    if ($("#status").text() === "User does not exist") {
      if (
        $("#fullname").val() === "" ||
        $("#email").val() === "" ||
        $("#contact").val() === ""
      ) {
        displayInfo("Some fields are empty");
        return;
      }
    } else {
      if ($("#email").val() === "") {
        displayInfo("Some fields are empty");
        return;
      }
    }
    //Continue processing
    const amount = Number($("#raw-course-amount").text());
    const nairaAmount = formatCurrency(amount, "NGN");
    const internationalAmount = formatCurrency(amount + 500, currency);
    //Show amount based on country
    if ($("#user-country").val() === "Nigeria") {
      //$("#bankLogo").attr("src", "assets/img/fidelity.png");
      $("#bankLogo").attr("src", "assets/img/moniepoint.jpg");
      $("#pay-text")
        .css({ color: "#ffffff" })
        .html(
            `Pay <b>${nairaAmount}</b> to this Moniepoint Bank Account: 
            <br> 
            <br> 
            <center> <b>6797159674 | Chromstack Enterprise</b> </center>
            <br>
            After payment, click on the <b>I have paid button</b> to get started
            `
        );
    } else if (eversendCountries.includes($("#user-country").val())) {
      $("#bankLogo").attr("src", "assets/img/eversend.png");
      $("#pay-text")
        .css({ color: "#ffffff" })
        .html(
             `Pay <b>${internationalAmount}</b> to this Wema Bank Account via Eversend app: 
                <br> 
                <br>
                <center> <b>8548539053 | Chukwuebuka Augustine </b> </center> 
                <br>
                After payment, click on the <b>I have paid button</b> to get started
            `
        );
    } else {
      $("#bankLogo").attr("src", "assets/img/momo.png");
      $("#pay-text")
        .css({ color: "#ffffff" })
        .html(
           `Pay <b>${internationalAmount}</b> to this Mobile Money (MoMo) Bank Account: 
            <br> 
            <br>
            <center> <b>9131963174 | Chukwuebuka Augustine </b> </center>
            <br>
            After payment, click on the <b>I have paid button</b> to get started
           `
        );
    }
    $("#payment-modal-overlay").show();
    document.getElementById('paymentDetails').style.visibility = 'visible';
    $("#show-continue").show();
    $("#hide-continue").hide();
    $("#ref-div").hide();
  }
  else{ displayInfo("Country not supported"); }
});

$("#show-continue").click(function () {
  $("#ref-div, #hide-continue").show();
  $("#pay-text").html(`
    Enter the payment reference number from your payment app or channel and click on the <b>Buy Now</b> button afterwards to complete purchase
  `);
  $(this).hide();
  $("#hide-continue").show();
});

$("#reference").on("keyup blur", function () {
  $("#hide-continue").prop("disabled", $(this).val() === "");
});

$("#hide-continue").click(function () {
  const paymentRef = $("#reference").val();
  if (!paymentRef) {
    $("#reference").focus();
    return;
  }
  $("#payment-modal-overlay").hide();
  $("#reference").val("");
  checkout(paymentRef);
});

$("#payment-overlay-close").on("click", function () {
  $("#payment-modal-overlay").css({ display: "none" });
});

$("#close-decsription-view").on("click", function () {
  $("#description-overlay").css({ display: "none" });
  $("#description-text p").html("");
});