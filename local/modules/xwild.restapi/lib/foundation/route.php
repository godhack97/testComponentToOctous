<?php
    namespace Xwild\Restapi\Foundation;
    
    class Route {
      private static $instance = null;
      private static $path;
      private static $route;
  
      public function getRoute(){
        return self::$route;
      }
      public function getPath(){
        return self::$path;
      }
      public function isActive(){
        return !($this->getRoute()['active'] === false);
      }
  
      public static function checkRoutApi($path, $route) {
        $path = trim($path, '/');
        $route = trim($route, '/');
        if (strpos($route, '{{') === false){
          return $path === $route ? true : false;
        }
        $path = explode('/', $path);
        $route = explode('/', $route);
    
        if (count($path) !== count($route)){
          return false;
        }
        $rsCheckedRoute = [];
    
        foreach ($route as $fromKey => $fromData) {
          if (strpos($fromData, '{{') === false) {
            if ($fromData === $path[$fromKey]) {
              continue;
            }
            else {
              return false;
            }
          }
          if (!isset($path[$fromKey])){
            return false;
          }
          $fromData = str_replace(['{{', '}}'], '', $fromData);
          $rsCheckedRoute[$fromData] = $path[$fromKey];
        }
        return $rsCheckedRoute;
      }
  
      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          $confObj = Config::getInstance();
          self::$instance = new self();
          
          if (SITE_DIR !== '/' && defined('SITE_DIR') === true) {
            self::$path = str_replace(SITE_DIR, '', self::$path);
          }
          
          self::$path = trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');
  
          if ($confObj->getOption('pathRestApi') !== Helper::getInstance()->ROOT()) {
            self::$path = str_replace($confObj->getOption('pathRestApi') . '/', '', self::$path);
          }
          
          $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes';
          $files[$dir] = array_diff(scandir($dir), ['..', '.']);
          if ($confObj->getOption('localRouteMap')) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $confObj->getOption('localRouteMap');
            $files[$dir] = array_diff(scandir($dir), ['..', '.']);
          }
          $requestMethod = isset($_SERVER['REDIRECT_REQUEST_METHOD']) && $_SERVER['REDIRECT_REQUEST_METHOD'] !== $_SERVER['REQUEST_METHOD'] ? $_SERVER['REDIRECT_REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
          foreach ($files as $dir => $items) {
            foreach ($items as $file) {
              $file = $dir . DIRECTORY_SEPARATOR . $file;
              if (is_array($ar = require $file)) {
                foreach ($ar as $type => $routes) {
                  if ($requestMethod === $type) {
                    foreach ($routes as $route => $config) {
                      if ($params = self::checkRoutApi(self::$path, $route)) {
                        self::$route = $config;
                        if (is_array($params)) {
                          self::$route['paramsUri'] = $params;
                        }
                        break;
                      }
                    }
                  }
                }
              }
            }
          }
        }
    
        return self::$instance;
      }
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Route');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }