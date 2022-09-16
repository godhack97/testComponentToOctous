
function CartCallback(i, e) 
{
  //console.log(e);
  var el = e.vars.cache.nodes[i];
  $('#CITY').val(el.DISPLAY);
}


function SetOrder(add) 
{
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
    
    if($('.buyer input[name=ORDER_PROP_5]').length)
    {
      if(!$('.buyer input[name=ORDER_PROP_5]').val().length)
      {
        popupOpenMessage('Пожалуйста, введите адрес доставки');
        return;
      }
    }
    
    
    if(!$('.buyer input[name=ORDER_PROP_17]').val().length)
    {
      popupOpenMessage('Пожалуйста, введите номер телефона');
      return;
    }
    
    
    if(!$('.buyer input[name=ORDER_PROP_6]').val().length)
    {
      popupOpenMessage('Пожалуйста, введите e-mail');
      return;
    }
    
    
    if (!$('#user-agree').prop("checked"))
    {
      //      $('.error_message').empty();
      //      var err = $('<p><font class="errortext">Необходимо согласие</font></p>');
      //      $('.error_message').append(err);
      
      //      popupMessage.find('.title').html('Ошибка');
      //      popupMessage.find('.info').html('Необходимо согласие на обработку персональных данных');
      //      popupOpen('message');
      popupOpenMessage('Необходимо согласие на обработку персональных данных');

      $('html, body').animate({scrollTop: $('.buyer').offset().top}, 300);
      
      return; 
    }
    
    if($('#ID_DELIVERY_ID_47').is(':checked')) // PickPoint
    {
      if(!$('#pp_id').val().length) // Заставляем выбрать ПВЗ
      {
        popupOpenMessage('Пожалуйста, <a class="black js-link-to-pp-pvz">выберите</a> пункт выдачи заказа');
        return;
      }
    }
        
    // Переключение на выбор оплаты
    if(!$('form[name=ORDER_FORM]').hasClass('display-col-payment') && !$('.order .methods.col-payment .items').hasClass('count-1')) 
    {
      $('form[name=ORDER_FORM]').addClass('display-col-payment');
      $('#basketOrderButton2').val('Оплатить');
      $('body, html').animate({'scrollTop': 0}, 0);
      $('.order .methods.col-payment .items input:checked').trigger('click'); // снимаем фокус с кнопки оплаты      
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
      console.log(msg);
      
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
          window.location = "/order/?ORDER_ID=" + msg.order.ID;
        }
      } 
      else 
      {
        
//        if(objData["DELIVERY_ID"] == 60) 
//        {
//          var splitmsg = msg.split('<h1>');
//          $("#order-form-wrap").html('<h1>' + splitmsg[1]);
//        } 
//        else 
//        {
          $("#order-form-wrap").html(msg);
          var btn = IPOLSDEK_pvz.buttonPVZ;
          btn = btn.replace('#id#', IPOLSDEK_pvz.cityID);
          $('#pvz-insert').html(btn);
        //}
        
      }

      autoCompleteAddress();

    }
  });
}


$(function () {

  $(document).on('click', '.js-button-back-to-order', function(){
    $('form[name=ORDER_FORM]').removeClass('display-col-payment');
    $('#basketOrderButton2').val('Продолжить');
    $('body, html').animate({'scrollTop': 0}, 0);
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
    var deliveryID = $('.field-radio input[name=DELIVERY_ID]:checked').val();
    if(deliveryID == 72 || deliveryID == 71 || deliveryID == 61 || deliveryID == 60 || deliveryID == 66 || deliveryID == 40)
    {
      if($(this).val() != '')
      {
        if($(this).val().indexOf(' ') == -1)
        {
          popupOpenMessage('Пожалуйста, укажите ФИО полностью');
        }
      }
    }
  });

  $('#order-form-wrap').on('change', 'input[name=ORDER_PROP_5]', function(){
    if($('.field-radio input[name=DELIVERY_ID]:checked').val() == 41) // delivery
      SetOrder(); //console.log('log: '+ $('.field-radio input[name=DELIVERY_ID]:checked').val() );
  });
  
  $("#order-form-wrap").on('change', 'input[type=radio]', function () {
    SetOrder();
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
  

  $('#PHONE').inputmasks(maskPhoneOpts).addClass('js-mask-inited');

  autoCompleteAddress();
  
});

$(document).on('change', '[name="PAY_CURRENT_ACCOUNT"]', function () {
  var order_price_with_domestic_account = $('.order-total-price').attr('data-total-price-with-domestic-account');
  var order_price_formated = $('.order-total-price').attr('data-total-price-formated');

  if($(this).prop('checked')){
    $('.alert-domestic-account').show();
    $('.order-total-price').text(order_price_with_domestic_account);
  }
  else{
    $('.alert-domestic-account').hide();
    $('.order-total-price').text(order_price_formated);
  }
});


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
  
  $('[name="city"]').fias('controller').setValueByName(cityName);
  console.log('autoCompleteAddress cityName = '+ cityName);
}


