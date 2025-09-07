
 //Display Success Message
 export function displaySuccess(msg) {
    const Toast = Swal.mixin({
     toast: true,
     position: 'center',
     text: msg,
     type: 'success',
     timer: 3000,
     showCancelButton: false,
     showConfirmButton: false
 });
 Toast.fire();
 }
 
 //Display Error Message
 export function displayInfo(info) {
    const Toast = Swal.mixin({
     toast: true,
     position: 'center',
     text: info,
     type: 'info',
     timer: 3000,
     showCancelButton: false,
     showConfirmButton: false
 });
 Toast.fire();
 }