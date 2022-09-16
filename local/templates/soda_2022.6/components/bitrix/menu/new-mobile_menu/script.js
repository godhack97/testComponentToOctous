$(function () {
  $('.menu-toggle-btn').click(function () {
    $(this).toggleClass('active').siblings('.menu-toggle-block').slideToggle(200);
  });
});