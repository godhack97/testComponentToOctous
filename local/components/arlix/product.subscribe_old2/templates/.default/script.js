BX.ready(function() {


  $(document).on('click', '.js-open-learn-receipt', function(){
    $('.popup-learn-receipt input[name=productId]').val(this.dataset.productArticle);
    $('.popup-learn-receipt .article-receipt').html(this.dataset.productArticle);
    popupOpen('learn-receipt');
    $('.popup-learn-receipt input[name=phone]').inputmasks(maskPhoneOpts);
  });


  // Подписаться неавторизованному
  $(".subscribe-not-auth").click(function(){
    popupOpen('product-subscribe', false, BX('content-popup-product-subscribe'));
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(".unsubscribe-not-auth").hide();
        $(".subscribe-not-auth").show();
        timestamp = new Date().getTime();
        popupOpen('product-subscribe', false, '<p>' + result.message + '</p>');
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(button).hide();
        $(".unsubscribe-user").show();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(button).hide();
        $(".subscribe-user").show();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        popupsClose();
        popupOpenMessage(result.message);
        $(".unsubscribe-not-auth").show();
        $(".subscribe-not-auth").hide();
        //timestamp = new Date().getTime();
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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
BX.ready(function() {
  
  
  $(document).on('click', '.js-open-learn-receipt', function(){
    $('.popup-learn-receipt input[name=productId]').val(this.dataset.productArticle);
    $('.popup-learn-receipt .article-receipt').html(this.dataset.productArticle);
    popupOpen('learn-receipt');
    $('.popup-learn-receipt input[name=phone]').inputmasks(maskPhoneOpts);
  });
  

  // Подписаться неавторизованному
  $(".subscribe-not-auth").click(function(){
    popupOpen('product-subscribe', false, BX('content-popup-product-subscribe'));
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(".unsubscribe-not-auth").hide();
        $(".subscribe-not-auth").show();
        timestamp = new Date().getTime();
        popupOpen('product-subscribe', false, '<p>' + result.message + '</p>');
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(button).hide();
        $(".unsubscribe-user").show();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        $(button).hide();
        $(".subscribe-user").show();
        timestamp = new Date().getTime();
        popupOpenMessage(result.message);
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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

    BX.ajax({
      url: BX.message('APS_AJAX_URL'),
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function (result) {
        popupsClose();
        popupOpenMessage(result.message);
        $(".unsubscribe-not-auth").show();
        $(".subscribe-not-auth").hide();
        //timestamp = new Date().getTime();
        //getPopup("subscribe-popup-result" + timestamp, "<div class='subscribe-popup-result'>" + result.message + "</div>");
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
