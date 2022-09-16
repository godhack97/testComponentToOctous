$(document).ready(function(){
  
  $(document).on('click', '.load_more', function(){

    var targetContainer = $('.items-list .row'),
        url =  $('.load_more').attr('data-url');

    if (url !== undefined) {
      $.ajax({
        type: 'GET',
        url: url,
        dataType: 'html',
        success: function(data){
          //  Удаляем старую навигацию
          $('.load_more').remove();
          var elements = $(data).find('.items-col'),
              pagination = $(data).find('.load_more');

          targetContainer.append(elements);
          targetContainer.parent().find('.navigation-action').append(pagination);
        }
      });
    }
    
  });
  
});