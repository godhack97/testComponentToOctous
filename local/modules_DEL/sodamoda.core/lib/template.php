<?php
	namespace Sodamoda\Core;

	use Bitrix\Main\Page\Asset;
	use Bitrix\Main\Config\Option;

    class Template {
		private static $instance = null;
		private $templatePath = SITE_TEMPLATE_PATH;
		private $resourcePath = '/local/resource';
		private $assets = null;
		private $counters = null;
		public $moduleID = 'sodamoda.core';

		function registerExtends(){}

        public function config(){}

        //helper func
        public function techMessage(){
            ob_start();
            ?>
            <style>
                .warning-work{
                    margin: 0 0 .375rem;
                    font-family: "DIN Pro", Arial, sans-serif;
                    font-size: 1.125rem;
                    line-height: 1.278;
                    color: #ffffff;
                    background-color: #b7243b;
                    padding: 15px 20px;
                    text-align: center;
                    border-top: 2px solid #fff;
                    display: <?=(Option::get($this->moduleID, 'tech_show') ? 'block' : 'none');?>
                }
            </style>
            <div class="warning-work"><?=Option::get($this->moduleID, 'tech_message');?></div>
            <?php
            $code = ob_get_contents();
            ob_end_clean();
            return $code;
        }
      

        //base singleton
		public static function getInstance(){
			if (null === self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}
		private function __clone(){}
		private function __construct(){}
		private function __sleep(){}
		private function __wakeup(){}
	}
