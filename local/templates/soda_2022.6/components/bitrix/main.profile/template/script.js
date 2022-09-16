
$(function(){
  
	$(".profile-partner .partner-form-wrapper .accept-btn").click(function(){
		$(this).closest(".partner-form-wrapper").addClass("accepted");
	});
  
	$('#PHONE').inputmasks(maskPhoneOpts).addClass('js-mask-inited');

	$('#PHONE').on('keyup touchend', function(){
		var phoneVal = $(this).val();
		if(phoneVal.length > 0){
			waitCheck();

			$.ajax({
				type: 'POST',
				url: '/include/ajax.php',
				data: '&site_id=' + BX.message('SITE_ID') + '&mode=personal-check-confirm-phone&phone='+phoneVal,
				dataType: 'json',
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log(XMLHttpRequest);
					console.log(textStatus);
					console.log(errorThrown);
				},
				success: function (result) {
					if(result.status)
					{
						$('.button-confirm-phone').hide();
					}
					else
					{
						$('.button-confirm-phone').show();
					}
					waitCheck('close');
				}
			});
		}
	})

	$('.button-confirm-phone').on('click', function (){
	  if($('#code_confirmed').length > 0 && $('#PHONE').val() != ''){
			waitCheck();

			$.ajax({
				type: 'POST',
				url: '/include/ajax.php',
				data: $(this).parents('form').serialize() + '&site_id=' + BX.message('SITE_ID'),
				dataType: 'json',
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log(XMLHttpRequest);
					console.log(textStatus);
					console.log(errorThrown);
				},
				success: function (result) {
					if(result.status)
					{
						if(result.mode == 'profile-check-phone')
						{
							popupOpen('phone-check');
							$('#popup-phone-field').val( $('#PHONE').val() )
						}
					}
					else
					{
						if(result.mode == 'personal_check_phone')
						{
							popupOpenMessage('<p>Ваш номер не найден или является неверным.</p><p>Пожалуйста проверьте номер телефона и повторите попытку.</p>');
							$('#phone_field').val($('#PHONE').val())
						}
					}
					waitCheck('close');
				}
			});
	  }
	});
  
	$('#send_verify_code').on('click', function (event){
	  event.preventDefault();
	  ajaxQuery($('#verify_code_form'));
	});
  
	$('form[name=verify_phone]').submit(function () {
		ajaxQuery($(this));
		return false;
	});

	$('form[name="became_partner"]').on('submit', function (e) {
		e.preventDefault();
		if(document.became_partner.reportValidity()){
			$.ajax({
				type: 'POST',
				url: '/include/ajax.php',
				data: $(this).serialize(),
				dataType: 'json',
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log(XMLHttpRequest);
					console.log(textStatus);
					console.log(errorThrown);

					return false;
				},
				success: function (result) {
					if(result['status'] === true)
					{
						popupOpenMessage(result['message']);
						//location.reload();
					}
					else
						popupOpenMessage(result['message']);
				}
			});
		}
	});
});

