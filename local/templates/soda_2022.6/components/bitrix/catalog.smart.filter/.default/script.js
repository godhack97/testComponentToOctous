function initRangeSliderBX() {
  $("#range-slider").slider({
    min: 1,
    max: 3,
    step: 1,
    value: $("#range-slider").data('view-mode'),
    slide: function( event, ui ) {
      let container = document.getElementById('range-slider');
      container.dataset.viewMode = ui.value;
      $.cookie('viewMode', ui.value, { expires: 365, path: '/' });

      let viewMode = 'type-1';
      switch (ui.value){
        case 1:
          ym(54055981,'reachGoal','catalog_veiw_1');
          viewMode = 'type-1';
          $('.ajax_load_items').addClass(viewMode).removeClass('type-2').removeClass('type-3');
          $('.ajax_load_items').trigger('change');
          break;
        case 2:
          ym(54055981,'reachGoal','catalog_veiw_2');
          viewMode = 'type-2';
          $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-3');
          $('.ajax_load_items').trigger('change');
          break;
        case 3:
          ym(54055981,'reachGoal','catalog_veiw_3');
          viewMode = 'type-3';
          $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-2');
          $('.ajax_load_items').trigger('change');
          break;
      }
    }
  });
}
function initRangeSlider() {
  $(function() {
    $("#range-slider").slider({
      min: 1,
      max: 3,
      step: 1,
      value: $("#range-slider").data('view-mode'),
      slide: function( event, ui ) {
        let container = document.getElementById('range-slider');
        container.dataset.viewMode = ui.value;
        $.removeCookie('viewMode');
        $.cookie('viewMode', ui.value, { expires: 365, path: '/' });

        let viewMode = 'type-1';
        switch (ui.value){
          case 1:
            ym(54055981,'reachGoal','catalog_veiw_1');
            viewMode = 'type-1';
            $('.ajax_load_items').addClass(viewMode).removeClass('type-2').removeClass('type-3');
            $('.ajax_load_items').trigger('change');
            break;
          case 2:
            ym(54055981,'reachGoal','catalog_veiw_2');
            viewMode = 'type-2';
            $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-3');
            $('.ajax_load_items').trigger('change');
            break;
          case 3:
            ym(54055981,'reachGoal','catalog_veiw_3');
            viewMode = 'type-3';
            $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-2');
            $('.ajax_load_items').trigger('change');
            break;
        }
      }
    });
  });
}
function initViewCatalog() {
  switch (parseInt($.cookie('viewMode'))){
    case 1:
      viewMode = 'type-1';
      $('.ajax_load_items').addClass(viewMode).removeClass('type-2').removeClass('type-3');
      $('.ajax_load_items').trigger('change');
      break;
    case 2:
      viewMode = 'type-2';
      $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-3');
      $('.ajax_load_items').trigger('change');
      break;
    case 3:
      viewMode = 'type-3';
      $('.ajax_load_items').addClass(viewMode).removeClass('type-1').removeClass('type-2');
      $('.ajax_load_items').trigger('change');
      break;
  }
}
function SmartFilterSodamoda()
{
  var name = 'smart_filter_sodamoda_form',
      obj = $('[data-name=' + name + ']'),
      action = obj.data('action'),
      filterNumber = obj.find('[data-filter-number]'),
      filterItems = obj.find('[data-filter-items]'),
      filterClear = obj.find('[data-filter-clear]'),
      itemProp = obj.find('[data-item-prop]');

  function RecalculateFilter() {
    var l = 0, prop = {};
    itemProp.filter(":checked").each(function (index) {
      prop[$(this).attr('name')] = $(this).val();
      if($(this).data('mod-id')){
        prop[$(this).data('mod-id')] = $(this).val();
      }
      l++;
    });

    if (l > 0)
      prop['set_filter'] = 'set_filter';
    else
      prop['del_filter'] = 'del_filter';

    ReloadContent(prop);
  }

  function RecalculateNumber() {
    filterNumber.text(itemProp.filter(":checked").length);
    if (itemProp.filter(":checked").length > 0)
      filterItems.addClass('is-applied');
    else
      filterItems.removeClass('is-applied');
  }

  function ReloadContent(prop) {
    var url = action + '?' + Object.keys(prop).reduce(function (r, x) {
      return r + x + '=' + prop[x] + '&';
    }, '').slice(0, -1);
    history.replaceState(null, null, url);
    waitCheck();
    $.ajax({
      method: "GET",
      url: url,
      success: function (msg) {
        $('[data-content]').html(msg);
        $('.wrapper-back-mobile').addClass('active');
        waitCheck('close');
        initSliderInSection();
        initViewCatalog();

        $('.ajax_load_items').find('.image-container').each(function () {
          let imageContainer = $(this);

          imageContainer.find('img').on("load", function(e) {
            imageContainer.closest('.item-wrapper').find('.skeleton:not(.image-container)').removeClass('skeleton');
            imageContainer.removeClass('skeleton');
          });
        });
        loadNextSlide();
      }
    });
  }

  itemProp.on('change', function (e) {
    RecalculateFilter();
    RecalculateNumber();
  });

  filterClear.on('click', function (e) {
    e.preventDefault();
    $(this).closest('.filter-item').removeClass('choose');
		$(this).closest('.filter-item').find('.filter-content-item.active').removeClass('active');
    itemProp.prop('checked', false);
    var prop = {};
    prop['del_filter'] = 'del_filter';
    ReloadContent(prop);
    RecalculateNumber();
    popupsClose();
    return false;
  });

  RecalculateNumber();

  $('.js-c-filter-display').click(function(){
    var filter = $(this).closest('.catalog-filter');
    if(!filter.hasClass('open'))
    {
      filter.addClass('open');
      popupOpen('filter', true);
    }
    else
      popupsClose();
  });
  return this;
}

