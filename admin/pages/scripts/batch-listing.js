
$('.button-row').children('button').each(function (index, el) {
    $(el).on('click', function () {
        const batchID = $(el).parent().parent().attr('id');
        //Redirect to status page
        window.location = `../views/transaction-status-view?batchID=${batchID}`;
   });
});