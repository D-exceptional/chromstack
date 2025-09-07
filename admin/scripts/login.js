import { displayInfo } from './export.js';

//Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

 $("#email").val('').focus();
 $("#password").val('');

    //Check All Inputs

    $('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#login').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#login').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#login').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#login').attr('disabled', true);
        }
    });
});

var trials = 2;

function login() {

    let email = $("#email").val();
    let password = $("#password").val();
  
    if (email == "" || password == "") {
      displayInfo("Some fields are empty !");
    }
    else{
            $.ajax({
              type: "POST",
              url: "server/login.php",
              data:  { email: email, password: password },
              dataType: 'json',
              success: function (response) {
  
                  const content = response.Info;
  
                  if (content !== 'Invalid credentials. Check your email or password again' && content !== 'No record found' && content !== 'All inputs must be filled out') {
  
                      $("#email").val('');
                      $("#password").val('');
  
                      //Redirect to dashboard
                      
                      window.location = response.admin.link;
  
                  }
                  else{
                    displayInfo(content);
  
                              trials--;
  
                          if (trials === 0) {
                              //Show modal
                                setTimeout(() => {
                                    displayInfo('You have entered incorrect password 2 times');
                                }, 4000);
                              setTimeout(function () { 
                                  displayInfo('It seems you forgot your password; reset it now via the link below');
                              }, 7000);
                      }  
                  }
              },
              error: function (e) {
                    displayInfo(e.responseText);
                  }
            });
        }
}


$("#login-form").on("submit", function(e){
    e.preventDefault();
   login();
});
