import { displayInfo } from "../scripts/export.js";

//Toggle status
function toggleStatus(input, id) {
    if (input == 'Deactivate') {
        $.ajax({
            type: "POST",
            url: "../server/deactivate-vendor.php", 
            data: { vendorID: id },
            dataType: 'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if(content !== 'Error updating status' && content !== 'Vendor ID missing'){

                         $('.rows').each(function () {
                            if($(this).attr('id') == id){
                                $(this).find('button').removeClass('btn btn-danger btn-sm').addClass('btn btn-success btn-sm').text('Activate');
                                $(this).find('.status').text('Deactivated');
                            }
                         });
                         displaySuccess(content);	
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
    else {
        $.ajax({
            type: "POST", 
            url: "../server/activate-vendor.php", 
            data: { vendorID: id },
            dataType:'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if(content !== 'Error updating status' && content !== 'Vendor ID missing'){
                            $('.rows').each(function () {
                                if($(this).attr('id') == id){
                                    $(this).find('button').removeClass('btn btn-success btn-sm').addClass('btn btn-danger btn-sm').text('Deactivate');
                                    $(this).find('.status').text('Active');
                                }
                             });
                            displaySuccess(content);	
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
}

let imgCard, status = '';

function fetchActiveVendors() {
   $.ajax({
      type: "GET",
      url: "../server/fetch-active-vendors.php",
      dataType: "json",
      success: function(response){
        $('tbody').empty();
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (content !== 'No record found') {
                if (content.vendor_profile !== null && content.vendor_profile !== 'null') {
                    imgCard = `<img alt="Avatar" class="table-avatar" src="../../../uploads/${content.vendor_profile}" style="width: 50px;height: 50px;border-radius: 50%;">`;
                } 
                else {
                    imgCard = `<img alt="Avatar" class="table-avatar" src="../../../assets/img/user.png" style="width: 50px;height: 50px;border-radius: 50%;">`;
                }

                switch (content.vendor_status) {
                    case 'Deactivated':
                      status = '<button class="btn btn-success btn-sm">Activate</button>';
                    break;
                    case 'Active':
                       status = '<button class="btn btn-danger btn-sm">Deactivate</button>';
                    break;
                }

               let vendorCard = `  <tr class='rows' id='${content.vendorID}'>
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
                                    <td class='status'>${content.vendor_status}</td>
                                    <td>
                                       ${status}
                                    </td>
                                </tr> 
                                `;

                            $('tbody').append(vendorCard);

                }
                else{
                    displayInfo(content);
                }
            }
        }

         const counter = Number($("tbody").children("tr").length);
         $(".col-sm-6 h1")
           .empty()
           .html("<b>Active Vendors " + "(" + counter + ")");

        $('tr button').each(function (index, el) {
            $(el).on('click', function () {
                const status = $(el).text();
                const id = $(el).parent().parent().attr('id');
                toggleStatus(status, id);
           });
        });        
     },
     error: function (e) {
        console.log(e.responseText)
     }
      });
   }

   fetchActiveVendors();

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