import { fetchDescription } from "./active-challenge.js";

function fetchContests() {
$("tbody").empty();
$.ajax({
    type: "GET",
    url: "../server/completed-challenge.php",
    data: { vendor: $('#fullname').val() },
    dataType: "json",
    success: function(response){
    for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content !== 'No contest found' && content !== 'No contest set') {
            let contestCard = `  <tr class='contest-list' id='${content.contestID}'>
                                    <td>${content.contest_title}</td>
                                    <td class='contest-description'>${content.contest_description}</td>
                                    <td class='contest-start-date'>${content.contest_start_date}</td>
                                    <td class='contest-end-date'>${content.contest_end_date}</td>
                                    <td class='contest-current-status'>
                                        <button class="btn btn-danger btn-sm">Completed</button>
                                    </td>
                                    <td class='button-row'>
                                    <button class="btn btn-info btn-sm" style="margin-bottom: 8px;width: 60px !important;">Details</button>
                                    <button class="btn btn-info btn-sm" style="margin-bottom: 8px;width: 60px !important;">Stats</button>
                                    </td>
                                </tr>
                                `;

                        $('tbody').append(contestCard);

            }
            else{
                //displayInfo(content);
                $('tbody').empty();
            }
        }
    }

    $('.contest-list .contest-description').each(function (index, el) {
        $(el).on('click', function () {
            let contestID = $(el).parent().attr('id');
            $('#description-overlay').css({'display':'flex'});
            fetchDescription(contestID);
        });
    });

    $('.button-row').children('button').each(function (index, el) {
        //Attach click events
        $(el).on('click', function () {
            let contestID = $(el).parent().parent().attr('id');
            let buttonText = $(el).text();
            switch (buttonText) {
                case 'Details':
                    $('#description-overlay').css({'display':'flex'});
                    fetchDescription(contestID);
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
    $('#button-div button').text('Edit');
}); 

$('#close-view').on('click', function () {
    $('#full-details-overlay').css({ 'display': 'none' });
    $('#full-details-overlay iframe').attr('src', '');
});

//Indent all inner child navs
$('.nav-sidebar').addClass('nav-child-indent');
