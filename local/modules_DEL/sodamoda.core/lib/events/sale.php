<?php
	namespace Sodamoda\Core\Events;
	
	use Bitrix\Main\Loader;
	use Bitrix\Main\Mail\Event;
	
	class Sale{
		function onBeforeBasketUpdate($ID, &$arFields) {
		
		}
		function onSaleOrderSaved(\Bitrix\Main\Event $event) {
		
		}
		function onSalePayOrderEventSend(\Bitrix\Main\Event $event) {
  
		}
		function onSaleComponentOrderProperties(&$arFields) {
		
		}
		function onSaleComponentOrderOneStepComplete($orderID, $arFieldValues, $arParams) {
		
		}
	}