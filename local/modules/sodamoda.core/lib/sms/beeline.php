<?php
	namespace Sodamoda\Core\Sms;
	
	use Bitrix\Main\Error;
	
	use Bitrix\MessageService\Sender\Result\MessageStatus;
	use Bitrix\MessageService\Sender\Result\SendMessage;
	
	use Bitrix\MessageService;
	use Bitrix\Main\Diag;
	
	class Beeline extends \Bitrix\MessageService\Sender\Base
	{
		private $login;
		
		private $password;
		private $host;
		private $senderName;
		
		private $client;
		
		public function __construct() {
			$this->senderName = 'SODAMODA';
			$this->login = '1710691';
			$this->password = '17106911';
			$this->host = 'a2p-sms-https.beeline.ru/proto/http/';
			
			$this->client = new QTSMS($this->login, $this->password, $this->host);
		}
		
		public function sendMessage(array $messageFields) {
			if (!$this->canUse()) {
				$result = new SendMessage();
				$result->addError(new Error('Ошибка отправки. СМС-сервис отключен'));
				return $result;
			}
			if ($messageFields['MESSAGE_FROM']) {
				$parameters['sender'] = $messageFields['MESSAGE_FROM'];
			}
			
			$result = new SendMessage();
			$response = $this->client->post_message($messageFields['MESSAGE_BODY'], $messageFields['MESSAGE_TO'], $this->senderName);
			if (!$response) {
				$result->addErrors($response->getErrors());
				return $result;
			}
			
			return $result;
		}
		
		public static function isSupported(){
			return true;
		}
		
		public function isDemo(){
			return false;
		}
		
		public function getShortName() {
			return 'Beeline';
		}
		
		public function getId() {
			return 'beeline';
		}
		
		public function getName() {
			return 'Beeline СМС';
		}
		
		public function canUse() {
			return true;
		}
		
		public function getFromList() {
			//$data = $this->client->getSenderList();
			//if ($data->isSuccess()) {
			//	return $data->getData();
			//}
			
			return [
				[
					'id' => 'SODAMODA',
					'name' => 'SODAMODA',
				]
			];
		}
		
		public static function onGetSmsSenders()
		{
			$class = __CLASS__;
			return [new $class()];
		}
	}
?>