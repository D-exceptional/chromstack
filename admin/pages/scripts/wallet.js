// With custom settings, forcing a "US" locale to guarantee commas in output
function formatAmount(amount, decimalPrecision = 2) {
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: decimalPrecision,
    maximumFractionDigits: decimalPrecision,
  });
}

function clearError() {
  setTimeout(() => {
    $("#info-span").text(``).css({ color: "gray" });
    $("#request-withdrawal").attr("disabled", false);
  }, 1000);
}

const fullname = $("#session_Name").val();
const email = $("#session_Email").val();
const bank = $("#bankName").text();

$("#withdrawalAmount").on("keyup", function () {
  const availableAmount = parseFloat($("#availableAmount").val()).toFixed(2); // Ensure it's a float with two decimals
  const withdrawalAmount = parseFloat($(this).val()).toFixed(2); // Ensure the withdrawal amount is captured with two decimals
  const formattedAvailableAmount = formatAmount(availableAmount); // Assuming formatAmount is a function to display the formatted amount

  // Check if the withdrawal amount is a valid number
  if (withdrawalAmount === "") {
    $("#info-span").text("Enter a valid amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If the withdrawal amount is zero
  else if (withdrawalAmount === "0.00") {
    $("#info-span").text("Enter a non-zero amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If available balance is zero
  else if (availableAmount === "0.00") {
    $("#info-span").text("Insufficient balance").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If the withdrawal amount exceeds the available amount
  else if (withdrawalAmount > availableAmount) {
    $("#info-span")
      .html(
        `Enter an amount not greater than <b>${formattedAvailableAmount}</b>`
      )
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If the withdrawal amount is valid
  else {
    $("#info-span").text("");
    $("#request-withdrawal").attr("disabled", false);
  }
});

$("#withdraw-form").on("submit", function (event) {
  event.preventDefault();
  const availableAmount = parseFloat($("#availableAmount").val()).toFixed(2);
  const withdrawalAmount = parseFloat($("#withdrawalAmount").val()).toFixed(2);
  const amount = formatAmount(availableAmount);
  const withdraw = formatAmount(withdrawalAmount);

  if (bank === "" || bank === "null") {
    $("#info-span")
      .text("Add your bank details via the settings page to be able to proceed")
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  }
  // Check if the withdrawal amount is a valid number
  else if (withdrawalAmount === "") {
    $("#info-span").text("Enter a valid amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If the withdrawal amount is zero
  else if (withdrawalAmount === "0.00") {
    $("#info-span").text("Enter a non-zero amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If available balance is zero
  else if (availableAmount === "0.00") {
    $("#info-span").text("Insufficient balance").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  }
  // If the withdrawal amount exceeds the available amount
  else if (withdrawalAmount > availableAmount) {
    $("#info-span")
      .html(`Enter an amount not greater than <b>${amount}</b>`)
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  } else {
    //Do something else
    $("#info-span").text(``).css({ color: "gray" });
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
    const narration = `Withdrawal request of $${withdraw} for ${currentDay}, ${week}`;
    let balance = ((availableAmount - withdrawalAmount) * 1000).toFixed(2);
    if (balance <= 0) {
      balance = 0.10;
    }
    //Send details to server
    $.ajax({
      type: "POST",
      url: "../server/wallet.php",
      data: {
        name: fullname,
        email: email,
        amount: withdrawalAmount,
        bank: bank,
        narration: narration,
        balance: balance,
      },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Withdrawal request placed successfully") {
              $("#info-span")
                .html(
                  `Your withdrawal request for <b>$${withdraw}</b> was successful. Check your mail for more details`
                )
                .css({ color: "green" });
              setTimeout(function () {
                //window.location.reload();
                $("#withdraw-overlay").css({ display: "none" });
                $("#withdrawalAmount").val("");
                clearError();
              }, 3000);
            } else if (
              content === "Withdrawal request have been previously placed"
            ) {
              $("#info-span").html(`${content}`).css({ color: "red" });
              clearError();
            } else {
              $("#info-span")
                .html(
                  `Your withdrawal request for <b>$${withdraw}</b> failed. Kindly try again shortly`
                )
                .css({ color: "red" });
              clearError();
            }
          }
        }
      },
      error: function (e) {
        $("#info-span")
          .text(`Error ocurred while placing request`)
          .css({ color: "red" });
        clearError();
      },
    });
  }
});

$("#withdraw").on("click", function () {
  $("#withdraw-overlay").css({ display: "flex" });
  clearError();
});

$("#close-view").on("click", function () {
  $("#withdraw-overlay").css({ display: "none" });
  $("#withdrawalAmount").val("");
  clearError();
});
