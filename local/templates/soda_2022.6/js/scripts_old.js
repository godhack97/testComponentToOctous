window.isMobile = function () {
  var check = false;
  (function (a) {
    if (
      /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(
        a
      ) ||
      /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
        a.substr(0, 4)
      )
    )
      check = true;
  })(navigator.userAgent || navigator.vendor || window.opera);
  return check;
};

var maskPhoneList = {};
var maskPhoneOpts = {};

$(function () {
  lazyLoadProducts();

  maskPhoneList = $.masksSort(
    $.masksLoad(
      '/local/templates/sodamoda/js/inputmask-multi/data/phone-codes.json'
    ),
    ['#'],
    /[0-9]|#/,
    'mask'
  );
  maskPhoneOpts = {
    inputmask: {
      definitions: {
        '#': {
          validator: '[0-9]',
          cardinality: 1,
        },
      },
      //clearIncomplete: true,
      showMaskOnHover: false,
      autoUnmask: true,
    },
    match: /[0-9]/,
    replace: '#',
    list: maskPhoneList,
    listKey: 'mask',
    onMaskChange: function (maskObj, completed) {},
  };

  if ($('input[name=USER_PHONE]').length)
    $('input[name=USER_PHONE]').inputmasks(maskPhoneOpts);

  if ($('input.js-mask-phone').length)
    $('input.js-mask-phone').inputmasks(maskPhoneOpts);

  if ($('#popup-form-field-phone').length)
    $('#popup-form-field-phone').inputmasks(maskPhoneOpts);

  $(document).on('click', '.js-metrika-order-start', function () {
    ym(54055981, 'reachGoal', 'order-start');
  });

  $(document).on('click', '.js-metrika-order-send', function () {
    ym(54055981, 'reachGoal', 'order-send');
  });

  $(document).on('click', 'a[href*="instagram.com/sodamoda.ru"]', function () {
    ym(54055981, 'reachGoal', 'go-to-instagram');
  });

  $(document).on(
    'click',
    'a[href*="api.whatsapp.com/send?phone="]',
    function () {
      ym(54055981, 'reachGoal', 'go-to-whatsapp');
    }
  );

  $(document).on('click', '.js-metrika-order-whatsapp', function () {
    ym(54055981, 'reachGoal', 'go-to-whatsapp-order');
  });

  $(document).on('click', '.js-metrika-order-phone', function () {
    ym(54055981, 'reachGoal', 'go-to-phone-order');
  });

  $('.js-menu-left-toggle').click(function () {
    $(this).toggleClass('show').siblings('.items').toggleClass('display');
  });

  $('.toggle-menu-arrow').click(function () {
    $(this).toggleClass('show');
  });

  $('.js-menu-subs-display').click(function () {
    $(this).siblings('.subs').toggleClass('display');
  });

  $('.js-set-search').click(function () {
    $(this).closest('.search').addClass('set-search').removeClass('no-active');
    $(this).closest('.search').find('.query').focus();
  });

  $('.js-check-email-subscribe').keyup(function () {
    if ($(this).val() != '') {
      $(this).closest('.subscribe').addClass('show-send');
    } else $(this).closest('.subscribe').removeClass('show-send');
  });

  $('.js-mobile-open-search').click(function () {
    popupOpen('search');
    setTimeout(function () {
      $('.popup-search .field-text').focus();
    }, 400);
  });

  $('.js-mobile-open-call').click(function () {
    popupOpen('call');
  });

  if (BX.getCookie('BITRIX_SM_RULES_COOKIE') == undefined)
    $('#js-popup-cookie').fadeIn(200);

  $(document).on('click', '.popup-cookie .ibutton', function () {
    closePopupCookie();
  });

  $(document).on(
    'click',
    '.popup-cookie-content .close-button-wrap',
    function () {
      closePopupCookie();
    }
  );

  $(document).on(
    'click',
    'a, button, input[type=button], input[type=submit]',
    function () {
      closePopupCookie();
    }
  );

  $(document).scroll(function () {
    closePopupCookie();
    lazyLoadProducts();
  });

  $(document).on('click', '.action-banner-section .action-close', function () {
    closeActionBannerSection();
  });

  $(document).on('click', '.js-switch-color', function () {
    $(this).closest('.image').find('img').hide();
    $(this)
      .closest('.image')
      .find('img[data-color="' + $(this).data('color') + '"]')
      .show();
    $(this).addClass('active').siblings('.color').removeClass('active');
  });

  $('.overlay-content').click(function () {
    popupsClose();
  });

  $('.js-mobile-menu').click(function () {
    if (!$(this).hasClass('is-active')) {
      $(this).toggleClass('is-active');
      popupOpen('menu', true);
    } else popupsClose();
  });

  // Открытие меню при ховере
  if ($('body').hasClass('mode-aside-hover') && !isMobile()) {
    $('.hamburger .hover-area').hover(
      function () {
        if (!$('.popup-menu').hasClass('active'))
          $('.js-mobile-menu').trigger('click');
      },
      function () {}
    );

    $('.popup-menu').hover(
      function () {},
      function () {
        if ($(this).hasClass('active')) $('.js-mobile-menu').trigger('click');
      }
    );
  }

  $('.js-basket-display').hover(
    function () {
      if ($('.basket-mini .item').length > 0 && !isMobile())
        popupOpen('basket');
    },
    function () {
      popupsClose();
    }
  );

  $('.js-calling-display').hover(
    function () {
      popupOpen('calling');
    },
    function () {
      popupsClose();
    }
  );

  $(document).on('mouseenter', '.js-langs-display', function () {
    popupOpen('langs');
  });

  $(document).on('mouseleave', '.js-langs-display', function () {
    popupsClose();
  });

  $('.js-addtobasket').click(function () {
    // Это должна быть единственная функция добавления товара в корзину

    var size = $(this).data('size');

    // Если размера нету в вёрстке совсем, то добавляем товар в корзину без размера
    if (!$(this).closest('.desktop-display').find('.size-module .size').length)
      size = true;

    var color = {
      NAME: $(this).data('nameColor'),
      CODE: $(this).data('codeColor'),
      VALUE: $(this).data('valueColor'),
    };

    var article = {
      NAME: $(this).data('nameArticle'),
      CODE: $(this).data('codeArticle'),
      VALUE: $(this).data('valueArticle'),
    };

    if (!size) {
      // Если не указан размер, останавливаем процесс добавления в корзину
      popupOpen('product-size');
    } else {
      waitCheck();

      $.ajax({
        type: 'POST',
        data: {
          action: 'ADD2BASKET',
          id: $(this).data('id'),
          site_id: BX.message('SITE_ID'),
          prop: [size, color, article],
        },
        url: '/ajax/add.php',
        success: function (data) {
          $('#basket-mini-wr').html(data);
          waitCheck('close');
          popupOpen('basket');
          // Через X секунды закрываем окно, если он только не на нём
          setTimeout(function () {
            if (!$('header .basket-mini').is(':hover')) popupsClose();
          }, 2000);
        },
      });
    }
  });

  var propid = $.cookie('propid');
  var elid = $.cookie('elid');

  if (
    typeof propid != 'undefined' &&
    typeof elid != 'undefined' &&
    propid &&
    elid
  ) {
    var el = $(
      '.desktop-display .js-set-size[data-propid=' +
        propid +
        '][data-elid=' +
        elid +
        ']'
    );
    if (el.length == 1) {
      el.closest('.item')
        .addClass('active')
        .siblings('.item')
        .removeClass('active');
      var size = {
        NAME: el.data('name'),
        CODE: el.data('code'),
        VALUE: el.data('value'),
      };
      el.closest('.info').find('.js-addtobasket').data('size', size);
    }
  }

  $('.desktop-display .js-set-size').click(function () {
    var size = addCookieSize(this);
  });

  $(document).on('click', '[data-js-addtobasket-go-item]', function () {
    console.log($(this));
    if ($(this).hasClass('disabled')) {
      return false;
    }

    // Это добавление товара в МОБ. ВЕРСИИ
    console.log('Добавление в корзину — mobile');

    var sizeModule = $(this).closest('.size-module');
    if (sizeModule.hasClass('is-loading')) return;
    else sizeModule.addClass('is-loading');

    var data = getPropOffer($(this));
    var mobileBuy = $(this).closest('.mobile-buy');

    mobileBuy.find('.size-module').removeClass('display');
    $('.mobile-message-added .insert-size').text($(this).data('valueSize'));
    waitCheck();

    $.ajax({
      type: 'POST',
      data: data,
      url: '/ajax/add.php',
      success: function (data) {
        $('#basket-mini-wr').html(data);
        waitCheck('close');
        $('.mobile-message-added').addClass('display');
        sizeModule.removeClass('is-loading');
        displayBasketAfterAdd();

        ym(54055981, 'reachGoal', 'addtobasket');

        setTimeout(function () {
          $('.mobile-message-added').removeClass('display');
        }, 4000);
      },
    });
  });

  $('[data-addtobasket-offer]').on('click', function (e) {
    // Добавление торгового предложения
    console.log('Добавление в корзину — main');

    var BSize = $(
      '.desktop-display [data-offer-color-sizes]:visible .item.active a'
    );
    if (BSize.length > 0) {
      waitCheck();
      var data = getPropOffer(BSize);

      $.ajax({
        type: 'POST',
        data: data,
        url: '/ajax/add.php',
        success: function (data) {
          $('#basket-mini-wr').html(data);
          waitCheck('close');
          popupOpen('basket');

          ym(54055981, 'reachGoal', 'addtobasket');

          // Через X секунды закрываем окно, если он только не на нём
          setTimeout(function () {
            if (!$('header .basket-mini').is(':hover')) popupsClose();
          }, 2000);

          BSize.closest('.item')
            .removeClass('active')
            .siblings('.item')
            .removeClass('active');
        },
      });
    } else popupOpen('product-size');
  });

  //  $(document).ready(function(){
  //    $('.js-change-color.active').trigger('click');
  //  });

  $(document).on('click', '.js-change-color', function () {
    var checkStoreLink = $('#js-store-checked');
    var keyColor = $(this).data('key');
    var availableColor = $(this).data('available');
    var preorderColor = $(this).data('preorder');
    var product = $(this).closest('.product-detail');
    let currentPriceContainer = $('#price-current');
    let oldPriceContainer = $('#price-old');
    let isDiscount = $(this).data('discount');

    if (checkStoreLink.length) {
      var baseUrlCheckStore = checkStoreLink.data('base-url');
      checkStoreLink.attr('href', baseUrlCheckStore + '&color=' + keyColor);
    }

    $('.cc_list').hide();
    $('#cc_' + keyColor).show();

    $(this).addClass('active').siblings('a').removeClass('active');
    if (isDiscount) {
      oldPriceContainer.html($(this).data('oldPrice')).addClass('price-old');
      currentPriceContainer.html($(this).data('currentPrice'));
    } else {
      oldPriceContainer.html('').removeClass('price-old');
      currentPriceContainer.html($(this).data('currentPrice'));
    }
    $(this).parent('.value').siblings('.value').find('a').removeClass('active');

    $('.size-module .size').removeClass('active');
    $('.size-module .size[data-key=' + keyColor + ']').addClass('active');

    $('.desktop-display')
      .find('[data-offer-color-sizes] .item')
      .removeClass('active');

    if (!isMobile()) {
      $('.product-detail-slider .image').addClass('display-none');
      $('.product-detail-slider .image.color-' + keyColor).removeClass(
        'display-none'
      );
    }

    if (
      isMobile() &&
      product.find('.product-detail-slider.mobile-colors').length
    ) {
      product
        .find('.product-detail-slider.mobile-colors .tns-outer')
        .removeClass('active');
      product
        .find(
          '.product-detail-slider.mobile-colors .tns-outer.color-' + keyColor
        )
        .addClass('active');
      toBottom();
    }

    if ($('.favorites-list .favorite-item').length) {
      $('.favorites-list .favorite-item').removeClass('active');
      $(
        '.favorites-list .favorite-item[data-color-key=' + keyColor + ']'
      ).addClass('active');
    }
  });

  $(document).on('click', '.mobile-buy .js-addtobasket-go-item', function () {
    console.log('ничёсе 1.1');

    var size = addCookieSize(this);
    var color = {
      NAME: $(this).data('nameColor'),
      CODE: $(this).data('codeColor'),
      VALUE: $(this).data('valueColor'),
    };
    var article = {
      NAME: $(this).data('nameArticle'),
      CODE: $(this).data('codeArticle'),
      VALUE: $(this).data('valueArticle'),
    };

    var mobileBuy = $(this).closest('.mobile-buy');

    mobileBuy.find('.size-module').removeClass('display');

    $('.mobile-message-added .insert-size').text(size.VALUE);

    waitCheck();

    $.ajax({
      type: 'POST',
      data: {
        action: 'ADD2BASKET',
        id: $(this).data('id'),
        site_id: BX.message('SITE_ID'),
        prop: [size, color, article],
      },
      url: '/ajax/add.php',
      success: function (data) {
        $('#basket-mini-wr').html(data);
        waitCheck('close');
        $('.mobile-message-added').addClass('display');
        displayBasketAfterAdd();
        setTimeout(function () {
          $('.mobile-message-added').removeClass('display');
        }, 5000);
      },
    });
  });

  $(document).on('click', '.js-open-popup-cares', function () {
    popupOpen('product-cares');
  });

  $(document).on('click', '.js-open-popup-delivery', function () {
    popupOpen('delivery');
  });

  $(document).on('click', '.js-open-popup-table-sizes', function () {
    popupOpen('table-sizes');
  });

  $(document).on('click', '.js-popup-close', function () {
    popupsClose();
  });

  $(document).on('click', '.js-open-popup-name', function () {
    popupOpen($(this).data('form-name'));
    if ($(this).data('form-name') == 'vacancy')
      $('form[name=vacancy] input[name="vacancy"]').val(
        $(this).closest('.block').find('h2').text()
      );
  });

  //  $('.main-slider').owlCarousel({
  //    items: 1,
  //    autoplay: true,
  //    autoplayTimeout: 5000,
  //    fallbackEasing: 'linear',
  //    loop: true,
  //    autoplaySpeed: 1000,
  //    dots: false
  //  });

  var sliderIndexTwo = new Swiper('.sliderIndexTwo', {
    slidesPerView: 3,
    spaceBetween: 40,
    centeredSlides: true,
    speed: 400,
    loop: true,
    autoplay: {
      delay: 2500,
      // disableOnInteraction: false,
    },
    breakpoints: {
      // when window width is >= 320px
      0: {
        slidesPerView: 1.2,
        spaceBetween: 10,
      },
      // when window width is >= 480px
      480: {
        slidesPerView: 1.5,
        spaceBetween: 20,
      },
      // when window width is >= 640px
      992: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });

  // if ($('.main-slider-init').length) {
  //   var sliderIndex = tns({
  //     container: '.main-slider-init',
  //     items: 1,
  //     nav: false,
  //     controls: false,
  //     autoplay: true,
  //     autoplayButtonOutput: false,
  //     autoplayTimeout: 5000,
  //     autoHeight: true,
  //     mouseDrag: true,
  //     loop: true,
  //     preventScrollOnTouch: 'auto',
  //     onInit: function () {
  //       $('.main-slider').removeClass('initial-end');
  //       if ($('#tns1 .tns-item.tns-slide-active').hasClass('is-dark-mode')) {
  //         $('body').addClass('slider-index-dark-mode');
  //       }
  //     },
  //   });

  //   var sliderIndexTransitionStart = function (info, eventName) {
  //     // console.log(info.event.type, info.container.id);
  //     if (
  //       $('#' + info.container.id + ' .tns-item.tns-slide-active').hasClass(
  //         'is-dark-mode'
  //       )
  //     ) {
  //       $('body').addClass('slider-index-dark-mode');
  //     } else {
  //       $('body').removeClass('slider-index-dark-mode');
  //     }
  //   };

  //   sliderIndex.events.on('transitionStart', sliderIndexTransitionStart);
  // }

	//slider premier
  if ($('.slider-premiere-init').length) {
    var sliderIndex = tns({
      container: '.slider-premiere-init',
      items: 1,
      nav: false,
      controls: false,
      autoplay: true,
      autoplayButtonOutput: false,
      autoplayTimeout: 5000,
      autoHeight: true,
      mouseDrag: true,
      loop: true,
      preventScrollOnTouch: 'auto',
    });
  }

  if ($('.page-main .main-history .items-init').length && isMobile()) {
    var sliderIndexHistory = tns({
      container: '.main-history .items-init',
      items: 'auto',
      nav: false,
      controls: false,
      autoplay: false,
      autoplayButtonOutput: false,
      autoHeight: true,
      autoWidth: true,
      mouseDrag: true,
      loop: false,
      preventScrollOnTouch: 'auto',
      onInit: function () {
        $('.main-history .items').removeClass('initial-end');
      },
    });
  }

  $(document).mouseup(function (e) {
    // событие клика по веб-документу
    var div = $('header .search'); // тут указываем ID элемента
    if (
      !div.is(e.target) && // если клик был не по нашему блоку
      div.has(e.target).length === 0
    ) {
      // и не по его дочерним элементам
      div.addClass('no-active').removeClass('set-search');
    }
  });

  // $(document).on('click', 'header .info .basket .wrap-icons', function () {
  //   location.href = BX.message('BASKET_URL_LANG');
  // });
  $(document).on('click', '.header-info__nav .basket .wrap-icons', function () {
    location.href = BX.message('BASKET_URL_LANG');
  });

  $.fancybox.defaults.animationEffect = 'zoom-in-out'; // "fade";
  $.fancybox.defaults.buttons = ['close'];
  $.fancybox.defaults.infobar = false;

  initStickyKit();

  initSliderInSection();

  $('.js-open-chat').click(function () {
    $('body').addClass('chat24-show');
    //    setTimeout(function(){
    //      console.log('button.messenger');
    //      $('button.messenger').trigger('click');
    //    }, 1000);

    // desktop
    if (!isMobile()) {
      //console.log('.js-open-chat 121');
      //$('.Controls-module__messenger___1ovCY').trigger('click');
      //$('#chat-24-roll-icon').trigger('click');
      //      setTimeout(function(){
      //        console.log('.js-open-chat 123');
      //        $('#chat-24-icon-7').trigger('click');
      //      }, 0);
      //$('.b24-widget-button-inner-item[data-b24-crm-button-icon="openline"]').trigger('click');
    }

    // mobile
    else {
      //console.log('.js-open-chat MOBILE 2');
      //      $('.Controls-module__list___1lFlP .Controls-module__button___2xf6U:eq(0)').trigger('click');
      //      popupsClose();
      //$('#chat-24-roll').trigger('click');
      //      setTimeout(function(){
      //        $('#chat-24-icon-7').trigger('click');
      //      }, 300);
      //
      //      $('#chat-24-widget-container #chat-24-mobile #chat-24-popup-7').css({
      //        display: 'block',
      //        opacity: 1,
      //        visibility: 'visible'
      //      });
      //$('#chat-24-icon-7').trigger('click');
    }
  });

  // Авторизация и регистрация
  $('form[name=auth], form[name=register]').submit(function () {
    ajaxQuery($(this));
    return false;
  });
});

$(window).resize(function () {
  initStickyKit();
});

function lazyLoadProducts() {
  if ($('.page-catalog .products .item__hide').length) {
    var windowTop = $(window).scrollTop();
    var windowHeight = $(window).height();

    $('.page-catalog .products .item__hide').each(function (index) {
      if (windowTop + windowHeight * 2 > $(this).offset().top) {
        $(this).removeClass('item__hide');

        var IMG = $(this).find('img');
        IMG.attr('src', IMG.attr('data-img-src')).attr(
          'srcset',
          IMG.attr('data-img-srcset')
        );

        $(this)
          .find('source')
          .each(function () {
            $(this).attr('srcset', $(this).attr('data-img-srcset'));
          });
      }
    });
  }
}

function closePopupCookie() {
  if ($('#js-popup-cookie').is(':visible')) {
    BX.setCookie('BITRIX_SM_RULES_COOKIE', 'Y', {
      expires: 86400 * 350,
      path: '/',
      secure: true,
    });
    $('#js-popup-cookie').fadeOut(200);
  }
}

function closeActionBannerSection() {
  if ($('.action-banner-section').is(':visible')) {
    BX.setCookie('BITRIX_SM_ACTION_BANNER_SECTION', 'HIDE', {
      expires: 86400 * 350,
      path: '/',
      secure: true,
    });
    $('.action-banner-section').hide();
    $('body').removeClass('action-banner-visible');
  }
}

/**
 * Инициализация слайдера в каталоге
 */
function initSliderInSection() {
  if ($('.slider-in-section').length) {
    console.log('slider-in-section');
    //    $('.slider-in-section').bxSlider({
    //      captions: false,
    //      pager: false,
    //      oneToOneTouch: true,
    //      swipeThreshold: 100,
    //      slideWidth: 0
    //    });

    //    $('.slider-in-section').owlCarousel({
    //      items: 1,
    //      autoplay: false,
    //      fallbackEasing: 'linear',
    //      loop: false,
    //      dots: false
    //    });

    var arrSliderInSection = [];

    $('.slider-in-section').each(function (i) {
      console.log('__ i = ' + i);
      $(this).addClass('count-' + i);
      arrSliderInSection[i] = tns({
        container: '.slider-in-section.count-' + i,
        items: 1,
        nav: false,
        controls: false,
        autoplay: false,
        autoplayButtonOutput: false,
        autoHeight: true,
        mouseDrag: true,
        loop: false,
        preventScrollOnTouch: 'auto',
      });
    });
  }
}

function displayBasketAfterAdd() {
  if ($('#basket-mini-wr').find('.count').hasClass('display'))
    $('#basket-mini-wr').addClass('icon-mobile-display');
}

function initStickyKit() {
  if ($(window).innerWidth() > 768) {
    if ($('body').hasClass('page-product')) {
      //$('.product-detail .js-sticky-kit-go').stick_in_parent({offset_top: 114}); // {offset_top: 60}
      console.log('.product-detail .js-sticky-kit-go');
      $('.product-detail .js-sticky-kit-go')
        .stick_in_parent({ offset_top: 114 })
        .on('sticky_kit:bottom', function (e) {
          $('body').addClass('is-header-overlaps');
        })
        .on('sticky_kit:unbottom', function (e) {
          $('body').removeClass('is-header-overlaps');
        });
    } else if ($('body').hasClass('page-catalog'))
      $('aside .js-sticky-kit-go').stick_in_parent({ offset_top: 138 });
    else if ($('body').hasClass('page-content'))
      $('aside .js-sticky-kit-go').stick_in_parent({ offset_top: 114 });
    else if ($('body').hasClass('page-main')) {
      $('aside .js-sticky-kit-go').stick_in_parent({ offset_top: 0 });
      $('.menu-left-bottom .toggle').on('click', function () {
        $('aside .js-sticky-kit-go').trigger('sticky_kit:recalc');
      });
    }
  } else $('.js-sticky-kit-go').trigger('sticky_kit:detach');
}

function popupOpenMessage(popupText) {
  popupOpen('message', false, popupText);
}

function popupOpen(popupName, fixedHeader, popupText) {
  //disableBodyScroll(true, '.overlay-content');
  fixedHeader = fixedHeader || false;
  popupText = popupText || false;
  $('.popup-' + popupName).addClass('active');

  if (popupName == 'basket') {
    $('header .basket-mini').addClass('display');
    $('header .header-search').addClass('is-hide');
  }

  if (popupName == 'calling') {
    $('header .calling-window').addClass('active');
		$('header .header-search').addClass('is-hide');
  }

  if (popupName == 'langs') {
    $('header .langs-window').addClass('active');
		$('header .header-search').addClass('is-hide');
  }

  if (popupName == 'product-size' && !isMobile()) {
    $('.popup-product-size .modal-size-items').html(
      $('.desktop-display .size-module .size.active').clone()
    );
  }

  if (popupText != false) {
    $('.popup-' + popupName)
      .find('.info')
      .html(popupText);
  }

  if (popupName == 'header-menu') $('body').addClass('open-menu');
  if (popupName != 'header-menu') $('body').addClass('open-popup');

  if (fixedHeader) {
    $('body').addClass('header-fixed');

    //    //https://benfrain.com/preventing-body-scroll-for-modals-in-ios/
    //    var body = document.body;
    //    var freezeVp = function(e) {
    //      e.preventDefault();
    //    };
    //    body.setAttribute("data-reg-document-modal", "active");
    //    body.addEventListener("touchmove", freezeVp, false);
  }
}

function popupsClose() {
  $('.popups').removeClass('active');
  $('body').removeClass('open-popup').removeClass('header-fixed');
  $('body').removeClass('open-menu');
  $('header .hamburger').removeClass('is-active');
  $('header .basket-mini').removeClass('display');
  $('header .calling-window').removeClass('active');
  $('header .langs-window').removeClass('active');

	$('header .header-search').removeClass('is-hide');

  if ($('.catalog-filter').hasClass('open'))
    $('.catalog-filter').removeClass('open');

  if (isMobile()) {
    if ($('.product-detail .info .size-module').length)
      $('.product-detail .info .size-module').removeClass('display');
  }

  //disableBodyScroll(false);
}

/**
 * Меняем курсор мышки
 */
function waitCheck(mode) {
  mode = mode || 'open';

  switch (mode) {
    case 'close':
      $('body').removeClass('wait');
      if ($('#content-loading').length)
        $('#content-loading').removeClass('active');
      break;
    default:
      $('body').addClass('wait');
      if ($('#content-loading').length)
        $('#content-loading').addClass('active');
      break;
  }
}

/**
 * Отправляем данные формы для авторизации/регистрации/др. пользователя
 */
function ajaxQuery(form) {
  if ($('body').hasClass('wait')) return;

  waitCheck();

  console.log('start ajaxQuery');

  $.ajax({
    type: 'POST',
    url: '/include/ajax.php',
    data: form.serialize() + '&site_id=' + BX.message('SITE_ID'),
    dataType: 'json',
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      console.log(textStatus);
      console.log(errorThrown);
    },
    success: function (result) {
      console.log(result);
      if (result.status) {
        if (
          result.mode == 'authorize' ||
          result.mode == 'register-check-phone' ||
          result.mode == 'order_verify_phone'
        ) {
          location.reload();
        }

        if (result.mode == 'register') {
          // Прячем форму регистрации и показываем форму ввода кода подтверждения
          $('#register_form').hide();
          $('#form-register-check-phone').show();
        }

        if (result.mode == 'return') {
          form[0].reset();

          popupMessage = $('.popup-message');
          popupMessage.find('.title').hide();
          popupMessage
            .find('.info')
            .html(BX.message('RETURN_REQUEST_SENT_SUCCESS'));
          popupOpen('message');

          setTimeout(function () {
            location.href = '/personal/order/' + result.order_id + '/';
          }, 2000);
        }

        if (result.mode == 'returnNotAuth') {
          form[0].reset();

          popupMessage = $('.popup-message');
          popupMessage.find('.title').hide();
          popupMessage
            .find('.info')
            .html(BX.message('RETURN_REQUEST_SENT_SUCCESS'));
          popupOpen('message');
        }

        if (result.mode == 'fitting') {
          popupsClose();
          setTimeout(function () {
            popupOpenMessage(
              'Запись успешно отправлена!<br>В ближайшее время наш менеджер свяжется с Вами'
            );
          }, 500);
        }

        if (result.mode == 'userinfo') {
          console.log(result);
          popupsClose();
          setTimeout(function () {
            popupOpenMessage('Заявка успешно отправлена!');
          }, 500);
        }
      } else formResultDisplay(form, result.message);

      waitCheck('close');
    },
  });
}

