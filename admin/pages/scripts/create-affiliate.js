import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {
 // $(document).ready(function () {
 
    let countriesArray = [];
    let dialingCode = "+234";
 
    $.getJSON("../../../countries-details.json", function (data) {
      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          const content = data[key];
          //Prepare object
            const countryObject = {
              name: content.country_name,
              code: content.phone_code
            };
            //Save in array
            countriesArray.push(countryObject);
          //Get country names
          const countryName = `<option value='${content.country_name}'>${content.country_name}</option>`;
          $("#affiliateCountry").append(countryName);
        }
      }
    });
  //});
  
    function setDialingCode() {
      const selectedCountry = $("#affiliateCountry").val();
      //Get code associated with country
      $.each(countriesArray, function (index, country) {
         if (country.name === selectedCountry) {
           dialingCode = `+` + country.code;
         }
      });
    }
    
    //Track dialing code at select change event
    $("#affiliateCountry").change(function () {
      setDialingCode();
    });

  $("#create-affiliate").on("click", function () {
    const affiliateName = $("#affiliateName").val();
    const affiliateEmail = $("#affiliateEmail").val();
    const affiliateContact = $("#affiliateContact").val();
    const affiliateCountry = $("#affiliateCountry").val();
    const affiliateCode = dialingCode;
    const creator = $("#creator").val();

    if (
      affiliateName === "" ||
      affiliateEmail === "" ||
      affiliateContact === ""
    ) {
      displayInfo("Fill out all fields before submitting !");
    } else {
      const request = new FormData();
      request.append("name", affiliateName);
      request.append("email", affiliateEmail);
      request.append("contact", affiliateContact);
      request.append("country", affiliateCountry);
      request.append("code", affiliateCode);
      request.append("creator", creator);

      if ($("#userCountry").val() == "" || $("#dialingCode").val()) {
        displayInfo("Could not get Country or Dialing code !");
      } else {
        $.ajax({
          type: "POST",
          url: "../server/create-affiliate.php",
          data: request,
          dataType: "json",
          processData: false,
          contentType: false,
          cache: false,
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "New affiliate created successfully") {
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
      }
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

});
