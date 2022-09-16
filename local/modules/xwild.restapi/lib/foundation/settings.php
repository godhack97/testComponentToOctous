<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\Localization\Loc;

    Loc::loadLanguageFile(__FILE__);

    class Settings {
      private static $instance = null;
  
      public function getTokenField(){
        return Config::getInstance()->getOption('tokenFieldCode') ? Config::getInstance()->getOption('tokenFieldCode') : $this->getSettings()['config']['token'];
      }
      public function getTable(){
        return $this->getSettings()['config']['table'];
      }
      public function getTokenExpireField(){
        return $this->getSettings()['config']['token']['expire'];
      }
      public function getPrefix(){
        return $this->getSettings()['config']['prefix'];
      }
      public function getModule(){
        return $this->getSettings()['module'];
      }
      public function getAuthor(){
        return $this->getSettings()['author'];
      }
      public function getPath(){
        return $this->getSettings()['path'];
      }
      public function getFile(){
        return $this->getSettings()['file'];
      }
      public function getTokenExpire() {
        $lifetime = Config::getInstance()->getOption('tokenLifetime') ? Config::getInstance()->getOption('tokenLifetime') . ' seconds' : $this->getSettings()['config']['token']['expire']['lifetime'];
        return date('d.m.Y H:i:s', strtotime('+' . $lifetime));
      }

      public function getSettings(){
        return [
          'module' => [
            'id' => getMessageModule('XwildRestModuleId'),
            'name' => getMessageModule('XwildRestModuleName'),
            'description' => getMessage('XwildRestModuleDescription'),
            'connectionString' => 'if (Bitrix\Main\Loader::includeModule(\'' . getMessage('XwildRestModuleId') . '\')) \Xwild\Restapi\Foundation\Core::getInstance()->run();',
            'version' => '1.0.0',
            'versionDate' => '2020-06-21 12:00:00'
          ],
          'author' => [
            'company' => getMessage('XwildRestAuthorCompany'),
            'name' => getMessage('XwildRestAuthorName'),
            'email' => 'nexsysis@gmail.com',
            'website' => 'https://xwild-dev.ru/'
          ],
          'partner' => [
            'name' => getMessage('XwildRestAuthorName'),
            'website' => 'https://xwild-dev.ru/'
          ],
          'config' => [
            'prefix' => 'parameter:',
            'token' => [
              'code' => 'UF_REST_API_TOKEN',
              'expire' => [
                'code' => 'UF_API_TOKEN_EXPIRE',
                'lifetime' => '3 years'
              ]
            ],
            'table' => [
              'request-response' => getMessage('XwildRestTablePrefix') . 'request_response',
              'request-limit' => getMessage('XwildRestTablePrefix') . 'request_limit'
            ]
          ],
          'path' => [
            'reviews' => 'http://marketplace.1c-bitrix.ru/solutions/' . getMessage('XwildRestModuleId') . '/#tab-rating-link',
            'marketplace' => 'http://marketplace.1c-bitrix.ru/solutions/' . getMessage('XwildRestModuleId') . '/'
          ],
          'file' => [
            'native' => '_native.php',
            'example' => '_example.php',
          ],
        ];
      }
  
      //base singleton
      public static function getInstance(): Settings{
        if(null === self::$instance){
          self::$instance = new self();
        }
    
        return self::$instance;
      }
  
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Settings');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }