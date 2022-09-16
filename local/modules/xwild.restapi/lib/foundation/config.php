<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Context;
    use Bitrix\Main\ArgumentNullException;
    use Bitrix\Main\ArgumentOutOfRangeException;
    use Bitrix\Main\Localization\Loc;
    use CSite;
    use CUserTypeEntity;

    class Config {
      private static $instance = null;
      private $moduleID = 'xwild.restapi';

      private $parameters;
      private $data;

      public function getOption($param){
        if (!is_array($this->parameters)) {
          try {
            $this->parameters = Option::getForModule(Settings::getInstance()->getSettings()['module']['id']);
          } catch (ArgumentNullException $e) {
            return false;
          }
        }
        return $this->parameters[$param];
        //$param = Settings::getInstance()->getPrefix().$param;
        //p(\Bitrix\Main\Config\Option::getForModule($this->moduleID));
        //var_dump(Option::get($this->moduleID, $param));
        //return !empty(Option::get($this->moduleID, $param)) ? Option::get($this->moduleID, $param) : false;
      }
  
      public function save()
      {
        $this->prepare();
        foreach ($this->data as $code => $value) {
          try {
            Option::set(Settings::getInstance()->getSettings()['module']['id'], $code, $value, false);
            $this->parameters[$code] = $value;
          } catch (ArgumentOutOfRangeException $e) {
          }
        }
        $this->addTokenField();
        $this->registerEventHandlerForGenerateToken();
      }
      public function setOption(){
        $this->prepareData();

        foreach ($this->data as $code => $value) {
          if(!$value){
            Option::delete(Settings::getInstance()->getSettings()['module']['id'], ['name' => $code]);
          }
          else{
            Option::set(Settings::getInstance()->getSettings()['module']['id'], $code, $value, false);
            $this->parameters[$code] = $value;
          }
        }

        $this->addTokenData();
        //$this->registerEventHandlerForGenerateToken();
      }
      public function restoreOption(){
        $this->prepareData();

        foreach ($this->data as $code => $value) {
          try {
            Option::delete(Settings::getInstance()->getSettings()['module']['id'], ['name' => $code]);
            unset($this->parameters[$code]);
          } catch (ArgumentNullException $e) {}
          Option::delete($this->moduleID, ['name' => $code]);
          unset($this->parameters[$code]);
        }
  
        //$this->registerEventHandlerForGenerateToken();
      }
      private function prepareData(){
        $settingsObj = Settings::getInstance();
        $newParams = [];
        $paramsData = Context::getCurrent()->getRequest()->getPostList();

        switch ($paramsData['form']){
          case 'rest-api-config':
            // Checkboxes
            $newParams[$settingsObj->getPrefix().'useRestApi'] = !isset($paramsData[$settingsObj->getPrefix() . 'useRestApi']) ? false : $paramsData[$settingsObj->getPrefix() . 'useRestApi'];
            $newParams[$settingsObj->getPrefix().'useLateStart'] = !isset($paramsData[$settingsObj->getPrefix() . 'useLateStart']) ? false : $paramsData[$settingsObj->getPrefix() . 'useLateStart'];
            $newParams[$settingsObj->getPrefix().'useJournal'] = !isset($paramsData[$settingsObj->getPrefix() . 'useJournal']) ? false : $paramsData[$settingsObj->getPrefix() . 'useJournal'];
            $newParams[$settingsObj->getPrefix().'showExamples'] = !isset($paramsData[$settingsObj->getPrefix() . 'showExamples']) ? false : $paramsData[$settingsObj->getPrefix() . 'showExamples'];
            $newParams[$settingsObj->getPrefix().'useExampleRoute'] = !isset($paramsData[$settingsObj->getPrefix() . 'useExampleRoute']) ? false : $paramsData[$settingsObj->getPrefix() . 'useExampleRoute'];
            $newParams[$settingsObj->getPrefix().'useNativeRoute'] = !isset($paramsData[$settingsObj->getPrefix() . 'useNativeRoute']) ? false : $paramsData[$settingsObj->getPrefix() . 'useNativeRoute'];

            // Arrays
            if (isset($paramsData[$settingsObj->getPrefix(). 'siteList'])) {
              $newParams[$settingsObj->getPrefix() . 'siteList'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'siteList']);
            }
            else {
              $newParams[$settingsObj->getPrefix() . 'siteList'] = false;
              $by = 'ID';
              $order = 'ASC';
              $result = CSite::GetList($by, $order, ['ACTIVE' => 'Y', 'DEFAULT' => 'Y']);
              while ($site = $result->fetch()) {
                $newParams[$settingsObj->getPrefix() . 'siteList'] = $site['ID'];
              }
            }
            
            $newParams[$settingsObj->getPrefix().'siteList'] = !isset($paramsData[$settingsObj->getPrefix() . 'siteList']) ? false : $newParams[$settingsObj->getPrefix() . 'siteList'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'siteList']);;
            $newParams[$settingsObj->getPrefix().'accessDocumentation'] = !isset($paramsData[$settingsObj->getPrefix() . 'accessDocumentation']) ? false : $newParams[$settingsObj->getPrefix() . 'accessDocumentation'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'accessDocumentation']);;
            $newParams[$settingsObj->getPrefix().'accessSecurity'] = !isset($paramsData[$settingsObj->getPrefix() . 'accessSecurity']) ? false : $newParams[$settingsObj->getPrefix() . 'accessSecurity'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'accessSecurity']);;
            $newParams[$settingsObj->getPrefix().'accessJournal'] = !isset($paramsData[$settingsObj->getPrefix() . 'accessJournal']) ? false : $newParams[$settingsObj->getPrefix() . 'accessJournal'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'accessJournal']);;
            $newParams[$settingsObj->getPrefix().'accessSupport'] = !isset($paramsData[$settingsObj->getPrefix() . 'accessSupport']) ? false : $newParams[$settingsObj->getPrefix() . 'accessSupport'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'accessSupport']);;
            $newParams[$settingsObj->getPrefix().'accessMenuItems'] = !isset($paramsData[$settingsObj->getPrefix() . 'accessMenuItems']) ? false : $newParams[$settingsObj->getPrefix() . 'accessMenuItems'] = implode('|', $paramsData[$settingsObj->getPrefix() . 'accessMenuItems']);;
            
            // Strings
            $newParams[$settingsObj->getPrefix().'pathRestApi'] = !empty($paramsData[$settingsObj->getPrefix() . 'pathRestApi']) ? str_replace(' ', '', trim($paramsData[$settingsObj->getPrefix() . 'pathRestApi'], '/')) : false;
            $newParams[$settingsObj->getPrefix().'localRouteMap'] = !empty($paramsData[$settingsObj->getPrefix() . 'localRouteMap']) ? str_replace(' ', '', trim($paramsData[$settingsObj->getPrefix() . 'localRouteMap'], '/')) : false;
            
            break;
          case 'rest-api-security':
            // Checkboxes
            $newParams[$settingsObj->getPrefix().'useLoginPassword'] = !isset($paramsData[$settingsObj->getPrefix() . 'useLoginPassword']) ? false : $paramsData[$settingsObj->getPrefix() . 'useLoginPassword'];
            $newParams[$settingsObj->getPrefix().'useToken'] = !isset($paramsData[$settingsObj->getPrefix() . 'useToken']) ? false : $paramsData[$settingsObj->getPrefix() . 'useToken'];
            $newParams[$settingsObj->getPrefix().'checkExpireToken'] = !isset($paramsData[$settingsObj->getPrefix() . 'checkExpireToken']) ? false : $paramsData[$settingsObj->getPrefix() . 'checkExpireToken'];
            $newParams[$settingsObj->getPrefix().'useGenerateTokenRegisterUser'] = !isset($paramsData[$settingsObj->getPrefix() . 'useGenerateTokenRegisterUser']) ? false : $paramsData[$settingsObj->getPrefix() . 'useGenerateTokenRegisterUser'];
            $newParams[$settingsObj->getPrefix().'useRequestLimit'] = !isset($paramsData[$settingsObj->getPrefix() . 'useRequestLimit']) ? false : $paramsData[$settingsObj->getPrefix() . 'useRequestLimit'];
            $newParams[$settingsObj->getPrefix().'useCorsFilter'] = !isset($paramsData[$settingsObj->getPrefix() . 'useCorsFilter']) ? false : $paramsData[$settingsObj->getPrefix() . 'useCorsFilter'];
  
            if (isset($paramsData[$settingsObj->getPrefix().'corsListDomains'])) {
              $newParams[$settingsObj->getPrefix().'corsListDomains'] = $paramsData[$settingsObj->getPrefix().'corsListDomains'];
              $list = &$newParams[$settingsObj->getPrefix().'corsListDomains'];
              $list = str_replace([' ', "\n\r", "\n", "\r"], '', $list);
              $list = explode(';', $list);
              foreach ($list as &$item) {
                $item = rtrim($item, '/');
              }
              $list = implode(";\n", $list);
            }
            else{
              $newParams[$settingsObj->getPrefix().'corsListDomains'] = false;
            }
  
            if($newParams[$settingsObj->getPrefix().'useRequestLimit']){
              $newParamsData[$settingsObj->getPrefix().'requestLimit'] = false;
              $allParams = $paramsData;
              foreach ($allParams as $key => $value) {
                if (strpos($key, 'data:requestLimit') !== false) {
                  $code = explode('-', strtolower(str_replace('data:requestLimit', '', $key)));
                  $id = $code[1];
                  $code = $code[0];
                  $newParamsData[$settingsObj->getPrefix().'requestLimit'][$id][$code] = $value;
                  //unset($allParams[$key]);
                }
              }
              $newParams[$settingsObj->getPrefix().'requestLimit'] = json_encode($newParamsData[$settingsObj->getPrefix().'requestLimit']);
            }
            else{
              $newParamsData[$settingsObj->getPrefix().'requestLimit'] = false;
            }

            // Strings
            //$newParams[$settingsObj->getPrefix().'tokenKey'] = !empty($paramsData[$settingsObj->getPrefix() . 'tokenKey']) ? str_replace(' ', '', trim($paramsData[$settingsObj->getPrefix() . 'tokenKey'], '/')) : false;
            //$newParams[$settingsObj->getPrefix().'tokenLifetime'] = !empty($paramsData[$settingsObj->getPrefix() . 'tokenLifetime']) ? str_replace(' ', '', trim($paramsData[$settingsObj->getPrefix() . 'tokenLifetime'], '/')) : false;
            //$newParams[$settingsObj->getPrefix().'tokenFieldCode'] = !empty($paramsData[$settingsObj->getPrefix() . 'tokenFieldCode']) ? str_replace(' ', '', trim($paramsData[$settingsObj->getPrefix() . 'tokenFieldCode'], '/')) : false;
            
            break;
        }

        foreach ($newParams as $key => $value) {
          if (mb_stripos($key, $settingsObj->getPrefix()) !== false) {
            $code = str_replace($settingsObj->getPrefix(), '', $key);
            $this->data[$code] = trim($value);
          }
        }
      }
      private function addTokenData(){
        $settingsObj = Settings::getInstance();
        
        $entity = new \CUserTypeEntity;
        $fieldToken = [
          'ENTITY_ID' => 'USER',
          'FIELD_NAME' => $settingsObj->getTokenField()['code'],
          'XML_ID' => $settingsObj->getTokenField()['code'],
          'USER_TYPE_ID' => 'string',
          'SORT' => 100,
          'MULTIPLE' => 'N',
          'MANDATORY' => 'N',
          'SHOW_FILTER' => 'I',
          'SHOW_IN_LIST' => 'Y',
          'EDIT_IN_LIST' => 'Y',
          'IS_SEARCHABLE' => 'N',
          'SETTINGS' => [
            'SIZE' => 40,
            'ROWS' => 1,
            'REGEXP' => '',
            'MIN_LENGTH' => 0,
            'MAX_LENGTH' => 0,
            'DEFAULT_VALUE' => ''
          ],
          'EDIT_FORM_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenField')
          ],
          'LIST_COLUMN_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenField')
          ],
          'LIST_FILTER_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenField')
          ],
          'HELP_MESSAGE' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenFieldHint', ['#MODULE_NAME#' => Settings::getInstance()->getModule()['name']])
          ],
        ];
        $entity->Add($fieldToken);

        $entity = new \CUserTypeEntity;
        $fieldTokenExpire = [
          'ENTITY_ID' => 'USER',
          'FIELD_NAME' => $settingsObj->getTokenField()['expire']['code'],
          'XML_ID' => $settingsObj->getTokenField()['expire']['code'],
          'USER_TYPE_ID' => 'datetime',
          'SORT' => 100,
          'MULTIPLE' => 'N',
          'MANDATORY' => 'N',
          'SHOW_FILTER' => 'I',
          'SHOW_IN_LIST' => 'Y',
          'EDIT_IN_LIST' => 'Y',
          'IS_SEARCHABLE' => 'N',
          'SETTINGS' => [
            'SIZE' => 40,
            'ROWS' => 1,
            'REGEXP' => '',
            'MIN_LENGTH' => 0,
            'MAX_LENGTH' => 0,
            'DEFAULT_VALUE' => ''
          ],
          'EDIT_FORM_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenFieldExpire')
          ],
          'LIST_COLUMN_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenFieldExpire')
          ],
          'LIST_FILTER_LABEL' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenFieldExpire')
          ],
          'HELP_MESSAGE' => [
            'en' => '',
            'ru' => getMessageModule('XwildRestTokenFieldHint', ['#MODULE_NAME#' => Settings::getInstance()->getModule()['name']])
          ],
        ];
        $entity->Add($fieldTokenExpire);
      }
  
      private function registerEventHandlerForGenerateToken()
      {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        if ($this->getOption('useGenerateTokenRegisterUser')) {
          $eventManager->registerEventHandler('main', 'OnBeforeUserAdd', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserAdd');
          $eventManager->registerEventHandler('main', 'OnBeforeUserRegister', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserRegister');
          $eventManager->registerEventHandler('main', 'OnBeforeUserSimpleRegister', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserSimpleRegister');
        } else {
          $eventManager->unRegisterEventHandler('main', 'OnBeforeUserAdd', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserAdd');
          $eventManager->unRegisterEventHandler('main', 'OnBeforeUserRegister', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserRegister');
          $eventManager->unRegisterEventHandler('main', 'OnBeforeUserSimpleRegister', 'main', '\\Xwild\\Restapi\\Foundation\\Event', 'OnBeforeUserSimpleRegister');
        }
      }

      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Config');
      }

      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
        }

        return self::$instance;
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }