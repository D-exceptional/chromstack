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
          $("#vendorCountry").append(countryName);
        }
      }
    });
  //});
  
    function setDialingCode() {
      const selectedCountry = $("#vendorCountry").val();
      //Get code associated with country
      $.each(countriesArray, function (index, country) {
         if (country.name === selectedCountry) {
           dialingCode = `+` + country.code;
         }
      });
    }
    
    //Track dialing code at select change event
    $("#vendorCountry").change(function () {
      setDialingCode();
    });

  $("#create-vendor").on("click", function () {
    const vendorName = $("#vendorName").val();
    const vendorEmail = $("#vendorEmail").val();
    const vendorContact = $("#vendorContact").val();
    const vendorCountry = $("#vendorCountry").val();
    const vendorCode = dialingCode;
    const creator = $("#creator").val();

    if (
      vendorName === "" ||
      vendorEmail === "" ||
      vendorContact === ""
    ) {
      displayInfo("Fill out all fields before submitting !");
    } else {
      const request = new FormData();
      request.append("name", vendorName);
      request.append("email", vendorEmail);
      request.append("contact", vendorContact);
      request.append("country", vendorCountry);
      request.append("code", vendorCode);
      request.append("creator", creator);

      if ($("#vendorCountry").val() === "" || $("#dialingCode").val()) {
        displayInfo("Could not get Country or Dialing code !");
      } else {
        $.ajax({
          type: "POST",
          url: "../server/create-vendor.php",
          data: request,
          dataType: "json",
          processData: false,
          contentType: false,
          cache: false,
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "New vendor created successfully") {
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
