import { displaySuccess, displayInfo } from './export.js';
  
$("#addEmail").on("click", function () {
  const email = $("#subscriptionEmail").val();
  if (email == "") {
      displayInfo("Email field is empty !");
  }
  else{
        $.ajax({
            type: "POST",
            url: "./assets/server/check-subscription.php",
            data:  { email: email },
            dataType: 'json',
            success: function (response) {
                 $("#subscriptionEmail").val("");
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if (content !== 'This email address has not been registered' && content !== 'The supplied email is not valid' && content !== 'Email field is empty') {
                            displaySuccess(content);
                        }
                        else{
                            displayInfo(content);
                        }
                    }
                }
            },
            error: function () {
                    displayInfo('Error connecting to server');
                }
        });
  }

});

/*
 $.ajax({
   type: "POST",
   url: "./assets/server/counters.php",
   dataType: 'json',
   success: function (response) {
     console.log(response);
   },
   error: function () {
     displayInfo("Error connecting to server");
   },
 });
 */
