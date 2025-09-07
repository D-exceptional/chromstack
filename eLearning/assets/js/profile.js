 import { displaySuccess, displayInfo } from "./export.js";
 
 //Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});
 
//Preview selected image
function previewImage(input) {
    if (input.files && input.files[0]) {
        let extension = input.files[0].name.split('.').pop().toLowerCase();
        //let sizeCal = input.files[0].size / 1024 / 1024;
        switch (extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
                displaySuccess('Image format supported');
            break;
            case 'mp3':
            case 'mp4':
            case 'pdf':
            case 'zip':
            case 'jfif':
                displayInfo('Selected file format not supported. Choose an image with either .jpg, .jpeg or .png extension !');
            break;
        }
    }
}

//Attach change event
$('#hidden-file-input').on('change', function () {
    previewImage(this);
});

//Attach save action
$("#save").on("click", function () {
  const id = $("#userID").val();
  const profile = $("#profile").val();
  const name = $("#fullname-container").val();
  if (profile === "") {
    displayInfo("Select an image!");
  }
  else {
     //Prepare request data
    const data = new FormData();
          data.append("id", id);
          data.append(
            "profile",
            document.getElementById("profile").files[0]
          );
          data.append('name', name);
    //Send data to server
    $.ajax({
      type: "POST",
      enctype: "multipart/form-data",
      url: "./assets/server/update-user-profile.php",
      data: data,
      dataType: "json",
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (
                  content === "Profile updated successfully" ||
                  content === "Profile set successfully"
                ) {
                  //Notify user
                  displaySuccess(content);
                  setTimeout(function(){
                      window.location.reload();
                  },1500);
                } else {
                  //Notify user
                  displayInfo(content);
                }
            }
        }
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  }
});