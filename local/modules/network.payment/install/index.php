<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;
IncludeModuleLangFile(__FILE__);

Class network_payment extends CModule {
    var $MODULE_ID = 'network.payment';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_PATH;

    var $PAYMENT_HANDLER_PATH;
	
	public function __construct() {
		return $this->actionInfoModule();
	}
	
	//Method install default
	public function DoInstall() {
		return $this->actionInstall();
	}
	public function InstallDB() {
		return $this->actionInstallDB();
	}
	public function InstallFiles() {
		return $this->actionInstallFiles();
	}
	public function InstallEvent() {
		return $this->actionInstallEvents();
	}
	
	//Method uninstall default
	public function DoUninstall() {
		return $this->actionUninstall();
	}
	public function UnInstallDB() {
		return $this->actionUninstallDB();
	}
	public function UnInstallEvent() {
		return $this->actionUninstallEvents();
	}
	public function UnInstallFiles() {
		return $this->actionUninstallFiles();
	}
	////////////////////////////////////
	////////////////////////////////////
	
	// Advanced method construct
	private function actionInfoModule(){
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/install/index.php"));
		
		$this->MODULE_PATH = $path;
		$this->MODULE_ID = str_replace("_", ".", get_class($this));;
		$this->MODULE_NAME =  Loc::getMessage('NETWORK_PAYMENT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('NETWORK_PAYMENT_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = Loc::getMessage('NETWORK_PAYMENT_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('NETWORK_PAYMENT_PARTNER_URI');
		
		if(file_exists(__DIR__."/version.php")){
			$arModuleVersion = [];
			include_once(__DIR__."/version.php");
			
			$this->MODULE_VERSION 	   = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		
		$ps_dir_path = strlen(Option::get('sale', 'path2user_ps_files')) > 3 ? Option::get('sale', 'path2user_ps_files') : '/bitrix/php_interface/include/sale_payment/';
		$this->PAYMENT_HANDLER_PATH = $_SERVER["DOCUMENT_ROOT"] . $ps_dir_path . str_replace(".", "_", $this->MODULE_ID) . "/";
	}
	
	// Advanced methods installing
	private function actionInstall(){
		ModuleManager::registerModule($this->MODULE_ID);
		
		$this->InstallDB();
		$this->InstallFiles();
		//$this->InstallEvent();
		
		COption::SetOptionInt($this->MODULE_ID, "delete", false);
		
		return true;
	}
	private function actionInstallDB(){
		require __DIR__ . '/migration/up.php';
		return true;
	}
	private function actionInstallFiles(){
		CopyDirFiles($this->MODULE_PATH . "/install/setup/handler_include", $this->PAYMENT_HANDLER_PATH, true, true);
		CopyDirFiles($this->MODULE_PATH . "/install/setup/images/logo", $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/sale/sale_payments/');
		$this->changeFiles(new DirectoryIterator($this->PAYMENT_HANDLER_PATH));
		$this->changeFiles(new DirectoryIterator($this->PAYMENT_HANDLER_PATH . 'template/'));
		return true;
	}
	private function actionInstallEvents(){
		return true;
	}
	
	// Advanced methods uninstalling
	private function actionUninstall(){
		$this->UnInstallDB();
		$this->UnInstallFiles();
		//$this->UnInstallEvent();
		
		ModuleManager::unRegisterModule($this->MODULE_ID);
		
		return true;
	}
	private function actionUninstallDB(){
		require __DIR__ . '/migration/down.php';
		return true;
	}
	private function actionUninstallFiles(){
		$ps_dir_path = strlen(Option::get('sale', 'path2user_ps_files')) > 3 ? Option::get('sale', 'path2user_ps_files') : '/bitrix/php_interface/include/sale_payment/';
		DeleteDirFilesEx($ps_dir_path . str_replace(".", "_", $this->MODULE_ID));
		return true;
	}
	private function actionUninstallEvents(){
		return true;
	}
	
	function changeFiles($files) {
		foreach ($files as $file) {
			if ($file->isDot() === false) {
				$path_to_file = $file->getPathname();
				$file_contents = file_get_contents($path_to_file);
				$file_contents = str_replace("{module_path}", $this->MODULE_ID, $file_contents);
				file_put_contents($path_to_file, $file_contents);
			}
		}
	}
}

?>