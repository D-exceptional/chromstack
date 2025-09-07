import { displaySuccess, displayInfo } from "../scripts/export.js";

// Toggle affiliate status (Activate/Deactivate)
function toggleStatus(action, id, name) {
  const confirmMessage = action === "Deactivate" 
    ? `Are you sure you want to deactivate ${name}'s account?`
    : `Are you sure you want to activate ${name}'s account?`;

  // Ask for confirmation before proceeding
  if (confirm(confirmMessage)) {
    const url = action === "Deactivate" ? "../server/deactivate-affiliate.php" : "../server/activate-affiliate.php";
    const postData = { affiliateID: id };

    // Perform AJAX request
    $.ajax({
      type: "POST",
      url: url,
      data: postData,
      dataType: "json",
      success: function(response) {
        handleStatusChange(response, action, id);
      },
      error: function(e) {
        displayInfo(e.responseText);
      },
    });
  }
}

// Handle the status change response
function handleStatusChange(response, action, id) {
  // Iterate through the response object
  for (const key in response) {
    if (Object.hasOwnProperty.call(response, key)) {
      const content = response[key];

      if (content !== "Error updating status" && content !== "Affiliate ID missing") {
        updateAffiliateRow(id, action);
        displaySuccess(content);
      } else {
        displayInfo(content);
      }
    }
  }
}

// Update affiliate row based on the action (Activate/Deactivate)
function updateAffiliateRow(id, action) {
  $(".rows").each(function () {
    const row = $(this);
    if (row.attr("id") === id) {
      const button = row.find("button");
      const status = row.find(".status");

      if (action === "Deactivate") {
        button.removeClass("btn-danger").addClass("btn-success").text("Activate");
        status.text("Deactivated");
      } else {
        button.removeClass("btn-success").addClass("btn-danger").text("Deactivate");
        status.text("Active");
      }
    }
  });
}

// Update active affiliates counter
function updateAffiliatesCounter() {
  const counter = $("tbody").children("tr").length;
  $(".col-sm-6 h1").html(`<b>Active Affiliates (${counter})</b>`);
}

// Event delegation for button click (Activate/Deactivate)
$("tbody").on("click", "button", function () {
  const status = $(this).text();
  const id = $(this).closest("tr").attr("id");
  const name = $(this).closest("tr").find(".fullname").text();
  const action = status === "Deactivate" ? "Deactivate" : "Activate";
  
  toggleStatus(action, id, name);
});

// Indent all child navs in sidebar
$(".nav-sidebar").addClass("nav-child-indent");

// Search functionality for table rows
$("#page-search").on("keyup", function () {
  const searchValue = $(this).val().toLowerCase();
  
  // Filter table rows based on search value
  $(".rows").each(function () {
    const row = $(this);
    const rowText = row.find("td").text().toLowerCase();

    row.toggle(rowText.includes(searchValue));
  });
});

// Initialize active affiliates count
updateAffiliatesCounter();
