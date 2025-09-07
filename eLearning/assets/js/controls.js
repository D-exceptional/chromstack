import { displayInfo } from "./export.js";

$(document).ready(function () {
  //Get latest tracks from the server when the page is refreshed
  window.addEventListener("beforeunload", function (event) {
    window.location.reload(true); // Force reload from the server
  });

  //Get course details
  const courseID = $("#course-id-input").val();
  const courseType = $("#course-type-input").val();
  const courseTitle = $("#course-title-input").val();
  //Get logged in user ID and type
  const userID = $("#access-id-holder").text();
  const userType = $("#access-type-holder").text();
  let wishlistID = "";

  function manageWishlist(action, id, filename) {
    if (action !== "" && id !== "" && filename !== "") {
      //Take action
      switch (action) {
        case "obtain":
          //Prepare payload
          const obtainPayload = {
            action: "obtain",
            userid: userID,
            usertype: userType,
            courseid: courseID,
            coursetype: courseType,
            coursetitle: courseTitle,
            filename: filename,
            wishlist: id,
          };
          //Send to server
          $.ajax({
            type: "POST",
            url: "assets/server/manage-wishlist.php",
            data: obtainPayload,
            dataType: "json",
            success: function (response) {
              console.log(response);
              for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                  const content = response.Info;
                  if (
                    content === "Wishlist Found" ||
                    content === "Wishlist Created"
                  ) {
                    //Set wishlist ID
                    wishlistID = response.details.id;
                    return;
                  } else {
                    //displayInfo(response.details.error);
                    console.log(response.details.error);
                  }
                }
              }
            },
            error: function () {
              displayInfo("Error connecting to server");
            },
          });
          break;
        case "track":
          //Prepare payload
          const trackPayload = {
            action: "track",
            userid: userID,
            usertype: userType,
            courseid: courseID,
            coursetype: courseType,
            coursetitle: courseTitle,
            filename: filename,
            wishlist: id,
          };
          //Send to server
          $.ajax({
            type: "POST",
            url: "assets/server/manage-wishlist.php",
            data: trackPayload,
            dataType: "json",
            success: function (response) {
              console.log(response);
              for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                  const content = response.Info;
                  switch (content) {
                    case "Update Successful":
                    case "Course Completion":
                      displayInfo(response.details.message);
                      break;
                    case "Error":
                      //displayInfo(response.details.error);
                      console.log(response.details.error);
                      break;
                  }
                }
              }
            },
            error: function (e) {
              displayInfo("Error connecting to server");
            },
          });
          break;
      }
    } else {
      displayInfo("Some parameters are missing");
    }
  }

  //Get wishlistID for this user on this course
  manageWishlist("obtain", 1.5, "no_file");

  // Function to format seconds into h:mm:ss
  function displayTime(duration) {
    var hours = Math.floor(duration / 3600);
    var minutes = Math.floor((duration % 3600) / 60);
    var seconds = Math.floor(duration % 60);
    if (hours < 10) {
      hours = "0" + hours;
    }
    if (minutes < 10) {
      minutes = "0" + minutes;
    }
    if (seconds < 10) {
      seconds = "0" + seconds;
    }
    //track time
    return hours + ":" + minutes + ":" + seconds;
  }

  // Function to format seconds into h:mm:ss
  function formatTime(duration) {
    var hours = Math.floor(duration / 3600);
    var minutes = Math.floor((duration % 3600) / 60);
    var seconds = Math.floor(duration % 60);
    if (hours < 10) {
      hours = "0" + hours;
    }
    if (minutes < 10) {
      minutes = "0" + minutes;
    }
    if (seconds < 10) {
      seconds = "0" + seconds;
    }
    //track time
    $("#video-playtime").text(hours + ":" + minutes + ":" + seconds);
  }

  //Get each video duration
  $(".video_content").each(function (index, el) {
    const videoURL = $(el).find(".file-path").text();
    const videoItem = document.createElement("video");
    videoItem.src = videoURL;
    //Get duration
    videoItem.addEventListener("loadedmetadata", function () {
      $(el).find(".video-duration").text(displayTime(videoItem.duration));
    });
  });

  $(
    "#video-content-overlay, video, #image-content-overlay, img, #word-content-overlay, #word_container, #pdf-content-overlay, #pdf_renderer"
  ).on("contextmenu", function (e) {
    e.preventDefault();
    displayInfo("Content is not downloadable!");
  });

  $("#rangeSelector").val(0);

  //Process PDF file
  const myState = {
    pdf: null,
    currentPage: 1,
    zoom: 1,
  };

  function render() {
    myState.pdf.getPage(myState.currentPage).then((page) => {
      const canvas = document.getElementById("pdf_renderer");
      const ctx = canvas.getContext("2d");
      //Calculate viewport
      const viewport = page.getViewport(myState.zoom);
      canvas.width = viewport.width;
      canvas.height = viewport.height;
      //Render page
      page.render({
        canvasContext: ctx,
        viewport: viewport,
      });
    });
  }

  //Go to previous page
  $("#go_previous").on("click", function (e) {
    if (myState.pdf === null || myState.currentPage === 1) {
      return;
    }
    myState.currentPage -= 1;
    $("#current_page").val(myState.currentPage);
    render();
  });

  //Go to next page
  $("#go_next").on("click", function (e) {
    if (
      myState.pdf === null ||
      myState.currentPage > myState.pdf._pdfInfo.numPages
    ) {
      return;
    }
    myState.currentPage += 1;
    $("#current_page").val(myState.currentPage);
    render();
  });

  //Dynamically select page
  $("#current_page")
    .on("keypress", function (e) {
      if (myState.pdf === null) {
        return;
      }
      // Get key code
      const code = e.keyCode ? e.keyCode : e.which;
      // If key code matches that of the Enter key
      if (code == 13) {
        const desiredPage =
          document.getElementById("current_page").valueAsNumber;
        if (desiredPage >= 1 && desiredPage <= myState.pdf._pdfInfo.numPages) {
          myState.currentPage = desiredPage;
          $("#current_page").val(desiredPage);
          render();
        }
      }
    })
    .on("keyup", function (e) {
      if (myState.pdf === null) {
        return;
      }
      const desiredPage = document.getElementById("current_page").valueAsNumber;
      if (desiredPage >= 1 && desiredPage <= myState.pdf._pdfInfo.numPages) {
        myState.currentPage = desiredPage;
        $("#current_page").val(desiredPage);
        render();
      }
    });

  //Zoom in file
  $("#zoom_in").on("click", function (e) {
    if (myState.pdf === null) {
      return;
    }
    myState.zoom += 0.5;
    render();
  });

  //Zoom out file
  $("#zoom_out").on("click", function (e) {
    if (myState.pdf === null) {
      return;
    }
    myState.zoom -= 0.5;
    render();
  });

  //Open docx file
  function openWordDocument(url, file) {
    //Send a XmlHttpRequest to the URL.
    const request = new XMLHttpRequest();
    request.open("GET", url, true);
    request.responseType = "blob";
    request.onload = function () {
      //Set the ContentType to docx.
      const contentType =
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
      //Convert BLOB to File object.
      const doc = new File([request.response], contentType);
      //If Document not NULL, render it.
      if (doc !== null) {
        //Set the Document options.
        const docxOptions = Object.assign(docx.defaultOptions, {
          useMathMLPolyfill: true,
        });
        //Reference the Container DIV.
        const container = document.querySelector("#word_container");
        //Render the Word Document.
        docx.renderAsync(doc, container, null, docxOptions);
      }
    };
    request.send();
    //Save progress
    manageWishlist("track", wishlistID, file);
  }

  //View contents for list course items
  $(".lectures_lists")
    .children("li")
    .each(function (index, el) {
      $(el).on("click", function () {
        const itemClass = $(el).attr("class");
        switch (itemClass) {
          case "image_content":
            const imagePath = $(el).find(".file-path").text();
            const imageName = $(el).find(".file-title").text();
            $("#image-content-overlay").css({
              display: "flex",
            });
            $("#image-content-overlay img").attr("src", imagePath);
            //Save progress
            manageWishlist("track", wishlistID, imageName);
            break;
          case "video_content":
            const videoPath = $(el).find(".file-path").text();
            const videoName = $(el).find(".file-title").text();
            $("#video-content-overlay").css({
              display: "flex",
            });
            $("#rangeSelector").val(0);
            $("#video-content-overlay video").attr("src", videoPath);
            //Save progress
            manageWishlist("track", wishlistID, videoName);
            break;
          case "pdf_content":
            const pdfPath = $(el).find(".file-path").text();
            const pdfUrl = pdfPath.replace('./../', 'https://chromstack.com/');
            const pdfName = $(el).find(".file-title").text();
            //Save progress
            manageWishlist("track", wishlistID, pdfName);
            $("#pdf-content-overlay").css({
              display: "flex",
            });
            //Display PDF file
            /*pdfjsLib.getDocument(pdfPath).then((pdf) => {
              myState.pdf = pdf;
              render();
            });*/
            $("#pdf-content-overlay iframe").attr('src', `https://docs.google.com/viewer?embedded=true&url=${pdfUrl}`);
            break;
          case "word_content":
            const wordPath = $(el).find(".file-path").text();
            const wordUrl = wordPath.replace('./../', 'https://chromstack.com/');
            const wordName = $(el).find(".file-title").text();
            $("#word-content-overlay").css({
              display: "flex",
            });
            //openWordDocument(wordPath, wordName);
            $("#word-content-overlay iframe").attr('src', `https://docs.google.com/viewer?embedded=true&url=${wordUrl}`);
            //Save progress
            manageWishlist("track", wishlistID, wordName);
            break;
        }
      });
    });
    

  //Bind events
  const video = document.getElementById("course-video");
  const image = document.getElementById("course-image");

  function adjustVideoResolution(downlinkSpeed) {
    // Define resolution thresholds based on network speed
    if (downlinkSpeed < 1.0) {
      video.src = "low_resolution_video.mp4";
    } else if (downlinkSpeed >= 1.0 && downlinkSpeed < 5.0) {
      video.src = "medium_resolution_video.mp4";
    } else {
      video.src = "high_resolution_video.mp4";
    }
  }

  /* Check if the browser supports the Network Information API
if ('connection' in navigator) {
  // Monitor network changes
  navigator.connection.addEventListener('change', function() {
    if(!video.paused){
      adjustVideoResolution(navigator.connection.downlink); // Pass downlink speed to adjust resolution
    }
  });
}*/

  //Monitor video events
  $("#course-video")
    .on("onloadedmetadata", function (e) {
      $(e.target).parent().find("#rangeSelector").empty().val(0);
      formatTime(e.target.currentTime);
    })
    .on("timetrack", function (e) {
      const percent = (e.target.currentTime / e.target.duration) * 100;
      $(e.target).parent().find("#rangeSelector").empty().val(percent);
      setInterval(() => {
        formatTime(e.target.currentTime);
      }, 1000);
    })
    .on("timeupdate", function (e) {
      setInterval(() => {
        formatTime(e.target.currentTime);
      }, 1000);
      const percent = (e.target.currentTime / e.target.duration) * 100;
      $(e.target).parent().find("#rangeSelector").empty().val(percent);
    })
    .on("pause", function (e) {
      const percent = (e.target.currentTime / e.target.duration) * 100;
      $(e.target).parent().find("#rangeSelector").empty().val(percent);
      formatTime(e.target.currentTime);
    })
    .on("ended", function (e) {
      // Extract the filename from the video source
      $(e.target)
        .parent()
        .find(".first-row .icon-div i")
        .removeClass("fa-pause")
        .addClass("fa-play");
      $(e.target).parent().find("#rangeSelector").empty().val(0);
    });

  //Toggle playback time
  $("#rangeSelector").on("input", function (e) {
    video.currentTime = (e.target.value / 100) * video.duration;
    formatTime(video.currentTime);
  });

  //Play / pause video by icon toggle
  $(".first-row .icon-div i").on("click", function () {
    if ($(this).hasClass("fa-play")) {
      $(this).removeClass("fa-play").addClass("fa-pause");
      if (video.paused) {
        video.play();
      }
    } else {
      $(this).removeClass("fa-pause").addClass("fa-play");
      if (!video.paused) {
        video.pause();
      }
    }
  });

  //Play / pause video by video click
  $("video").on("click", function () {
    if (video.paused) {
      video.play();
      $("video")
        .parent()
        .find(".first-row .icon-div i")
        .removeClass("fa-play")
        .addClass("fa-pause");
    } else {
      video.pause();
      $("video")
        .parent()
        .find(".first-row .icon-div i")
        .removeClass("fa-pause")
        .addClass("fa-play");
    }
  });

  //Toggle playback time
  $("#video-speed").on("change", function () {
    const selecetedSpeed = $(this).val();
    const speed = Number(selecetedSpeed);
    video.playbackRate = speed;
  });

  //Close overlays
  $("#close-video").on("click", function () {
    video.pause();
    video.setAttribute("src", "");
    $("#rangeSelector").empty().val(0);
    $(".first-row .icon-div i").removeClass("fa-pause").addClass("fa-play");
    $("#video-content-overlay").css({
      display: "none",
    });
  });

  $("#close-image").on("click", function () {
    image.setAttribute("src", "");
    $("#image-content-overlay").css({
      display: "none",
    });
  });

  $("#close-pdf").on("click", function () {
    $("#pdf-content-overlay").css({
      display: "none",
    });
  });

  $("#close-word").on("click", function () {
    $("#word-content-overlay").css({
      display: "none",
    });
  });
});
