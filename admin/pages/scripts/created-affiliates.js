import { displayInfo } from "../scripts/export.js";

function fetchCreatedAffiliates() {
    $('tbody').empty();
   $.ajax({
      type: "GET",
      url: "../server/created-affiliates.php",
      dataType: "json",
      success: function(response){
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (content !== 'No record found') {
                let affiliateCard = `  <tr>
                                        <td class='fullname'>${content.fullname}</td>
                                        <td class='email'>${content.email}</td>
                                        <td>${content.contact}</td>
                                        <td>${content.country}</td>
                                        <td>${content.created_on}</td>
                                        <td>${content.created_by}</td>
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
        .html("<b>Created Affiliates " + "(" + counter + ")");
     },
     error: function (e) {
        console.log(e.responseText)
     }
      });
   }

   fetchCreatedAffiliates();

   //Indent all inner child navs
   $('.nav-sidebar').addClass('nav-child-indent');

   //Search function
   $('#page-search').on('keyup', function () { 
    let searchValue = $(this).val();
    if (searchValue !== "") {
        $('.rows').each(function (index, el) {
            if($(el).find('.fullname').text().toLowerCase().includes(searchValue) || $(el).find('.email').text().toLowerCase().includes(searchValue)){
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