import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {

/*
    Use these codes as algorithm to manage unread chat messages counters
*/

    $(document).ready(function(){
        $.getJSON('../../../countries-list-1.json', function(data) {
            for (const key in data) {
                if (Object.hasOwnProperty.call(data, key)) {
                    const content = data[key];
                    //Get country names
                    const countryName = `<option value='${content.name}'>${content.name}</option>`;
                    $('#adminCountry').append(countryName);
                     //Get country codes
                     const countryCode = `<option value='${content.dial_code}'>${content.dial_code}</option>`;
                     $('#adminCode').append(countryCode);
                }
            }
        });
     });

    $("#create-admin").on("click", function () {
        const adminName = $("#adminName").val();
        const adminEmail = $("#adminEmail").val();
        const adminContact = $("#adminContact").val();
        const adminPassword = $("#adminPassword").val();
        const adminCountry =  $("#adminCountry").val();
        const adminCode = $("#adminCode").val();

        if (adminName == "" || adminEmail == "" || adminContact == "" || adminPassword == "") {
            displayInfo("Fill out all fields before submitting !");
        }
        else{  
                const request = new FormData();
                request.append('name', adminName);
                request.append('email', adminEmail);
                request.append('contact', adminContact);
                request.append('password', adminPassword);
                request.append('country', adminCountry);
                request.append('code', adminCode);

                if ( $("#userCountry").val() == '' || $("#dialingCode").val()) {
                    displayInfo("Could not get Country or Dialing code !");
                }
                else {
                        $.ajax({
                        type: "POST",
                        url: "../server/create-admin.php",
                        data: request,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (response) {
                            for (const key in response) {
                                if (Object.hasOwnProperty.call(response, key)) {
                                    const content = response[key];
                                    if (content !== 'Email has been registered on the site!' && content !== 'Error creating admin' && content !== 'Supplied email is not valid'  && content !== 'Some fields are empty'){
                                        displaySuccess(content);
                                    }
                                    else{
                                        displayInfo(content);
                                    }
                                }
                            }
                        },
                        error: function (e) {
                            displayInfo(e.responseText);
                            console.log(e.responseText);
                        }
                    });
                }
            }
    });

    //Indent all inner child navs
    $('.nav-sidebar').addClass('nav-child-indent');
});

