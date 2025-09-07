
import { displaySuccess, displayInfo } from './export.js';

//Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

//Get the URL
const queryString = new URL(window.location);
// We can then parse the query string’s parameters using URLSearchParams:
const urlParams = new URLSearchParams(queryString.search);
//Then we call any of its methods on the result.
const email = urlParams.get('email');
const input = document.createElement('input');
    input.type = 'text';
    input.id = 'email';
    input.name = 'email';
    input.value = email;
    input.setAttribute('hidden', true);
$("#update-form").append(input);

//Check All Inputs

$('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#update').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#update').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#update').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#update').attr('disabled', true);
        }
    });
});

function update() {

    let password = $("#password").val();
    let changedPassword = $("#confirm-password").val();
  
    if (password !== changedPassword) {
        displayInfo("Passwords do not match");
        return;
    }
    else{
            $.ajax({
              type: "POST",
              url: "server/change-password.php",
              data:  { email: $("#email").val(), password: changedPassword },
              dataType: 'json',
              success: function (response) {
  
                  const content = response.Info;
  
                  if (content !== 'Error changing password' && content !== 'No record found' && content !== 'All inputs must be filled out') {

                    displaySuccess(content);

                    setTimeout(function () { 

                        window.location = response.page.link;
                      
                    }, 5000);

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

$("#update-form").on("submit", function(e){
    e.preventDefault();
   update();
});
