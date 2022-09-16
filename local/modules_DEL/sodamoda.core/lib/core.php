<?php
  
  namespace Sodamoda\Core;
  
  use COption;
  use CCurrency;
  use CCatalogProduct;
  use CFile;
  use CModule;
  use CCatalogDiscount;
  use CCatalogSKU;
  use CPrice;
  use CCurrencyLang;
  use CCurrencyRates;
  use CIBlockElement;
  use CIBlockPriceTools;
  
  class Core
  {
    private static $instance = null;
    
    function init()
    {
      //Handlers::getInstance()->setup();
    }
    
    //base singleton
    public static function getInstance()
    {
      if (null === self::$instance) {
        self::$instance = new self();
      }
      return self::$instance;
    }
    
    private function __clone(){}
    private function __construct(){}
    private function __sleep(){}
    private function __wakeup(){}
  }