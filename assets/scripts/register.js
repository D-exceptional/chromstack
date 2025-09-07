import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {

  //Store country code here
  let dialingCode = "+234";
  let currency = "NGN";
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
  
    const supportedCountries = [
    "Benin",
    "Botswana",
    "Burkina Faso",
    "Burundi",
    "Cameroon",
    "Chad",
    "China", // Asia
    "Congo",
    "Congo (DRC)",
    "C么te d'Ivoire",
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

  //Track dialing code at select change event
  function setDialingCode() {
    const selectedCountry = $("#user-country").val();
    if (selectedCountry !== '') {
      //Get code associated with country
      $.each(countriesArray, function (index, country) {
        if (country.name === selectedCountry) {
          dialingCode = `+` + country.code;
          currency = country.currency;
        }
      });
    } else {
      displayInfo('Select your country');
      return;
    }
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
        $("form button").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $("form button").attr("disabled", true);
      }
    })
    .on("blur", function () {
      if ($(this).val() !== "") {
        $(this).css({ border: "none" });
        $("form button").attr("disabled", false);
      } else {
        $(this).css({ border: "2px solid red" });
        $("form button").attr("disabled", true);
      }
    });

  //Sign up
  $("form").on("submit", function (event) {
    event.preventDefault();
    const name = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const country = $("#user-country").val();

    if (supportedCountries.includes(country)) {
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
        //Send to server
        $.ajax({
          type: "POST",
          url: "./assets/server/free-affiliate-signup.php",
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
                  Hello, <b>${user}</b>! 
                  <br>
                  Your registration for the Chromstack Affiliate Membership is successful. 
                  <br>
                  Click on the link sent to <b>${mail}</b> to login to your account and get started as an affiliate on Chromstack.
                  Welcome on board!
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
      }
    }
    else{
      displayInfo("Country not supported");
      return;
    }
  });

  $("#makePayment").on("click", function () {
    $("#page-overlay").css({ height: "0%", padding: "0%" });
  });

});
