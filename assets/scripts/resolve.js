import { displaySuccess, displayInfo } from "./export.js";

$("#email").val("");

//Check status by email
$("#email")
  .on("keyup", function () {
    if ($(this).val() === "") {
      displayInfo("No email supplied!");
      $("button").attr("disabled", true);
    } else {
      $("button").attr("disabled", false);
    }
  })
  .on("blur", function () {
    if ($(this).val() !== "") {
      $("button").attr("disabled", false);
    } else {
      displayInfo("No email supplied !");
      $("button").attr("disabled", true);
    }
  });

//Check status by select toggle
$("#prospect").on("change", function () {
  if ($(this).val() === "None") {
    displayInfo("Select prospect type!");
    $("button").attr("disabled", true);
  } else {
    $("button").attr("disabled", false);
  }
});

function resolveIssue() {
  const email = $("#email").val();
  const type = $("#prospect").val();

  if(type === 'None'){
    displayInfo(
      "Select prospect type from the dropdown menu"
    );
    return;
  }
  else if (email === "") {
    displayInfo("Email field is empty!");
    return;
  } else {
    $("#email").val("");
    // Send to server
    $.ajax({
      type: "POST",
      url: "./assets/server/resolve.php",
      data: { email: email, type: type },
      dataType: "json",
      success: function (response) {
        const content = response.Info;
        if (content === "Resolution mails sent") {
          displaySuccess("Resolution mails sent to prospect successfully");
          setTimeout(() => {
            window.location.reload();
          }, 2000);
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

$("form").on("submit", function (e) {
  e.preventDefault();
  resolveIssue();
});
