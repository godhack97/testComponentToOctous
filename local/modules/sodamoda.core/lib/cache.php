<?php
	namespace Sodamoda\Core;

	use Bitrix\Main\Loader;
	
	class Cache{
		private static $instance = null;

		function setCache($timeSeconds, $cacheId, $callback, $arCallbackParams = '') {
			$cache = \Bitrix\Main\Data\Cache::createInstance(); // получаем экземпляр класса
			$cachePath = '/'.SITE_ID.'/'.$cacheId;
			if ($cache->initCache($timeSeconds, $cacheId, $cachePath)) { // проверяем кеш и задаём настройки
				$vars = $cache->getVars(); // достаем переменные из кеша
				$result = $vars['result'];
			}
			elseif ($cache->startDataCache()) {
				// некоторые действия...
				$result = $callback($arCallbackParams);
				$cache->endDataCache(array('result' => $result)); // записываем в кеш
			}
			return $result;
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