function formResultDisplay(form, message) {
  var resultBlock = $(form).find('.message-result');
  resultBlock.removeClass('success').html(message).show();
}

function getPropOffer(_this) {
  var color = {
    NAME: _this.data('nameColor'),
    CODE: _this.data('codeColor'),
    VALUE: _this.data('valueColor'),
  };

  var article = {
    NAME: _this.data('nameArticle'),
    CODE: _this.data('codeArticle'),
    VALUE: _this.data('valueArticle'),
  };

  var size = {
    NAME: _this.data('nameSize'),
    CODE: _this.data('codeSize'),
    VALUE: _this.data('valueSize'),
  };
  var data;
  if (_this.data('preorder')) {
    var preorder = {
      NAME: 'Покупка по предзаказу',
      CODE: 'PREORDER_PRODUCT',
      VALUE: 'Y',
    };

    data = {
      action: 'ADD2BASKET',
      id: _this.data('elid'),
      site_id: BX.message('SITE_ID'),
      prop: [preorder, size, color, article],
    };
  } else {
    data = {
      action: 'ADD2BASKET',
      id: _this.data('elid'),
      site_id: BX.message('SITE_ID'),
      prop: [size, color, article],
    };
  }

  return data;
}

function addCookieSize(_this) {
  $(_this)
    .closest('.item')
    .addClass('active')
    .siblings('.item')
    .removeClass('active');
  var size = {
    NAME: $(_this).data('name'),
    CODE: $(_this).data('code'),
    VALUE: $(_this).data('value'),
  };

  var propid = $(_this).data('propid');
  var elid = $(_this).data('elid');
  var elurl = $(_this).data('elurl');

  $.cookie('propid', propid, { expires: 365, path: elurl });
  $.cookie('elid', elid, { expires: 365, path: elurl });

  $(_this).closest('.info').find('.js-addtobasket').data('size', size);

  return size;
}

function number_format(number, decimals, dec_point, separator) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');

  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = typeof separator === 'undefined' ? ',' : separator,
    dec = typeof dec_point === 'undefined' ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k).toFixed(prec);
    };

  // Фиксим баг в IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }

  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }

  return s.join(dec);
}

$(window).on('load', function () {
  function setHeaderClassOnScroll() {
    var lastScrollTop = 0;
    $(window).scroll(function (event) {
      var st = $(this).scrollTop();
      if (st > lastScrollTop && st > 0) {
        $('header').addClass('head-animated').removeClass('head-hide');
        $('.catalog-filter').addClass('head-animated').removeClass('head-hide');
      } else if (st == 0) {
        $('header').removeClass('head-animated head-hide');
        $('.catalog-filter').removeClass('head-animated head-hide');
      } else {
        $('header').removeClass('head-animated').addClass('head-hide');
        $('.catalog-filter').removeClass('head-animated').addClass('head-hide');
      }
      lastScrollTop = st;
    });
  }

  if ($('body').hasClass('page-catalog') && isMobile()) {
    $('body').addClass('is-mobile');
    setHeaderClassOnScroll();
  }
});
