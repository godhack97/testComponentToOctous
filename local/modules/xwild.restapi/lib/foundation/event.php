<?php
    namespace Xwild\Restapi\Foundation;

    use Xwild\Restapi\Foundation\Core;

    class Event {
      private static $instance = null;
      
      public function OnProlog() {
        Core::getInstance()->start();
      }
  
      public function OnBeforeUserAdd(&$arFields)
      {
        // Generate token for new user
        if (Config::getInstance()->getOption('useGenerateTokenRegisterUser') && empty($arFields[Settings::getInstance()->getTokenField()])) {
          $token = Helper::getInstance()->generateToken($arFields['ID'], $arFields['LOGIN']);
          $arFields[Settings::getInstance()->getTokenField()] = $token;
          $arFields[Settings::getInstance()->getTokenExpireField()] = Settings::getInstance()->getTokenExpire();
        }
      }
  
      public function OnBeforeUserRegister(&$arFields)
      {
        // Generate token for new user
        if (Config::getInstance()->getOption('useGenerateTokenRegisterUser') && empty($arFields[Settings::getInstance()->getTokenField()])) {
          $token = Helper::getInstance()->generateToken($arFields['ID'], $arFields['LOGIN']);
          $arFields[Settings::getInstance()->getTokenField()] = $token;
          $arFields[Settings::getInstance()->getTokenExpireField()] = Settings::getInstance()->getTokenExpire();
        }
      }
  
      public function OnBeforeUserSimpleRegister(&$arFields)
      {
        // Generate token for new user
        if (Config::getInstance()->getOption('useGenerateTokenRegisterUser') && empty($arFields[Settings::getInstance()->getTokenField()])) {
          $token = Helper::getInstance()->generateToken($arFields['ID'], $arFields['LOGIN']);
          $arFields[Settings::getInstance()->getTokenField()] = $token;
          $arFields[Settings::getInstance()->getTokenExpireField()] = Settings::getInstance()->getTokenExpire();
        }
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