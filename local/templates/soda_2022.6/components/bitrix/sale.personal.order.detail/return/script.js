
$(function(){
  
  //Авторизация и регистрация
  $('form[name=return]').submit(function () {
    ajaxQuery($(this));
    return false;
  });
  
  
  $('form[name=returnNotAuth]').submit(function () {
    ajaxQuery($(this));
    return false;
  });
  

  $('input[name=RETURN_TYPE]').change(function(){
    var fieldsTC = $('.fields-return-tc');
    if($(this).data('type') == 'tc' && $(this).is(':checked'))
    {
      fieldsTC.removeClass('display-none');
      fieldsTC.find('input').attr('aria-required', 'true').attr('required', 'required');
    }
    else
    {
      fieldsTC.addClass('display-none');
      fieldsTC.find('input').removeAttr('aria-required').removeAttr('required');
    }
  });
  
  
  $(".js-add-product").click(function(){
    var tr = $("#basket-item-table tr:last").clone();
    tr.find('input').val('');
    $("#basket-item-table tr:last").after(tr);
  });


  $("#basket-item-table").on("click", ".control .delete", function () {
    $(this).closest('tr').remove();
    calculateReturnPrice();
  });


  $("input[name=dateSale], input[name=TC_DATE]").click(function(){
    BX.calendar({node: this, field: this, bTime: false});
  });


  $("input[name=orderId]").focus(function(){
    var phoneField = $("input[name=USER_PHONE]");
    if(phoneField.val() === '') {
      popupOpenMessage('Пожалуйста, введите номер телефона, который указали в заказе');
      $(this).val('');
      phoneField.focus();
    }
  });


  $("input[name=orderId]").keyup(function(){
    $(this).val( $(this).val().replace(/[^\d]/g, '') );
  });


  $("input[name=orderId]").change(function(){
    //var self = this;
    var orderId = $(this).val();
    var phone = $("input[name=USER_PHONE]").val();
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'orderId': orderId,
      'phone': phone,
      'event': 'isOrder',
    };
    
    if(phone === '') {
      popupOpenMessage('Пожалуйста, введите номер телефона, который указали в заказе');
      $(this).val('');
      return false;
    }

    BX.ajax({
      url: '/ajax/return.php',
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (data) {
        
        $('#basket-item-table input').val('');
        $('#basket-item-table tr:gt(0)').remove();
        $('#selectProduct option:gt(0)').remove();
        $('.return-total-price').removeClass('active');
        
        if(data.error == true) 
        {
          popupOpenMessage(data.message);
          $('#selectProduct').prop('disabled', true);
          $("input[name=orderId]").val('');
          $("input[name=totalPrice]").val('');
        } 
        else
        {
          $("input[name=totalPrice]").val(data.totalPrice);
          $('#selectProduct').prop('disabled', false);

          var res = data.data;

          $.each(res, function(key, value) {
            $('#selectProduct').append('<option value="' + value.id + '" data-name="' + value.text + '" data-size="' + value.size + '" data-articul="' + value.articul + '" data-quantity="' + value.quantity + '"  data-price="' + value.price + '"  data-pricenum="' + value.price_number + '">' + value.text + '</option>');
          });

        }
      },
    });
  });
  

  $(document).on("click", "td.desc, td.desc *", function () {
    if($(this).find('select').prop("disabled")) {
      popupOpenMessage('Пожалуйста, введите номер заказа');
      $("input[name=orderId]").focus();
    }
  });


  $(document).on("change", "#selectProduct", function () {
    var select = $(this);
    var data = select.find("option[value='" + this.value + "']").data()
    var tr = select.closest('tr');
    
    tr.find("input[name='quantity[]']").val(data.quantity);
    tr.find("input[name='nameProduct[]']").val(data.name);
    tr.find("input[name='size[]']").val(data.size);
    tr.find("input[name='article[]']").val(data.articul);
    tr.find("input[name='price[]']").val(data.price);
    tr.find("input[name='pricenum[]']").val(data.pricenum);

    $("#selectProduct option").prop('disabled', false);
    $('select').each(function(i, elem) {
      if(elem.id == 'selectProduct') {
        $("#selectProduct option[value='" + elem.value + "']").prop('disabled', true);
      }
    });

    calculateReturnPrice();
  });
  
});

function calculateReturnPrice()
{
  var totalReturnPrice = 0;
  $('#basket-item-table tr').each(function(i, elem) {
    totalReturnPrice += Number($(elem).find("input[name='pricenum[]']").val());
  });

  $(".return-total-price").addClass('active');
  $(".return-total-price span").html(String(totalReturnPrice).replace(/(.)(?=(\d{3})+$)/g,'$1 ') + ' ' + $("form[name=returnNotAuth] input[name=currency]").val());
}
