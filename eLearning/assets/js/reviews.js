  import { displaySuccess, displayInfo } from "./export.js";
  
  //Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

  const sessionName = $("#fullname-container").val();
  const sessionProfile = $("#profile-container").val();
  let image = '';
  
  //Post review
  function appendComment(name, comment, time) {
    //if (name === sessionName) {
       if (
         sessionProfile !== null &&
         sessionProfile !== "" &&
         sessionProfile !== "null"
       ) {
         image = "./../uploads/" + sessionProfile;
       } else {
         image = "./../assets/img/user.png";
       }
    //}

      const sentMessage = ` 
                      <div class='reviews-comments-item'>
                          <div class='review-comments-avatar'>
                              <img src='${image}' class='img-fluid' alt='Profile'> 
                          </div>
                          <div class='reviews-comments-item-text'>
                              <h4>
                                  <a href='#' style='padding-right: 5px;'>${name}</a>
                                  <span class='reviews-comments-item-date'>
                                      <!--<i class='ti-calendar theme-cl'></i>-->
                                      ${time}
                                  </span>
                              </h4>
                              <!--<div class='listing-rating high' data-starrating2='5'>
                                  <i class='ti-star active'></i>
                                  <i class='ti-star active'></i>
                                  <i class='ti-star active'></i>
                                  <i class='ti-star active'></i>
                                  <i class='ti-star active'></i>
                                  <span class='review-count'>4.9</span>
                              </div>-->
                              <div class='clearfix'></div>
                              <p>
                                  ${comment}
                              </p>
                              <div class='pull-left reviews-reaction'>
                                  <a href='#' class='comment-like active'><i class='ti-thumb-up'></i> 0</a>
                                  <a href='#' class='comment-dislike active'><i class='ti-thumb-down'></i> 0</a>
                                  <a href='#' class='comment-love active'><i class='ti-heart'></i> 0</a>
                              </div>
                          </div>
                      </div>
                  `; 

      $("#reviews-list").prepend(sentMessage);   
      $(".list-single-main-item-title.fl-wrap")
        .find("span")
        .empty()
        .text(
            $("#reviews-list").find('.reviews-comments-item').length
        );
      $(".reviews-comments-wrap").animate({ scrollTop: 0 }, "slow");   
      
      $('#reviewsParagraph').hide();

  }

  $("#review-form").on("submit", function (e) {
    e.preventDefault();
    $('form button').attr('disabled', true);
    const fullname = $("#fullname").val();
    const profile = $("#profile-container").val();
    const comment = $("#comment").val();
    const courseID = $("#course-id-input").val();
    const courseType = $("#course-type-input").val();
    const now = new Date();
    const time = now.toLocaleString();

    if (fullname === "") {
      displayInfo("Enter your fullname!");
      $('form button').attr('disabled', false);
      return;
    }
    else if(comment === ""){
        displayInfo("Type a review!");
        $('form button').attr('disabled', false);
        return;
    }
    else if (fullname === "" && comment === "") {
      displayInfo("Enter your fullname and type a review to proceed!");
      $('form button').attr('disabled', false);
      return;
    }
    else {
      //Prepare params
      const request = new FormData();
      request.append("name", fullname);
      request.append("profile", profile);
      request.append("comment", comment);
      request.append("id", courseID);
      request.append("type", courseType);
      request.append("time", time);
      //Send to server
      $.ajax({
        type: "POST",
        url: "assets/server/submit-review.php",
        data: request,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (
                content !== "Error submitting review" &&
                content !== "Some fields are empty"
              ) {
                appendComment(fullname, comment, time);
                $("#comment").val("");
                displaySuccess(content);
                $('form button').attr('disabled', false);
              } 
              else {
                displayInfo(content);
              }
            }
          }
        },
        error: function (e) {
          displayInfo('Error connecting to server');
           console.log(e.responseText);
        },
      });
    }
  });