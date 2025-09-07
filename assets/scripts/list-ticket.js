import {  displayInfo } from "../scripts/export.js";


//Get day of the week
const currentDate = new Date();
const weekdays = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];
const currentDay = weekdays[currentDate.getDay()];
const currentTime = currentDate.getHours();

function listTickets() {
  $.ajax({
    type: "GET",
    url: "./assets/server/list-tickets.php",
    dataType: "json",
    success: function (response) {
        for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content !== "No ticket found") {

                    let ticketLists = `     <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s" id='${content.id}'>
                                                <div class="course-item bg-light">
                                                    <div class="position-relative overflow-hidden">
                                                        <img class="img-fluid" src="../../tickets/${content.banner}" alt="" style='width: 100% !important;height: 400px !important;'>
                                                        <!--<div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                                            <a href="how-to-be-a-vendor" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Learn More</a>
                                                            <a href="membership-signup?type=vendor" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Get Started</a>
                                                        </div>-->
                                                    </div>
                                                    <div class="text-center p-4 pb-0">
                                                        <h1 class="mb-4">${content.title}</h1>
                                                        <h5 class="mb-4">(${content.organizer})</h5>
                                                        <div class="mb-3">
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                            <small class="fa fa-star text-primary" style="color: #debc0ca3 !important;"></small>
                                                        </div>
                                                        <div style="display: flex;flex-direction: column;align-items: flex-start;justify-content: flex-start;padding: 0% 8%;">
                                                            <p>
                                                                ${content.description}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex border-top"></div>
                                                    <div class="d-flex" style='display: flex;flex-direction: row;align-items: center;justify-content: center;padding: 3%;'>
                                                        <button class='btn btn-success btn-sm' style='width: 40%;background-color: #57c477 !important;'>
                                                        <a href='buy-ticket.php?ticketID=${content.id}' style='color: white;text-decoration: none;'>Buy Now</a>
                                                        </button>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        `;

                    $("#ticket-list").append(ticketLists);
                } 
                else {
                   // displayInfo(content);
                    $("#ticket-list")
                    .empty()
                    .html(`
                        <center>
                            <span>No tickets available currently. To list your ticket, visit the <a href='upload-ticket'>upload ticket</a> page</span>
                        </center>
                    `);
                }
            }
        }
    },
    error: function (e) {
      displayInfo('Error connecting to server');
    },
  });
}

listTickets();

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search function
$("#page-search").on("keyup", function () {
  let searchValue = $(this).val();
  if (searchValue !== "") {
    $(".uploaded-course-list, .main-course-list").each(function (index, el) {
      if (
        $(el).find(".course-title").text().toLowerCase().includes(searchValue)
      ) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".uploaded-course-list, .main-course-list").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});
