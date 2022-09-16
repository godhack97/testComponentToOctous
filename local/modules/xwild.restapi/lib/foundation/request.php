<?php
    namespace Xwild\Restapi\Foundation;


    class Request {
      private static $instance = null;
  
      private static $params;
      private $headers = null;
      private $ip = null;
  
      /**
       * Получение данных текущего запроса
       *
       * @param string $code
       * @return array | string
       */
      public function getData($code = '') {
        return $code ? self::$params[$code] : self::$params;
      }
  
      /**
       * Сохранение информации в текущий запрос
       *
       * @param $code
       * @param $value
       */
      public function setData($code, $value) {
        self::$params[$code] = $value;
      }
      
      /**
       * Метод для обновления данных запроса
       * Например, после преобразования данных
       *
       * @param $request
       */
      public function update($request)
      {
        self::$params = $request;
      }
  
      /**
       * Получение заголовков текущего запроса
       *
       * @param string $code
       * @return array | string
       */
      public function getHeader($code = '') {
        if ($this->headers === null) {
          if (!function_exists('apache_request_headers')) {
            foreach ($_SERVER as $name => $value) {
              if (substr($name, 0, 5) === 'HTTP_') {
                $key = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))));
                $this->headers[$key] = $value;
              }
            }
          }
          else {
            $this->headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
          }
        }
        return !empty($code) ? $this->headers[$code] : $this->headers;
      }
  
      /**
       * Метод текущего запроса
       *
       * @return mixed
       */
      public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
      }
  
      /**
       * IP-адрес клиента текущего запроса
       *
       * @return mixed
       */
      public function getIp() {
        if ($this->ip === null) {
          if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
          }
          else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
          }
          else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
          }
        }
        return $this->ip;
      }

      /**
       * Путь по которому был совершен текущий запрос
       *
       * @return mixed
       */
      public function getPath() {
        return Route::getInstance()->getPath();
      }
  
      /**
       * Карта роута предназначенная для текущего запроса
       *
       * @return mixed
       */
      public function getMap() {
        return Route::getInstance()->getRoute();
      }
  
      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
          self::$params = $_GET;
          if (strpos($_SERVER['CONTENT_TYPE'], Helper::getInstance()->contentTypeJson()) !== false) {
            $ar = json_decode(file_get_contents('php://input'), true);
            foreach ($ar as $k => $v) {
              self::$params[$k] = $v;
            }
          }
          else {
            if ($_SERVER['REQUEST_METHOD'] === Helper::getInstance()->POST()) {
              self::$params = array_merge(self::$params, $_POST);
            }
            else {
              parse_str(file_get_contents('php://input'), $data);
              self::$params = array_merge(self::$params, $data);
            }
          }
          if (Route::getInstance()->getRoute()['paramsUri']) {
            self::$params = array_merge(self::$params, Route::getInstance()->getRoute()['paramsUri']);
          }
        }
    
        return self::$instance;
      }
  
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Request'.print_r($arguments, 1));
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }