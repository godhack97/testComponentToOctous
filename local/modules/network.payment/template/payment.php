<?php
	IncludeModuleLangFile(__FILE__);
?>
<div class="network_wrapper">
	<div class="network_content">
		<?php if($params['NETWORK_HANDLER_AUTO_REDIRECT'] == 'Y') {?>
			<script>
				var needRedirect = true;
				var currentPage = window.location.pathname;
				var auto_redirect_exceptions = JSON.parse('<?=json_encode($params['auto_redirect_exceptions']);?>');
				auto_redirect_exceptions.forEach((element) => {
					if(currentPage.match(element)) {
						needRedirect = false;
					}
				})
				if(needRedirect) {
					window.location = '<?=$params['payment_link']?>';
				}
			</script>
		<?php } ?>
		<span class="network_price-string"><?=getMessage("NETWORK_PAYMENT_PAYMENT_TITLE");?>: <b><?=CurrencyFormat($params['amount'], $params['currency'])?></b> <?=$params['currency'] == 'USD' ? '('.round(\CCurrencyRates::ConvertCurrency($params['NETWORK_ORDER_AMOUNT'], "USD", "RUB"), 2).' RUB)' : '';?></span>
		<a href="<?=$params['payment_link']?>" class="network_payment-link"><?=getMessage("NETWORK_PAYMENT_PAYMENT_BUTTON_NAME");?></a>
		<span class="network_payment-description"><?=getMessage("NETWORK_PAYMENT_PAYMENT_DESCRIPTION");?></span>
	</div>
	<div class="network_footer">
		<span class="network_description"><?=getMessage("NETWORK_PAYMENT_FOOTER_DESCRIPTION");?></span>
	</div>
</div>

<style>
	body .network_wrapper {
		font-family: arial;
		text-align: left;
		margin-bottom: 20px;
		margin-top: 20px;
	}
	body .network_price-block {
		font-family: arial;
		display: block;
		margin: 20px 0px;
	}
	body .network_price-string {
		font-family: arial;
		font-weight: bold;
		font-size: 14px;
	}
	body .network_price-string b {
		font-family: arial;
		font-size: 20px;
	}
	body .network_content {
		font-family: arial;
	    width: 400px;
	    max-width: 100%;
	    padding: 10px 10px 13px;
	    border: 1px solid #e5e5e5;
	    text-align: center;
	    margin-bottom: 12px;
	}
	body .network_payment-link {
		font-family: arial;
		display: inline-block;
		width: 320px;
		max-width: 100%;
		margin: 8px 0 5px;
		background-color: #1eb42f !important;
		color: #fff !important;
		border:none;
		box-shadow: none;
    	outline: none;
    	font-size: 14px;
	    font-weight: normal;
	    line-height: 1.42857143;
	    text-align: center;
    	white-space: nowrap;
    	vertical-align: middle;
    	padding: 6px 12px;
    	text-decoration: none !important;
	}
	body .network_payment-link:hover,body .network_payment-link:active,body .network_payment-link:focus {
		font-family: arial;
		background: #189d27;
		color: #fff;
	}
	body .network_payment-description {
		font-family: arial;
		display: block;
		font-size: 12px;
		color: #939393;
	}
	body .network_description {
		font-family: arial;
		font-size: 12px;
		max-width: 400px;
		display: block;
	}
	body .network_error-code {
		font-family: arial;
		color: red;
		font-size: 20px;
		display: block;
		margin-top:5px;
		margin-bottom: 7px;
	}
	body .network_error-message {
		font-family: arial;
		color:#000;
		font-size: 14px;
		display: block;
	}
</style>