//Show or hide backup button
const rowCount = $("tbody tr").length;
if (rowCount === 0) {
  $("#backup, #pay-all").css({ display: "none" });
  $("tbody").empty().html("<center><h3>No sales yet!</h3></center>");
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

//Sales amount
let salesAmount = 0;

//Get total money made
const totalAmountGenerated = $("#total-amount-value").val();

let amountLeft = parseFloat($("#amount-left-value").val());
//console.log("Amount left is " + amountLeft);

//Reroute funds from unreal accounts
$("tbody tr").each(function (index, el) {
  const email = $(el).find(".email").text();
  if (email === "jerry@gmail.com" || email === "hassan@gmail.com") {
    const intValue = $(el).find(".amount-row").text().split(" ");
    const currencyAmount = intValue[1].split(",").join("");
    const totalPayoutAmount = parseFloat(currencyAmount);
    //Update payout
    salesAmount += totalPayoutAmount;
    $(el).remove();
  }
});

let finalAmount = amountLeft + salesAmount;
//console.log("Final amount left is " + finalAmount);

//Final admins payouts
/*
$(".admin-rows td.fullname").each(function (index, el) {
  const email = $(el).parent().find(".email").text();
  const amount = $(el).parent().find(".amount-row").text().split(" ");
  const currencyAmount = amount[1].split(",").join("");
  const totalPayoutAmount = parseFloat(currencyAmount);

  if (email === "izuchukwuokuzu@gmail.com") {
    const payoutOne = totalPayoutAmount + (finalAmount / 3);
    const payoutAmountOne = formatAmount(payoutOne);
    const payoutAmountOneInUSD = formatAmount(payoutOne / 1000);
    $(el)
      .parent()
      .find(".amount-row")
      .empty()
      .html("&#x20A6 " + payoutAmountOne + " / " + "$" + payoutAmountOneInUSD);
    //console.log(`Izuchukwu's payout is ${payoutOne}`);
  }

  if (email === "chukwuebukaokeke09@gmail.com") {
    const payoutTwo = totalPayoutAmount + (finalAmount / 3);
    const payoutAmountTwo = formatAmount(payoutTwo);
    const payoutAmountTwoInUSD = formatAmount(payoutTwo / 1000);
    $(el)
      .parent()
      .find(".amount-row")
      .empty()
      .html("&#x20A6 " + payoutAmountTwo + " / " + "$" + payoutAmountTwoInUSD);
    //console.log(`Chukwuebuka's payout is ${payoutTwo}`);
  }

  if (email === "mrwisdom8086@gmail.com") {
    const payoutFour = totalPayoutAmount + (finalAmount / 3);
    const payoutAmountFour = formatAmount(payoutFour);
    const payoutAmountFourInUSD = formatAmount(payoutFour / 1000);
    $(el)
      .parent()
      .find(".amount-row")
      .empty()
      .html(
        "&#x20A6 " + payoutAmountFour + " / " + "$" + payoutAmountFourInUSD
      );
    //console.log(`Wisdom's payout is ${payoutFour}`);
  }
});*/

//Remove zero value rows
$("tbody tr").each(function (index, el) {
  const intValue = $(el).find(".amount-row").text().split(" ");
  const currencyAmount = intValue[1].split(",").join("");
  const totalPayoutAmount = parseFloat(currencyAmount);
  //Update view
  if (totalPayoutAmount === 0) {
    //$(el).remove();
    $(el).css({ border: '2px solid red' });
  }
});

//Get count
const counter = Number($("tbody").children("tr").length);
const total = formatCounter(counter);
$(".col-sm-6 h1")
  .empty()
  .html(
    "<b>Transaction " +
      "(" +
      total +
      " Beneficiaries, " +
      "(" +
      "&#8358;" +
      totalAmountGenerated +
      ")" +
      ")</b>"
  );

//Search function
$("#page-search").on("keyup", function () {
  const searchValue = $(this).val().toLowerCase();
  if (searchValue !== "") {
    $(".rows, .admin-rows").each(function (index, el) {
      if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".rows, .admin-rows").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});
