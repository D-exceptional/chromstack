import { displaySuccess, displayInfo } from "../scripts/export.js";

const adminID = $('body').attr('id');

//Shorten URL
export function shortenLink(link) {
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

//Toggle status
function toggleStatus(input, id) {
    if (input == 'Pending') {
        $.ajax({
            type: "POST",
            url: "../server/approve-main-course.php", 
            data: { courseID: id },
            dataType: 'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if(content !== 'Error approving course' && content !== 'Course ID missing'){
  
                         $('.main-course-list').each(function () {
                            if($(this).attr('id') == id){
                                $(this).find('.main-course-status-row').children('button').removeClass('btn btn-danger btn-sm').addClass('btn btn-success btn-sm').text('Approved');
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
            url: "../server/disapprove-main-course.php", 
            data: { courseID: id },
            dataType:'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if(content !== 'Error disapproving course' && content !== 'Course ID missing'){
                            $('.main-course-list').each(function () {
                                if($(this).attr('id') == id){
                                    $(this).find('.main-course-status-row').children('button').removeClass('btn btn-success btn-sm').addClass('btn btn-danger btn-sm').text('Pending');
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

 //Get day of the week
const currentDate = new Date();
const weekdays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
const currentDay = weekdays[currentDate.getDay()];

function updateMainCourseDetails(id, title, amount, admin, affiliate) {
    const currentTime = currentDate.getHours();
    /*
        Before updating any course details, there will be five (5) very strict checks
        1. The day of the week must be Saturday and the time of the day must be 12pm
        2. The admin percentage for the must not be altered..i.e, it must always be 50%
        3. Neither the affiliate or vendor commission will be zero or empty
        4. The sum of the affiliate and vendor commission must be equal to 100%
        5. The special character `%` must be the suffix of all percentages before any detail can be updated
     */
    if (currentDay !== 'Saturday') {
        displayInfo('Course updates are only allowed on Saturdays');
    }
    else {
        if (currentTime < 12) {
            displayInfo('Course updates are only allowed from 12pm and above!');
        }
        else {
            if (admin === '' || affiliate === '') {
                displayInfo('Admin or affiliate commission value cannot be empty!');
            }
            else{
                let adminValue = admin.match(/\d+/g);
                let affiliateValue = affiliate.match(/\d+/g);
                const adminCommission = Number(adminValue[0]);
                const affiliateCommission = Number(affiliateValue[0]);
                const formattedAdminCommission = adminCommission + '%';
                const formattedAffiliateCommission = affiliateCommission + '%';
                const sumCommission = adminCommission + affiliateCommission;
                //console.log(adminCommission, affiliateCommission, formattedAdminCommission, formattedAffiliateCommission);
                
                if(adminCommission === 0 || affiliateCommission === 0){
                    displayInfo('Admin or affiliate commission value cannot be zero!');
                }
                else if(adminCommission > 100 && affiliateCommission === 0){
                    displayInfo('Affiliate commission must be a reasonable non-zero value!');
                }
                else if(adminCommission === 0 && affiliateCommission > 100 ){
                    displayInfo('Admin commission must be a reasonable non-zero value!');
                }
                else if(sumCommission < 100 || sumCommission > 100){
                    displayInfo('The sum of the admin and affiliate commission must be exactly 100!');
                }
                else{
                     $.ajax({
                            type: "POST", 
                            url: "../server/update-main-course-details.php", 
                            data: { id: id, title: title, amount: amount, admin: formattedAdminCommission, affiliate: formattedAffiliateCommission },
                            dataType:'json',
                            success: function (response) {
                                for (var key in response){
                                    if (Object.hasOwnProperty.call(response, key)) {
                                        const content = response[key];
                                        if(content !== 'Error updating course details' && content !== 'Some fields are empty'){
                                            let usdAmount = amount / 1000;
                                            $('.main-course-list').each(function () {
                                                if($(this).attr('id') == id){
                                                    $(this).find('.course-title, .amount, .admin-percentage, .affiliate-percentage').attr('contenteditable', false);
                                                    $(this).find('.course-title').empty().text(title);
                                                    $(this).find('.amount').empty().text(`\u20a6${amount}` + ` / ` + `$${usdAmount}`);
                                                    $(this).find('.admin-percentage').empty().text(formattedAdminCommission);
                                                    $(this).find('.affiliate-percentage').empty().text(formattedAffiliateCommission);
                                                    $(this).find('button').each(function (index, el) {
                                                        if ($(el).text() == 'Save') {
                                                            $(el).text('Edit');
                                                        }
                                                    });
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
        }
    }
}

function updateCourseDescription(id, type, description) {
    if (type === 'Affiliate') {
         $.ajax({
                type: "POST", 
                url: "../server/update-main-course-description.php", 
                data: { id: id, description: description },
                dataType: 'json',
                success: function (response) {
                    for (var key in response){
                        if (Object.hasOwnProperty.call(response, key)) {
                            const content = response.Info;
                            if(content !== 'Error updating course description' && content !== 'Some fields are empty'){
                                $('.main-course-list').each(function () {
                                    if($(this).attr('id') == id){
                                        $(this).find('.course-description').attr('contenteditable', false).empty().text(response.details.description);
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
            url: "../server/update-course-description.php", 
            data: { id: id, description: description },
            dataType: 'json',
            success: function (response) {
                for (var key in response){
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response.Info;
                        if(content !== 'Error updating course description' && content !== 'Some fields are empty'){
                            $('.uploaded-course-list').each(function () {
                                if($(this).attr('id') == id){
                                    $(this).find('.course-description').attr('contenteditable', false).empty().text(response.details.description);
                                    $('#button-div button').text('Edit');
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

export function fetchCourseDescription(type, id) {
    if (type == 'Affiliate') {
        $.ajax({
            type: "GET", 
            url: "../server/fetch-main-course-description.php", 
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
                console.log(e.responseText);
            }
        });
    }
    else {
        $.ajax({
            type: "GET", 
            url: "../server/fetch-course-description.php", 
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
}

function appendMainCourse() {
    let status = '';
   $("tbody").empty();
   $.ajax({
      type: "GET",
      url: "../server/main-course.php",
      data: { id: adminID, type: 'Admin' },
      dataType: "json",
      success: function(response){
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
               const content = response[key];
               if (response.Info !== 'No course found') {
                    switch (content.course_status) {
                        case 'Pending':
                          status = '<button class="btn btn-danger btn-sm">Pending</button>';
                        break;
                        case 'Approved':
                           status = '<button class="btn btn-success btn-sm">Approved</button>';
                        break;
                    }

                    let USDAmount = content.course_amount / 1000;

                 let courseList = `  <tr class='main-course-list' id='${content.courseID}'>
                                    <td class='course-title'>${content.course_title}</td>
                                     <td>
                                        <img src='../../../courses/${content.folder_path}/${content.course_cover_page}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>
                                    </td>
                                    <td class='course-description' style='cursor: pointer;'>${content.course_description}</td>
                                    <td class='main-course-status-row'>${status}</td>
                                    <td class='author'>${content.course_authors}</td>
                                    <td class='amount'>\u20a6${content.course_amount} / $${USDAmount}</td>
                                    <td>${content.uploaded_on}</td>
                                    <td class='admin-percentage'>${content.admin_percentage}</td>
                                    <td class='affiliate-percentage'>${content.affiliate_percentage}</td>
                                    <td class='vendor-percentage'>0%</td>
                                    <td><a href='../views/course-preview-page.php?filePath=${content.folder_path}' class='main-course-folder-link'>Course Preview</a></td>
                                    <td class='main-sales-type-row'>
                                        <button class="btn btn-info btn-sm" style='width: 60px !important;margin-bottom: 8px;'>Admin</button>
                                        <button class="btn btn-info btn-sm" style='width: 70px !important;margin-bottom: 8px;'>Affiliates</button>
                                        <button class="btn btn-success btn-sm" style='width: 60px !important;margin-bottom: 8px;'>Link</button>  
                                        <button class="btn btn-info btn-sm" style='width: 60px !important;margin-bottom: 8px;'>Edit</button>  
                                        <button class="btn btn-info btn-sm" style='width: 60px !important;margin-bottom: 8px;'>Details</button>  
                                        <button class="btn btn-info btn-sm" style='width: 70px !important;margin-bottom: 8px;'>Contest</button>
                                        <input type='text' class='course-type' value='${content.course_type}' hidden />
                                        <input type='text' class='course-image-data' value='courses/${content.folder_path}/${content.course_cover_page}' hidden />
                                        <input type='text' class='affiliate-course-main-link' value='${content.folder_path}' hidden />
                                        <input type='text' class='short-link' value='${content.short_link}' hidden />
                                    </td>
                                </tr>
                                `;
                                
                            $("tbody").prepend(courseList);

                }
                else{
                    displayInfo(content);
                }
            }
        }

        $('.main-course-status-row button').each(function (index, el) {
            $(el).on('click', function () {
                let btnText = $(el).text();
                let courseID = $(el).parent().parent().attr('id');
                toggleStatus(btnText, courseID);
           });
        });

        //Edit description

        $('.main-course-list .course-description').each(function (index, el) {
            $(el).on('click', function () {
                let courseID = $(el).parent().attr('id');
                let courseType = $(el).parent().find('.course-type').val();
                $('#description-overlay').css({'display':'flex'});
                fetchCourseDescription(courseType, courseID);
                $('#courseID').val(courseID);
                $('#courseType').val(courseType);
           });
        });

        $('.main-sales-type-row').children('button').each(function (index, el) {
            $(el).on('click', function () {
                const buttonText = $(el).text();
                const courseID = $(el).parent().parent().attr('id');
                const courseType = $(el).parent().parent().find('.course-type').val();
                const courseTitle = $(el).parent().parent().find('.course-title').text();
                switch (buttonText) {
                    case 'Admin':
                         window.location = `../views/admin-course-sales.php?courseID=${courseID}&type=${courseType}&sales-type=Admin`;
                    break;
                    case 'Affiliates':
                        window.location = `../views/affiliate-course-sales.php?courseID=${courseID}&type=${courseType}&sales-type=Affiliate`;
                    break;
                    case 'Details':
                        $('#full-details-overlay').css({'display':'block'});
                        $('#full-details-overlay iframe').attr('src', `../../../details.php?id=${courseID}&type=${courseType}`);
                    break;
                    case 'Edit':
                        if (currentDay !== 'Saturday') {
                            displayInfo('Course updates are only allowed on Saturdays after payout!');
                        }
                        else {
                            $(el).text('Save');
                            $(el).parent().parent().find('.course-title').attr('contenteditable', true);
                            $(el).parent().parent().find('.amount').attr('contenteditable', true);
                            $(el).parent().parent().find('.admin-percentage').attr('contenteditable', true);
                            $(el).parent().parent().find('.affiliate-percentage').attr('contenteditable', true);
                        }
                    break;
                    case 'Save':
                        let adminCommission = $(el).parent().parent().find('.admin-percentage').text();
                        let affiliateCommission = $(el).parent().parent().find('.affiliate-percentage').text();
                        let clickedAmount = $(el).parent().parent().find('.amount').text().split('/');
                        let processSplit = clickedAmount[0];
                        let processAmount = processSplit.slice(1);
                        let actualAmount = Number(processAmount);
                        //Update details
                        updateMainCourseDetails(courseID, courseTitle, actualAmount, adminCommission, affiliateCommission);
                    break;
                    case 'Contest':
                        window.location = `../views/project-add.php?courseID=${courseID}&type=${courseType}`;
                    break;
                    case 'Link':
                        const link = $(el)
                          .parent()
                          .find(".short-link")
                          .val();
                        shortenLink(link);
                    break;
                }
           });
        });

        $('.main-course-folder-link').each(function (index, el) {
            $(el).on('click', function (e) {
                e.preventDefault();
                const filePath = $(el).parent().parent().find('.affiliate-course-main-link').val();
                const title = $(el).parent().parent().find('.course-title').text();
                window.location = `../views/course-preview-page.php?filePath=${filePath}&title=${title}`;
           });
        });
     },
     error: function (e) {
        console.log(e.responseText);
     }
    });
   }
    
    appendMainCourse();

   $('#close-view').on('click', function () {
        $('#full-details-overlay').css({'display':'none'});
        $('#full-details-overlay iframe').attr('src', '');
   });

   $('#close-decsription-view').on('click', function () {
        $('#description-overlay').css({'display':'none'});
        $('#description-text').text('');
        $('#courseID, #courseType').val('');
        $('#button-div button').text('Edit');
   }); 

   $('#button-div button').on('click', function () {
       if ($(this).text() == 'Edit') {
            $('#description-text').attr('contenteditable', true);
            $(this).text('Update');
       }
       else {
            $('#description-text').attr('contenteditable', false);
            let courseID = $('#courseID').val();
            let courseType = $('#courseType').val();
            let courseDescription = $('#description-text').text();
            updateCourseDescription(courseID, courseType, courseDescription);
       }
   });

   //Indent all inner child navs
   $('.nav-sidebar').addClass('nav-child-indent');

    //Search function
    $('#page-search').on('keyup', function () { 
        let searchValue = $(this).val();
        if (searchValue !== "") {
            $('.uploaded-course-list, .main-course-list').each(function (index, el) {
                if($(el).find('.course-title').text().toLowerCase().includes(searchValue)){
                    $(el).css({'display':'table-row'});
                }else{
                    $(el).css({'display':'none'});
                }
            });
        }
        else{
            $('.uploaded-course-list, .main-course-list').each(function (index, el) {
                if($(el).css('display') === 'none'){
                    $(el).css({'display':'table-row'});
                }
            });
        }
    });    
