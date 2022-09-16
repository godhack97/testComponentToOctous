<?php
	
	namespace Sale\Handlers\PaySystem;
	
	use Bitrix\Main;
	use Bitrix\Main\Error;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Request;
	use Bitrix\Main\Text\Encoding;
	use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Web;
	use Bitrix\Sale\BusinessValue;
	use Bitrix\Sale\PaySystem;
	use Bitrix\Sale\Payment;
	use Bitrix\Sale\Order;
	use Bitrix\Main\Loader;
	use Bitrix\Main\Config\Option;
	
	IncludeModuleLangFile(__FILE__);
	Loader::includeModule('network.payment');
	
	/**
	 * Class NgeniusPaymentHandler
	 * @package Sale\Handlers\PaySystem
	 */
	class network_paymentHandler extends PaySystem\ServiceHandler implements PaySystem\IPrePayable
	{
		/**
		 * @param Payment $payment
		 * @param Request|null $request
		 * @return PaySystem\ServiceResult
		 */
		public function initiatePay(Payment $payment, Request $request = null)
		{
			Loader::includeModule('sale');
			$moduleId = 'network.payment';
			$fioPropID = 7;
			
			$networkGate = \Network\Payment\Gateway::getInstance();
			
			$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" ? 'https://' : 'http://';
			$domain_name = strtok($_SERVER['HTTP_HOST'], ":");
			
			if (strlen($domain_name) < 3) {
				$domain_name = $_SERVER['SERVER_NAME'];
			}
			$record = $networkGate->getDB()
				->query("select * from network_order where BITRIX_ORDER_ID = {$payment->getOrderId()}")
				->fetch();
			
			$Order = Order::load($payment->getOrderId());
			
			$propertyCollection = $Order->getPropertyCollection();
			
			if(!$record){
				$rsPaymentLink = $networkGate->createOrder([
					'ID' => $payment->getOrderId(),
					'amount' => $this->getBusinessValue($payment, 'NETWORK_ORDER_AMOUNT'),
					'payment' => $payment->getField('ID'),
					'currency' => $payment->getField('CURRENCY'),
					'email' => $propertyCollection->getUserEmail()->getValue(),
					'phone' => $propertyCollection->getPhone()->getValue(),
					'redirectUrl' => $protocol . $domain_name . '/bitrix/tools/sale_ps_result.php' . '?PAYMENT=NETWORK&ORDER_ID=' . $payment->getField('ORDER_ID') . '&CALLBACK_REDIRECT=1&PAYMENT_ID=' . $payment->getField('ID'),
					'billing' => [
						'fio' => $propertyCollection->getItemByOrderPropertyId($fioPropID)->getValue(),
						'location' => \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $propertyCollection->getDeliveryLocation()->getValue() ),
						'address' => $propertyCollection->getAddress()->getValue()
					]
				]);
        Main\Diag\Debug::writeToFile([
          'ID' => $payment->getOrderId(),
          'amount' => $this->getBusinessValue($payment, 'NETWORK_ORDER_AMOUNT'),
          'payment' => $payment->getField('ID'),
          'currency' => $payment->getField('CURRENCY'),
          'email' => $propertyCollection->getUserEmail()->getValue(),
          'phone' => $propertyCollection->getPhone()->getValue(),
          'redirectUrl' => $protocol . $domain_name . '/bitrix/tools/sale_ps_result.php' . '?PAYMENT=NETWORK&ORDER_ID=' . $payment->getField('ORDER_ID') . '&CALLBACK_REDIRECT=1&PAYMENT_ID=' . $payment->getField('ID'),
          'billing' => [
            'fio' => $propertyCollection->getItemByOrderPropertyId($fioPropID)->getValue(),
            'location' => \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $propertyCollection->getDeliveryLocation()->getValue() ),
            'address' => $propertyCollection->getAddress()->getValue()
          ]
        ], 'network-pay-request');
			}
			else {
				//$fetchOrder = $networkGate->fetchOrder($record['ORDER_ID']);
				//if($fetchOrder['_embedded']['payment'][0]['state'] === 'PURCHASED'){
				
				//}
        $rsPaymentLink = $networkGate->createOrder([
          'ID' => $payment->getOrderId(),
          'amount' => $this->getBusinessValue($payment, 'NETWORK_ORDER_AMOUNT'),
          'payment' => $payment->getField('ID'),
          'currency' => $payment->getField('CURRENCY'),
          'email' => $propertyCollection->getUserEmail()->getValue(),
          'phone' => $propertyCollection->getPhone()->getValue(),
          'redirectUrl' => $protocol . $domain_name . '/bitrix/tools/sale_ps_result.php' . '?PAYMENT=NETWORK&ORDER_ID=' . $payment->getField('ORDER_ID') . '&CALLBACK_REDIRECT=1&PAYMENT_ID=' . $payment->getField('ID'),
          'billing' => [
            'fio' => $propertyCollection->getItemByOrderPropertyId($fioPropID)->getValue(),
            'location' => \Bitrix\Sale\Location\Admin\LocationHelper::getLocationPathDisplay( $propertyCollection->getDeliveryLocation()->getValue() ),
            'address' => $propertyCollection->getAddress()->getValue()
          ]
        ], true);
				//$rsPaymentLink = $record['PAYMENT_LINK'];
			}
			$paymentCollection = $Order->getPaymentCollection();
			
			$isInnerPayment = $paymentCollection->isExistsInnerPayment();
			$paymentItem = false;
			if ($isInnerPayment && count($paymentCollection) > 1) {
				$paymentItem = $paymentCollection[1];
			}
			$isDelivery = $Order->getField('PRICE_DELIVERY') > 0;
			
			$phone = preg_replace('/\D+/', '', $this->getPropertyValueByCode($propertyCollection, $phone_key));
			
			if (substr($phone, 0, 1) == '7') {
				$phone = '+' . $phone;
			}
			if (substr($phone, 0, 1) == '8') {
				$phone[0] = '7';
				$phone = '+' . $phone;
			}
			
			$params = [
				'payment_link' => $rsPaymentLink,
				'amount' => $networkGate->convertCurrency($this->getBusinessValue($payment, 'NETWORK_ORDER_AMOUNT'), "AED"),
				'currency' => $payment->getField('CURRENCY') === 'RUB' ? 'AED' : $payment->getField('CURRENCY'),
				'auto_redirect_exceptions' => '/personal/orders/',
			];
			$this->setExtraParams($params);
			
			$result = $this->showTemplate($payment, "payment");
			if (method_exists($result, 'setPaymentUrl')) {
				$result->setPaymentUrl($rsPaymentLink);
			}
			
			return $result;
		}
		
		public function processRequest(Payment $payment, Request $request)
		{
			global $APPLICATION;
			$moduleId = 'network.payment';
			$result = new PaySystem\ServiceResult();
			// echo Option::get($moduleId, 'CALLBACKS_ENABLE');
			$networkGate = \Network\Payment\Gateway::getInstance();
      if($request->get('ref')){
        $record = $networkGate->getDB()
          ->query("select * from network_order where ORDER_ID = '{$request->get('ref')}'")
          ->fetch();
      }
			if($record && $record['STATE'] !== 'PURCHASED'){
				$fetchOrder = $networkGate->fetchOrder($record['ORDER_ID']);
				
				$successPayment = $fetchOrder['_embedded']['payment'][0]['state'] === 'PURCHASED';
				$change_order_status_logic = true;
    
				if ($request->get('ORDER_ID') != $this->getBusinessValue($payment, 'NETWORK_ORDER_NUMBER')) {
					$successPayment = false;
				}
				if ($fetchOrder['errors']) {
					$successPayment = false;
				}
        
    
				if ($successPayment && !$payment->isPaid() && $request->get('CALLBACK_REDIRECT') == 1) {
					$networkGate->getDB()
						->queryExecute("UPDATE network_order SET STATE = '{$fetchOrder['_embedded']['payment'][0]['state']}' where ORDER_ID = '{$request->get('ref')}'");
					
					$fields = array(
						'PS_INVOICE_ID' => $request->get('CALLBACK_REDIRECT') == 1 ? $request->get('mdOrder') : $request->get('orderId'),
						"PS_STATUS_CODE" => $fetchOrder['_embedded']['payment'][0]['state'],
						"PS_STATUS_DESCRIPTION" => $fetchOrder['_embedded']['payment'][0]["pan"] . ";" . $fetchOrder['_embedded']['payment'][0]["cardholderName"],
						"PS_SUM" => $networkGate->convertCurrency($fetchOrder['_embedded']['payment'][0]['amount']['value'], $fetchOrder['_embedded']['payment'][0]['amount']['currency']),
						"PS_STATUS" => 'Y',
						"PS_CURRENCY" => 'RUB',
						"PS_RESPONSE_DATE" => new DateTime()
					);
					$result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
					// if ($this->getBusinessValue($payment, 'PS_CHANGE_STATUS_PAY') === 'Y') {}
					
					$result->setPsData($fields);
					
					$order = Order::load($payment->getOrderId());
					
					// set order status
					$option_order_status = 'P';
					
					$statuses = [];
					$dbStatus = \CSaleStatus::GetList(["SORT" => "ASC"], ["LID" => LANGUAGE_ID], false, false, ["ID", "NAME", "SORT"]);
					while ($arStatus = $dbStatus->GetNext()) {
						$statuses[$arStatus["ID"]] = "[" . $arStatus["ID"] . "] " . $arStatus["NAME"];
					}
					
					if (array_key_exists($option_order_status, $statuses)) {
						$order->setField('STATUS_ID', $option_order_status);
					}
					else {
						echo '<span style="display:block; font-size:16px; display:block; color:red;padding:20px 0;">ERROR! CANT CHANGE ORDER STATUS</span>';
					}
					
					// set delivery status
					if ($this->getBusinessValue($payment, 'NETWORK_HANDLER_SHIPMENT') == 'Y') {
						$shipmentCollection = $order->getShipmentCollection();
						foreach ($shipmentCollection as $shipment) {
							if (!$shipment->isSystem()) {
								$shipment->allowDelivery();
							}
						}
					}
					
					$order->save();
				}
			}
			
			$returnPage = $this->getBusinessValue($payment, 'NETWORK_RETURN_URL');
			$failPage = $this->getBusinessValue($payment, 'NETWORK_FAIL_URL');
			
			if (strlen($returnPage) > 4 && $successPayment) {
				echo "<script>window.location='" . $this->getOrderDescription($payment, 'NETWORK_RETURN_URL') . "'</script>";
			}
			else if (strlen($failPage) > 4 && !$successPayment) {
				echo "<script>window.location='" . $this->getOrderDescription($payment, 'NETWORK_FAIL_URL') . "'</script>";
			}
			else {
				self::printResultText($payment, $successPayment);
			}
			
			return $result;
		}
		
		public function getPaymentIdFromRequest(Request $request)
		{
			$paymentId = $request->get('PAYMENT_ID');
			return intval($paymentId);
		}
		
		public function getCurrencyList()
		{
			return ['RUB', 'EUR', 'USD', 'UAH', 'BYN'];
		}
		
		public static function getIndicativeFields()
		{
			return ['PAYMENT' => 'NETWORK'];
		}
		
		//OLD Example
		static protected function isMyResponseExtended(Request $request, $paySystemId)
		{
			global $APPLICATION;
			
			$RBS_Gateway = new \Sberbank\Payments\Gateway;
			
			if ($request->get('OPERATION_CALLBACK') == 'NETWORK' && $request->get('CALLBACK_REDIRECT') != 1) {
				
				if (!$request->get('orderNumber')) {
					$RBS_Gateway->baseLogger('CALLBACK_RETURN', 'CALLBACK', $request->getQueryList(), [], 'ERROR PROCESSING CALLBACK');
					return false;
				}
				
				$arrOrderIds = explode("_", $request->get('orderNumber'));
				array_pop($arrOrderIds);
				$R_PAYMENT_ID = array_pop($arrOrderIds);
				$R_ORDER_ID = implode('_', $arrOrderIds);
				
				if (!$R_ORDER_ID) {
					return false;
				}
				
				$order = is_numeric($R_ORDER_ID) ? Order::load($R_ORDER_ID) : false;
				
				if ($order) {
					$paymentCollection = $order->getPaymentCollection();
					foreach ($paymentCollection as $payment) {
						if ($R_PAYMENT_ID == $payment->getId()) {
							LocalRedirect($APPLICATION->GetCurUri("ORDER_ID=" . $payment->getOrderId() . "&PAYMENT_ID=" . $R_PAYMENT_ID . "&CALLBACK_REDIRECT=1"));
						}
					}
				}
				
				$order = Order::loadByAccountNumber($R_ORDER_ID);
				if ($order) {
					$paymentCollection = $order->getPaymentCollection();
					foreach ($paymentCollection as $payment) {
						if ($R_PAYMENT_ID == $payment->getId()) {
							LocalRedirect($APPLICATION->GetCurUri("ORDER_ID=" . $payment->getOrderId() . "&PAYMENT_ID=" . $R_PAYMENT_ID . "&CALLBACK_REDIRECT=1"));
						}
					}
				}
				if (!$order) {
					$RBS_Gateway->baseLogger('CALLBACK_RETURN', 'CALLBACK', $request->getQueryList(), [], 'ERROR PROCESSING CALLBACK');
					return false;
				}
				return false;
			}
			if (!$request->get('ORDER_ID')) {
				return false;
			}
			
			$order = is_numeric($request->get('ORDER_ID')) ? Order::load($request->get('ORDER_ID')) : false;
			
			if (!$order) {
				$order = Order::loadByAccountNumber($request->get('ORDER_ID'));
			}
			if (!$order) {
				echo Loc::getMessage('RBS_MESSAGE_ERROR_BAD_ORDER');
				return false;
			}
			
			$paymentIds = $order->getPaymentSystemId();
			return in_array($paySystemId, $paymentIds);
		}
		
		private function getPropertyValueByCode($propertyCollection, $code)
		{
			$property = '';
			foreach ($propertyCollection as $property) {
				if ($property->getField('CODE') == $code)
					return $property->getValue();
			}
		}
		
		/**
		 * @return array
		 */
		protected function getUrlList()
		{
			return array();
		}
		
		/**
		 * @return array
		 */
		public function getProps()
		{
			$data = array();
			
			return $data;
		}
		
		/**
		 * @param Payment $payment
		 * @param Request $request
		 * @return bool
		 */
		public function initPrePayment(Payment $payment = null, Request $request)
		{
			return true;
		}
		
		/**
		 * @param array $orderData
		 */
		public function payOrder($orderData = [])
		{
		
		}
		
		/**
		 * @param array $orderData
		 * @return bool|string
		 */
		public function BasketButtonAction($orderData = [])
		{
			return true;
		}
		
		/**
		 * @param array $orderData
		 */
		public function setOrderConfig($orderData = [])
		{
			if ($orderData)
				$this->prePaymentSetting = array_merge($this->prePaymentSetting, $orderData);
		}
		
		protected function getOrderDescription(Payment $payment, $PROP_CODE)
		{
			/** @var PaymentCollection $collection */
			$collection = $payment->getCollection();
			$order = $collection->getOrder();
			$userEmail = $order->getPropertyCollection()->getUserEmail();
			
			$description = str_replace(
				[
					'#PAYMENT_NUMBER#',
					'#ORDER_NUMBER#',
					'#PAYMENT_ID#',
					'#ORDER_ID#',
					'#USER_EMAIL#'
				],
				[
					$payment->getField('ACCOUNT_NUMBER'),
					$order->getField('ACCOUNT_NUMBER'),
					$payment->getId(),
					$order->getId(),
					($userEmail) ? $userEmail->getValue() : ''
				],
				$this->getBusinessValue($payment, $PROP_CODE)
			);
			
			return $description;
		}
		
		private static function encode(array $data)
		{
			return Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);
		}
		
		protected function printResultText($payment, $successPayment)
		{
			global $APPLICATION;
			echo '<div class="sberbank-center" style="width: 100%;display: flex;align-items: center;align-content: center;justify-content: center;height: 100%;position: fixed;"><div style="display: block;background:#fff;padding: 10px 10px; margin-left:-10px;border-radius: 6px;max-width: 400px; border: 1px solid #e7e7e7;">';
			echo '<div class="sberbank-result-message" style="margin:5px; text-align:center;padding:10px 20px; 0"><span style=" font-family: arial;font-size: 16px;">';
			
			if ($successPayment) {
				$APPLICATION->SetTitle(Loc::getMessage('NETWORK_PAYMENT_MESSAGE_THANKS'));
				echo Loc::getMessage('NETWORK_PAYMENT_MESSAGE_THANKS_DESCRIPTION') . $this->getBusinessValue($payment, 'NETWORK_ORDER_NUMBER');
			} else {
				$APPLICATION->SetTitle(Loc::getMessage('NETWORK_PAYMENT_MESSAGE_ERROR'));
				echo Loc::getMessage('NETWORK_PAYMENT_MESSAGE_ERROR') . ' #' . $this->getBusinessValue($payment, 'NETWORK_ORDER_NUMBER');
			}
			echo '<div style=" display: block; margin:10px 10px 0;"><a style="font-family: arial;font-size: 16px;color: #21a038;" href="/">' . Loc::getMessage('NETWORK_RETURN_LINK') . '</a></div>';
			echo "</span></div>";
			echo "</div></div>";
		}
		
		public function isRefundableExtended(){}
		public function confirm(Payment $payment){}
		public function cancel(Payment $payment){}
		public function refund(Payment $payment, $refundableSum){}
		public function sendResponse(PaySystem\ServiceResult $result, Request $request){}
	}