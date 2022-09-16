
$(function(){
  
  
  $('.js-checked-item').click(function(){
    if($(this).is(':checked'))
      $(this).closest('.field-checkbox').addClass('active');
    else
      $(this).closest('.field-checkbox').removeClass('active');
    
    displayReturnButton();
  });
  
  
  $('.js-return-send').click(function(){
    
//    if($('body').hasClass('is-admin'))
//    {
      location.href = '/return/send/?order='+ $(this).data('order') +'&items='+ $(this).data('items');
//    }
//    else
//      location.href = '/return/';
  });
  
  
});




function displayReturnButton()
{
  var isDisplay = false;
  var arItems = [];
  
  $('.js-checked-item').each(function(){
    if($(this).is(':checked'))
    {
      isDisplay = true;
      arItems.push($(this).val());
    }
  });
  
  if(isDisplay)
  {
    $('.return-button').addClass('active');
    $('.return-button a').attr('data-items', arItems.join('-') );
  }
  else
    $('.return-button').removeClass('active');
}

