<?php
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\ModuleManager;
    //use Bitrix\Main\EventManager;
    //use Bitrix\Main\Application;
    //use Bitrix\Main\IO\Directory;
    //use Bitrix\Highloadblock as HL;

    class xwild_restapi extends CModule {
        var $MODULE_ID = 'xwild.restapi';

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
        public function InstallEvent() {
            return $this->actionInstallEvents();
        }
        public function InstallFiles() {
            return $this->actionInstallFiles();
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

        // Advanced method construct
        private function actionInfoModule(){
            $this->MODULE_ID = str_replace("_", ".", get_class($this));;
            $this->MODULE_NAME = Loc::getMessage('XwildModuleName');
            $this->MODULE_DESCRIPTION = Loc::getMessage('XwildModuleDescription');
            $this->PARTNER_NAME = Loc::getMessage('XwildModulePartner');
            $this->PARTNER_URI = Loc::getMessage('XwildModuleURL');

            if(file_exists(__DIR__."/version.php")){
                $arModuleVersion = [];
                include_once(__DIR__."/version.php");

                $this->MODULE_VERSION 	   = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            }
        }

        // Advanced methods installing
        private function actionInstall(){
            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvent();
            $this->InstallFiles();

            $GLOBALS['APPLICATION']->IncludeAdminFile(Loc::getMessage('restApiInstall'), __DIR__ . '/step.php');
            return true;
        }
        private function actionInstallDB(){
            require __DIR__ . '/migration/up.php';
            return true;
        }
        private function actionInstallEvents(){
            return true;
        }
        private function actionInstallFiles(){
            CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
            CopyDirFiles(__DIR__ . '/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css', true, true);
            return true;
        }

        // Advanced methods uninstalling
        private function actionUninstall(){
            $this->UnInstallDB();
            $this->UnInstallEvent();
            $this->UnInstallFiles();

            ModuleManager::unRegisterModule($this->MODULE_ID);

            $GLOBALS['APPLICATION']->IncludeAdminFile(Loc::getMessage('restApiUninstall'), __DIR__ . '/unstep.php');

            return true;
        }
        private function actionUninstallDB(){
            require __DIR__ . '/migration/down.php';
            return true;
        }
        private function actionUninstallEvents(){
            return true;
        }
        private function actionUninstallFiles(){
            DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
            DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID);
            return true;
        }
    }