$("#info, #range-div").css({ display: "none" });
$("#amount-div, #sales-div").css({ display: "flex" });

const id = $('#affiliateID').val();

function getDetails(val) {
  switch (val) {
    case "Daily":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "./server/fetch-sales-details.php",
        data: { id: id, type: "daily" },
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
    case "Weekly":
      $("#info, #range-div").css({ display: "none" });
      $("#amount-div, #sales-div").css({ display: "flex" });
      //Get details from server
      $.ajax({
        type: "GET",
        url: "./server/fetch-sales-details.php",
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
        url: "./server/fetch-sales-details.php",
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
        url: "./server/fetch-sales-details.php",
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
      url: "./server/fetch-sales-details.php",
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

// With custom settings, forcing a "US" locale to guarantee commas in output
function formatAmount(amount, decimalPrecision = 2) {
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: decimalPrecision,
    maximumFractionDigits: decimalPrecision,
  });
}

//Currency filter
$("#currency-filter").on("change", function () {
  const value = $(this).val();
  switch(value){
      case "Dollar":
        $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text()) / 1000;
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#x24;${outputAmount}`);
            }
        });
      break;
      case "Naira":
          $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text());
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#x20A6;${outputAmount}`);
            }
        });
      break;
      case "Cedis":
           $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text()) / 100;
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#x20B5;${outputAmount}`);
            }
        });
      break;
      case "Shillings":
          $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text()) * 0.077;
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#83;${outputAmount}`);
            }
        });
      break;
      case "Cefa":
          $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text()) * 0.37;
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#x20A3;${outputAmount}`);
            }
        });
      break;
      case "Rand":
          $(".col-lg-3.col-6").each(function(index, el){
            if($(el).find('span').length > 0){
                const amount = Number($(el).find(".raw-value").text()) * 0.011;
                const outputAmount = formatAmount(amount);
                $(el)
                .find("h3")
                .empty()
                .html(`&#82;${outputAmount}`);
            }
        });
      break;
  }
});
