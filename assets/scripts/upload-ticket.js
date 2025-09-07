import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {

    $('#file-click').on('click', function () {
        $('#banner').click();
    });

    //Check zip file
      function previewCourseFile(input) {
        if (input.files && input.files[0]) {
          let name = input.files[0].name;
          let extension = input.files[0].name.split('.').pop().toLowerCase();
          let sizeCal = input.files[0].size / 1024 / 1024;
          switch (extension) {
              case 'jpg':
              case 'jpeg':
              case 'png':
                   const reader = new FileReader();
                      reader.onload = function(e) {
                          $('#banner-name').css({'display':'block'}).empty().text(name);
                          $('#upload').attr('disabled', false);
                      }
                      reader.readAsDataURL(input.files[0]);
                      displaySuccess('Selected file format supported');
              break;
              case 'zip':
              case 'mp3':
              case 'mp4':
              case 'pdf':
              case 'docx':
              case 'jfif':
                  $('#banner-name').css({'display':'block'}).empty().text(name);
                  displayInfo('Selected file is not an image!');
                  $('#upload').attr('disabled', true);
              break;
          }
        }
      }
      
    $('#banner').on('change', function () {
         previewCourseFile(this);
    });
    
    function checkStatus(value) {
      $.ajax({
        type: "POST",
        url: "./assets/server/check-owner-details.php",
        data: { email: value },
        dataType: "json",
        success: function (response) {
          for (var key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response[key];
              if (content === "Email is registered") {
                displaySuccess(content);
                $(
                  "#fullname-div, #contact-div, #business-div"
                ).css({ display: "none" });
                $("#status").val("Registered");
              } else {
                displayInfo(content);
                $(
                  "#fullname-div, #contact-div, #business-div"
                ).css({ display: "block" });
                $("#status").val("Unregistered");
              }
            }
          }
        },
        error: function () {
          displayInfo("Error connecting to server");
        },
      });
    }
    
    $("#email").on("blur", function () {
      if ($(this).val() !== "") {
        checkStatus($(this).val());
      } else {
        displayInfo("No email supplied !");
        $("#fullname-div, #contact-div, #business-div").css({
          display: "none",
        });
      }
    });
    

    //Check All Inputs
    $('.form-control').each(function (index, el) {
        $(el).on('keyup', function () {
            if ($(el).val() !== '') {
                $(el).css({'border':'none'});
                $('#upload').attr('disabled', false);
            }
             else {
                $(el).css({'border':'2px solid red'});
                $('#upload').attr('disabled', true);
            }
        })
        .on('blur', function () {
            if ($(el).val() !== '') {
                $(el).css({'border':'none'});
                $('#upload').attr('disabled', false);
            }
             else {
                $(el).css({'border':'2px solid red'});
                $('#upload').attr('disabled', true);
            }
        });
    });


    $("#ticket-form").on("submit", function (event) {
        event.preventDefault();
        if ($('#banner').val() !== '') {
            if ($('#title').val() !== '' && $('#description').val() !== '' && $('#email').val() !== '') {
              //Upload form
              const form = document.getElementById("ticket-form");
              const request = new FormData(form);
              //console.log(request);
              $("#upload").empty().text("Submitting...").attr("disabled", true);
              //$("#uploadBar").css({ display: "block" });
              //Send to server
              $.ajax({
                type: "POST",
                enctype: "multipart/form-data",
                url: "./assets/server/upload-ticket.php",
                data: request,
                dataType: "json",
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                  for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                      const content = response[key];
                      if (content === "Details saved successfully") {
                        displaySuccess(content);
                        $("#upload")
                          .empty()
                          .text("Details submitted")
                          .attr("disabled", true);
                        $("#ticket-form")[0].reset();
                        $("#uploadBar").css({ display: "none" });
                        $("#progressBar").css({ width: "0%" }).text("");
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                      } else {
                        displayInfo(content);
                         $("#upload")
                          .empty()
                          .text("Submit")
                          .attr("disabled", false);
                      }
                    }
                  }
                },
                error: function (e) {
                  displayInfo("Error connecting to server");
                  console.log(e.responseText);
                  $("#uploadBar").css({ display: "none" });
                  $("#progressBar").css({ width: "0%" }).text("");
                  $("#upload")
                          .empty()
                          .text("Submit")
                          .attr("disabled", false);
                },
              });
            }
            else{
                displayInfo('Fill out all fields to proceed!');
            }
        }
        else{
            displayInfo('Attach the required file to proceed!');
        }
    });
});