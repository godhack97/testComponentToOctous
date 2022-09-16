$(function () {
	$('.mobile-menu').on('click', '.menu-name', function () {
		$(this).closest('.menu-item').addClass('active').siblings().removeClass('active');
		$(this).closest('.menu-list').addClass('hide')
	});


	$('.mobile-menu').on('click', '.menu-content-back', function () {
		$(this).closest('.menu-item').removeClass('active');
		$(this).closest('.menu-list').removeClass('hide');
	});
});