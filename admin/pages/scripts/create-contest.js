import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {
  $("#createContest").on("click", function () {
    const contestName = $("#contestName").val();
    const contestDescription = $("#compose-textarea").val();
    const startDate = $("#startDate").val();
    const endDate = $("#endDate").val();
    const courseID = $("#courseID").val();
    const courseType = $("#courseType").val();
    const author = $(".info").text();

    if (
      contestName === "" ||
      contestDescription === "" ||
      startDate === "" ||
      endDate === ""
    ) {
      displayInfo("Fill out all fields before submitting !");
    } else {
      const request = new FormData();
      request.append("name", contestName);
      request.append("description", contestDescription);
      request.append("start", startDate);
      request.append("end", endDate);
      request.append("id", courseID);
      request.append("type", courseType);
      request.append("author", author);

      $.ajax({
        type: "POST",
        url: "../server/create-contest.php",
        data: request,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response[key];
              if (
                content !== "Error creating new contest" &&
                content !== "Some fields are empty"
              ) {
                displaySuccess(content);
                //Reset values
                $("#contestName").val("");
                $(".note-editable.card-block").text("");
                $("#startDate").val("");
                $("#endDate").val("");
              } else {
                displayInfo(content);
                console.log(content);
              }
            }
          }
        },
        error: function (e) {
          displayInfo(e.responseText);
          console.log(e.responseText);
        },
      });
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

});
