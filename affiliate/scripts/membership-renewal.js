import { displayInfo } from './export.js';

$(document).ready(function () {

    /*
       Use these codes as algorithm to manage unread chat messages counters
   */

    $.getJSON('../../countries-list-1.json', function (data) {
        for (const key in data) {
            if (Object.hasOwnProperty.call(data, key)) {
                const content = data[key];
                //Get country names
                const countryName = `<option value='${content.name}'>${content.name}</option>`;
                $('#country').append(countryName);
            }
        }
    });

    $("#renew-membership").on("click", function () {
        const name = $("#name").val();
        const email = $("#email").val();
        const contact = $("#contact").val();
        const amount = $("#amount").val();
        const type = 'Affiliate';
        const country = $('#country').val();

        if (name == "" || email == "" || contact == "" || amount == "" || country == "") {
            displayInfo("Some fields are empty !");
        }
        else {
            const request = new FormData();
            request.append('name', name);
            request.append('email', email);
            request.append('contact', contact);
            request.append('amount', amount);
            request.append('type', type);
            request.append('country', country);

            $.ajax({
                type: "POST",
                url: "../server/membership-renewal.php",
                data: request,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    for (const key in response) {
                        if (Object.hasOwnProperty.call(response, key)) {
                            const content = response.Info;
                            if (content !== 'Error connecting to Rave payment API' && content !== 'Rave payment API error ocuured') {
                                displayInfo(content);
                                //$('#course-purchase-form')[0].reset();
                                setTimeout(function () {
                                    window.location = response.details.link;
                                }, 3100);
                            }
                            else {
                                //displayInfo(content);
                                displayInfo(response.details.error);
                            }
                        }
                    }
                },
                error: function (e) {
                    displayInfo('Error connecting to server');
                    console.log(e.responseText);
                }
            });
        }
    });
});

