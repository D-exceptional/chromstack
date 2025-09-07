import { displayInfo } from "./export.js";

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

let current = ``;

function fetchContests() {
    $('tbody').empty();
   $.ajax({
      type: "GET",
      url: "../server/completed-contests.php",
      dataType: "json",
      success: function(response){
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (content !== 'No contest found') {
                    switch (content.contest_status) {
                        case 'Pending':
                            current = '<button class="btn btn-danger btn-sm">Pending</button>';
                        break;
                        case 'Completed':
                            current = '<button class="btn btn-danger btn-sm">Completed</button>';
                        break;
                        case 'Active':
                            current = '<button class="btn btn-success btn-sm">Active</button>';
                        break;
                    }

                let contestCard = `  <tr class='contest-list' id='${content.contestID}'>
                                    <td>${content.contest_title}</td>
                                    <td class='contest-description'>${content.contest_description}</td>
                                    <td class='contest-start-date'>${content.contest_start_date}</td>
                                    <td class='contest-end-date'>${content.contest_end_date}</td>
                                    <!--<td class='contest-current-status'>${current}</td>-->
                                    <td class='contest-current-status'>
                                        <button class="btn btn-danger btn-sm">Completed</button>
                                    </td>
                                    <td class='button-row'>
                                        <button class="btn btn-info btn-sm" style='margin-bottom: 8px;'>Details</button>
                                        <button class="btn btn-info btn-sm" style='margin-bottom: 8px;'>Stats</button>
                                    </td>
                                </tr>
                                `;

                            $('tbody').append(contestCard);

                }
                else{
                    displayInfo(content);
                    $('tbody').empty();
                }

            }
        }

         const counter = Number($("tbody").children("tr").length);
         $(".col-sm-6 h1")
           .empty()
           .html("<b>Completed Contests " + "(" + counter + ")");

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
                        $('#full-details-overlay').css({'display':'block'});
                        $('#full-details-overlay iframe').attr('src', `../../../contest-statistics.php?id=${contestID}`);
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