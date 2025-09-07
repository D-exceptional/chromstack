import { displayInfo, displaySuccess } from "./export.js";

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
        displayInfo(e.responseText);
    }
});
}

function updateContestDetails(id, title, date) {
$.ajax({
    type: "POST",
    url: "../server/update-contest-details.php",
    data: { id: id, title: title, date: date },
    dataType: 'json',
    success: function (response) {
        for (var key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content !== 'Error updating details' && content !== 'Some fields are empty') {
                    $('.contest-list').each(function () {
                        if ($(this).attr('id') == id) {
                            $(this).find('.contest-title').attr('contenteditable', false);
                            $(this).find('.contest-title').empty().text(title);
                            $(this).find('.contest-end-date').empty().text(date);
                            $(this).find('button').each(function (index, el) {
                                if ($(el).text() == 'Save') {
                                    $(el).text('Edit');
                                }
                            });
                        }
                    });
                    displaySuccess(content);
                }
                else {
                    displayInfo(content);
                }
            }
        }
    },
    error: function (e) {
        displayInfo(e.responseText);
    }
});
}

function updateContestDescription(id, description) {
    $.ajax({
        type: "POST",
        url: "../server/update-contest-description.php",
        data: { id: id, description: description },
        dataType: 'json',
        success: function (response) {
            for (var key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response.Info;
                    if (content !== 'Error updating description' && content !== 'Some fields are empty') {
                        $('.contest-list').each(function () {
                            if ($(this).attr('id') == id) {
                                $(this).find('.contest-description').attr('contenteditable', false).empty().text(response.details.description);
                                $('#button-div button').text("Edit");
                            }
                        });
                        displaySuccess(content);
                    }
                    else {
                        displayInfo(content);
                    }
                }
            }
        },
        error: function (e) {
            displayInfo(e.responseText);
        }
    });
}

function fetchContests() {
    $("tbody").empty();
    $.ajax({
        type: "GET",
        url: "../server/active-challenge.php",
        data: { vendor: $('#fullname').val() },
        dataType: "json",
        success: function(response){
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content !== 'No contest found' && content !== 'No contest set') {
                let contestCard = `  <tr class='contest-list' id='${content.contestID}'>
                                    <td class='contest-title'>${content.contest_title}</td>
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
                                        <button class="btn btn-info btn-sm" style="margin-bottom: 8px;width: 60px !important;">Edit</button>
                                        <button class="btn btn-info btn-sm" style="margin-bottom: 8px;width: 60px !important;">Details</button>
                                        <button class="btn btn-info btn-sm" style="margin-bottom: 8px;width: 60px !important;">Stats</button>
                                    </td>
                                </tr>
                                `;

                            $('tbody').append(contestCard);

                }
                else{
                    displayInfo(content);
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

        //Fetch description
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
                        $('#description-overlay').css({ 'display': 'flex' });
                        fetchDescription(contestID);
                        $('#contestID').val(contestID);
                    break;
                    case 'Edit':
                        $(el).text('Update');
                        $(el).parent().parent().find('.contest-title').attr('contenteditable', true);
                        $(el).parent().parent().find('.contest-end-date').attr('contenteditable', true);
                    break;
                    case 'Save':
                        let updateTitle = $(el).parent().parent().find('.contest-title').text();
                        let updateEndDate = $(el).parent().parent().find('.contest-end-date').text();
                        updateContestDetails(contestID, updateTitle, updateEndDate);
                    break;
                    case 'Stats':
                        $('#full-details-overlay').css({ 'display': 'block' });
                        $('#full-details-overlay iframe').attr('src', `../../contest-statistics.php?id=${contestID}`);
                    break;
                }
            });
        });

        },
        error: function (e) {
        console.log(e.responseText)
        }
    });

}

fetchContests();

$('#close-decsription-view').on('click', function () {
    $('#description-overlay').css({'display':'none'});
    $('#description-text').text('');
    $('#contestID').val('');
}); 

$('#close-view').on('click', function () {
    $('#full-details-overlay').css({ 'display': 'none' });
    $('#full-details-overlay iframe').attr('src', '');
});

$('#button-div button').on('click', function () {
    if ($(this).text() == 'Edit') {
        $('#description-text').attr('contenteditable', true);
        $(this).text('Update');
    }
    else {
        $('#description-text').attr('contenteditable', false);
        let contestID = $('#contestID').val();
        let courseDescription = $('#description-text').text();
        updateContestDescription(contestID, courseDescription);
    }
});
