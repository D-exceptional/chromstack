import { displayInfo, displaySuccess } from "../scripts/export.js";

const activeEmail = $("#active-email").val();
const allowedEmails = ["chukwuebukaokeke09@gmail.com", "izuchukwuokuzu@gmail.com"];

// Check if active email is authorized
if (!allowedEmails.includes(activeEmail)) {
  // Hide `Pay All` button
  $("#pay-all").hide();
  // Disable other payment buttons
  $("tbody tr .action button").attr("disabled", true);
}

//Show or hide backup button
const rowCount = $("tbody tr").length;
if (rowCount === 0) {
  $("#backup, #pay-all").css({ display: "none" });
  $("tbody").empty().html("<center><h3>No withdrawals yet!</h3></center>");
}

// With custom settings, forcing a "US" locale to guarantee commas in output
function formatAmount(amount, decimalPrecision = 2) {
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: decimalPrecision,
    maximumFractionDigits: decimalPrecision,
  });
}

function formatCounter(number) {
  // Convert number to string
  let numString = number.toString();
  // Check if number has a decimal point
  const decimalIndex = numString.indexOf(".");
  let decimalPart = "";
  if (decimalIndex !== -1) {
    decimalPart = numString.substring(decimalIndex);
    numString = numString.substring(0, decimalIndex);
  }
  // Add commas to the integer part
  let formattedNumber = "";
  while (numString.length > 3) {
    formattedNumber = "," + numString.slice(-3) + formattedNumber;
    numString = numString.slice(0, -3);
  }
  formattedNumber = numString + formattedNumber;
  // Concatenate the decimal part
  return formattedNumber + decimalPart;
}

function updateCurrency() {
  $.getJSON("../../../countries-details.json", function (data) {
    for (const key in data) {
      if (Object.hasOwnProperty.call(data, key)) {
        const content = data[key];
        const country = content.country_name;
        const code = content.currency_code;
        //Loop through each row and update the currency by country
        $("tbody tr").each(function (index, el) {
          if ($(el).find(".country").text() === country) {
            $(el).find(".currency").empty().text(code);
          }
        });
      }
    }
  });
}

function updateStatus(count) {
  $.ajax({
    type: "GET",
    url: "../server/get-transfers-status.php",
    data: { count: count },
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content === "No transfers found") {
            //displayInfo(content);
            $("body").css({ opacity: 1 });
          } else {
            //Loop through the table rows
            $("tbody tr").each(function (index, el) {
              if ($(el).find(".email").text() === content.email) {
                //Decide what button to show
                switch (content.status) {
                  case "Completed":
                    $(el)
                      .find(".status button")
                      .removeClass("btn btn-danger btn-sm")
                      .addClass("btn btn-success btn-sm")
                      .text("Completed");
                    $(el)
                      .find(".action button")
                      .removeClass("btn btn-info btn-sm")
                      .addClass("btn btn-success btn-sm")
                      .html(
                        "<i class='fas fa-check' style='padding-right: 5px;'></i>  Done"
                      )
                      .attr("disabled", true)
                      .css({ background: "green", width: "80px" });
                    break;
                  case "Failed":
                    $(el).find(".status button").text("Failed");
                    $(el).find(".action button").text("Retry");
                    break;
                  case "Reversed":
                    $(el).find(".status button").text("Reversed");
                    $(el).find(".action button").text("Retry");
                    break;
                }
              }
            });
          }
        }
      }
    },
    error: function (e) {
      displayInfo(e.responseText);
    },
  });
}

//Get total money made
const totalAmountGenerated = $("#total-amount-value").val();

//Remove zero value rows
$("tbody tr").each(function (index, el) {
  const intValue = $(el).find(".amount-row").text().split(" ");
  const currencyAmount = intValue[1].split(",").join("");
  const totalPayoutAmount = parseFloat(currencyAmount);
  //Update view
  if (totalPayoutAmount === 0) {
    $(el).remove();
  }
});

