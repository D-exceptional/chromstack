import { displayInfo } from "./export.js";

$("#info, #range-div").css({ display: "none" });
$("#amount-div, #sales-div").css({ display: "flex" });

const id = $('body').attr('id');

function getDetails(val) {
  switch (val) {
    case "Daily":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "../server/fetch-sales-details.php",
        data: { id: id, type: "daily" },
        dataType: "json",
        success: function (response) {
            console.log(response);
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content !== "Some parameters are empty") {
                  $("#commission-overlay").css({ display: "flex" });
                  $("#amount-div b").empty().text(response.details.amount);
                  $("#sales-div b").empty().text(response.details.sales);
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
    break;
    case "Weekly":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "../server/fetch-sales-details.php",
        data: { id: id, type: "weekly" },
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content !== "Some parameters are empty") {
                $("#commission-overlay").css({ display: "flex" });
                $("#amount-div b").empty().text(response.details.amount);
                $("#sales-div b").empty().text(response.details.sales);
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
    break;
    case "Monthly":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "../server/fetch-sales-details.php",
        data: { id: id, type: "monthly" },
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content !== "Some parameters are empty") {
                $("#commission-overlay").css({ display: "flex" });
                $("#amount-div b").empty().text(response.details.amount);
                $("#sales-div b").empty().text(response.details.sales);
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
    break;
    case "Yearly":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "../server/fetch-sales-details.php",
        data: { id: id, type: "yearly" },
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content !== "Some parameters are empty") {
                $("#commission-overlay").css({ display: "flex" });
                $("#amount-div b").empty().text(response.details.amount);
                $("#sales-div b").empty().text(response.details.sales);
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
    break;
    case "Custom":
      $("#info, #range-div").css({ display: "block" });
      $("#amount-div, #sales-div").css({ display: "none" });
    break;
  }
}

function getRangeSales(from, to) {
  if (from === "" || to === "") {
    displayInfo('Select a range of dates to continue');
  } 
  else {
    let start = from.split("/");
    let end = to.split("/");
    let formatOne = start[0].split("-");
    let formatTwo = end[0].split("-");
    const formattedStart = formatOne[0] + "-" + formatOne[1] + "-" + formatOne[2];
    const formattedEnd = formatTwo[0] + "-" + formatTwo[1] + "-" + formatTwo[2];
    //Get details from server
    $.ajax({
      type: "GET",
      url: "../server/fetch-sales-details.php",
      data: { id: id, from: formattedStart, to: formattedEnd, type: "custom" },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content !== "Some parameters are empty") {
              $("#info, #range-div").css({ display: "none" });
              $("#amount-div, #sales-div").css({ display: "flex" });
              $("#amount-div b").empty().text(response.details.amount);
              $("#sales-div b").empty().text(response.details.sales);
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
  }
}

$("#commission-filter").on("change", function () {
  const value = $(this).val();
  if (value !== "Select") {
    $("#commission-overlay").css({ display: "flex" });
    getDetails(value);
  } 
});

$("#check-range-sales").on("click", function () {
   const from = $("#from").val();
   const to = $("#to").val();
   getRangeSales(from, to);
});

$("#close-div").on("click", function () {
  $("#commission-overlay").css({ display: "none" });
  $("#amount-div, #sales-div").find("b").text("");
});
