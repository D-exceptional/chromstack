
import { displayInfo } from './export.js';

//Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

//Check All Inputs

$('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#verify').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#verify').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#verify').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#verify').attr('disabled', true);
        }
    });
});


function verify() {

    let email = $("#email").val();
  
    if (email == "") {
        displayInfo("Enter your email");
    }
    else{
            $.ajax({
              type: "POST",
              url: "server/check-email.php",
              data:  { email: email },
              dataType: 'json',
              success: function (response) {
  
                  const content = response.Info;
  
                  if (content !== 'No record found' && content !== 'Email field is empty') {

                    window.location = response.page.link;

                  }
                  else{
                      displayInfo(content);
                  }
              },
              error: function (e) {
                      displayInfo(e.responseText);
                  }
            });
        }
}

$("#verify-form").on("submit", function(e){
    e.preventDefault();
   verify();
});
