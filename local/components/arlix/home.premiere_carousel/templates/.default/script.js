$(document).ready(function () {

  var sliderIndexTwo = new Swiper('.sliderIndexTwo', {
    slidesPerView: 3,
    initialSlide: 1,
    spaceBetween: 0,
    centeredSlides: true,
    speed: 300,
    loop: false,
    breakpoints: {
      0: {
        slidesPerView: 1.215,
      },
      567: {
        slidesPerView: 1.5,
        spaceBetween: 50,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 65,
      },
      992: {
        slidesPerView: 2.6,
        spaceBetween: 65,
      },
      1280: {
        slidesPerView: 2.9,
        spaceBetween: 96,
      },
      1465: {
        slidesPerView: 2.68,
        spaceBetween: 96,
      },
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });

  var sliderIndexTwoText = new Swiper('.sliderIndexTwo-text', {
    slidesPerView: 3,
    initialSlide: 1,
    spaceBetween: 0,
    speed: 300,
    centeredSlides: true,
    loop: false,
    breakpoints: {
      0: {
        slidesPerView: 1.215,
      },
      567: {
        slidesPerView: 1.5,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 40,
      },
      992: {
        slidesPerView: 2.3,
        spaceBetween: 50,
      },
      1400: {
        slidesPerView: 2.5,
        spaceBetween: 72,
      },
      1600: {
        slidesPerView: 2.68,
        spaceBetween: 96,
      },
    },
  });

  sliderIndexTwo.controller.control = sliderIndexTwoText;
  sliderIndexTwoText.controller.control = sliderIndexTwo;


  var sliderIndexTwoTextblock_itemML = $(
    '.sliderIndexTwo-textblock_item'
  ).css('margin-left');
  console.log(sliderIndexTwoTextblock_itemML);
  $('.sliderIndexTwo-text.swiper').css(
    'margin-left',
    '-' + sliderIndexTwoTextblock_itemML
  );

});


$('.sliderIndexTwo-text.swiper').width(
  $('.sliderIndexTwo-textblock').width()
);
$('.sliderIndexTwo-text.swiper').css(
  'margin-left',
  $('.sliderIndexTwo-textblock_item').css('margin-left')
);
$('.sliderIndexTwo-textblock_item').css(
  'width',
  $('.sliderIndexTwo .swiper-slide').width()
);


$(window).resize(function () {

  $('.sliderIndexTwo-textblock_item').css(
    'width',
    $('.sliderIndexTwo .swiper-slide').width()
  );
  $('.sliderIndexTwo-text.swiper').width(
    $('.sliderIndexTwo-textblock').width()
  );
  var sliderIndexTwoTextblock_itemML = $(
    '.sliderIndexTwo-textblock_item'
  ).css('margin-left');
  $('.sliderIndexTwo-text.swiper').css(
    'margin-left',
    '-' + sliderIndexTwoTextblock_itemML
  );

});
