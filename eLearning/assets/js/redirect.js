//Redirect to profile page
$("#user-image").on("click", function () {
  const id = $("#access-id-holder").text();
  const type = $("#access-type-holder").text();
  window.location = `profile.php?access=${type}&accessID=${id}`;
});

//View courses by clicking on course image
$(".education_block_thumb.n-shadow img").each(function (index, el) {
    $(el).on('click', function () {
        const link = $(el).parent().parent().parent().find(".bl-title a").attr('href');
        window.location = link;
    });
});