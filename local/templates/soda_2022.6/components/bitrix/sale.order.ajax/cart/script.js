
function CartCallback(i, e) 
{
  //console.log(e);
  var el = e.vars.cache.nodes[i];
  $('#CITY').val(el.DISPLAY);
}

var d;

function SetOrder(add) 
{
  console.log('SetOrder( '+ add +' )');
  
  if(add === undefined)
  {
    console.log('SetOrder( OGO )');
    //return;
  }
  
  var url           = "/ajax/order.php",
      dataType      = 'text',
      data          = $("#order-form-wrap form").serializeArray(),
      objData       = {},
      popupMessage  = $('.popup-message');
  
  $.each(data, function (index, value) {
    objData[value.name] = value.value;
  });
  
  objData['COL_PAYMENT'] = 'N';
  
  if (add > 0)
  {
    
    if($('#ADDRESS').length)
    {
      if(!$('#ADDRESS').val().length)
      {
        popupOpenMessage(BX.message('SALE_ORDER_JS_ENTER_SHIPPING_ADDRESS'));
        return;
      }
    }
    
    
    if(!$('#PHONE').val().length)
    {
      popupOpenMessage(BX.message('SALE_ORDER_JS_ENTER_PHONE'));
      return;
    }
      
    
    if(!$('#EMAIL').val().length)
    {
      popupOpenMessage(BX.message('SALE_ORDER_JS_ENTER_EMAIL'));
      return;
    }
    
    
    if (!$('#user-agree').prop("checked"))
    {
      popupOpenMessage(BX.message('SALE_ORDER_JS_CONSENT_PERSONAL_DATA'));
      $('html, body').animate({scrollTop: $('.buyer').offset().top}, 300);
      return; 
    }
    
    if (!$('#user-return').prop("checked"))
    {
      popupOpenMessage(BX.message('SALE_ORDER_JS_CONSENT_RULES_RETURN'));
      //$('html, body').animate({scrollTop: $('.buyer').offset().top}, 300);
      return; 
    }
    
    if($('#ID_DELIVERY_ID_47').is(':checked')) // PickPoint
    {
      if(!$('#pp_id').val().length) // Заставляем выбрать ПВЗ
      {
        popupOpenMessage(BX.message('SALE_ORDER_JS_SELECT_PICKUP_POINT'));
        return;
      }
    }
        
    // Переключение на выбор оплаты
    if(!$('form[name=ORDER_FORM]').hasClass('display-col-payment') && !$('.order .methods.col-payment .items').hasClass('count-1')) 
    {
      $('form[name=ORDER_FORM]').addClass('display-col-payment');
      $('body, html').animate({'scrollTop': 0}, 0);
      
      $('.order .methods.col-payment .items input:checked').trigger('click'); // снимаем фокус с кнопки оплаты      
      $('#basketOrderButton2').val( BX.message('SALE_ORDER_JS_PAY') );
      
      // Если курьерская доставка
      if($('.order .methods.col-payment .items input:checked').val() == 10) {
        $('#basketOrderButton2').val( BX.message('SALE_ORDER_JS_SEND') );
      }
      
      return false;
    }
    
    objData["soa-action"] = "saveOrderAjax";
    dataType = 'json';
  }
  else 
  { 
    if($('form[name=ORDER_FORM]').hasClass('display-col-payment'))
      objData['COL_PAYMENT'] = 'Y';
    else
      objData['COL_PAYMENT'] = 'N';

    objData["soa-action"] = "processOrder";
  }
    
  waitCheck();
  
  $('#SDEK_pvz').remove();
  
  $.ajax({
    type: "POST",
    url: url,
    dataType: dataType,
    data: objData,
    error: function (jqXHR, textStatus, errorThrown) {
      waitCheck('close');
    },
    success: function (msg) {

      console.log('FLAG '+ dataType);
      
      waitCheck('close');
       
      if (add > 0) 
      {
        if (typeof (msg.order.ERROR) != 'undefined')
        {
          var errorStr = '';
          $('.error_message').empty();
          
          $.each(msg.order.ERROR, function (index, value) {
            $.each(value, function (errindex, errvalue) {
              errorStr += '<p>'+ errvalue +'</p>';
            });
          });
          
          //if ($('.error_message').length > 0)
          //  $('html, body').animate({scrollTop: $('.error_message').offset().top - 500}, 500);
          
          popupOpenMessage(errorStr);
          
          
          $('html, body').animate({scrollTop: $('.buyer').offset().top}, 300);
        }
        if (typeof (msg.order.ID) != 'undefined')
        {
          window.location = msg.order.REDIRECT_URL;
          // window.location = "/order/?ORDER_ID=" + msg.order.ID;
        }
      } 
      else 
      {

        var newmsg=$("<div id='msg_wrapper'>"+msg+"</div>")
        $("#DPD_pvz_popup",newmsg).remove();
        $("#order-form-wrap").empty().append(newmsg);

        if (window.BX_SetPlacemarks_dpd_map)
        {
          if(typeof window.GLOBAL_arMapObjects['dpd_map'] !== "undefined")
          {
            window.BX_SetPlacemarks_dpd_map(window.GLOBAL_arMapObjects['dpd_map']);
            //BX("DPD_pvz").DpdPickupMap.setComponentParams(d);
          }
        }

        var btn = IPOLSDEK_pvz.buttonPVZ;
        btn = btn.replace('#id#', IPOLSDEK_pvz.cityID);
        $('#pvz-insert').html(btn);

      }

      $('#PHONE').trigger('blur');

      if($('#js-order-check-bonus').find('input').is(':checked')){
        $('#js-order-check-bonus').find('input').trigger('click').trigger('click');
      }
      autoCompleteAddress();
    },
    error: function(response){
      waitCheck('close');
      popupOpenMessage(BX.message(response.responseText));
    }
  });
}

