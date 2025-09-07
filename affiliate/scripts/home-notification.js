import { displayInfo } from './export.js';

//Get latest updates from the server when the page is refreshed
window.addEventListener('beforeunload', function(event) {
    window.location.reload(true); // Force reload from the server
});

$('#page-refresh').on('click', function (e) {
    e.preventDefault();
    window.location.reload(true); // Force reload from the server
});

$('#notification-link').on('click', function () {
  if ($(this).find('span').css('display') === 'block') {
    $(this).find('span').hide().text('');
    //Update status
    $.ajax({
      type: 'POST',
      url: './server/update-notification-status.php',
      data: { email: $('#sessionEmail').text() },
      dataType: 'json',
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (
              content !== 'Error updating status' &&
              content !== 'No notifications available' &&
              content !== 'No email supplied'
            ) {
              $('.dropdown-menu dropdown-menu-lg dropdown-menu-right')
                .empty()
                .html(
                  `<span class='dropdown-item dropdown-header'>No new notifications</span>
                    <a href='./views/timeline.php' class='dropdown-item dropdown-footer'>View all</a>
                  `
                );
            } else {
              displayInfo(content);
            }
          }
        }
      },
      error: function () {
        displayInfo('Error connecting to server');
      },
    });
  }
   else {
    $(this).css({ opacity: '1' });
  }
});

export const sessionEmail = $("#sessionEmail").text();
