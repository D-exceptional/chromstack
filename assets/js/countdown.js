// Set the date we're counting down to
var countDownDate = new Date("Aug 1, 2024 00:00:00").getTime();

// Update the count down every 1 second
var start = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  
  if(days < 10){
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

  // Display the result 
  $('#counter-days').text(days);
  $('#counter-hours').text(hours);
  $('#counter-minutes').text(minutes);
  $('#counter-seconds').text(seconds);

  // If the count down is finished, write some text
  if (distance <= 0) {
    clearInterval(start);
    // Display the result 
    $('#counter-days').text('0');
    $('#counter-hours').text('0');
    $('#counter-minutes').text('0');
    $('#counter-seconds').text('0');
    
    //Update text
    $('#countdown-overlay-intro').html('Contest has started &#128640;&#128293;');
    
    //Update image
    $('#contest-image').attr('src', 'sales-challenge/sales-challenge.jpg');
  }
}, 1000);

//Close overlay
$('#countdown-overlay-close').on('click', function(){
    $('#countdown-overlay').css({ display: 'none'});
});