function SetUser(ui) 
{
  if(ui.email)
    $("#EMAIL").val(ui.email);
  else
    $("#EMAIL").val('');
  
  if(ui.value)
    $("#CONTACT_PERSON").val(ui.value);
  else
    $("#CONTACT_PERSON").val('');
  
  if(ui.phone)
    $("#PHONE").val(ui.phone);
  else
    $("#PHONE").val('');

  if(ui.id)
    $("#user_selection_id").val(ui.id);
  else
    $("#user_selection_id").val(0);

  if(ui.label)
    $("#USER_SELECTION").val(ui.label);
  else
    $("#USER_SELECTION").val();
}


$(function(){

  $("#add_user_form").validate({
    rules: {
      name: "required",
      phone: "required",
      email: {
        required: true,
        email: true
      }
    },
    messages: {
      name: BX.message('SALE_ORDER_JS_INDICATE_NAME'),
      phone: BX.message('SALE_ORDER_JS_INDICATE_PHONE'),
      email: {
        required: BX.message('SALE_ORDER_JS_INDICATE_EMAIL'),
        email: BX.message('SALE_ORDER_JS_INDICATE_VALID_EMAIL')
      }
    },
    errorPlacement: function(error, element) {
      error.appendTo(element.parent(".field"));
    },
    submitHandler: function() {
      waitCheck();
      $.ajax({
        type: "POST",
        url: '/ajax/add_user.php',
        dataType: 'json',
        data: $("#add_user_form").serialize(),
        error: function (jqXHR, textStatus, errorThrown) {
          waitCheck('close');
        },
        success: function (msg) {
          waitCheck('close');
          SetUser(msg);
          //$('#PHONE').trigger('blur');
          popupsClose();
          //SetOrder();
        }
      });
    }
  });


  d = $("#DPD_pvz").attr('data-component-params');
  
  $(document).on('click', '.add-user-selection', function(){
    var popupMessage  = $('.popup-message');
    popupMessage.find('.title').html(BX.message('SALE_ORDER_JS_ADD_USER'));
    //popupMessage.find('.info').html('Пожалуйста, введите номер телефона');
    popupOpen('message');
  });
  
  $(document).on('click', '.js-button-back-to-order', function(){
    $('form[name=ORDER_FORM]').removeClass('display-col-payment');
    $('body, html').animate({'scrollTop': 0}, 0);
    var inputSend = $(this).siblings('input[name=BasketOrder]');
    inputSend.val( inputSend.attr('data-name-continue') );
  });

  $(document).on('click', '.pointitem .select', function(){
    console.log('CLICK .pointitem .select');
  });
  
  $(document).on('click', '.bx-ui-sls-fake', function() {
    $(this).select();
  });
  
  $(document).on('click', '.js-link-to-pp-pvz', function(){
    $('.js-pp-pvz').trigger('click');
    popupsClose();
  });
  
  $('.field-code-location').on('change', '.bx-ui-sls-fake', function(){
    console.log('location change: '+ $(this).val())
  });
  
  $("#order-form-wrap").on('change', '#CONTACT_PERSON', function () {
    var delID = $('.field-radio input[name=DELIVERY_ID]:checked').val();
    if(delID == 35 || delID == 72 || delID == 2)
    {
      if($(this).val() != '')
      {
        if($(this).val().indexOf(' ') == -1 ) 
        {
          $('.popups.popup-message .title').hide();
          $('.popups.popup-message .info').css('padding-bottom', '6px');
          popupOpen('message', false, BX.message('SALE_ORDER_JS_INDICATE_VALID_NAME'));
        }
      }
    }
  });

  $('#order-form-wrap').on('change', 'input[name=ORDER_PROP_5]', function(){
    if($('.field-radio input[name=DELIVERY_ID]:checked').val() == 41) // delivery
      SetOrder(); //console.log('log: '+ $('.field-radio input[name=DELIVERY_ID]:checked').val() );
  });
  
  $("#order-form-wrap").on('change', 'input[type=radio]', function(){
    SetOrder();
  });

  //подменяем выбор типа плательщика через чекбокс
  $("#order-form-wrap").on('change', '#PERSON_TYPE_COPY', function(){
    if ( $(this).is(':checked') ) {
      $('#user-2').click();
    }
    else {
      $('#user-1').click();
    }
  });
  
  $("#order-form-wrap").on('change', 'input[class*=bx-ui-sls-fake]', function () {
    setTimeout(function(){
      SetOrder();
    }, 400);
  });

  $('#order-form-wrap').on('submit', 'form#ORDER_FORM', function (e) {
    e.preventDefault();
    SetOrder(true);
    return false;
  });
  
  $("#order-form-wrap").on('click', '.js-calendar-field', function(){
    if($(this).val() == '')
      $(this).closest('.field').find('img.calendar-icon').trigger('click');
  });
  
  $("#order-form-wrap").on('change', '#js-set-email', function(){
    if($(this).is(':checked'))
      $('.field-code-email').find('input').attr('aria-required', 'true').attr('required', 'required');
    else
      $('.field-code-email').find('input').removeAttr('aria-required').removeAttr('required');
  });
  
  $("#order-form-wrap").on('change', '#js-set-bonus', function(){
    var item = $(this);
    if(!item.data('user-confimed'))
    {
      if($(this).is(':checked'))
      {
        if($('.field-code-phone').find('input').val() != '')
        {
          $(this).prop('checked', false)
          if($('#code_confirmed').length > 0){
            ajaxQuery($('#ORDER_FORM'));
          }
        }
        else
        {
          popupOpenMessage('Пожалуйства введите номер телефона');
          $(this).prop('checked', false);
          if($('#js-order-check-bonus').find('input').is(':checked')){
            $('#js-order-check-bonus').find('input').trigger('click');
          }
          $('#js-order-check-bonus').hide();
        }
      }
    }
    else
    {
      if($(this).is(':checked'))
      {
        $('#js-order-check-bonus').show();
      }
      else
      {
        if($('#js-order-check-bonus').find('input').is(':checked')){
          $('#js-order-check-bonus').find('input').trigger('click');
        }
        $('#js-order-check-bonus').hide();
      }
    }
  });
  
  $("#order-form-wrap").on('change', '.buyer input[type=text]', function(){
    saveCookieBuyerFields($(this));
  });
  
  $("#order-form-wrap").on('keyup', '#PHONE', function(){
    saveCookieBuyerFields($(this));
  });
  
  //  $("#order-form-wrap").on('change', 'input[name=DELIVERY_ID]', function(){
  //    if($(this).val() == 3) // Самовывоз
  //      $('.buyer').addClass('mode-pickup');
  //    else
  //      $('.buyer').removeClass('mode-pickup');
  //  });
  
  
  //BX.calendar({node: this, field: this, bTime: false});
  
  // Устанавливаем текущую дату
  var today = new Date();
  var Data = new Date(today.getTime() + (24 * 60 * 60 * 1000));
  $('.js-calendar-field').val( Data.getDate() +'.'+ ('0' + (Data.getMonth()+1)).slice(-2) +'.'+ Data.getFullYear() +' 12:00:00' );
  

  $('#form-field-phone').inputmasks(maskPhoneOpts).addClass('js-mask-inited');
  
  $('#form-field-phone').on('blur', function (){
    if($(this).val()){
      waitCheck();
      $.ajax({
        type: "POST",
        url: '/ajax/add_user.php',
        dataType: 'json',
        data: 'action=search_user_by_phone&phone='+$(".js-check-exist-phone").val(),
        error: function (jqXHR, textStatus, errorThrown) {
          waitCheck('close');
        },
        success: function (msg) {
          waitCheck('close');

          if(msg.email)
            $('#add_user_form').find('#form-field-email').val(msg.email);
          else
            $('#add_user_form').find('#form-field-email').val('');

          if(msg.value)
            $('#add_user_form').find('#form-field-name').val(msg.value);
          else
            $('#add_user_form').find('#form-field-name').val('');

          //popupsClose();
        }
      });
    }
  });
  
  $('.js-open-userinfo').click(function() {
    popupOpen('userinfo');
    $('.popup-userinfo form input[name=user]').val($(this).data('user'));
    $('.popup-userinfo form input[name=order]').val($(this).data('order'));
  });

  $('form[name=userinfo]').submit(function () {
    ajaxQuery($(this));
    return false;
  });

  autoCompleteAddress();

  window.basket_items = {
    available: [],
    not_available: [],
    first_check: true,
  }
  
  if($('body').hasClass('is-admin')){
    let timerCheckId = setTimeout(function tickCheck() {
      var globalMessage = '';
      //waitCheck();
      $.ajax({
        type: "POST",
        url: '/include/ajax.php',
        dataType: 'json',
        data: 'mode=user-check-basket',
        error: function (jqXHR, textStatus, errorThrown) {
          //waitCheck('close');
        },
        success: function (msg) {
          let changed = false;
          let itemsList = msg.messages;
          let kAval, kNotAval;
          var checkAvailable, checkNotAvailable;
          if(window.basket_items.first_check){
            if(itemsList.available){
              for(kAval in itemsList.available){
                window.basket_items.available.push(itemsList.available[kAval].product);
              }
            }
            if(itemsList.not_available){
              for(kNotAval in itemsList.not_available){
                window.basket_items.not_available.push(itemsList.not_available[kNotAval].product);
              }
            }
            window.basket_items.first_check = false;
          }
          else {
            if(itemsList.available){
              for(let kAval in itemsList.available){
                checkAvailable = window.basket_items.available.filter(function(item){
                  return item === itemsList.available[kAval].product;
                });
                if(checkAvailable.length < 1){
                  changed = true;
                }
              }
            }
            if(itemsList.not_available){
              for(let kAval in itemsList.not_available){
                checkNotAvailable = window.basket_items.not_available.filter(function(item){
                  return item === itemsList.not_available[kAval].product;
                });
                if(checkNotAvailable.length < 1){
                  changed = true;
                }
              }
            }
          }
          if(changed){
            let productName = '';
            let color = '';
            let size = '';
            let messageNotAvailable = BX.message('SALE_ORDER_NOT_AVAILABLE_POPUP');
            let messageAvailable = BX.message('SALE_ORDER_AVAILABLE_POPUP');
            if(itemsList.not_available){
              globalMessage += '<div class="products"><div class="caption">'+ messageNotAvailable +'</div>';
              itemsList.not_available.forEach(function(item){
                productName = item.name;
                size = ', '+item.props.SIZES.VALUE;
                color = ', '+item.props.COLOR.VALUE;

                globalMessage += '<div class="product-item">'+ productName+color+size + '</div>';
              });
              globalMessage += '</div>';
            }
            if(itemsList.available){
              globalMessage += '<div class="products"><div class="caption">'+ messageAvailable +'</div>';
              itemsList.available.forEach(function(item){
                productName = item.name;
                size = ', '+item.props.SIZES.VALUE;
                color = ', '+item.props.COLOR.VALUE;

                globalMessage += '<div class="product-item">'+ productName+color+size + '</div>';
              });
              globalMessage += '</div>';
            }
            if(!msg.status){
              $('.popup-order-change').find('.ibutton.js-refresh-order').hide();
            }
            else{
              $('.popup-order-change').find('.ibutton.js-refresh-order').show();
            }
            popupOpen('order-change', false, globalMessage);

            window.basket_items.available = [];
            window.basket_items.not_available = [];

            if(itemsList.available){
              for(kAval in itemsList.available){
                window.basket_items.available.push(itemsList.available[kAval].product);
              }
            }
            if(itemsList.not_available){
              for(kNotAval in itemsList.not_available){
                window.basket_items.not_available.push(itemsList.not_available[kNotAval].product);
              }
            }
          }
        }
      });
      timerCheckId = setTimeout(tickCheck, 10000); // (*)
    }, 1000);

    $('.js-refresh-order').on('click', function(){
      popupsClose();
      SetOrder();
    });
  }
});


