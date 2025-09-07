// Get the URL of the current page
let link = window.location.href;

// Splitting the link by '?' character
let parts = link.split("?");

// Extracting the code from the second part
const code = parts[1];

//Redirect to link
$.ajax({
  type: "POST",
  url: "./assets/server/contest-redirect.php",
  data: { code: code },
  dataType: "json",
  success: function (response) {
    for (const key in response) {
      if (Object.hasOwnProperty.call(response, key)) {
        const content = response.Info;
        if (content === "Redirecting") {
          window.location = response.link;
        } else {
          alert(response.details);
        }
      }
    }
  },
  error: function () {
    alert("Error connecting to server");
  },
});
