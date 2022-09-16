<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\Localization\Loc;

    class Page {
      private static $instance = null;
  
      public function checkAccess($code) {
        if ($code === 'accessConfig' && !$GLOBALS['USER']->IsAdmin()) {
          $GLOBALS['APPLICATION']->AuthForm(getMessage('XwildRestAccessDenied'));
        }
        elseif ($code === 'accessMenuItems') {
          $groupsAccess = explode('|', Config::getInstance()->getOption($code));
          $userGroups = $GLOBALS['USER']->GetUserGroupArray();
          return !Security::getInstance()->checkAccessByGroups($groupsAccess, $userGroups) ? false : true;
        }
        else {
          $groupsAccess = explode('|', Config::getInstance()->getOption($code));
          $userGroups = $GLOBALS['USER']->GetUserGroupArray();
      
          if (!Security::getInstance()->checkAccessByGroups($groupsAccess, $userGroups) || !$this->checkAccess('accessMenuItems')) {
            $GLOBALS['APPLICATION']->AuthForm(getMessage('XwildRestAccessDenied'));
          }
        }
      }
  
      public function loadLanguage($file) {
        Loc::loadLanguageFile($file);
      }
  
      public function loadMessages($file) {
        Loc::loadMessages($file);
      }
  
      public function loadCustomMessages($file) {
        Loc::loadCustomMessages($file);
      }
  
      public function setTitle($title) {
        $GLOBALS['APPLICATION']->SetTitle($title);
      }
  
      public function addCss($file) {
        if (!is_file($_SERVER['DOCUMENT_ROOT'] . $file)) {
          CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . Settings::getInstance()->getModule()['id'] . '/install/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css', true, true);
        }
        $GLOBALS['APPLICATION']->SetAdditionalCSS($file);
      }
  
      public function addJs($file)
      {
        if (!is_file($_SERVER['DOCUMENT_ROOT'] . $file)) {
          CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . settings()->get('module')['id'] . '/install/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . settings()->get('module')['id'], true, true);
        }
        $GLOBALS['APPLICATION']->AddHeadScript($file);
      }
  
      public function addHeadString($string) {
        $GLOBALS['APPLICATION']->AddHeadString($string, true);
      }
  
      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
        }
    
        return self::$instance;
      }
  
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Page');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }