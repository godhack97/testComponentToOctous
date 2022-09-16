$(document).ready(function(){

  $(document).on('click', '.js-more-products', function(){
    
    var targetContainer = $('#product_list'),
        url = $('.js-more-products').attr('data-url');

    if (url !== undefined) {
      waitCheck();
      $.ajax({
        type: 'GET',
        url: url,
        dataType: 'html',
        success: function(data){
          
            //  Удаляем старую навигацию
            $('.js-more-products').remove();
            var elements = $(data).find('.item-row'),
                pagination = $(data).find('.js-more-products');

            targetContainer.append(elements);
            targetContainer.parent().parent().find('.navigation-action').append(pagination);
            waitCheck('close');
        }
      });
    }
    
  });
  
});