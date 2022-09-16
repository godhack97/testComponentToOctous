<?php
	if(class_exists('sodamoda_core')){
		return;
	}
	
	use Bitrix\Main\IO\File;
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\ModuleManager;
	use Bitrix\Main\Config\Option;
	use Bitrix\Main\EventManager;
	use Bitrix\Main\Application;
	use Bitrix\Main\IO\Directory;
	use Bitrix\Highloadblock as HL;

	Loc::loadMessages(__FILE__);

	Class sodamoda_core extends CModule{
		function __construct(){
			if(file_exists(__DIR__."/version.php")){

				$arModuleVersion = [];

				include_once(__DIR__."/version.php");

				$this->MODULE_ID 		   = str_replace("_", ".", get_class($this));
				$this->MODULE_VERSION 	   = $arModuleVersion["VERSION"];
				$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
				$this->MODULE_NAME 		   = Loc::getMessage("SODAMODA_MODULE_NAME");
				$this->MODULE_DESCRIPTION  = Loc::getMessage("SODAMODA_MODULE_DESCRIPTION");
				$this->PARTNER_NAME 	   = Loc::getMessage("SODAMODA_MODULE_PARTNER_NAME");
				$this->PARTNER_URI  	   = Loc::getMessage("SODAMODA_MODULE_PARTNER_URI");
			}

			return false;
		}

		function InstallDB(){
			return true;
		}
		function UnInstallDB(){
			return true;
		}

		function InstallHL(){

			return true;
		}
		function UnInstallHL(){
			
			return true;
		}

		function InstallEvents(){
			//EventManager::getInstance()->registerEventHandler(
			//	"sale",
			//	"onSaleDeliveryHandlersClassNamesBuildList",
			//	$this->MODULE_ID,
			//	"\Xwild\Cse\SaleHandler",
			//	"addDeliveryServices",
			//	100
			//);
			//EventManager::getInstance()->registerEventHandler(
			//	"main",
			//	"OnAdminContextMenuShow",
			//	$this->MODULE_ID,
			//	'\Xwild\Cse\SaleHandler',
			//	'saleAdminContextMenuShowHandler'
			//);
			return true;
		}
		function UnInstallEvents(){
			//EventManager::getInstance()->unregisterEventHandler(
			//	"sale",
			//	"onSaleDeliveryHandlersClassNamesBuildList",
			//	$this->MODULE_ID,
			//	"\Xwild\Cse\SaleHandler",
			//	"addDeliveryServices",
			//	100
			//);
			//EventManager::getInstance()->unregisterEventHandler(
			//	'main',
			//	'OnAdminContextMenuShow',
			//	$this->MODULE_ID,
			//	'\Xwild\Cse\SaleHandler',
			//	'saleAdminContextMenuShowHandler'
			//);
			
			return true;
		}

		function InstallFiles(){
			return true;
		}
		function UnInstallFiles(){
			return true;
		}

		function DoInstall(){
			global $APPLICATION;
			
			if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){
				$this->InstallDB();
				$this->InstallHL();
				$this->InstallFiles();
				
				ModuleManager::registerModule($this->MODULE_ID);
				
				$this->InstallEvents();
				
				$APPLICATION->IncludeAdminFile(Loc::getMessage("INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/step.php");
			}
			else{
				$APPLICATION->ThrowException(
					Loc::getMessage("INSTALL_ERROR_VERSION")
				);
			}
		}
		function DoUninstall(){
			global $APPLICATION;
			$this->UnInstallFiles();
			$this->UnInstallHL();
			$this->UnInstallDB();

			$this->UnInstallEvents();

			//COption::RemoveOption($this->MODULE_ID);
			ModuleManager::unRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(Loc::getMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/unstep.php");
		}
	}