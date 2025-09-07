import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {

  $(`#admin-section .user-card`).each(function () {
    const name = $(this).find(".user-card-name").text();
    if (name === $('#admin-name').val()) {
      //$(this).remove();
      $(`#admin-section`).prepend($(this));
    }
  });

  //Preview selected image
  function previewImage(input) {
    if (input.files && input.files[0]) {
      let extension = input.files[0].name.split(".").pop().toLowerCase();
      //let sizeCal = input.files[0].size / 1024 / 1024;
      switch (extension) {
        case "zip":
        case "jfif":
          displayInfo(
            "Selected file format not supported. Choose a file with either .jpg, .jpeg, .png, .pdf, .docx, .mp4 and .mp3 extension !"
          );
          $("#send-email").attr("disabled", true);
          break;
        case "jpg":
        case "jpeg":
        case "png":
        case "pdf":
        case "docx":
        case "mp3":
        case "mp4":
          displaySuccess("Included attachment is supported");
          $("#send-email").attr("disabled", false);
          break;
      }
    }
  }

  $("#attachment").on("change", function () {
    previewImage(this);
  });

  //New codes begin here

  $("#choose-recipient").on("click", function () {
    $(".mail-overlay").css({ display: "flex" });
  });

  function scrollToAdmin() {
    document.getElementById("admin-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-admins").css({ "border-bottom": "3px solid #181d38" });
    $("#view-affiliates, #view-vendors").css({ "border-bottom": "none" });
  }

  function scrollToAffiliate() {
    document.getElementById("affiliate-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-affiliates").css({ "border-bottom": "3px solid #181d38" });
    $("#view-admins, #view-vendors").css({ "border-bottom": "none" });
  }

  function scrollToVendor() {
    document.getElementById("vendor-section-view").scrollIntoView({
      behavior: "smooth",
      inline: "nearest",
      block: "center",
    });
    $("#view-vendors").css({ "border-bottom": "3px solid #181d38" });
    $("#view-affiliates, #view-admins").css({ "border-bottom": "none" });
  }

  $("#view-admins").on("click", function () {
    scrollToAdmin();
  });

  $("#view-affiliates").on("click", function () {
    scrollToAffiliate();
  });

  $("#view-vendors").on("click", function () {
    scrollToVendor();
  });

  //Storage arrays
  let mailRecipients = [];

  //Remove single recipient
  function removeRecipient(id) {
    // Remove the object with the given id
    let newArray = mailRecipients.filter((obj) => obj.id !== id);
    // Update the original array
    mailRecipients = newArray;
  }

  //Remove multiple recipients
  function removeMultiple(view) {
    let idsToRemove = [];
    // Check if the type is 'admin'
    $(`#${view}-section .user-card .add-user`).each(function () {
      const id = $(this).attr("id");
      idsToRemove.push(id);
    });
    // Remove objects from the array based on their IDs
    let newArray = mailRecipients.filter(
      (obj) => !idsToRemove.includes(obj.id)
    );
    // Update the original array
    mailRecipients = newArray;
  }

  //Add and remove items singly to admin array
  $("#admin-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name,
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-admin").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Add and remove items singly to affiliates array
  $("#affiliate-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name          
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-affiliate").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Add and remove items singly to vendors array
  $("#vendor-section")
    .find('input[type="checkbox"]')
    .each(function () {
      $(this).on("change", function () {
        const id = $(this).attr("id");
        const email = $(this).parent().find(".email-address").text();
        const name = $(this).parent().parent().find(".user-card-name").text();
        if (this.checked) {
          //Add item to array
          const receiver = {
            id: id,
            email: email,
            name: name
          };
          mailRecipients.push(receiver);
        } else {
          //Remove master or sub-master check
          $("#send-to-all").prop("checked", false);
          $("#send-all-vendor").prop("checked", false);
          //Remove item from array
          removeRecipient(id);
        }
      });
    });

  //Custom function for mass adding / removing of members
  function addMembers(view) {
    removeMultiple(view);
    $(`#${view}-section .user-card`).each(function () {
      const id = $(this).find(".add-user").attr("id");
      const email = $(this).find(".email-address").text();
      const name = $(this).find(".user-card-name").text();
      $(this).find(".add-user").prop("checked", true);
      //Add item to array
      const receiver = { 
        id: id, 
        email: email, 
        name: name
      };
      mailRecipients.push(receiver);
    });
  }

  //Custom fucntion for unchecking all checkboxes at once
  function uncheckBoxes(view) {
    $(`#${view}-section .user-card`).each(function () {
      $(this).find(".add-user").prop("checked", false);
    });
  }

  //Add or remove all admins at once
  $("#send-all-admin").on("change", function () {
    if (this.checked) {
      removeMultiple('admin');
      addMembers("admin");
    } else {
      removeMultiple("admin");
      uncheckBoxes("admin");
    }
  });

  //Add  or remove all affiliates at once
  $("#send-all-affiliate").on("change", function () {
    if (this.checked) {
       removeMultiple("affiliate");
      addMembers("affiliate");
    } else {
       removeMultiple("affiliate");
      uncheckBoxes("affiliate");
    }
  });

  //Add  or remove all vendors at once
  $("#send-all-vendor").on("change", function () {
    if (this.checked) {
       removeMultiple("vendor");
      addMembers("vendor");
    } else {
       removeMultiple("vendor");
      uncheckBoxes("vendor");
    }
  });

  //Add  or remove everyone at once
  $("#send-to-all").on("change", function () {
    if (this.checked) {
      $("#send-all-admin, #send-all-affiliate, #send-all-vendor").prop(
        "checked",
        true
      );
      //Clear the array
      mailRecipients = [];
      addMembers("admin");
      addMembers("affiliate");
      addMembers("vendor");
    } else {
      $("#send-all-admin, #send-all-affiliate, #send-all-vendor").prop(
        "checked",
        false
      );
      //Clear the array
      mailRecipients = [];
      uncheckBoxes("admin");
      uncheckBoxes("affiliate");
      uncheckBoxes("vendor");
    }
  });

  $("#close-modal").on("click", function () {
    $(".mail-overlay").css({ display: "none" });
  });

  //New code ends here

  $("#send-email").on("click", function () {
    //const sender = $("#admin-name").val();
    const sender = 'Chromstack';
    const subject = $("#subject").val();
    const message = $("#compose-textarea").val();
    const attachment = $("#attachment").val();
    const recipients = JSON.stringify(mailRecipients);

    if (sender === "" || subject === "" || message === "") {
      displayInfo("Fill out all text fields before submitting !");
      $("#send-email").attr("disabled", false);
    } else if (mailRecipients.length === 0) {
      $(".mail-overlay").css({ display: "flex" });
      return false;
    } else {
      if (attachment === "" || attachment === null) {
        const decision = confirm("Send without an attachment ?");
        if (decision === true) {
          $("#send-email").text("Sending...").attr("disabled", true);
          //Prepare request parameters
          setTimeout(() => {
            $.ajax({
              type: "POST",
              url: "../server/send-email-without-attachment.php",
              data: {
                recipients: recipients,
                subject: subject,
                message: message,
                sender: sender,
              },
              dataType: "json",
              success: function (response) {
                for (const key in response) {
                  if (Object.hasOwnProperty.call(response, key)) {
                    const content = response[key];
                    if (content === "Message sent successfully") {
                      displaySuccess(content);
                      $(
                        "#send-to-all, #send-all-admin, #send-all-affiliate, #send-all-vendor"
                      ).prop("checked", false);
                      uncheckBoxes("admin");
                      uncheckBoxes("affiliate");
                      uncheckBoxes("vendor");
                      mailRecipients = [];
                      $("#subject, #attachment, #compose-textarea").val(
                        ""
                      );
                      $("#send-email")
                        .html("<i class='fas fa-check'></i> Sent")
                        .attr("disabled", true);
                      setTimeout(() => {
                        $("#send-email")
                          .html("<i class='far fa-envelope'></i> Send")
                          .attr("disabled", false);
                      }, 2000);
                      window.location.reload();
                    } else {
                      displayInfo(content);
                      $("#send-email")
                        .html("<i class='far fa-envelope'></i> Send")
                        .attr("disabled", false);
                    }
                  }
                }
              },
              error: function (e) {
                displayInfo(e.responseText);
                $("#send-email")
                  .html("<i class='far fa-envelope'></i> Send")
                  .attr("disabled", false);
              },
            });
          }, 4000);
        } else {
          displayInfo("Select a file to continue..");
        }
      } else {
        const request = new FormData();
        request.append("recipients", recipients);
        request.append("sender", sender);
        request.append("subject", subject);
        request.append("message", message);
        request.append(
          "attachment",
          document.getElementById("attachment").files[0]
        );
        $("#send-email").text("Sending...").attr("disabled", true);
        $.ajax({
          type: "POST",
          url: "../server/send-email-with-attachment.php",
          data: request,
          dataType: "json",
          processData: false,
          contentType: false,
          cache: false,
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "Message sent successfully") {
                  displaySuccess(content);
                   $(
                     "#send-to-all, #send-all-admin, #send-all-affiliate, #send-all-vendor"
                   ).prop("checked", false);
                   uncheckBoxes("admin");
                   uncheckBoxes("affiliate");
                   uncheckBoxes("vendor");
                   mailRecipients = [];
                  $("#subject, #attachment, #compose-textarea").val("");
                  $("#send-email")
                    .html("<i class='fas fa-check'></i> Sent")
                    .attr("disabled", true);
                  setTimeout(() => {
                    $("#send-email")
                      .html("<i class='far fa-envelope'></i> Send")
                      .attr("disabled", false);
                  }, 2000);
                  window.location.reload();
                } else {
                  displayInfo(content);
                  $("#send-email")
                    .html("<i class='far fa-envelope'></i> Send")
                    .attr("disabled", false);
                }
              }
            }
          },
          error: function (e) {
            displayInfo(e.responseText);
            $("#send-email")
              .html("<i class='far fa-envelope'></i> Send")
              .attr("disabled", false);
          },
        });
      }
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");
});
