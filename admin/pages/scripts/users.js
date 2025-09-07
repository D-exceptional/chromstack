import { displayInfo } from "../scripts/export.js";

function activateUser(id) {
    const verify = confirm('Are you sure to activate this account ?');
    if (verify === true) {
        $.ajax({
          type: "POST",
          url: "../server/activate-user-account.php",
          data: { id: id },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "User status updated successfully") {
                  $(".rows").each(function () {
                    if ($(this).attr("id") === id) {
                      $(this)
                        .find(".action-row")
                        .children("button")
                        .removeClass("btn btn-danger btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .text("Active");
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
          },
        });
    } else {
         $("body").css({ opacity: 1 });
    }
} 


let actionButton = '';

function fetchUsers() {
    $('tbody').empty();
   $.ajax({
      type: "GET",
      url: "../server/fetch-users.php",
      dataType: "json",
      success: function(response){
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (content !== 'No record found') {
                 if (content.user_status === "Active") {
                    actionButton = `<button class="btn btn-success btn-sm">Active</button>`;
                 } else {
                    actionButton = `<button class="btn btn-danger btn-sm">Pending</button>`;
                 }

                let userCard = `  <tr class='rows' id="${content.userID}">
                                    <td class='fullname'>${content.fullname}</td>
                                    <td>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <img alt="Avatar" class="table-avatar" src="../../../assets/img/user.png" style="width: 50px;height: 50px;border-radius: 50%;">
                                            </li>
                                        </ul>
                                    </td>
                                    <td class='email'>${content.email}</td>
                                    <td>${content.contact}</td>
                                    <td>${content.country}</td>
                                    <td>${content.created_on}</td>
                                    <td class='action-row'>${actionButton}</td>
                                </tr> 
                                `;

                            $('tbody').append(userCard);

                }
                else{
                    displayInfo(content);
                }

            }
        }

        $(".action-row button").each(function (index, el) {
          $(el).on("click", function () {
            const userID = $(el).parent().parent().attr("id");
            if ($(el).text() === "Pending") {
                activateUser(userID);
            }
          });
        });

        const counter = Number($("tbody").children("tr").length);
        $(".col-sm-6 h1")
        .empty()
        .html("<b>Users " + "(" + counter + ")");
     },
     error: function (e) {
        console.log(e.responseText)
     }
      });
   }

   fetchUsers();

   //Indent all inner child navs
   $('.nav-sidebar').addClass('nav-child-indent');

    //Search function
    $('#page-search').on('keyup', function () { 
        let searchValue = $(this).val();
        if (searchValue !== "") {
            $('.rows').each(function (index, el) {
                if($(el).find('.fullname').text().toLowerCase().includes(searchValue) || $(el).find('.email').text().toLowerCase().includes(searchValue) ){
                    $(el).css({'display':'table-row'});
                }else{
                    $(el).css({'display':'none'});
                }
            });
        }
        else{
            $('.rows').each(function (index, el) {
                if($(el).css('display') === 'none'){
                    $(el).css({'display':'table-row'});
                }
            });
        }
    });
