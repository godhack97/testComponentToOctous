<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\Data\Cache as CacheBX;

    class Cache {
        private static $instance = null;
        private static $cacheObj = null;

        public function getCache($id, $ttl = 86400, $dir = false){
            return $this->getObjCache()->initCache($ttl, $id, $dir) ? $this->getObjCache()->getVars() : false;
        }
        public function setCache($data){
            if($this->getObjCache()->startDataCache()){
                $this->getObjCache()->endDataCache($data);
            }
        }
        public function clearCache($dir){
            \BXClearCache(true, $dir);
        }

        public function getObjCache(){
            return self::$cacheObj;
        }

        //base singleton
        public static function getInstance(): Cache{
            if(null === self::$instance){
                self::$instance = new self();
                self::$cacheObj = CacheBX::createInstance();
            }

            return self::$instance;
        }
        
        public function __call($name, $arguments) {
          Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Cache');
        }
        private function __clone(){}
        private function __construct(){}
        private function __sleep(){}
        private function __wakeup(){}
    }