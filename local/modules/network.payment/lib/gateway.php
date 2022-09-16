<?php
	namespace Network\Payment;
	
	use Bitrix\Main\Config\Option;
	use Bitrix\Main\Diag\Debug;

	class Gateway {
		private static $instance = null;
		private $mid = 'network.payment';
		
		private $apiKey = '';
		private $apiUrl = '';
		
		public function getDB(){
			return \Bitrix\Main\Application::getConnection();
		}
		public function init(){
			$this->apiUrl = $this->getApiUrl();
			$this->apiKey = $this->getApiKey();
		}
		
		/**
		 * Gets Environment.
		 *
		 * @return string
		 */
		public function getEnvironment() {
			return Option::get($this->mid, 'environment');
		}
		
		/**
		 * Gets Identity Url.
		 *
		 * @return string
		 */
		public function getIdentityUrl() {
			switch ( $this->getEnvironment() ) {
				case 'uat':
					return Option::get($this->mid, 'uat_identity_url');
				case 'live':
					return Option::get($this->mid, 'live_identity_url');
			}
		}
		
		/**
		 * Gets Api Url.
		 *
		 * @return string
		 */
		public function getApiUrl() {
			switch ( $this->getEnvironment() ) {
				case 'uat':
					return Option::get($this->mid, 'uat_api_url');
				case 'live':
					return Option::get($this->mid, 'live_api_url');
			}
		}
		public function getTokenEndpoint() {
			return Option::get($this->mid, 'token_endpoint');
		}
		public function getOrderEndpoint() {
			return Option::get($this->mid, 'order_endpoint');
		}
		public function getFetchEndpoint() {
			return Option::get($this->mid, 'fetch_endpoint');
		}
		public function getCaptureEndpoint() {
			return Option::get($this->mid, 'capture_endpoint');
		}
		public function getRefundEndpoint() {
			return Option::get($this->mid, 'refund_endpoint');
		}
		public function getVoidEndpoint() {
			return Option::get($this->mid, 'void_endpoint');
		}
		
		public function getOutlet() {
			return Option::get($this->mid, 'outlet_ref');
		}
		public function getTenant() {
			$tenant = [
				'uat'  => 'ni',
				'live' => 'networkinternational',
			];
			
			return $tenant[$this->getEnvironment()];
		}
		public function getPaymentAction() {
			return Option::get($this->mid, 'payment_action');
		}
		public function getApiKey() {
			return Option::get($this->mid, 'api_key');
		}
		
		public function getToken(){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->getApiUrl().$this->getTokenEndpoint());
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"accept: application/vnd.ni-identity.v1+json",
				"authorization: Basic ".$this->getApiKey(),
				"content-type: application/vnd.ni-identity.v1+json"
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,  "{\"realmName\":\"".$this->getTenant()."\"}");
			$response = curl_exec($ch);
			
			$output = json_decode($response);
			return $output->access_token;
		}
		public function createOrder($order, $exist = false){
			$fio = explode(' ', $order['billing']['fio']);
			$postData = new \StdClass();
			$postData->action = "PURCHASE";
			$postData->amount = new \StdClass();
			$postData->amount->currencyCode = "AED";
			$postData->emailAddress = $order['email'];
			$amount = $this->convertCurrency($order['amount'], $order['currency']);
			$postData->amount->value = $amount * 100;
			$postData->shippingAddress->firstName = $fio[1];
			$postData->shippingAddress->lastName = $fio[0];
			$postData->shippingAddress->address1 = $order['billing']['address'];
			$postData->shippingAddress->city = $order['billing']['location'];
			$postData->merchantAttributes->redirectUrl = $order['redirectUrl'];
			$postData->merchantOrderReference = 'sodamoda-'.$order['ID'];
			$postData->merchantDefinedData->phone = $order['phone'];
			$postData->merchantDefinedData->orderId = $order['ID'];

			$token = $this->getToken();
			
			$json = json_encode($postData);
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $this->getApiUrl().sprintf($this->getOrderEndpoint(), $this->getOutlet()));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer ".$token,
				"Content-Type: application/vnd.ni-payment.v2+json",
				"Accept: application/vnd.ni-payment.v2+json"
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			
			$response = curl_exec($ch);
			$output = json_decode($response, true);
			$order_reference = $output['reference'];
			$orderPayUrl = $output['_links']['payment']['href'];
			$data = [
				'BITRIX_ORDER_ID' => $order['ID'],
				'ORDER_ID' => $order_reference,
				'AMOUNT' => $output['amount']['value'],
				'CURRENCY' => $output['amount']['currencyCode'],
				'REFERENCE' => $output['reference'],
				'STATE' => $output['_embedded']['payment']['state'],
				'ACTION' => $output['action'],
				'PAYMENT_LINK' => $orderPayUrl,
				'PAYMENT_ID' => $order['payment'],
			];
			if($exist){
        $this->getDB()->queryExecute('UPDATE network_order SET PAYMENT_LINK = "'.$orderPayUrl.'" WHERE BITRIX_ORDER_ID = "'.$order['ID'].'"');
        $this->getDB()->queryExecute('UPDATE network_order SET ORDER_ID = "'.$order_reference.'" WHERE BITRIX_ORDER_ID = "'.$order['ID'].'"');
        $this->getDB()->queryExecute('UPDATE network_order SET REFERENCE = "'.$output['reference'].'" WHERE BITRIX_ORDER_ID = "'.$order['ID'].'"');
        $this->getDB()->queryExecute('UPDATE network_order SET STATE = "'.$output['_embedded']['payment']['state'].'" WHERE BITRIX_ORDER_ID = "'.$order['ID'].'"');
        $this->getDB()->queryExecute('UPDATE network_order SET ACTION = "'.$output['action'].'" WHERE BITRIX_ORDER_ID = "'.$order['ID'].'"');
      }
      else {
        $this->getDB()->add('network_order', $data);
      }
			
			curl_close ($ch);
			
			return $orderPayUrl;
		}
		public function fetchOrder($orderID){
			$token = $this->getToken();
			
			$ch = curl_init();
			$endpoint = sprintf( $this->getFetchEndpoint(), $this->getOutlet(), $orderID);
			curl_setopt($ch, CURLOPT_URL, $this->getApiUrl().$endpoint);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer ".$token,
				"Content-Type: application/vnd.ni-payment.v2+json",
				"Accept: application/vnd.ni-payment.v2+json"
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			$output = json_decode($response, true);
			
			curl_close ($ch);
			
			return $output;
		}
		public function cancelOrder($link){
			$token = $this->getToken();
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer ".$token,
				"Content-Type: application/vnd.ni-payment.v2+json",
				"Accept: application/vnd.ni-payment.v2+json"
			]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			$output = json_decode($response, true);
			
			curl_close ($ch);
			
			return $output;
		}
		
		public function convertCurrency($amount, $currency){
			switch ($currency){
				case 'RUB':
					return round(\CCurrencyRates::ConvertCurrency(doubleval($amount), $currency, "AED"),2);
				case 'USD':
					return round(\CCurrencyRates::ConvertCurrency(\CCurrencyRates::ConvertCurrency(doubleval($amount), $currency, "RUB"), "RUB", "AED"),2);
				case 'AED':
				default:
					return $amount;
			}
		}
		
		//base singleton
		public static function getInstance()
		{
			if (null === self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		
		private function __clone(){}
		private function __construct(){}
		private function __sleep(){}
		private function __wakeup(){}
	}