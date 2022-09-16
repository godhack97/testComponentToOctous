BX.ready(function() {

  $(document).on('click', '.js-open-learn-receipt', function(){
    $('.popup-learn-receipt input[name=productId]').val(this.dataset.productArticle);
    $('.popup-learn-receipt .article-receipt').html(this.dataset.productArticle);
    popupOpen('learn-receipt');
    $('.popup-learn-receipt input[name=phone]').inputmasks(maskPhoneOpts);
  });

  // Подписаться неавторизованному
  $(".subscribe-not-auth").click(function(){
    popupOpen('product-subscribe', false, BX('content-popup-product-subscribe-'+this.dataset.productId));
  });

  // Отписаться неавторизованному
  $(".unsubscribe-not-auth").click(function(){
    var button = this;
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'unsubscribe-not-auth',
      'product_id': this.dataset.productId
    };
    waitCheck();
    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(".unsubscribe-not-auth[data-product-id=\""+ button.dataset.productId +"\"]").hide();
        $(".subscribe-not-auth[data-product-id=\""+ button.dataset.productId +"\"]").show();
        timestamp = new Date().getTime();
        popupOpen('product-subscribe', false, '<p>' + result.message + '</p>');
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
        waitCheck('close');
      }
    });
  });

  // Подписаться авторизованному
  $(".subscribe-user").click(function(){
    var button = this;
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'subscribe-user',
      'product_id': this.dataset.productId
    };
    waitCheck();
    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(".unsubscribe-user[data-product-id=\""+ button.dataset.productId +"\"]").show();
        $(".subscribe-user[data-product-id=\""+ button.dataset.productId +"\"]").hide();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
        waitCheck('close');
      }
    });
  });


  // Отписаться авторизованному
  $(".unsubscribe-user").click(function(){
    var button = this;
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'unsubscribe-user',
      'product_id': this.dataset.productId
    };
    
    waitCheck();

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(".unsubscribe-user[data-product-id=\""+ button.dataset.productId +"\"]").hide();
        $(".subscribe-user[data-product-id=\""+ button.dataset.productId +"\"]").show();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
        waitCheck('close');
      }
    });
  });


  // Форма для указания почты
  $("form[name=product-subscribe]").submit(function(event){
    event.preventDefault();
    var button = this;
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'subscribe',
      'props': $(this).closest('form').serialize()
    };
    var dataForm = $(this).closest('form').serializeArray();
    var dataObj = {};
    $(dataForm).each(function(index, obj){
      dataObj[obj.name] = obj.value;
    });
    waitCheck();
    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        popupsClose();
        popupOpenMessage(result.message);

        $(".unsubscribe-not-auth[data-product-id=\""+ dataObj.productId +"\"]").show();
        $(".subscribe-not-auth[data-product-id=\""+ dataObj.productId +"\"]").hide();
        //timestamp = new Date().getTime();
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
        waitCheck('close');
      }
    });
  });

  $("form[name=learn-receipt]").submit(function(event){
    event.preventDefault();
    var postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'subscribe-phone',
      'props': $(this).closest('form').serialize()
    };

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        popupsClose();
      }
    });
  });
});


function getPopup(popupId, popupContent)
{
  var popup = BX.PopupWindowManager.create(popupId, null, {
    content: popupContent,
    darkMode: false,
    autoHide: true,
    closeIcon: true,
    overlay : true,
    onPopupClose: function () {
      this.destroy();
    },
  });

  popup.show();
}