//Get currency
updateCurrency();

let tracker = 0;

function showButton() {
  if (rowCount > 0) {
    $("tbody tr").each(function (index, el) {
      if ($(el).find(".status button").text() === "Pending") {
        tracker++;
      }
    });
    if (tracker === 0) {
      $("#backup").show();
      $("#pay-all").hide();
    } else {
      $("#backup").hide();
      $("#pay-all").show();
    }
  } else {
    $("#backup").hide();
  }
}

setTimeout(() => {
  showButton();
}, 2000);

//Get current day and week
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
const week = currentDate.toLocaleDateString("en-US", {
  month: "long",
  day: "numeric",
  year: "numeric",
});
const reason = `Chromstack Payout`;
const payoutDay = currentDay + ", " + week;
//console.log(currentDay);

/*
  SINGLE TRANSFER
*/

$(".action button").each(function (index, el) {
  $(el).on("click", function () {
    const name = $(el).parent().parent().find(".fullname").text();
    const email = $(el).parent().parent().find(".email").text();
    const recipient = $(el).parent().parent().find(".recipient").text();
    const account = $(el).parent().parent().find(".account").text();
    const bank = $(el).parent().parent().find(".bank").text();
    const code = $(el).parent().parent().find(".code").text();
    const currency = $(el).parent().parent().find(".currency").text();
    const amount = $(el)
      .parent()
      .parent()
      .find(".amount-row")
      .text()
      .split(" ");
    const currencyAmount = amount[1].split(",").join("");
    const parsedAmount = parseFloat(currencyAmount);
    if (isNaN(parsedAmount)) {
      displayInfo(`Invalid amount ${parsedAmount}`);
      return;
    }
    const payoutAmount = parsedAmount * 100;
    //const transferAmount = parseFloat(payoutAmount.toFixed(2));
    let buttonText = "";

    if (currentDay !== "Friday") {
      displayInfo("Payout is only done on Fridays!");
      $(el).text("Pay");
      return;
    } else {
      //Filter beneficiaries
      if (recipient !== "" && recipient !== "null") {
        $(el).text("Processing...");
        //Send transfer data to server
        $.ajax({
          type: "POST",
          url: "../server/make-transfer.php",
          data: {
            name: name,
            email: email,
            account: account,
            bank: bank,
            code: code,
            recipient: recipient,
            amount: payoutAmount,
            currency: currency,
            reason: reason,
          },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "Transfer was successful") {
                  $("tbody tr").each(function (index, el) {
                    if ($(el).find(".email").text() === email) {
                      $(el)
                        .find(".status button")
                        .removeClass("btn btn-danger btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .text("Completed");
                      $(el)
                        .find(".action button")
                        .removeClass("btn btn-info btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .html(
                          "<i class='fas fa-check' style='padding-right: 5px;'></i>  Done"
                        )
                        .attr("disabled", true)
                        .css({ background: "green", width: "80px" });
                    }
                  });
                  displaySuccess(content);
                } else if (
                  content === "Transfer failed" ||
                  content === "Transfer was reversed" ||
                  content === "Transfer is processing"
                ) {
                  //Decide what to show button
                  switch (content) {
                    case "Transfer failed":
                      buttonText = "Failed";
                      break;
                    case "Transfer was reversed":
                      buttonText = "Reversed";
                      break;
                    case "Transfer is processing":
                      buttonText = "Pending";
                      break;
                  }
                  //Update UI
                  $("tbody tr").each(function (index, el) {
                    if ($(el).find(".email").text() === email) {
                      $(el).find(".status button").text(buttonText);
                      $(el).find(".action button").text("Retry");
                    }
                  });
                  displayInfo(content);
                } else {
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo("Error connecting to server");
            console.log(
              `Error response is: ${e.responseText} and error status is: ${e.statusText}`
            );
          },
        });
      } else {
        displayInfo("Recipient code not found");
        return;
      }
    }
  });
});

