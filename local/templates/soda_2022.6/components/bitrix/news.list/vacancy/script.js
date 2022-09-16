$(function(){

  $('form[name=vacancy]').submit(function(){
    console.log('submit');
    ajaxQueryVacancy($(this));
    $('form[name=vacancy] input[name="vacancy"]').val( $(this).closest('.block').find('h2').text() );
    return false;
  });

  function ajaxQueryVacancy(form)
  {
    if($('body').hasClass('wait'))
      return false;
    
    console.log('name: '+ form.find('input[name=name]').val() );
    
//    if(!form.find('input[name=name]').val().length)
//    {
//      popupOpenMessage('Пожалуйста, укажите Ваше имя');
//      return false;
//    }


    waitCheck();


    var formData = new FormData(form[0]);
    formData.append('site_id', BX.message('SITE_ID'));

    $.ajax({
      type: 'POST',
      url: '/include/ajax.php',
      async: false,
      cache: false,
      contentType: false, 		
      processData: false, 		
      data: formData,
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
          popupsClose();
		  
		  if(!result.ext_status)
		    setTimeout(function(){
              popupOpenMessage(result.ext_message);
            }, 500);
	      else
		    setTimeout(function(){
              popupOpenMessage('Заявка успешно отправлена!');
            }, 500);
        }   
        else
          formResultDisplay(form, result.message);

        waitCheck('close');
      }
    });
  }
	
});