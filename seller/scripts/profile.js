import { displaySuccess, displayInfo } from "../scripts/export.js";

//Update Profile Picture
function updateProfile() {
    const request = new FormData();
        request.append('profile', document.getElementById('profile-image').files[0]);
        request.append('id', $('#vendorID').val());
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "../server/update-profile.php",
        data: request,
        dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (
                    content !== 'Something went wrong' 
                    && content !== 'Failed to upload image' 
                    && content !== 'Image must have either .jpeg, .png or .jpg extension' 
                    && content !== 'Upload a valid image' 
                    && content !== 'All fields must be filled up'
                 )
                 {
                    displaySuccess(content);
                    setTimeout(function () {
                        window.location.reload();
                    },1500); 
                }
                else{
                displayInfo(content);
                }
            }
        }
    },
    error: function (e) {
        displayInfo(e.responseText);
        }
    });
}

function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        let extension = input.files[0].name.split('.').pop().toLowerCase();
        switch (extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            let reader = new FileReader();
                reader.onload = function(e) {
                    $('.text-center img').attr('src', e.target.result); 
                    updateProfile();
                }
                reader.readAsDataURL(input.files[0]);
            break;
            case 'mp3':
            case 'mp4':
            case 'pdf':
            case 'zip':
                displayInfo('Selected file is not an image !');
                $('.text-center img.profile-user-img img-fluid img-circle').attr('src', '../../../uploads/user.png');
            break;
        }
    }
}  

//Toggle Profile Preview
$('#profile-image').on('change', function () {
    previewProfileImage(this);
});

//Initialize Profile Submit
$('#update-profile').on('click', function () {
    $('#profile-image').click();
});

//Edit Bio Data
$('#details-form').on('submit', function (event) {

    event.preventDefault();

        if ($('#vendorID').val() === 'null' || $('#vendorID').val() === '') {
            displayInfo('ID missing');
        }
        else {
            $.ajax({
                type: "POST",
                url: "../server/update-details.php",
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    for (const key in response) {
                        if (Object.hasOwnProperty.call(response, key)) {
                            const content = response[key];
                            if (content !== 'Something went wrong' && content !== 'Some fields are empty') {
                                displaySuccess(content);
                                setTimeout(function () {
                                    window.location.reload();
                                },1500);
                            }
                            else{
                            displayInfo(content);
                            }
                        }
                    }
                },
                error: function (e) {
                displayInfo(e.responseText);
                }
            });
        }
});

//Toggle tab view
$('.nav-item a').each(function (pos, el) {
 $(el).on("click", function () {
   const linkText = $(el).text();
   switch (linkText) {
     case "Details":
        $('#settings').css({ display: 'block'});
        $("#bank, #security").css({ display: "none" });
       break;
     case "Earnings":
         $("#bank").css({ display: "block" });
         $("#settings, #security").css({ display: "none" });
       break;
     case "Security":
         $("#security").css({ display: "block" });
         $("#bank, #settings").css({ display: "none" });
       break;
   }
 });
});