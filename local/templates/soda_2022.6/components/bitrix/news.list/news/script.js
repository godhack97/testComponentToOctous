function initSticky() {
  if (window.matchMedia('(min-width: 1024px)').matches) {
    $('.js-sticky-kit-go').stick_in_parent({offset_top: 120});
  }

  if (window.matchMedia('(max-width: 1023px)').matches) {
    $('.js-sticky-kit-go').trigger('sticky_kit:detach');
  }
}

function imageLazyLoad() {
  if ($('img[data-src]').length) {
    $('img[data-src]').each(function () {
      $(this).attr('src', $(this).attr('data-src'));
    });
  }

  if ($('source[data-src]').length) {
    $('source[data-src]').each(function () {
      $(this).attr('srcset', $(this).attr('data-src'));
    });
  }
}

$(function () {
  initSticky();

  imageLazyLoad();

  $(window).on('resize', function () {
    initSticky();
  });
});