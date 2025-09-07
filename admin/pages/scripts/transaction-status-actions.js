import { displayInfo, displaySuccess } from "../scripts/export.js";

function verifyPayment(id) {
     $.ajax({
          type: "POST",
          url: "../server/verify-payment.php",
          data: { id: id },
          dataType: 'json',
          success: function (response) {
               const responseHeader = response.Info;
               if (responseHeader !== 'Error connecting to payment gateway') {
                    const id = response.details.id;
                    const account = response.details.account;
                    const name = response.details.name;
                    const amount = response.details.amount;
                    const status = response.details.status;
                    const bank = response.details.bank;
                    const date = response.details.date;
                    const message = response.details.message;
                    const fee = response.details.fee;
                    const currency = response.details.currency;
                    const alertMessage = `
                                             Here are the details for this payment:

                                             Message: ${message},
                                             Receiver: ${name},
                                             Bank: ${bank}
                                             Account Number: ${account},
                                             Amount: ${amount},
                                             Date: ${date},
                                             Currency: ${currency},
                                             Fee Charged: ${fee},
                                             Transaction ID: ${id},
                                             Transaction Status: ${status}

                                        `;
                    //Show message
                    displayInfo(alertMessage);
                              
               }
               else {
                    const transactionError = response.details.error;
                    displayInfo(transactionError);
               }
          },
          error: function (e) {
               displayInfo(e.responseText);
          }
     });
}

function retryPayment(id) {
    $.ajax({
          type: "POST",
          url: "../server/retry-payment.php",
          data: { id: id },
          dataType: 'json',
          success: function (response) {
               const responseHeader = response.Info;
               if (responseHeader !== 'Error connecting to payment gateway') {
                    const transactionMessage = response.details.message;
                    const transactionID = response.details.id;
                    displaySuccess(transactionMessage);
               }
               else {
                    const transactionError = response.details.error;
                    displayInfo(transactionError);
               }
          },
          error: function (e) {
               displayInfo(e.responseText);
          }
    });
}

$('tr button').each(function (index, el) {
     $(el).on('click', function () {
          const buttonText = $(el).text();
          const id = $(el).parent().parent().attr('id');
          const reference = $(el).parent().parent().find('.reference').text();
         switch (buttonText) {
             case 'Verify':
                 verifyPayment(id);
             break;
             case 'Failed':
                 retryPayment(id);
             break;
         }
    });
 });
