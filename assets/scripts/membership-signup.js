import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {
  //Set the button text and disable it by default
  $("#Signup").text("Sign Up");

  //Get the URL
  const queryString = new URL(window.location);
  // We can then parse the query string’s parameters using URLSearchParams:
  const urlParams = new URLSearchParams(queryString.search);
  //Then we call any of its methods on the result.
  const type = urlParams.get("type");

  if (type === "affiliate") {
    $("#signup-type").text("Affiliate Signup");
    $("#type-link").text("Become an affiliate");
    $("#makePayment").text("Pay Now");
    $("#header-text").text("Set Up Your Affiliate Account");
  } else {
    $("#signup-type").text("Vendor Signup");
    $("#type-link").text("Become a vendor");
    $("#header-text").text("Set Up Your Vendor Account");
    $("#makePayment").text("Close");
  }

  //Store country code here
  let isoCode = 0;
  let dialingCode = "+234";
  let currency = "NGN";
  let countriesArray = [];
  let countriesISOArray = [];

  function generatePaymentReference(length = 12) {
    const chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let reference = "";

    for (let i = 0; i < length; i++) {
      const randomIndex = Math.floor(Math.random() * chars.length);
      reference += chars[randomIndex];
    }

    return reference + "dbp"; // Reference pattern for direct registrations . dbp means `Dashboard purchase`
  }

  const transactionReference = generatePaymentReference();
  const paymentAmount = 5000;
  const charge = 200; // Payment processing charges
  const totalAmount = (paymentAmount + charge) * 100; // Convert to kobo or minor currency unit

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
    const reference = transactionReference;

    //Start processing
    if (fullname === "" || email === "" || contact === "") {
      displayInfo("Some fields are empty");
      return;
    } else {
      //Prepare params
      request.append("fullname", fullname);
      request.append("email", email);
      request.append("contact", contact);
      request.append("code", code);
      request.append("country", country);
      request.append("reference", reference);
      request.append("amount", paymentAmount);
      //Send to server
      $.ajax({
        type: "POST",
        url: "./assets/server/purchase-dashboard.php",
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
        },
      });
    }
  }

  //Sign up
  $("#signup-form").on("submit", function (event) {
    event.preventDefault();
    const name = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const country = $("#user-country").val();
    //Start processing
    if (name === "" || email === "" || contact === "" || country === "") {
      displayInfo("Please fill in all required fields");
      return;
    } else {
      const data = new FormData();
      data.append("name", name);
      data.append("email", email);
      data.append("contact", contact);
      data.append("country", country);
      data.append("code", dialingCode);
      //Process registration
      if (type === "vendor") {
        $.ajax({
          type: "POST",
          url: "./assets/server/vendor-registration.php",
          data: data,
          dataType: "json",
          cache: false,
          processData: false,
          contentType: false,
          success: function (response) {
            const content = response.Info;
            if (content === "You have registered successfully") {
              displaySuccess(content);
              $("#signup-form")[0].reset();
              $("#server-email").text(response.details.email);
              $("#server-contact").text(response.details.contact);
              $("#page-overlay").css({ height: "100%", padding: "3%" });
              const user = response.details.fullname;
              const mail = response.details.email;
              $("#info-p").html(`
                             <p>
                                Hi, <b>${user}</b>! 
                                Your registration for the vendor membership is almost done. 
                                Complete your registration by activating your account via the mail sent to the email address: <b>${mail}</b>.
                             </p>
                            `);
            } else {
              displayInfo(content);
            }
          },
          error: function () {
            displayInfo("Error connecting to server");
          },
        });
      } else {
        // Sample payment request
        const request = {
          merchant_code: "MX115942",
          site_redirect_url:
            "https://chromstack.com/membership-signup?type=affiliate",
          pay_item_id: "Default_Payable_MX115942",
          pay_item_name: `Digital Dashboard`,
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
});
