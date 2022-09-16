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

  imageLazyLoad();

});