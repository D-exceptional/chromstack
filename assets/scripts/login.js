import { displaySuccess, displayInfo } from "./export.js";

$("#email").val("");
$("#password").val("");

$('#psw-div i').on("click", function() {
    if ($(this).hasClass('fa-eye')) {
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        $("#password").attr('type', 'text');
    } else {
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        $("#password").attr('type', 'password');
    }
});

$('#new-psw-div i').on("click", function() {
    if ($(this).hasClass('fa-eye')) {
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        $("#new-password").attr('type', 'text');
    } else {
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        $("#new-password").attr('type', 'password');
    }
});

$("#code-div, #new-psw-div, #code-trigger").css({ display: "none" });

//Email verification logics
let isVerified = "needsVerification";

function countdownTimer() {
  let timeValid = 1200; //20 minutes
  let start = setInterval(() => {
    timeValid--;
    if (timeValid === 0) {
      clearInterval(start);
      isVerified = "reVerify";
      timeValid = 1200; //Reset the timer
      $("#code-trigger").show();
    }
  }, 1000);
}

//One-time OTP code
function getToken(type) {
  $("#code-trigger").hide();
  if ($("#email").val() !== "") {
    $.ajax({
      type: "POST",
      url: "./assets/server/get-code.php",
      data: {
        email: $("#email").val(),
        type: type,
      },
      dataType: "json",
      cache: false,
      success: function (response) {
        const content = response.Info;
        if (content === "Verification code sent successfully") {
          displaySuccess(content);
          isVerified = "Verified";
          countdownTimer();
          $("#code-div").show();
          setTimeout(() => {
            $("#code-trigger").show();
          }, 2000);
        } else {
          displayInfo(response.details.error);
          $("#code-trigger").show();
        }
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  } else {
    displayInfo("Email field is empty");
  }
}

function processOTP() {
  switch (isVerified) {
    case "needsVerification":
      getToken("First");
      break;
    case "Verified":
      displayInfo("Enter the OTP code sent to your email");
      break;
    case "reVerify":
      getToken("New");
      break;
  }
}

//Get user type by email
function getUserType(email, type) {
  if(type === 'None'){
    displayInfo('Select your membership type from the dropdown menu');
    return;
  }
  else{
    $.ajax({
      type: "POST",
      url: "./assets/server/get-user-type.php",
      data: { email: email, type: type },
      dataType: "json",
      success: function (response) {
        for (var key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "User" || content === "Affiliate" || content === "Vendor") {
              //Do anything here
              $("#loginUser").attr("disabled", false);
              if ($("#loginUser").text() === "Verify email") {
                setTimeout(function () {
                  $("#psw-div, #new-psw-div, #code-div, #code-trigger").css({
                    display: "block",
                  });
                  processOTP();
                  $("#password, #new-password").val("");
                  $("#email-div").css({ display: "none" });
                  $("#loginUser").text("Update password");
                }, 2000);
              }
            } else {
              displayInfo(content);
              $("#loginUser").attr("disabled", true);
            }
          }
        }
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  }
}

//Check status by email
$("#email").on("keyup", function () {
  if ($(this).val() === "") {
    displayInfo("No email supplied !");
    $("#loginUser").attr("disabled", true);
  } else {
    $("#loginUser").attr("disabled", false);
  }
});

//Get user type at blur
$("#email").on("blur", function () {
  if ($(this).val() !== "") {
    getUserType($(this).val(), $("#membership-type").val());
    $("#loginUser").attr("disabled", false);
  } else {
    displayInfo("No email supplied !");
    $("#loginUser").attr("disabled", true);
  }
});

//Check status by select toggle
$("#membership-type").on("change", function () {
  if ($("#email").val() === "") {
    displayInfo("No email supplied !");
    $("#loginUser").attr("disabled", true);
  } else {
    getUserType($("#email").val(), $(this).val());
    $("#loginUser").attr("disabled", false);
  }
});

//Get new code when the code link is clicked
$("#get-code").on("click", function (e) {
  e.preventDefault();
  processOTP();
});

//Prevent empty input fields
$("#email, #password, #new-password, #user-code")
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

function login() {
  const email = $("#email").val();
  const password = $("#password").val();
  const type = $("#membership-type").val();

  if(type === 'None'){
    displayInfo(
      "Select your membership type from the dropdown menu"
    );
    return;
  }
  else if (email == "" || password == "") {
    displayInfo("Some fields are empty !");
    return;
  } else {
    $.ajax({
      type: "POST",
      url: "./assets/server/login.php",
      data: { email: email, password: password, type: type },
      dataType: "json",
      success: function (response) {
        const content = response.Info;
        if (content === "You have successfully logged in") {
          $("#email").val("");
          $("#password").val("");
          //Redirect to dashboard
          window.location = response.user.link;
        } else {
          displayInfo(content);
        }
      },
      error: function (e) {
        displayInfo("Error connecting to server");
      },
    });
  }
}

function verify() {
  const email = $("#email").val();
  if (email == "") {
    displayInfo("Enter your email");
  } else {
    getUserType(email, $("#membership-type").val());
  }
}

function update() {
  const password = $("#password").val();
  const changedPassword = $("#new-password").val();
  const otp = $("#user-code").val();
  const type = $("#membership-type").val();

  if (type === "None") {
    displayInfo("Select your membership type from the dropdown menu");
    return;
  }
  else if (password === "") {
    displayInfo("Input a password");
    return;
  } else if (otp === "") {
    displayInfo("Enter the OTP sent to your email or get a new one");
    return;
  } else if (changedPassword === "") {
    displayInfo("Re-type your password");
    return;
  } else if (password !== changedPassword) {
    displayInfo("Passwords do not match");
    return;
  } else {
    $.ajax({
      type: "POST",
      url: "./assets/server/change-password.php",
      data: {
        email: $("#email").val(),
        password: changedPassword,
        type: type,
        otp: otp,
      },
      dataType: "json",
      success: function (response) {
        const content = response.Info;
        if (content === "Password changed successfully") {
          displaySuccess(content);
          setTimeout(function () {
            $("#psw-div, #email-div, #forgot-p").css({
              display: "block",
            });
            $("#new-psw-div, #code-div, #code-trigger").css({
              display: "none",
            });
            $("#loginUser").text("Login");
          }, 2000);
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

$("#login-form").on("submit", function (e) {
  e.preventDefault();
  let loginButtonText = $("#loginUser").text();
  switch (loginButtonText) {
    case "Login":
      login();
      break;
    case "Verify email":
      verify();
      break;
    case "Update password":
      update();
      break;
  }
});

$("#reset-link").on("click", function (e) {
  e.preventDefault();
  $("#psw-div, #info-p").css({ display: "none" });
  $("#email").val("");
  $("#loginUser").text("Verify email");
});