function saveCookieBuyerFields(field)
{
  console.log( field.attr('name') +' = '+ field.val() );
  BX.setCookie('BITRIX_SM_'+ field.attr('name'), field.val(), {expires: 86400 * 350, path: '/', secure: true});
}

function autoCompleteAddress(){

  $('[name="city"]').fias({
    
    type: $.fias.type.city,
    change: function (obj) {

      console.log('autoCompleteAddress');
      //console.log(obj);
      //alert(obj.id);

      parentId = parseInt(obj.id);

      $('[name="ORDER_PROP_5"]').fias({
        oneString: true,
        parentType: $.fias.type.city,
        parentId: parentId,

        change: function (obj) {
          $('[name="ORDER_PROP_4"]').val(obj.zip);
        },

        labelFormat:function (obj, query) {
          if(obj.contentType == 'building'){
            var label = obj.parents[2].type.toLowerCase() + " " + obj.parents[2].name + ", " + obj.type +  " " + obj.name;
          }
          else {
            var label = obj.type.toLowerCase() +  " " + obj.name;
          }
          return label;
        },

        valueFormat:function (obj, query){
          if(obj.contentType == 'building'){
            var label = obj.parents[2].type.toLowerCase() + " " + obj.parents[2].name + ", " + obj.type +  " " + obj.name;

          }
          else{
            var label = obj.type.toLowerCase() +  " " + obj.name;
          }
          return label;
        },

      });
    },
  });

  //var cityName = $('.js-city-current').text();
  var cityName = $('.field-code-location .bx-ui-sls-fake').val();

  if($('[name="city"]').fias('controller')!==null)
    $('[name="city"]').fias('controller').setValueByName(cityName);
  
  console.log('autoCompleteAddress cityName = '+ cityName);
}




