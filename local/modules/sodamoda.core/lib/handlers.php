<?php
	namespace Sodamoda\Core;
	
	use \Bitrix\Main\EventManager;
	use Sodamoda\Core\Events;
	
	class Handlers{
		private static $instance = null;
		public $eventManager;
		
		function init(){
			$this->eventManager = EventManager::getInstance();
			
			$this->getMain();
			$this->getIblock();
			$this->getCatalog();
			$this->getSale();
			$this->getForm();
			$this->getMessageService();
			$this->getHighload();
		}
		
		private function getMain(){
			$this->eventManager->addEventHandler('main', 'OnPageStart', ["\\Sodamoda\\Core\\Events\\Main", 'OnPageStart']);
      $this->eventManager->addEventHandler('main', 'OnBeforeUserAdd', ["\\Sodamoda\\Core\\Events\\Main", 'OnBeforeUserAdd']);
      $this->eventManager->addEventHandler('main', 'OnBeforeUserUpdate', ["\\Sodamoda\\Core\\Events\\Main", 'onBeforeUserUpdate']);
      $this->eventManager->addEventHandler('main', 'OnBeforeUserRegister', ["\\Sodamoda\\Core\\Events\\Main", 'OnBeforeUserRegister']); //пустой в исходнике
      $this->eventManager->addEventHandler('main', 'OnAfterUserAdd', ["\\Sodamoda\\Core\\Events\\Main", 'OnAfterUserAdd']);
      $this->eventManager->addEventHandler('main', 'OnBeforeEventAdd', ["\\Sodamoda\\Core\\Events\\Main", 'OnBeforeEventAdd']);
      $this->eventManager->addEventHandler('main', 'OnBeforeEventSend', ["\\Sodamoda\\Core\\Events\\Main", 'OnBeforeEventSend']);
      $this->eventManager->addEventHandler('main', 'OnAdminTabControlBegin', ["\\Sodamoda\\Core\\Events\\Main", 'OnAdminTabControlBegin']);
      $this->eventManager->addEventHandler('main', 'OnAdminSaleOrderEdit', ["\\Sodamoda\\Core\\Events\\Main", 'checkManagerResponsibility']);
      $this->eventManager->addEventHandler('main', 'OnAdminSaleOrderView', ["\\Sodamoda\\Core\\Events\\Main", 'checkManagerResponsibility']);
      /*
			$this->eventManager->addEventHandler(
				"main", "OnBuildGlobalMenu",
				["\\Sodamoda\\Core\\Events\\Main", "doBuildGlobalMenu"]
			);
			$this->eventManager->addEventHandler(
				"main", "OnBeforeUserRegister",
				["\\Sodamoda\\Core\\Events\\Main", "onBeforeUserRegister"]
			);
			$this->eventManager->addEventHandler(
				"main", "OnBeforeUserAdd",
				["\\Sodamoda\\Core\\Events\\Main", "onBeforeUserAdd"]
			);
			$this->eventManager->addEventHandler(
				"main", "OnAfterUserLogin",
				["\\Sodamoda\\Core\\Events\\Main", "onAfterUserLogin"]
			);
			//OnAfterUserLoginHandler - не нужно!
			
			$this->eventManager->addEventHandler(
				"main", "OnEpilog",
				["\\Sodamoda\\Core\\Events\\Main", "onEpilog"]
			);
      */

		}
		private function getIblock(){
      $this->eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementAdd', ["\\Sodamoda\\Core\\Events\\Iblock", 'OnBeforeIBlockElementAdd']);
      $this->eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', ["\\Sodamoda\\Core\\Events\\Iblock", 'OnBeforeIBlockElementUpdate']);
      $this->eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', ["\\Sodamoda\\Core\\Events\\Iblock", 'getUserTypeDescription']);
			$this->eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', ["\\Sodamoda\\Core\\Events\\Iblock", 'getUserTypeDescriptionСolor']);
      /*
			$this->eventManager->addEventHandler(
				"iblock", "OnBeforeIBlockSectionAdd",
				["\\Sodamoda\\Core\\Events\\Iblock", "iblockSectionImport"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnBeforeIBlockSectionUpdate",
				["\\Sodamoda\\Core\\Events\\Iblock", "iblockSectionImport"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnStartIBlockElementAdd",
				["\\Sodamoda\\Core\\Events\\Iblock", "OnStartIBlockElementAdd"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnStartIBlockElementAdd",
				["\\Sodamoda\\Core\\Events\\Iblock", "doIBlockAfterSave"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnStartIBlockElementUpdate",
				["\\Sodamoda\\Core\\Events\\Iblock", "OnStartIBlockElementUpdate"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnStartIBlockElementUpdate",
				["\\Sodamoda\\Core\\Events\\Iblock", "doIBlockAfterSave"]
			);
			$this->eventManager->addEventHandler(
				"iblock", "OnBeforeIBlockElementDelete",
				["\\Sodamoda\\Core\\Events\\Iblock", "onBeforeIBlockElementDelete"]
			);
      */
		}
		private function getCatalog(){
      $this->eventManager->addEventHandler('catalog', 'OnSuccessCatalogImport1C', ["\\Sodamoda\\Core\\Events\\Catalog", 'OnSuccessCatalogImport1C']);
      $this->eventManager->addEventHandler('catalog', 'OnStoreProductUpdate', ["\\Sodamoda\\Core\\Events\\Catalog", 'OnStoreProductUpdate']);
      $this->eventManager->addEventHandler('catalog', 'OnBeforeStoreProductUpdate', ["\\Sodamoda\\Core\\Events\\Catalog", 'OnBeforeStoreProductUpdate']);
      $this->eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', ["\\Sodamoda\\Core\\Events\\Catalog", 'currencyGetOptimalPrice']);
			//$this->eventManager->addEventHandler('catalog', 'OnBeforeProductUpdate', ["\\Sodamoda\\Core\\Events\\Catalog", 'onBeforeProductUpdate']);
			//$this->eventManager->addEventHandler('catalog', '\Bitrix\Catalog\Model\Product::OnBeforeUpdate', ["\\Sodamoda\\Core\\Events\\Catalog", 'onBeforeProductUpdate']);
			//$this->eventManager->addEventHandler('catalog', 'onBeforeProductUpdate', ["\\Sodamoda\\Core\\Events\\Catalog", 'onBeforeProductUpdate']);
			/*
			$this->eventManager->addEventHandler(
				"catalog", "OnStoreProductUpdate",
				["\\Sodamoda\\Core\\Events\\Catalog", "onStoreProductSave"]
			);
			$this->eventManager->addEventHandler(
				"catalog", "OnPriceAdd",
				["\\Sodamoda\\Core\\Events\\Catalog", "doIBlockAfterSave"]
			);
			$this->eventManager->addEventHandler(
				"catalog", "OnPriceUpdate",
				["\\Sodamoda\\Core\\Events\\Catalog", "doIBlockAfterSave"]
			);
			*/
		}
		private function getSale(){
      $this->eventManager->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', ["\\Sodamoda\\Core\\Events\\Sale", 'customDeliveryCalculate']);
      $this->eventManager->addEventHandler('sale', 'OnOrderNewSendEmail', ["\\Sodamoda\\Core\\Events\\Sale", 'OnOrderNewSendEmail']);
      $this->eventManager->addEventHandler('sale', 'OnOrderPaySendEmail', ["\\Sodamoda\\Core\\Events\\Sale", 'OnOrderNewSendEmail']);
      //$this->eventManager->addEventHandler(
      //  'sale', 'OnSaleOrderEntitySaved',
      //  ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleOrderEntitySaved']
      //);
      $this->eventManager->addEventHandler('sale', 'onSaleAdminOrderInfoBlockShow', ["\\Sodamoda\\Core\\Events\\Sale", 'onSaleAdminOrderInfoBlockShow']);
      $this->eventManager->addEventHandler('sale', 'OnSaleBeforeStatusOrderChange', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleBeforeStatusOrderChange']);
      $this->eventManager->addEventHandler('sale', 'OnBeforeSalePaymentSetField', ["\\Sodamoda\\Core\\Events\\Sale", 'OnBeforeSalePaymentSetField']);
      //$this->eventManager->addEventHandler(
      //  'sale', 'onSaleDeliveryServiceCalculate',
      //  ["\\Sodamoda\\Core\\Events\\Sale", 'onSaleDeliveryServiceCalculate']
      //);
      $this->eventManager->addEventHandler('sale', 'OnSalePaymentEntitySaved', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSalePaymentEntitySaved']);
      $this->eventManager->addEventHandler('sale', 'OnSaleShipmentEntitySaved', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleShipmentEntitySaved']);
      $this->eventManager->addEventHandler('sale', 'OnSaleShipmentItemEntitySaved', ["\\Sodamoda\\Core\\Events\\Sale", 'sendInfoOrderByModifyItem']);
      $this->eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleOrderSaved']);
      $this->eventManager->addEventHandler('sale', 'OnSaleOrderBeforeSaved', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleOrderBeforeSaved']);
      $this->eventManager->addEventHandler('sale', 'OnSaleComponentOrderOneStepComplete', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleComponentOrderOneStepComplete']);
      //$this->eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleOrderSaved']);
      $this->eventManager->addEventHandler('sale', 'OnSalePayOrder', ["\\Sodamoda\\Core\\Events\\Sale", 'generateCoupon']);
      //$this->eventManager->addEventHandler('sale', 'onSaleAdminOrderInfoBlockShow', ["\\Sodamoda\\Core\\Events\\Sale", 'onSaleAdminOrderInfoBlockShow']);
      $this->eventManager->addEventHandler('sale', 'OnSaleComponentOrderProperties', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleComponentOrderProperties']);
      $this->eventManager->addEventHandler('sale', 'OnSaleComponentOrderOneStepPersonType', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleComponentOrderOneStepPersonType']);
      $this->eventManager->addEventHandler('sale', 'OnSaleStatusOrder', ["\\Sodamoda\\Core\\Events\\Sale", 'OnSaleStatusOrder']);
      /*
			$this->eventManager->addEventHandler(
				"sale", "OnBeforeBasketUpdate",
				["\\Sodamoda\\Core\\Events\\Sale", "onBeforeBasketUpdate"]
			);
			$this->eventManager->addEventHandler(
				"sale", "OnSaleOrderSaved",
				["\\Sodamoda\\Core\\Events\\Sale", "onSaleOrderSaved"]
			);
			$this->eventManager->addEventHandler(
				"sale", "OnSaleOrderPaid",
				["\\Sodamoda\\Core\\Events\\Sale", "onSalePayOrderEventSend"]
			);
			$this->eventManager->addEventHandler(
				"sale", "OnSaleComponentOrderProperties",
				["\\Sodamoda\\Core\\Events\\Sale", "onSaleComponentOrderProperties"]
			);
			$this->eventManager->addEventHandler(
				"sale", "OnSaleComponentOrderOneStepComplete",
				["\\Sodamoda\\Core\\Events\\Sale", "onSaleComponentOrderOneStepComplete"]
			);
      */
		}
		private function getForm(){
      $this->eventManager->addEventHandler('form', 'onBeforeResultAdd', ["\\Sodamoda\\Core\\Events\\Form", 'my_onBeforeResultAdd']);
    }
		private function getMessageService(){
			$this->eventManager->addEventHandler('messageservice', 'onGetSmsSenders', [\Sodamoda\Core\Events\Messageservice::class, 'onGetSmsSenders']);
			//$this->eventManager->addEventHandler('messageservice', 'onGetSmsSenders', ["\\Sodamoda\\Core\\Events\\Messageservice", 'registerSmscService']);
    }
		private function getHighload(){
			$this->eventManager->addEventHandler('', 'COLOROnBeforeUpdate', [\Sodamoda\Core\Events\Highload::class, 'OnBeforeAddUpdate']);
			$this->eventManager->addEventHandler('', 'COLOROnBeforeAdd', [\Sodamoda\Core\Events\Highload::class, 'OnBeforeAddUpdate']);
    }
		
		//base singleton
		public static function getInstance(){
			if (null === self::$instance){
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		private function __clone() {}
		private function __construct() {}
		private function __sleep(){}
		private function __wakeup(){}
	}