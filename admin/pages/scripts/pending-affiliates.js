import { displayInfo, displaySuccess } from "../scripts/export.js";

//Toggle status
function activateAffiliate(id) {
  const verify = confirm('Are you sure to activate this account ?');
  if (verify === true) {
        $.ajax({
          type: "POST",
          url: "../server/activate-affiliate.php",
          data: { affiliateID: id },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "Affiliate status updated successfully") {
                  $(".rows").each(function () {
                    if ($(this).attr("id") === id) {
                      $(this).find(".status").text("Active");
                      $(this)
                        .find(".toggle-button button")
                        .text("Activated")
                        .attr("disabled", true);
                      //$(this).css({'display':'none'});
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
      $('body').css({ opacity: 1 });
  }
}

function deleteAccount(id, name) {
  const verify = confirm(`Are you sure to delete ${name}'s account ?`);
  if (verify === true) {
    $.ajax({
      type: "POST",
      url: "../server/delete-account.php",
      data: { id: id, type: 'Affiliate', name: name },
      dataType: "json",
      success: function (response) {
        for (var key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Account deleted successfully") {
              $(".rows").each(function () {
                if ($(this).attr("id") === id) {
                  $(this).remove();
                }
              });
              displaySuccess(content);
              const counter = Number($("tbody").children("tr").length);
            $(".col-sm-6 h1")
            .empty()
            .html("<b>Pending Affiliates " + "(" + counter + ")");
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

let imgCard = '';

function fetchPendingAffiliates() {
   $.ajax({
      type: "GET",
      url: "../server/fetch-pending-affiliates.php",
      dataType: "json",
      success: function(response){
        $('tbody').empty();
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (content !== 'No record found') {

                if (content.affiliate_profile !== null && content.affiliate_profile !== 'null') {
                    imgCard = `<img alt="Avatar" class="table-avatar" src="../../../uploads/${content.affiliate_profile}" style="width: 50px;height: 50px;border-radius: 50%;">`;
                } 
                else {
                    imgCard = `<img alt="Avatar" class="table-avatar" src="../../../assets/img/user.png" style="width: 50px;height: 50px;border-radius: 50%;">`;
                }

                let affiliateCard = `  <tr class='rows' id='${content.affiliateID}'>
                                    <td class='fullname'>${content.fullname}</td>
                                    <td>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                ${imgCard}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class='email'>${content.email}</td>
                                    <td>${content.contact}</td>
                                    <td>${content.country}</td>
                                    <td>${content.created_on}</td>
                                    <td class='status'>${content.affiliate_status}</td>
                                    <td class='toggle-button'>
                                        <button class="btn btn-success btn-sm" style='margin-bottom: 8px;width: 80px !important;'>Activate</button>
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </td>
                                </tr> 
                                `;

                            $('tbody').append(affiliateCard);

                }
                else{
                    displayInfo(content);
                }
            }
        }

        const counter = Number($("tbody").children("tr").length);
        $(".col-sm-6 h1")
        .empty()
        .html("<b>Pending Affiliates " + "(" + counter + ")");

        $('tr button').each(function (index, el) {
            $(el).on('click', function () {
                const id = $(el).parent().parent().attr('id');
                const name = $(el).parent().parent().find(".fullname").text();
                if ($(el).text() === "Activate") {
                  activateAffiliate(id);
                } else {
                  deleteAccount(id, name);
                }
           });
        });       
     },
     error: function (e) {
        console.log(e.responseText)
     }
      });
   }

   fetchPendingAffiliates();

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
