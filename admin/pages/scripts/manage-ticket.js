import { displaySuccess, displayInfo } from "../scripts/export.js";

//Shorten URL
function copyLink(link) {
    const textInput = document.createElement("input");
        textInput.setAttribute("type", "text");
        textInput.setAttribute("value", link);
        textInput.setAttribute("hidden", true);
        textInput.select();
        textInput.setSelectionRange(0, 99999);
    const shareLink = textInput.value;
        navigator.clipboard.writeText(shareLink);
    displaySuccess("Link copied");
}

//Delete a course
function deleteTicket(id, title) {
    const promptMessage = confirm('Are you sure to delete the ticket: ' + '" ' + title + ' "' + ' ?');
    if (promptMessage === true) {
        $.ajax({
            type: "POST", 
            url: "../server/manage-ticket.php", 
            data: { ticketID: id, action: "delete" },
            dataType:'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if (content === "Ticket deleted successfully") {
                          $(".ticket-list").each(function () {
                            if ($(this).attr("id") === id) {
                              $(this).css({ display: "none" });
                            }
                          });
                          displaySuccess(content);
                        } else {
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
    else {
        $('tbody').css({'opacity':'1'});
    }
}

//Toggle status
function toggleStatus(input, id) {
    if (input === 'Pending') {
        $.ajax({
          type: "POST",
          url: "../server/manage-ticket.php",
          data: { ticketID: id, action: "approve" },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "Ticket approved successfully") {
                  $(".ticket-list").each(function () {
                    if ($(this).attr("id") == id) {
                      $(this)
                        .find(".status")
                        .children("button")
                        .removeClass("btn btn-danger btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .text("Approved");
                      $(this).find(".btn.btn-danger").attr("disabled", true);
                    }
                  });
                  displaySuccess(content);
                } else {
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo(e.responseText);
            console.log(e.responseText);
          },
        });
    } 
    else {
        $.ajax({
          type: "POST",
          url: "../server/manage-ticket.php",
          data: { ticketID: id, action: "disapprove" },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "Ticket disapproved successfully") {
                  $(".ticket-list").each(function () {
                    if ($(this).attr("id") == id) {
                      $(this)
                        .find(".status")
                        .children("button")
                        .removeClass("btn btn-success btn-sm")
                        .addClass("btn btn-danger btn-sm")
                        .text("Pending");
                      $(this).find(".btn.btn-danger").attr("disabled", false);
                    }
                  });
                  displaySuccess(content);
                } else {
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo(e.responseText);
            console.log(e.responseText);
          },
        });
    }
} 

let ticketLists, status = '';

function listTickets() {
    $('tbody').empty();
   $.ajax({
     type: "GET",
     url: "../server/list-tickets.php",
     dataType: "json",
     success: function (response) {
       for (const key in response) {
         if (Object.hasOwnProperty.call(response, key)) {
           const content = response[key];
           if (content !== "No ticket found") {
             switch (content.status) {
               case "Pending":
                 status =
                   '<button class="btn btn-danger btn-sm">Pending</button>';
                 break;
               case "Approved":
                 status =
                   '<button class="btn btn-success btn-sm">Approved</button>';
                 break;
             }
            
             ticketLists = `  <tr class='ticket-list' id='${content.ticketID}'>
                                    <td class='title'>${content.title}</td>
                                     <td>
                                        <img src='../../../tickets/${content.banner}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>
                                    </td>
                                    <td class='description' style='cursor: pointer;'>${content.description}</td>
                                    <td>${content.created}</td>
                                    <td class='status'>${status}</td>
                                    <td class='owner'>${content.owner}</td>
                                    <td class='sales'>${content.sales}</td>
                                    <td class='amount'>\u20a6${content.amount}</td>
                                    <td class='action'>
                                        <button class="btn btn-success btn-sm" style='margin-bottom: 8px;width: 60px !important;'>Link</button>  
                                        <button class="btn btn-info btn-sm" style='margin-bottom: 8px;width: 60px !important;'>Details</button>  
                                        <button class="btn btn-danger btn-sm" style='margin-bottom: 8px;width: 70px !important;'>Delete</button>
                                    </td>
                                </tr>
                                `;

             $("tbody").append(ticketLists);
           } else {
             displayInfo(content);
           }
         }
       }

       $(".status button").each(function (index, el) {
         $(el).on("click", function () {
           const btnText = $(el).text();
           const ticketID = $(el).parent().parent().attr("id");
           toggleStatus(btnText, ticketID);
         });
         if ($(el).text() === "Pending") {
           $(el)
             .parent()
             .parent()
             .find(".btn.btn-danger")
             .attr("disabled", false);
         } else {
           $(el)
             .parent()
             .parent()
             .find(".btn.btn-danger")
             .attr("disabled", true);
         }
       });

       $(".action")
         .children("button")
         .each(function (index, el) {
           //Attach click events
            $(el).on("click", function () {
                const buttonText = $(el).text();
                const ticketID = $(el).parent().parent().attr("id");
                switch (buttonText) {
                    case "Link":
                    const shareLink = `https://chromstack.com/buy-ticket.php?ticket=${ticketID}`;
                    copyLink(shareLink);
                    break;
                    case "Details":
                        //Do something here
                    break;
                }
           });
         });

       $(".ticket-list .action .btn.btn-danger").each(function (index, el) {
         $(el).on("click", function () {
           const ticketID = $(el).parent().parent().attr("id");
           const ticketTitle = $(el)
             .parent()
             .parent()
             .find(".title")
             .text();
           deleteTicket(ticketID, ticketTitle);
         });
       });
     },
     error: function (e) {
       console.log(e.responseText);
     },
   });
   }

   listTickets();

   //Indent all inner child navs
   $('.nav-sidebar').addClass('nav-child-indent');

    //Search function
    $('#page-search').on('keyup', function () { 
    
    let searchValue = $(this).val();
    if (searchValue !== "") {
        $('.ticket-list').each(function (index, el) {
            if($(el).find('.title').text().toLowerCase().includes(searchValue)){
                $(el).css({'display':'table-row'});
            }else{
                $(el).css({'display':'none'});
            }
        });
    }
    else{
        $('.ticket-list').each(function (index, el) {
            if($(el).css('display') === 'none'){
                $(el).css({'display':'table-row'});
            }
        });
    }
});