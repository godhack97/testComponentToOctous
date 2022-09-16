var $ = jQuery.noConflict();

function sendDataRequest(params, mode){
  var phoneField = $('#PHONE');

  switch (mode){
    case 'check-confirm-phone':
      waitCheck();
      $.ajax({
        type: 'POST',
        url: '/include/ajax.php',
        data: params, //'&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&auth='+$('#PHONE').data('auth')+'&phone='+phoneVal,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
          console.log(textStatus);
          console.log(errorThrown);

          return false;
        },
        success: function (result) {
          if(result.status)
          {
            if(phoneField.data('auth')){
              changeViewBonus('show');
            }
            else{
              changeViewBonus('hide');
            }
          }
          else
          {
            changeViewBonus('hide');
          }

          waitCheck('close');
        }
      });
      break;
    case 'check-first-confirm-phone':
      $.ajax({
        type: 'POST',
        url: '/include/ajax.php',
        data: params, //'&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&auth='+$('#PHONE').data('auth')+'&phone='+phoneVal,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
          console.log(textStatus);
          console.log(errorThrown);

          return false;
        },
        success: function (result) {
          if(result.status)
          {
            if(phoneField.data('auth')){
              changeViewBonus('show');
            }
            else{
              changeViewBonus('hide');
            }
          }
          else
          {
            changeViewBonus('hide');
          }
        }
      });
      break;
    case 'check-phone':
      waitCheck();
      $.ajax({
        type: 'POST',
        url: '/include/ajax.php',
        data: params, //'&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&auth='+$('#PHONE').data('auth')+'&phone='+phoneVal,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
          console.log(textStatus);
          console.log(errorThrown);

          return false;
        },
        success: function (result) {
          if(result.status){
            $('#popup-phone-field').val(phoneField.val())
            console.log('TEST');
            popupOpen('phone-check');
          }

          waitCheck('close');
        }
      });
      break;
  }
}
function sendFormRequest(form) {
  if($('body').hasClass('wait'))
    return;

  waitCheck();

  $.ajax({
    type: 'POST',
    url: '/include/ajax.php',
    data: form.serialize() + '&site_id=' + BX.message('SITE_ID'),
    dataType: 'json',
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      console.log(textStatus);
      console.log(errorThrown);
    },
    success: function (result) {
      waitCheck('close');

      if(result.status)
      {
        if(result.mode == 'order_verify_phone')
        {
          waitCheck();
          location.reload();
        }

        if(result.mode == 'order-check-phone')
        {
          popupOpen('phone-check');
          $('#popup-phone-field').val(phoneField.val())
        }
      }
      else
      {
        if(result.mode == 'order-check-phone')
        {
          popupOpenMessage(result.message);
          $('#phone_field').val(phoneField.val())
        }
        if(result.mode == 'order_verify_phone')
        {
          $('#code_confirmed').val('');
          $(document).find('.popup-phone-check-result-message').text(result.message).addClass('active');
          $('#phone_field').val(phoneField.val())
        }

        formResultDisplay(form, result.message);
      }
    }
  });
}
function changeViewBonus(state) {
  var phoneConfirmBtn = $('#js-button-confirm-phone'),
    checkBonusProgram = $('#js-set-bonus'),
    checkBonusPay = $('#js-order-check-bonus');

  switch (state){
    case 'show':
      checkBonusProgram.prop('checked', true);
      phoneConfirmBtn.hide();
      checkBonusPay.show();
      break;
    case 'hide':
      checkBonusProgram.prop('checked', false);

      if(checkBonusPay.find('input').is(':checked')){
        checkBonusPay.find('input').trigger('click');
      }

      checkBonusPay.hide();
      phoneConfirmBtn.show();
      break;
    case 'all_hide':
      checkBonusPay.hide();
      phoneConfirmBtn.hide();
      break;
  }
}

$(function () {
  var phoneField = $('#PHONE'),
      codeConfirmed = $('#code_confirmed'),
      phoneConfirmBtn = $('#js-button-confirm-phone');

  phoneField.inputmasks(maskPhoneOpts).addClass('js-mask-inited');

  if(phoneField.val() != ''){
    var phoneVal = phoneField.val();
    var dataRequest = '&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&auth='+phoneField.data('auth')+'&phone='+phoneVal;

    sendDataRequest(dataRequest, 'check-first-confirm-phone');
  }
  else{
    changeViewBonus('all_hide');
  }

  $(document).on('blur', '#PHONE', function(){
    var phoneVal = $(this).val();
    if(phoneVal.length > 0){
      var dataRequest = '&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&auth='+phoneField.data('auth')+'&phone='+phoneVal;

      sendDataRequest(dataRequest, 'check-confirm-phone');
    }
    else
    {
      changeViewBonus('all_hide');
    }
  });
  $(document).on('input', '#code_confirmed', function(){
    if($(document).find('.result-message').hasClass('active')){
      $(document).find('.result-message').removeClass('active');
    }
  });
  $(document).on('click', '#js-button-confirm-phone', function () {
    if($('#PHONE').val() != '')
    {
      if($('body').hasClass('wait'))
        return;

      var dataRequest = 'site_id=' + BX.message('SITE_ID') +'&mode=order-check-phone&phone='+$('#PHONE').val();
      sendDataRequest(dataRequest, 'check-phone');
    }
  });

  $('#send_verify_code').on('click', function (event){
    event.preventDefault();
    sendFormRequest($('#verify_code_form'));
  });
  $('form[name=verify_phone]').submit(function () {
    sendFormRequest($(this));
    return false;
  });

  $(document).on('change', '[name="PAY_CURRENT_ACCOUNT"]', function () {
    var bonus_payment = $('.order-total-price').attr('data-bonus-payment');
    var bonus_amount = $('.order-total-price').attr('data-bonus-amount');
    var order_price_formated = $('.order-total-price').attr('data-total-price-formated');
    var order_big_bonus = $('.order-total-price').attr('data-big-bonus');

    if($(this).prop('checked')){
      $('.alert-domestic-account').show();
      $('#active_pay_bonus').show();
      $('#deactive_pay_bonus').hide();
      $('.order-total-price').text(bonus_payment);
    }
    else{
      $('.alert-domestic-account').hide();
      $('#active_pay_bonus').hide();
      $('#deactive_pay_bonus').show();
      $('.order-total-price').text(order_price_formated);
    }
  });
});