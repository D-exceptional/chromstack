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
  "Kenya",
  "Mali",
  "Niger",
  "Rwanada",
  "Senegal",
  "Uganda",
];

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
$("#username, #email, #contact")
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

  function register(val) {
    const request = new FormData();
    const fullname = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const country = $("#user-country").val();
    const code = dialingCode;
    let txn = val;
    const amount = $("#raw-course-amount").text();
    const type = $("#course-type").text();
    const sales = $("#course-sales-type").text();
    const narration = $("#course-sales-narration").text();
    const ref = $("#ref-id").text();
    const id = $("#courseID").text();

    //Start processing
    if (supportedCountries.includes(country)) {
      if ($("#status").text() === "Registered") {
        displayInfo("You are already registered as an affiliate!");
        return;
      } else {
        if(txn === ""){
          $("#payment-modal-overlay").css({ display: "flex" });
          $("#show-continue").css({ display: "none" });
          $("#ref-div, #hide-continue").css({ display: "block" });
          $("#reference").focus();
          return;
        }
        else if (fullname === "" || email === "" || contact === "") {
          displayInfo("Some fields are empty");
          return;
        } else {
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
          request.append("ref", ref);
          request.append("txn", txn);
          request.append("amount", amount);
          request.append("currency", currency);
          //Send to server
          $.ajax({
            type: "POST",
            url: "./assets/server/buy-main-course.php",
            data: request,
            dataType: "json",
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
              for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                  const content = response.Info;
                  if (content === "You have registered successfully") {
                    displaySuccess(
                      `
                      Your application for affiliate membership on Chromstack was successful. 
                      We are currently verifying your payment and will respond in the next one hour.
                      A confirmation email has been sent to your email address: ${email}
                    `);
                    setTimeout(function () {
                      window.location.reload();
                    }, 1500);
                  } else {
                    displayInfo(content);
                  }
                }
              }
            },
            error: function (e) {
              displayInfo("Error connecting to server");
            },
          });
        }
      }
    } else {
      displayInfo("Country not supported");
      return;
    }
  }

//Buy course
$("#proceed").on("click", function () {
  if (supportedCountries.includes($("#user-country").val())) {
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
        const amount = Number($("#raw-course-amount").text());
        const nairaAmount = formatCurrency(amount, 'NGN');
        const internationalAmount = formatCurrency((amount + 500), currency);
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
        $("#payment-modal-overlay").css({ display: "flex" });
        document.getElementById('paymentDetails').style.visibility = 'visible';
        $("#show-continue").show();
        $("#hide-continue").hide();
        $("#ref-div").css({ display: "none" });
      }
    }
  } else {
    displayInfo("Country not supported");
    return;
  }
});

$("#show-continue").on("click", function () {
  $("#ref-div, #hide-continue").css({ display: "block" });
   $("#pay-text")
     .css({ color: "#ffffff" })
     .html(
       `Enter the payment reference number from your payment app or channel and click on the <b>Register</b> button afterwards to register `
     );
  $(this).hide();
  $("#hide-continue").show();
});

$("#reference")
  .on("keyup", function () {
    if ($(this).val() === "") {
      $("#hide-continue").attr('disabled', true);
    } else {
      $("#hide-continue").attr("disabled", false);
    }
  })
  .on("blur", function () {
    if ($(this).val() === "") {
      $("#hide-continue").attr("disabled", true);
    } else {
      $("#hide-continue").attr("disabled", false);
    }
  });

  $("#hide-continue").on("click", function () {
    const paymentRef = $("#reference").val();
    if (paymentRef === "") {
      $("#reference").focus();
      return;
    } else {
      $("#payment-modal-overlay").css({ display: "none" });
      $("#reference").val('');
      register(paymentRef);
    }
  });

  $("#payment-overlay-close").on("click", function () {
    $("#payment-modal-overlay").css({ display: "none" });
  })
 

