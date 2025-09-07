import { displayInfo, displaySuccess } from "./export.js";

const userID = $("#button-div button").attr("id");

//Link generation
function copyLink(link) {
    navigator.clipboard.writeText(link);
    //Notify user
    $("#button-div button")
    .empty()
    .html("<i class='fas fa-check'></i>  Copied")
    .attr("disabled", true);
} 

export function fetchDescription(id) {
    $.ajax({
        type: "GET", 
        url: "../server/fetch-contest-description.php", 
        data: { id: id },
        dataType: 'json',
        success: function (response) {
            for (var key in response){
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response.Info;
                    if(content !== 'Some fields are empty'){
                        $('#description-text').empty().html(response.details.description); 
                    }
                    else{
                        displayInfo(content);
                    }
                }
            }
        },
        error: function (e) {
            displayInfo('Error connecting to server');
        }
    });
}

function fetchContests() {
    $("tbody").empty();
    $.ajax({
        type: "GET",
        url: "../server/active-challenge.php",
        data: { id: userID },
        dataType: "json",
        success: function(response){
            for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response[key];
                    if (content !== 'No contest found') {
                    let contestCard = `  <tr class='contest-list' id='${content.contestID}'>
                                        <td>${content.contest_title}</td>
                                        <td class='contest-description'>${content.contest_description}</td>
                                        <td class='contest-start-date'>${content.contest_start_date}</td>
                                        <td class='contest-end-date'>${content.contest_end_date}</td>
                                        <td class='end-time'>
                                            <ul class="list-inline">
                                                <li class="list-inline-item-rows days"></li>
                                                <li class="list-inline-item-rows hours"></li>
                                                <li class="list-inline-item-rows minutes" style='display: inline-block !important;'></li><br>
                                                <li class="list-inline-item-rows seconds" style='display: inline-block !important;'></li>
                                            </ul>
                                        </td>
                                        <td class='contest-current-status'>
                                            <button class="btn btn-success btn-sm">Active</button>
                                        </td>
                                        <td class='button-row'>
                                            <button class="btn btn-info btn-sm" style='margin-bottom: 8px;'>Details</button>
                                            <p style='display: none;'>${content.short_link}</p>
                                            <button class="btn btn-info btn-sm">Stats</button>
                                            <!--
                                            <button class="btn btn-success btn-sm">Link</button>
                                            -->
                                        </td>
                                    </tr>
                                    `;

                                $('tbody').append(contestCard);

                    }
                    else{
                            $("tbody").empty();
                    }
                }
            }

            $('.contest-list').each(function (index, el) {
                //Starter counters
                var endTime = $(el).find('.end-time');
                //End counters
                    var endDays = $(el).find('li.list-inline-item-rows.days');
                    var endHours = $(el).find('li.list-inline-item-rows.hours');
                    var endMinutes = $(el).find('li.list-inline-item-rows.minutes');
                    var endSeconds = $(el).find('li.list-inline-item-rows.seconds');
                let x = setInterval(function () {
                    let now = new Date().getTime();
                    let endDate = new Date($(el).find('.contest-end-date').text()).getTime();
                    let difference = endDate - now;
                    //Time calculations for days, hours, minutes and seconds
                    let days = Math.floor(difference / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((difference % (1000 * 60)) / 1000);

                    if (days < 10) {
                        days = '0' + days;
                    }

                    if (hours < 10) {
                        hours = '0' + hours;
                    }

                    if (minutes < 10) {
                        minutes = '0' + minutes;
                    }

                    if (seconds < 10) {
                        seconds = '0' + seconds;
                    }

                    endDays.html('<b>' + days + '</b>' + ' Days');
                    endHours.html('<b>' + hours + '</b>' + ' Hours');
                    endMinutes.html('<b>' + minutes + '</b>' + ' Minutes');
                    endSeconds.html('<b>' + seconds + '</b>' + ' Seconds');

                    if (difference <= 0) {
                        clearInterval(x);
                        endTime.empty().text('Contest has ended');
                        $(el).find('.contest-current-status').children('button').text('Completed').css({'background':'red'}).attr('disabled', true);
                        $(el).find('.button-row').children('button').each(function (index, button) {
                        if ($(button).text() == 'Stats') {
                                $(button).css({'display':'none'});
                        }
                        });;
                    }
                },1000);
            });

            //Edit description
            $('.contest-list .contest-description').each(function (index, el) {
                $(el).on('click', function () {
                    let contestID = $(el).parent().attr('id');
                    $('#description-overlay').css({'display':'flex'});
                    fetchDescription(contestID);
                    $('#contestID').val(contestID);
                });
            });

            $('.button-row').children('button').each(function (index, el) {
                //Attach click events
                $(el).on('click', function () {
                    let contestID = $(el).parent().parent().attr('id');
                    let buttonText = $(el).text();
                    switch (buttonText) {
                        case 'Details':
                            const contestLink = $(el).parent().find('p').text();
                            $('#contestLink').empty().val(contestLink);
                            $('#description-overlay').css({'display':'flex'});
                            $('#contestID').empty().val(contestID);
                            fetchDescription(contestID);
                        break;
                        case 'Stats':
                            $("#full-details-overlay").css({
                              display: "block",
                            });
                            $("#full-details-overlay iframe").attr(
                              "src",
                              `../views/leaderboard.php?id=${contestID}`
                            );
                        break;
                    }
                });
            });
        },
        error: function (e) {
             displayInfo("Error connecting to server");
        }
    });
}

fetchContests();

 //Attach click events
$("#button-div button").on("click", function () {
  const contestLink = $("#contestLink").val();
  copyLink(contestLink);
});

$('#close-decsription-view').on('click', function () {
    $('#description-overlay').css({'display':'none'});
    $('#description-text').text('');
    $('#contestID').val('');
    $("#button-div button").empty().text("Copy Link").attr('disabled', false);
    //$('#button-div button').text('Edit');
}); 

$("#close-view").on("click", function () {
  $("#full-details-overlay").css({ display: "none" });
  $("#full-details-overlay iframe").attr("src", "");
});

