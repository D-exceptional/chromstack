import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {

    //$("#fullname, #email, #contact").attr("disabled", true);

    //Prepare options
    function prepareOptions() {
        for (let index = 5; index < 105; index+=5) {
            let option = `<option value="${index}%" style='height: 40px;background: transparent !important;'>${index}%</option>`;
            $('#commission').append(option);
        }
    }

    prepareOptions();

    $('#file-click').on('click', function () {
        $('#cover-image').click();
    });

    //Check zip file
      function previewCourseFile(input) {
        if (input.files && input.files[0]) {
          let name = input.files[0].name;
          let extension = input.files[0].name.split('.').pop().toLowerCase();
          let sizeCal = input.files[0].size / 1024 / 1024;
          switch (extension) {
                case 'zip':
                case 'mp4':
                case 'mp3':
                case 'jfif':
                   $('#course-file-name').css({'display':'block'}).empty().text(name);
                   displayInfo('Selected file format not supported. Choose an image !');
                   $('#upload').attr('disabled', true);
              break;
              case 'jpg':
              case 'jpeg':
              case 'png':
                   const reader = new FileReader();
                      reader.onload = function(e) {
                          $('#course-file-name').css({'display':'block'}).empty().text(name);
                          $('#upload').attr('disabled', false);
                      }
                      reader.readAsDataURL(input.files[0]);
                      displaySuccess('Selected file format supported');
              break;
          }
        }
      }
      
      $('#cover-image').on('change', function () {
          previewCourseFile(this);
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


    $("#course-upload-form").on("submit", function (event) {
        event.preventDefault();
        if ($('#course-main-file').val() !== '') {
            if (
              $("#title").val() !== "" &&
              $("#description").val() !== "" &&
              $("#amount").val() !== "" &&
              $("#page").val() !== "" &&
              $("#commission").val() !== ""
            ) {
              //Upload form
              const form = document.getElementById("course-upload-form");
              const data = new FormData(form);
              //$(".form-control").attr("disabled", true);
              $("#upload").empty().text("Uploading...").attr("disabled", true);
              $("#uploadBar").css({ display: "block" });
              //Send to server
              $.ajax({
                type: "POST",
                enctype: "multipart/form-data",
                url: "server/upload-vendor-course.php",
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                cache: false,
                xhr: function () {
                  var xhr = new window.XMLHttpRequest();
                  // Upload progress
                  xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                      if (evt.lengthComputable) {
                        var percentComplete =
                          Math.round((evt.loaded / evt.total) * 100) + "%";
                        $("#progressBar")
                          .css({ width: percentComplete })
                          .text(percentComplete);
                        //Track level
                        if ($("#progressBar").text() === "100%") {
                          $("#upload")
                            .empty()
                            .text("Processing...")
                            .attr("disabled", true);
                        }
                      }
                    },
                    false
                  );
                  return xhr;
                },
                success: function (response) {
                  for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                      const content = response[key];
                      if (content === "Course uploaded successfully") {
                        displaySuccess(content);
                        $("#upload")
                          .empty()
                          .text("Upload Successful")
                          .attr("disabled", true);
                        $("#course-upload-form")[0].reset();
                        $("#uploadBar").css({ display: "none" });
                        $("#progressBar").css({ width: "0%" }).text("");
                        setTimeout(() => {
                          window.location.reload();
                        }, 2000);
                      } else {
                        displayInfo(content);
                      }
                    }
                  }
                },
                error: function (e) {
                  displayInfo("Error connecting to server");
                },
              });
            } else {
              displayInfo("Fill out all text fields to proceed!");
            }
        }
        else{
            displayInfo('Attach the required files to proceed!');
        }
    });
});