let productCounter = 0;
let userCounter = 0;
let affiliateCounter = 0;
let x, y, z;

function addCommaToNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function updateCounters() {
  $.ajax({
    type: "GET",
    url: "./assets/server/counters.php",
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          const products = content.courseCount;
          const users = content.userCount;
          const affiliates = content.affiliateCount;
          //Update products
          if (products === 0) {
             $("#product-count").text(0);
          } else {
             x = setInterval(() => {
               productCounter++;
               if (productCounter === products) {
                 clearInterval(x);
               }
               $("#product-count").text(addCommaToNumber(productCounter));
             }, 50);
          }
          //Update products
          if (users === 0) {
            $("#customer-count").text(0);
          } else {
             y = setInterval(() => {
               userCounter++;
               if (userCounter === users) {
                 clearInterval(y);
               }
               $("#customer-count").text(addCommaToNumber(userCounter));
             }, 50);
          }
          //Update products
          if (affiliates === 0) {
             $("#affiliate-count").text(0);
          } else {
             z = setInterval(() => {
               affiliateCounter++;
               if (affiliateCounter === affiliates) {
                 clearInterval(z);
               }
               $("#affiliate-count").text(addCommaToNumber(affiliateCounter));
             }, 50);
          }
        }
      }
    },
    error: function () {
      console.log("Error connecting to server");
    },
  });
}

let isCalled = false;

// Define your function to be called when the div is scrolled into view
function handleIntersect(entries, observer) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            // Call your function when the div is scrolled into view
            if (!isCalled) {
                 updateCounters();
                 isCalled = true;
            }
            // Unobserve the target div to stop further notifications
            observer.unobserve(entry.target);
        }
    });
}

// Select the target div
const targetDiv = document.getElementById("info-div");

// Create a new Intersection Observer
const observer = new IntersectionObserver(handleIntersect, { root: null, rootMargin: '0px', threshold: 0 });

// Start observing the target div
observer.observe(targetDiv);