$(function() {

	// $('.js-filter-item').hover(
	// 	function () {
	// 		$(this).addClass('active');
	// 		$('header').addClass('js-crack-display');
	// 		$('.catalog-new-filter').addClass('js-crack-display');
	// 		popupOpen('filter-popup');
	// 	},
	// 	function () {
	// 		$(this).removeClass('active');
	// 		setTimeout(function () {
	// 			$('header').addClass('js-crack-display');
	// 			$('.catalog-new-filter').removeClass('js-crack-display');
	// 		}, 300);
	// 		popupsClose();
	// 	}
	// );

	$(document).on({
		mouseenter: function () {
			$(this).addClass('active');
			$('header').addClass('js-crack-display');
			$('.catalog-new-filter').addClass('js-crack-display');
			popupOpen('filter-popup');
		},
		mouseleave: function () {
			$(this).removeClass('active');
			setTimeout(function () {
				$('header').addClass('js-crack-display');
				$('.catalog-new-filter').removeClass('js-crack-display');
			}, 300);
			popupsClose();
		}
	}, '.js-filter-item');

  $('.filter-content-item').each(function (item){
    if($(this).find($('input[type="checkbox"]')).prop("checked")){
      $(this).addClass('active');
      $(this).siblings().removeClass('active');
      //???????????????? ??????????????
      $(this).closest('.filter-item').removeClass('active').addClass('choose');

      //???????????????? ?? ???????????????????? ?????????????????? ??????????
      if($(this).closest('.filter-item').hasClass('filter-item__color')) {
        $(this).closest('.filter-item').find('.filter-choose-color-box').css('background-color', $(this).find('.filter-color-box').css('background-color'));
      }
      $(this).closest('.filter-item').find('.filter-choose-title').text($(this).find('.filter-content-title').text());
    }
  });

	$(document).on('click', '.filter-content-item', function () {
		//?????????????????? ???????????????? ?????????? ?? ???????????? ?? ?????????????? ?????? ?? ????????????
		$(this).addClass('active');
		$(this).siblings().removeClass('active');

		if($(this).hasClass('active')) {
			$(this).find($('input[type="checkbox"]')).prop("checked", true);
		}
		$(this).siblings().find($('input[type="checkbox"]')).prop("checked", false);

		//???????????????? ??????????????
		$(this).closest('.filter-item').removeClass('active').addClass('choose');

		//???????????????? ?? ???????????????????? ?????????????????? ??????????
		if($(this).closest('.filter-item').hasClass('filter-item__color')) {
			$(this).closest('.filter-item').find('.filter-choose-color-box').css('background-color', $(this).find('.filter-color-box').css('background-color'));
		}
		$(this).closest('.filter-item').find('.filter-choose-title').text($(this).find('.filter-content-title').text());

		popupsClose();
	});

	// $('.filter-choose-close').on('click',  function () {
	// 	$(this).closest('.filter-item').removeClass('choose');
	// 	$(this).closest('.filter-item').find('.filter-content-item.active').removeClass('active');
	// 	$(this).closest('.filter-item').find('.filter-content-item').find($('input[type="checkbox"]')).prop("checked", false);
	// });


  initViewCatalog();
});

if (window.frameCacheVars !== undefined)
{
  BX.addCustomEvent("onFrameDataReceivedBefore" , function(json) {
    $('.wrapper-back-mobile').addClass('active');
  });
  BX.addCustomEvent("onFrameDataReceived" , function(json) {
    initRangeSliderBX();
    new SmartFilterSodamoda();
  });
}
else {
  $(function() {
    initRangeSliderBX();
    new SmartFilterSodamoda();
    $('.wrapper-back-mobile').addClass('active');
  });
}