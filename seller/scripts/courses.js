import { displayInfo, displaySuccess } from "../scripts/export.js";

//URL shortening
function shortenLink(link) {
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

//Get day of the week
const currentDate = new Date();
const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const currentDay = weekdays[currentDate.getDay()];

function updateCourseDetails(id, title, amount, admin, affiliate, vendor) {
    const currentTime = currentDate.getHours();
    /*
       Before updating any course details, there will be five (5) very strict checks
       1. The day of the week must be Saturday and the time of the day must be 12pm
       2. The admin percentage for the must not be altered..i.e, it must always be 10%
       3. Neither the affiliate or vendor commission will be zero or empty
       4. The sum of the affiliate and vendor commission must be equal to 90%
       5. The special character `%` must be the suffix of all percentages before any detail can be updated
    */
    if (currentDay !== 'Saturday') {
        displayInfo('Course updates are only allowed on Saturdays!');
    }
    else {
        if (currentTime < 12) {
            displayInfo('Course updates are only allowed from 12pm and above!');
        }
        else {
            if (admin == '' || affiliate == '' || vendor == '') {
                displayInfo('Admin or affiliate or vendor commission value cannot be empty!');
            }
            else {
                let adminValue = admin.match(/\d+/g);
                let affiliateValue = affiliate.match(/\d+/g);
                let vendorValue = vendor.match(/\d+/g);
                const adminCommission = Number(adminValue[0]);
                const affiliateCommission = Number(affiliateValue[0]);
                const vendorCommission = Number(vendorValue[0]);
                const formattedAdminCommission = adminCommission + '%';
                const formattedAffiliateCommission = affiliateCommission + '%';
                const formattedVendorCommission = vendorCommission + '%';
                const sumCommission = adminCommission + affiliateCommission + vendorCommission;
                // console.log(adminCommission, affiliateCommission, vendorCommission);

                if (adminCommission === 0 || affiliateCommission === 0 || vendorCommission === 0) {
                    displayInfo('Admin or affiliate commission value cannot be zero!');
                }
                else if (adminCommission > 100 && affiliateCommission === 0 || vendorCommission === 0) {
                    displayInfo('Affiliate and vendor commission must be a reasonable non-zero value!');
                }
                else if (adminCommission === 0 && affiliateCommission > 100 || vendorCommission === 0) {
                    displayInfo('Admin and vendor commission must be a reasonable non-zero value!');
                }
                else if (adminCommission === 0 && affiliateCommission === 0 || vendorCommission > 100) {
                    displayInfo('Admin and affiliate commission must be a reasonable non-zero value!');
                }
                else if (sumCommission < 100 || sumCommission > 100) {
                    displayInfo('The sum of the admin, affiliate and vendor commission must be exactly 100!');
                }
                else {
                    $.ajax({
                        type: "POST",
                        url: "../server/update-course-details.php",
                        data: {
                                id: id, 
                                title: title, 
                                amount: amount, 
                                admin: formattedAdminCommission, 
                                affiliate: formattedAffiliateCommission, 
                                vendor: formattedVendorCommission 
                            },
                        dataType: 'json',
                        success: function (response) {
                            for (var key in response) {
                                if (Object.hasOwnProperty.call(response, key)) {
                                    const content = response[key];
                                    if (content !== 'Error updating course details' && content !== 'Some fields are empty') {
                                        let usdAmount = amount / 1000;
                                        $('.main-course-list').each(function () {
                                            if ($(this).attr('id') == id) {
                                                $(this).find('.course-title, .amount, .admin-percentage, .affiliate-percentage').attr('contenteditable', false);
                                                $(this).find('.course-title').empty().text(title);
                                                $(this).find('.amount').empty().text(`\u20a6${amount}` + ` / ` + `$${usdAmount}`);
                                                $(this).find('.admin-percentage').empty().text(formattedAdminCommission);
                                                $(this).find('.affiliate-percentage').empty().text(formattedAffiliateCommission);
                                                $(this).find('.vendor-percentage').empty().text(formattedVendorCommission);
                                                $(this).find('button').each(function (index, el) {
                                                    if ($(el).text() == 'Update') {
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
            }
        }
    }
}

function updateCourseDescription(id, description) {
    $.ajax({
        type: "POST",
        url: "../server/update-course-description.php",
        data: { id: id, description: description },
        dataType: 'json',
        success: function (response) {
            for (var key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response.Info;
                    if (content !== 'Error updating course description' && content !== 'Some fields are empty') {
                        $('.uploaded-course-list').each(function () {
                            if ($(this).attr('id') == id) {
                                $(this).find('.course-description').attr('contenteditable', false).empty().text(response.details.description);
                                $('#button-div button').text('Edit');
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

function fetchCourseDescription(id) {
    $.ajax({
        type: "GET",
        url: "../server/fetch-course-description.php",
        data: { id: id },
        dataType: 'json',
        success: function (response) {
            for (var key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response.Info;
                    if (content !== 'Some fields are empty') {
                        $('#description-text').empty().html(response.details.description);
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

function fetchCourses() {
    let status, coverPage, previewLink = '';
    $('tbody').empty();
    $.ajax({
        type: "GET",
        url: "../server/courses.php",
        data: { fullname: $('#fullname').val() },
        dataType: "json",
        success: function (response) {
            for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response[key];
                    if (content !== 'No course found') {
                        switch (content.course_status) {
                            case 'Pending':
                                status = '<button class="btn btn-danger btn-sm">Pending</button>';
                                break;
                            case 'Approved':
                                status = '<button class="btn btn-success btn-sm">Approved</button>';
                                break;
                        }

                        let USDAmount = content.course_amount / 1000;
                        
                        if(content.folder_path !== "null"){
                            coverPage = `<img src='../../courses/${content.folder_path}/${content.course_cover_page}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>`;
                            previewLink = `../views/course-preview-page.php?filePath=${content.folder_path}`;
                        }else{
                            coverPage = `<img src='../../assets/img/${content.course_cover_page}' style='width: 100px;height: 100px;border-radius: 5px;' alt='Cover Image'>`;
                           previewLink = `#!`;
                        }
                        
                        let courseLists = `  <tr class='uploaded-course-list' id='${content.courseID}'>
                                            <td class='course-title'>${content.course_title}</td>
                                             <td>
                                                ${coverPage}
                                            </td>
                                            <td class='course-description' style='cursor: pointer;'>${content.course_description}</td>
                                            <td class='course-status-row'>${status}</td>
                                            <td class='author'>${content.course_authors}</td>
                                            <!--<td class='amount'>\u20a6${content.course_amount} / $${USDAmount}</td>-->
                                            <td class='amount'>$${USDAmount}</td>
                                            <td>${content.uploaded_on}</td>
                                            <td class='affiliate-percentage'>${content.affiliate_percentage}</td>
                                            <td class='vendor-percentage'>${content.vendor_percentage}</td>
                                            <td><a href='${previewLink}' class='course-filepath'>Course Preview</a></td>
                                            <td class='sales-type-row'>
                                                <button class="btn btn-success btn-sm share" style='margin-bottom: 8px;width: 60px !important;'>Link</button>  
                                                <button class="btn btn-info btn-sm" style='margin-bottom: 8px;width: 60px !important;'>Sales</button>
                                                <button class="btn btn-info btn-sm" style='margin-bottom: 8px;width: 60px !important;'>Edit</button>
                                                <button class="btn btn-info btn-sm" style='margin-bottom: 8px;width: 60px !important;'>Details</button>  
                                                <button class="btn btn-info btn-sm" style='margin-bottom: 8px;width: 70px !important;'>Contest</button>
                                                <input type='text' class='short-link' value='${content.short_link}' hidden />
                                                <input type='text' class='course-image-data' value='courses/${content.folder_path}/${content.course_cover_page}' hidden />
                                                <input type='text' class='course-main-link' value='${content.folder_path}' hidden />
                                                <input type='text' class='course-type' value='${content.course_type}' hidden />
                                            </td>
                                        </tr>
                                    `;

                        $('tbody').append(courseLists);

                    }
                    else {
                        displayInfo(content);
                    }
                }
            }

            $('.course-filepath').each(function (index, el) {
                $(el).on('click', function (e) {
                    e.preventDefault();
                    const filePath = $(el).parent().parent().find('.course-main-link').val();
                    const title = $(el).parent().parent().find('.course-title').text();
                    window.location = `../views/course-preview-page.php?filePath=${filePath}&title=${title}`;
                });
            });

            //Edit description
            $('.uploaded-course-list .course-description').each(function (index, el) {
                $(el).on('click', function () {
                    let courseID = $(el).parent().attr('id');
                    $('#description-overlay').css({ 'display': 'flex' });
                    fetchCourseDescription(courseID);
                    $('#courseID').val(courseID);
                });
            });

            $('.sales-type-row').children('button').each(function (index, el) {
                //check if logged in admin is a vendor
                if ($(el).text() == 'Edit' && $(el).parent().parent().find('.author').text() !== $('#fullname').val()) {
                    $(el).css({'display': 'none'});
                }
                //control contest creation on courses
                if ($(el).text() == 'Contest' && $(el).parent().parent().find('.author').text() !== $('#fullname').val()) {
                    $(el).css({'display': 'none'});
                }
                //Attach click events
                $(el).on('click', function () {
                    let buttonText = $(el).text();
                    let activeEmail = $('#email').val();
                    let salesType = 'Regular';
                    let courseID = $(el).parent().parent().attr('id');
                    let courseType = $(el).parent().parent().find('.course-type').val();
                    let courseTitle = $(el).parent().parent().find('.course-title').text();
                    let courseAmount = $(el).parent().parent().find('.amount').text();
                    let courseImage = $(el).parent().find('.course-image-data').val();
                    let courseAuthor = $(el).parent().parent().find('.author').text();
                    switch (buttonText) {
                        case 'Sales':
                            window.location = `../views/affiliate-course-sales.php?courseID=${courseID}`;
                        break;
                        case 'Link':
                            const link = $(el).parent().find(".short-link").val();
                            shortenLink(link);
                        break;
                        break;
                        case 'Details':
                            $('#full-details-overlay').css({ 'display': 'block' });
                            $('#full-details-overlay iframe').attr('src', `../../details.php?id=${courseID}&type=${courseType}`);
                        break;
                        case 'Edit':
                            if (currentDay !== 'Saturday') {
                                displayInfo('Course updates are only allowed on Saturdays after payout!');
                            }
                            else {
                                $(el).text('Save');
                                $(el).parent().parent().find('.course-title').attr('contenteditable', true);
                                $(el).parent().parent().find('.amount').attr('contenteditable', true);
                                $(el).parent().parent().find('.affiliate-percentage').attr('contenteditable', true);
                                $(el).parent().parent().find('.vendor-percentage').attr('contenteditable', true);
                            }
                        break;
                        case 'Save':
                            let adminCommission = $(el).parent().parent().find('.admin-percentage').text();
                            let affiliateCommission = $(el).parent().parent().find('.affiliate-percentage').text();
                            let vendorCommission = $(el).parent().parent().find('.vendor-percentage').text();
                            let clickedAmount = $(el).parent().parent().find('.amount').text().split('/');
                            let processSplit = clickedAmount[0];
                            let processAmount = processSplit.slice(1);
                            let actualAmount = Number(processAmount);
                            //Update details
                            updateCourseDetails(courseID, courseTitle, actualAmount, adminCommission, affiliateCommission, vendorCommission);
                        break;
                        case 'Contest':
                            window.location = `../views/project-add.php?courseID=${courseID}&type=${courseType}`;
                        break;
                    }
                });
            });

            $('.course-filepath').each(function (index, el) {
                $(el).on('click', function (e) {
                    e.preventDefault();
                    const filePath = $(el).parent().parent().find('.course-main-link').val();
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

fetchCourses();

$('#close-view').on('click', function () {
    $('#full-details-overlay').css({ 'display': 'none' });
    $('#full-details-overlay iframe').attr('src', '');
});

$('#close-decsription-view').on('click', function () {
    $('#description-overlay').css({ 'display': 'none' });
    $('#description-text').text('');
    $('#courseID').val('');
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
        let courseDescription = $('#description-text').text();
        updateCourseDescription(courseID, courseDescription);
    }
});

//Indent all inner child navs
$('.nav-sidebar').addClass('nav-child-indent');

//Search function
$('#page-search').on('keyup', function () {
    let searchValue = $(this).val();
    if (searchValue !== "") {
        $('.uploaded-course-list').each(function (index, el) {
            if ($(el).find('.course-title').text().toLowerCase().includes(searchValue)) {
                $(el).css({ 'display': 'table-row' });
            } else {
                $(el).css({ 'display': 'none' });
            }
        });
    }
    else {
        $('.uploaded-course-list').each(function (index, el) {
            if ($(el).css('display') === 'none') {
                $(el).css({ 'display': 'table-row' });
            }
        });
    }
});