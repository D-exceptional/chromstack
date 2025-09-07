//Fetch Settings Data
let overviewCard = "";

 //Get current day and week
    const currentDate = new Date();
    const currentHour = currentDate.getHours();
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
    //Show withdraw button on the dashboard only on Thursday
    let button = ``;

function fetchSiteOverview() {
  $.ajax({
    type: "GET",
    url: "server/overview.php",
    data: { email: $('#active-email').val() },
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          
            if(currentDay === 'Thursday' && currentHour < 23){
                if(content.walletSavings > 0){
                    button = 
                        `<button type='button' class='btn btn-danger btn-sm' style='position: absolute;bottom: 0;right: 0;margin-bottom: 5px;height: 25px;font-size: 12px;z-index: 20000;' id='withdrawal'>
                            Withdraw
                        </button>
                        `;
                }
            }

          overviewCard = `   <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-wallet"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>\u20a6${content.overallEarnings} | <br> $${content.overallEarningsInUSD}</h3>
                                            <p>Total Earnings</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                            </div>
                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>\u20a6${content.totalSalesEarnings} | <br> $${content.totalSalesEarningsInUSD}</h3>
                                            <p>Weekly Earnings</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                            </div>
                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>\u20a6${content.totalAffiliateCourseEarnings} | <br> $${content.totalAffiliateCourseEarningsInUSD}</h3>
                                            <p>Main Course Earnings</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                                </div>
                            `;

          $("#content-overview").append(overviewCard);

          overviewCard = `   
                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                            <div class="inner" style='display: flex;flex-direction: row;'>
                                                <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                    <i class="fas fa-hand-holding-usd"></i>
                                                </div>
                                                <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                    <h3>\u20a6${content.totalUploadedCourseEarnings} | <br> $${content.totalUploadedCourseEarningsInUSD}</h3>
                                                    <p>Vendor Course Earnings</p>
                                                </div>
                                            </div>
                                            <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                        </div>
                                    </div>
                                `;

          $("#content-overview").append(overviewCard);
          
          overviewCard = `   <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-wallet"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>\u20a6${content.walletBalance} | <br> $${content.walletBalanceInUSD}</h3>
                                            <p>Wallet Balance</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                     ${button}
                                </div>
                            </div>
                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                           <i class="fas fa-chart-line"></i>
                                                        </div>
                                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                            <h3>${content.affiliateSalesCount}</h3>
                                                            <p>Main Course Sales</p>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                                </div>
                                            </div>
                                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                            <i class="fas fa-chart-line"></i>
                                                        </div>
                                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                            <h3>${content.salesCount}</h3>
                                                            <p>Vendor Course Sales</p>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                                </div>
                                            </div>
                                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                           <i class="fas fa-layer-group"></i>
                                                        </div>
                                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                            <h3>${content.courseCount}</h3>
                                                             <p>Quality Courses</p>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                                </div>
                                            </div>
                                        `;

          $("#content-overview").append(overviewCard);
          
          overviewCard = `   
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                        <i class="fas fa-medal"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                         <h3>${content.contestCount}</h3>
                                                        <p>Active Contests</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                            <i class="fas fa-user-shield"></i>
                                                        </div>
                                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                            <h3>${content.adminCount}</h3>
                                                            <p>Admins</p>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                                </div>
                                            </div>
                                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `   
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                       <i class="fas fa-user-tag"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.affiliateCount}</h3>
                                                        <p>Active Affiliates</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);

          overviewCard = `   
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                       <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.vendorCount}</h3>
                                                        <p>Active Vendors</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.userCount}</h3>
                                                        <p>Registered Users</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                           <i class="fas fa-ticket-alt"></i>
                                                        </div>
                                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                            <h3>${content.totalTickets}</h3>
                                                             <p>Available Tickets</p>
                                                        </div>
                                                    </div>
                                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                                </div>
                                            </div>
                                        `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                        <i class="fas fa-envelope-open-text"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.mailCount}</h3>
                                                        <p>Incoming Mails</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                            <div class="inner" style='display: flex;flex-direction: row;'>
                                                <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                  <i class="fas fa-mail-bulk"></i>
                                                </div>
                                                <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                   <h3>${content.mailListCount}</h3>
                                                    <p>Subscribed Emails</p>
                                                </div>
                                            </div>
                                            <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                        </div>
                                    </div>
                                `;

          $("#content-overview").append(overviewCard);

          overviewCard = `  
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                                <div class="inner" style='display: flex;flex-direction: row;'>
                                                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                                       <i class="fas fa-comment-alt"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.chatCount}</h3>
                                                        <p>Active Chats</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                            </div>
                                        </div>
                                    `;

          $("#content-overview").append(overviewCard);
        }
      }
      //Open Wihdrawal
        $('#withdraw').on("click", function(){
            window.location = 'views/wallet.php';
        });
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

fetchSiteOverview();

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search function
$("#page-search").on("keyup", function () {
  let searchValue = $(this).val();
  if (searchValue !== "") {
    $(".col-lg-3.col-6").each(function (index, el) {
      if ($(el).find("p").text().toLowerCase().includes(searchValue)) {
        $(el).css({ display: "block" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".col-lg-3.col-6").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "block" });
      }
    });
  }
});
