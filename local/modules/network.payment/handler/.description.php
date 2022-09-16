<?php
	
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Config\Option;
	use Bitrix\Sale\PaySystem;
	use Bitrix\Main\Loader;
	
	Loc::loadMessages(__FILE__);
	
	$data = [
		'NAME' => Loc::getMessage("NETWORK_PAYMENT_MODULE_TITLE"),
		'SORT' => 100,
		'CODES' => [
			"NETWORK_HANDLER_AUTO_REDIRECT" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_AUTO_REDIRECT_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_AUTO_REDIRECT_DESCR"),
				'SORT' => 200,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_HANDLER"),
				"INPUT" => [
					'TYPE' => 'Y/N'
				],
				'DEFAULT' => [
					"PROVIDER_VALUE" => "N",
					"PROVIDER_KEY" => "INPUT"
				]
			],
			
			"NETWORK_HANDLER_SHIPMENT" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_SHIPMENT_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_SHIPMENT_DESCR"),
				'SORT' => 320,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_HANDLER"),
				"INPUT" => [
					'TYPE' => 'Y/N'
				],
				'DEFAULT' => [
					"PROVIDER_VALUE" => "N",
					"PROVIDER_KEY" => "INPUT"
				]
			],
			"NETWORK_RETURN_URL" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_RETURN_URL_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_RETURN_URL_DESCR"),
				'SORT' => 330,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_HANDLER"),
			],
			"NETWORK_FAIL_URL" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_FAIL_URL_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_FAIL_URL_DESCR"),
				'SORT' => 340,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_HANDLER"),
			],
			"NETWORK_HANDLER_LOGGING" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_LOGGING_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_HANDLER_LOGGING_DESCR"),
				'SORT' => 360,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_HANDLER"),
				"INPUT" => [
					'TYPE' => 'Y/N'
				],
				'DEFAULT' => [
					"PROVIDER_VALUE" => "Y",
					"PROVIDER_KEY" => "INPUT"
				]
			],
			
			"NETWORK_ORDER_NUMBER" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_ORDER_NUMBER_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_ORDER_NUMBER_DESCR"),
				'SORT' => 650,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_ORDER"),
				'DEFAULT' => [
					'PROVIDER_KEY' => 'ORDER',
					'PROVIDER_VALUE' => 'ACCOUNT_NUMBER'
				]
			],
			"NETWORK_ORDER_AMOUNT" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_ORDER_AMOUNT_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_ORDER_AMOUNT_DESCR"),
				'SORT' => 660,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_ORDER"),
				'DEFAULT' => [
					'PROVIDER_KEY' => 'PAYMENT',
					'PROVIDER_VALUE' => 'SUM'
				]
			],
			"NETWORK_ORDER_DESCRIPTION" => [
				"NAME" => Loc::getMessage("NETWORK_PAYMENT_ORDER_DESCRIPTION_NAME"),
				"DESCRIPTION" => Loc::getMessage("NETWORK_PAYMENT_ORDER_DESCRIPTION_DESCR"),
				'SORT' => 670,
				'GROUP' => Loc::getMessage("NETWORK_PAYMENT_GROUP_ORDER"),
				'DEFAULT' => [
					'PROVIDER_KEY' => 'ORDER',
					'PROVIDER_VALUE' => 'USER_DESCRIPTION'
				]
			],
		]
	];