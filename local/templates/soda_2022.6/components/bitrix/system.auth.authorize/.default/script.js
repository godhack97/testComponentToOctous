
$(function(){
  
  // Ввод телефона
  $('#register_form #user_phone').on('input', function (){ // keyup
    var item = $(this);
    var val = item.val();
    $('#field-register-copy-phone').val( val );
  });
  
  // Авторизация и регистрация
  $('form[name=auth], form[name=register]').submit(function () {
    ajaxQuery($(this));
    return false;
  });
  
  $('#form-register-check-phone').submit(function () {
    ajaxQuery($(this));
    return false;
  });
  
});