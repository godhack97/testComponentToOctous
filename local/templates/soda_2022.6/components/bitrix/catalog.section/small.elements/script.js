$(function(){
  
  $(document).on('click', '.js-remove-favorite', function(){
    
    setTimeout(function(){
      document.location.reload();
    }, 200);
    
    /*
    if($('body').hasClass('wait'))
      return;

    var deleteID = $(this).closest('tr').attr('data-id');

    waitCheck();

    $.ajax({
      type: 'POST',
      url: '/include/ajax.php',
      data: 'mode=favorite_delete&id='+ deleteID +'&site_id=' + BX.message('SITE_ID'),
      dataType: 'json',
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log(XMLHttpRequest);
        console.log(textStatus);
        console.log(errorThrown);
      },
      success: function (result) {
        console.log(result);
        if(result.status)
        {
          
        }

        waitCheck('close');
      }
    });
     */
    
  });
  
  
  // Поделиться избранным
  $(document).on('click', '.js-favorite-share', function() {
    
    if(isMobile())
      console.log('is mobile');
    else
      console.log('not mobile');
    
    postData = {
      'sessid': BX.bitrix_sessid(),
      'site_id': BX.message('SITE_ID'),
      'event': 'favorite-share',
    };

    console.log(postData);
    
    //    BX.ajax({
    //      url: '/ajax/cart-sharing.php',
    //      method: 'POST',
    //      data: postData,
    //      dataType: 'json',
    //      onsuccess: function (data) {
    //        console.log(data);
    //        if(!data.error) {
    //          console.log(window);
    //          var link = window.location.origin + window.location.pathname + '?b=' + data.id;
    //          
    //          if(isMobile())
    //            cartShare(link);
    //          else
    //            executeCopy(link);
    //        }
    //      },
    //    });
  });
  
});