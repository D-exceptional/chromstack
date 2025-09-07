import { displayInfo } from "./export.js";

function fetchDescription(id) {
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

function fetchContests() {
$("tbody").empty();
$.ajax({
    type: "GET",
    url: "../server/completed-challenge.php",
    dataType: "json",
    success: function(response){
    for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (
                response !== "No contest found" &&
                response.Info !== "No contest found"
            ) {
                let contestCard = `  <tr class='contest-list' id='${content.contestID}'>
                                <td>${content.contest_title}</td>
                                <td class='contest-description'>${content.contest_description}</td>
                                <td class='contest-start-date'>${content.contest_start_date}</td>
                                <td class='contest-end-date'>${content.contest_end_date}</td>
                                <td class='contest-current-status'>
                                    <button class="btn btn-danger btn-sm">Completed</button>
                                </td>
                                <td class='button-row'>
                                    <button class="btn btn-info btn-sm">Details</button>
                                    <!--<button class="btn btn-info btn-sm">Stats</button>--->
                                </td>
                            </tr>
                            `;

                $("tbody").append(contestCard);
            } else {
                $("tbody").empty();
                displayInfo(content);
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
                    window.location = `../views/stats?contestID=${contestID}`;
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

//Indent all inner child navs
$('.nav-sidebar').addClass('nav-child-indent');

