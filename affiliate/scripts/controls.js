import { displayInfo } from "./export.js";

//Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

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
  //Update time
  $("#video-playtime").text(hours + ":" + minutes + ":" + seconds);
}

const video = document.getElementById("tutorial-video");
/*if (!video.paused) {
  video.pause();
}*/

$('video').on("contextmenu", function (e) {
  e.preventDefault();
  displayInfo('Video is not downloadable');
});

$("#rangeSelector").val(0);

//Monitor video events
$("#tutorial-video")
  .on("onloadedmetadata", function (e) {
    $(e.target).parent().find("#rangeSelector").val(0);
    formatTime(e.target.currentTime);
  })
  .on("timeupdate", function (e) {
    const percent = (e.target.currentTime / e.target.duration) * 100;
    $(e.target).parent().find("#rangeSelector").val(percent);
    setInterval(() => {
      formatTime(e.target.currentTime);
    }, 1000);
  })
  .on("pause", function (e) {
    const percent = (e.target.currentTime / e.target.duration) * 100;
    $(e.target).parent().find("#rangeSelector").val(percent);
    formatTime(e.target.currentTime);
  })
  .on("ended", function (e) {
    $(e.target)
      .parent()
      .find(".first-row .icon-div i")
      .removeClass("fa-pause")
      .addClass("fa-play");
    $(e.target).parent().find("#rangeSelector").val(0);
  });

//Toggle playback time
$("#rangeSelector").on("input", function (e) {
  video.currentTime = (e.target.value / 100) * video.duration;
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
$(video).on("click", function () {
  if (video.paused) {
    video.play();
    $(video)
      .parent()
      .find(".first-row .icon-div i")
      .removeClass("fa-play")
      .addClass("fa-pause");
  } else {
    video.pause();
    $(video)
      .parent()
      .find(".first-row .icon-div i")
      .removeClass("fa-pause")
      .addClass("fa-play");
  }
});

//Open overlay
$("#open-modal").on("click", function () {
  $("#video-content-overlay").css({ display: 'flex'});
  $("#rangeSelector").val(0);
});

//Close overlay
$("#close-modal").on("click", function () {
  video.pause();
  $("#video-content-overlay").css({ display: 'none'});
  $("#rangeSelector").val(0);
});
