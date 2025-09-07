$(document).ready(function () {
$(".dropdown").hover(function () {
    $(this).find("fas fa-chevron-down arrow").toggleClass("fas fa-chevron-down down");
});

$('.accordion-header').click(function() {
const accordionContent = $(this).next('.accordion-content');
const accordionSign = $(this).find('.accordion-sign');
if (accordionContent.is(':visible')) {
    accordionContent.slideUp();
    accordionSign.text('+');
    }
    else {
        $('.accordion-content').slideUp();
        $('.accordion-sign').text('+');
        accordionContent.slideDown();
        accordionSign.text('-');
    }
});

/*var typing = new Typed(".main-text", {
    strings: ["am Okeke Chukwuebuka Augustine", "am a fullstack web developer", "work with languages like HTML, CSS, JQuery, JavaScript and PHP", "am also into glassmorphic graphic design", "love to code !"],
    typeSpeed: 120,
    backSpeed: 40,
    //loop: true, //feel free to uncomment this line if you want this code to repeat after completion
    loop: false,
});*/

// Set the date we're counting down to
var countDownDate = new Date("Mar 2, 2024 23:59:00").getTime();

// Update the count down every 1 second
var x = setInterval(function () {
    // Get today's date and time
    var now = new Date().getTime();
    // Find the distance between now and the count down date
    var distance = countDownDate - now;
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    //Add preceding zeros
    if (days < 10) {
        days = "0" + days;
    }
    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    // Display the result in the element with id="countdown-timer"
    $("#countdown-timer").html(`
                                Free affiliate sign up ends in <b>${days}</b> Days <b>${hours}</b> Hours 
                                <b>${minutes}</b> Minutes <b>${seconds}</b>  Seconds
                             `);
    // If the count down is finished, write some text
    if (distance <= 0) {
        clearInterval(x);
        $("#timer")
        .empty()
        .text('Free registration has officially ended!');
    }
}, 1000);

$('.close').on('click', function(){
    $(".overlay-modal").hide();
});
});