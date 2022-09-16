$(function () {
  $('.js-header-menu .bx-nav-parent').hover(
    function () {
			$('header').addClass('js-crack-display');
			$('.catalog-new-filter').addClass('js-crack-display');
      popupOpen('header-menu');
    },
    function () {
			setTimeout(function () {
				$('header').removeClass('js-crack-display');
				$('.catalog-new-filter').removeClass('js-crack-display');
			}, 300);
      popupsClose();
    }
  );
});