/*
  BULK TRANSFER
*/

let paymentArray = [];

$("#pay-all").on("click", function () {
  if (currentDay !== "Friday") {
    displayInfo("Payout is only done on Fridays!");
    return;
  } else if (rowCount === 0) {
    displayInfo("No payout data available!");
    return;
  } else {
    //Loop through table row data
    $("tbody tr").each(function (index, el) {
      const name = $(el).find(".fullname").text();
      const email = $(el).find(".email").text();
      const recipient = $(el).find(".recipient").text();
      const account = $(el).find(".account").text();
      const code = $(el).find(".code").text();
      const currency = $(el).find(".currency").text();
      const amount = $(el).find(".amount-row").text().split(" ");
      const currencyAmount = amount[1].split(",").join("");
      const parsedAmount = Number(currencyAmount);
      if (isNaN(parsedAmount)) {
        displayInfo(`Invalid payout amount ${parsedAmount} for ${name}`);
        return;
      }
      const payoutAmount = parsedAmount * 100;
      //const transferAmount = parseFloat(payoutAmount.toFixed(2));

      //Filter beneficiaries
      if (account !== "" && code !== "null") {
        //if (email !== "hassan@gmail.com" && email !== "jerry@gmail.com") {
          //Prepare payment object
          const payoutObject = {
            fullname: name,
            email: email,
            account: account,
            bank: code,
            code: recipient,
            amount: payoutAmount,
            currency: currency,
            reason: reason,
          };
          //Queue up payment objects in array
          paymentArray.push(payoutObject);
       // }
      }
    });
    //Convert payment array to json string
    const beneficiaries = JSON.stringify(paymentArray);
    //Send payment data to server
    $.ajax({
      type: "POST",
      url: "../server/payment.php",
      data: { beneficiaries: beneficiaries },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Transfers queued successfully") {
              displaySuccess(content);
              //Check status of transfers
              setTimeout(() => {
                window.location.reload();
              }, 3000);
            } else {
              displayInfo(content);
            }
          }
        }
      },
      error: function (e) {
        displayInfo("Error connecting to server");
        console.log(
          `Error response is: ${e.responseText} and error status is: ${e.statusText}`
        );
      },
    });
  }
});

//Get count
const counter = Number($("tbody").children("tr").length);
const total = formatCounter(counter);
$(".col-sm-6 h1")
  .empty()
  .html(
    "<b>Payouts " +
      "(" +
      total +
      " Beneficiaries, " +
      "(" +
      "&#8358;" +
      totalAmountGenerated +
      ")" +
      ")</b>"
  );

updateStatus(counter);

//Back up data
$("#backup").on("click", function () {
  if (rowCount > 0) {
    $("#backup").text("Processing...").attr("disabled", true);
    $.ajax({
      type: "POST",
      url: "../server/back-up.php",
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Backup operations were successful") {
              displaySuccess(content);
              $("#backup").empty().text("Updating...").attr("disabled", true);
              //Loop through the table rows
              $("tbody tr").each(function (index, el) {
                //if ($(el).find(".status button").text() === "Completed") {
                setTimeout(() => {
                  $(el).remove();
                  if (rowCount === 0) {
                    $("#backup").html(
                      "<i class='fas fa-check' style='padding-right: 5px;'></i>  Done"
                    );
                    //Update text
                    setTimeout(() => {
                      $("#backup").html("Back Up").attr("disabled", true);
                    }, 2000);
                    //Hide button
                    setTimeout(() => {
                      $("#backup").attr("disabled", false).hide();
                      $("#pay-all").hide();
                      $("tbody").empty();
                    }, 3000);
                  }
                }, 1500);
                //}
              });
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
    displayInfo("No data available for back up");
  }
});

//Search function
$("#page-search").on("keyup", function () {
  const searchValue = $(this).val().toLowerCase();
  if (searchValue !== "") {
    $(".rows").each(function (index, el) {
      if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".rows").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});
