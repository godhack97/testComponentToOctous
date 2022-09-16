<?php
    namespace Xwild\Restapi\Foundation;

    use Xwild\Restapi\Foundation\Route;

    class Controller {
      private static $instance = null;
      
      public function run(){
        $routeObj = Route::getInstance();
        
        if (mb_strpos($routeObj->getRoute()['controller'], '.php') !== false) {
          require $routeObj->getRoute()['controller'];
          die;
        }
        $controller = explode('@', $routeObj->getRoute()['controller']);
        $action = $controller[1];
        $controller = $controller[0];
        if (!class_exists($controller)) {
          spl_autoload_register(function ($file) {
            $file = mb_strtolower($file);
            $file = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $file) . '.php';
            if (is_file($file)) {
              require $file;
            }
          });
        }
        $controller = new $controller();
        $controller->$action();
      }

      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
        }
        
        return self::$instance;
      }
  
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Controller');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }