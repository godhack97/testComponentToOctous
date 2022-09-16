$(function(){
  
  $("button[name=subscribe_form]").on("click", function (event) {
    
    event.preventDefault(); // отменяем отправку формы
    var form = $(this).closest('form');
    waitCheck();
    
    BX.ajax({
      url: '/include/ajax_subscribe.php',
      method: 'POST',
      data: form.serialize(),
      dataType: 'json',
      onsuccess: function (data) {
        
        var message;
        if(data.error) {
          message = form.find(".error");
        } else {
          message = form.find(".success");
          $(form)[0].reset();
        }

        message.text(data.message).show();

        setTimeout(function() {
          message.hide();
        }, 3000);
        
        waitCheck('close');
        
      },
    });
  });
});