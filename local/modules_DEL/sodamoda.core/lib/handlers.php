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
			//$this->getIblock();
			//$this->getCatalog();
			//$this->getSale();
		}
		
		private function getMain(){
      $this->eventManager->addEventHandler(
        'main', 'OnBeforeUserUpdate',
        ["\\Sodamoda\\Core\\Events\\Main", 'onBeforeUserUpdate']
      );
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
			/*
			$this->eventManager->addEventHandler(
				"catalog", "OnStoreProductAdd",
				["\\Sodamoda\\Core\\Events\\Catalog", "onStoreProductSave"]
			);
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