<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\Type\DateTime;
    use Bitrix\Main\UserTable;
    use Xwild\Restapi\Foundation\Security\Password;

    class Security {
        private static $instance = null;
        private $errors;

        /**
         * Получение настроек безопасности из настроек роута
         *
         * @return mixed
         */
        public function getSecurity() {
            return Route::getInstance()->getRoute()['security'];
        }

        /**
         * Проверка активности роута
         *
         * @return array
         */
        public function isActive() {
          if (Route::getInstance()->isActive() === true) {
            return [
              'result' => true
            ];
          }
          else {
            return [
              'result' => false,
              'message' => 'Route is disabled'
            ];
          }
        }

        /**
         * Проверка наличия контроллера для входящего запроса
         *
         * @return array
         */
        public function hasController() {
          if (Route::getInstance()->getRoute()['controller']) {
            return [
              'result' => true,
            ];
          }
          else {
            return [
              'result' => false,
              'message' => 'Request cannot be processed. Controller is missing.'
            ];
          }
        }

        /**
         * Проверка соответствия типа контента входящего запроса настройкам роута
         *
         * @return array
         */
        public function isValidContentType() {
          if (Route::getInstance()->getRoute()['contentType'] && mb_strpos($_SERVER['CONTENT_TYPE'], Route::getInstance()->getRoute()['contentType']) !== 0) {
            return [
              'result' => false,
              'message' => 'Invalid content type. Must be: ' . Route::getInstance()->getRoute()['contentType']
            ];
          }
          else {
            return [
              'result' => true
            ];
          }
        }

        /**
         * Проверка соответствия параметров входящего запроса настройкам роута
         *
         * @return bool
         */
        public function isValidParameters() {
          if (!Request::getInstance()->getMap()['parameters']){
            return true;
          }
  
          $request = Request::getInstance()->getData();
          $parameters = Request::getInstance()->getMap()['parameters'];
          $errors = [];
  
          $this->processRequestParameters($request, $parameters, $errors);
  
          if (count($errors) > 0) {
            $this->errors['badParameters'] = $errors;
            return false;
          }
  
          // Обновим данные входящего запроса, так как возможно, эти данные были преобразованы в ходе их проверки
          Request::getInstance()->update($request);
  
          return true;
        }
        private function processRequestParameters(&$request, &$parameters, &$errors)
        {
          foreach ($parameters as $code => &$config) {
        
            if (mb_strpos($code, 'separator:') !== false || !isset($config['required']) || $config['required'] !== true) continue;
        
            // Если указаны настройки для нескольких объектов/массивов
            // Тогда проверим каждый объект/массив
            if (isset($config['parameters'][0])) {
              foreach ($request[$code] as $index => $item) {
                $this->processRequestParameters($request[$code][$index], $config['parameters'][0], $errors[$code][$index]);
                // Если ошибок нет, тогда очистим ошибки
                if (count($errors[$code][$index]) === 0) {
                  unset($errors[$code][$index]);
                }
              }
            }
        
            // Проверим наличие параметра
            if (!array_key_exists($code, $request)) {
              $errors[$code]['required'] = $config['required'];
            }
        
            // Проверим характеристики
            if (isset($config['type'])) {
              $isType = 'is' . $config['type'];
              $value =& $request[$code];
          
              if (mb_strtolower($config['type']) === 'array' && count($value) === 0) {
                $value = null;
              }
          
              $result = Helper::getInstance()->$isType($value);
          
              if ($result === false) {
                $errors[$code]['invalidValueType'] = $value;
                $errors[$code]['valueTypeMustBe'] = $config['type'];
              }
              else {
                if ($config['type'] === 'integer' || $config['type'] === 'float') {
                  $value = $result;
                }
              }
            }
        
            if (isset($config['parameters'])) {
              //$this->processRequestParameters($request[$code], $config['parameters'], $errors[$code]);
              try {
                $this->processRequestParameters($request[$code], $config['parameters'], $errors[$code]);
              } catch (\Error $error) { // PHP 7
                //response()->json($error->getMessage());
              } catch (\Exception $error) { // PHP 5
                //response()->json($error->getMessage());
              }
            }
        
            // Если ошибок нет, тогда очистим ошибки
            if (count($errors[$code]) === 0) {
              unset($errors[$code]);
            }
          }
        }
        
        /**
         * Проверка авторизации при доступе к интерфейсу
         *
         * @return bool
         */
        public function isAuthorized() {
            if (!$this->getSecurity()['auth']['required']) return true;
            if (!$this->getSecurity()['auth']['type']) return false;
            if ($this->getSecurity()['auth']['type'] === Helper::getInstance()->token()) return $this->checkAccessByToken();
            if ($this->getSecurity()['auth']['type'] === Helper::getInstance()->login()) return $this->checkAccessByLoginPassword();
            return true;
        }

        /**
         * Проверка доступа к интерфейсу используя токен
         *
         * @return array
         */
        private function checkAccessByToken() {
          if (!Config::getInstance()->getOption('useToken')) {
            return [
              'result' => false,
              'message' => 'You must include the Authorization token in the module settings',
            ];
          }
          if (!Request::getInstance()->getHeader('authorization-token')) {
            return [
              'result' => false,
              'message' => 'Authorization-Token header is missing',
            ];
          }
          $token = Request::getInstance()->getHeader('authorization-token');
          // Token key
          if (Config::getInstance()->getOption('tokenKey')) {
            $token = explode(':', $token);
            $keyword = $token[0];
            $token = $token[1];
            if (Config::getInstance()->getOption('tokenKey') !== $keyword) {
              return [
                'result' => false,
                'message' => 'Invalid token keyword',
              ];
            }
          }
            // Whitelist
          if ($this->getSecurity()['token']['whitelist'] && !in_array($token, $this->getSecurity()['token']['whitelist'])) {
            return [
              'result' => false,
              'message' => 'Token authorization failed',
            ];
          }
          // Filter
          $filter = ['=' . Settings::getInstance()->getTokenField()['code'] => $token];
          // Expire
          if (!$this->getSecurity()['token']['whitelist'] &&
            ((isset($this->getSecurity()['token']['checkExpire']) && $this->getSecurity()['token']['checkExpire'] === true) ||
              (!isset($this->getSecurity()['token']['checkExpire']) && Config::getInstance()->getOption('checkExpireToken')))) {
            $date = new DateTime();
            $filter['>' . settings()->getTokenExpireFieldCode()] = $date->toString();
          }
          // User
          $user = UserTable::getList(['select' => ['*', 'UF_*'], 'filter' => $filter, 'limit' => 1]);
          if ($user->getSelectedRowsCount() === 0) {
            return [
              'result' => false,
              'message' => 'Authorization for the token has failed or expired token',
            ];
          }
          $user = $user->fetchRaw();
          $userGroups = UserTable::getUserGroupIds($user['ID']);
          // Whitelist groups
          if (!$this->getSecurity()['token']['whitelist'] && $this->getSecurity()['group']['whitelist'] && !array_intersect($userGroups, $this->getSecurity()['group']['whitelist'])) {
            return [
              'result' => false,
              'message' => 'Token authorization failed',
            ];
          }
          // Requests
          $this->requestLimit($token, $userGroups);
          // Prepare user
          foreach ($user as &$value) {
            if (is_string($value) && Helper::getInstance()->isSerialized($value)) {
              $value = unserialize($value);
            }
          }
          // Save user
          Request::getInstance()->setData('_user', $user);
          // Return
          return [
            'result' => true
          ];
        }

        /**
         * Проверка доступа к интерфейсу используя логин и пароль
         *
         * @return array
         */
        private function checkAccessByLoginPassword() {
          if (!Config::getInstance()->getOption('useLoginPassword')){
            return [
              'result' => false,
              'message' => 'You must activate authorization by login, password in module settings',
            ];
          }
          $login = false;
          $password = false;
          // Login, password
          if (Request::getInstance()->getHeader('authorization-login') && Request::getInstance()->getHeader('authorization-password')) {
            $login = Request::getInstance()->getHeader('authorization-login');
            $password = Request::getInstance()->getHeader('authorization-password');
          }
          elseif ($_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW']) {
            $login = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
          }
          if (empty($login) || empty($password)) {
            return [
              'result' => false,
              'message' => 'Login or password are missing'
            ];
          }
          // Whitelist
          if ($this->getSecurity()['login']['whitelist'] && !in_array($login, $this->getSecurity()['login']['whitelist'])) {
            return [
              'result' => false,
              'message' => 'Authorization by login, password failed'
            ];
          }
          // User
          if (isset(UserTable::getMap()['PASSWORD'])) {
            $user = UserTable::getList(['select' => ['*', 'UF_*'], 'filter' => ['=LOGIN' => $login], 'limit' => 1]);
          }
          else {
            $user = UserTable::getList(['select' => ['PASSWORD', '*', 'UF_*'], 'filter' => ['=LOGIN' => $login], 'private_fields' => ['PASSWORD'], 'limit' => 1]);
          }
          if ($user->getSelectedRowsCount() === 0) {
            return [
              'result' => false,
              'message' => 'Login not found'
            ];
          }
  
          $user = $user->fetchRaw();
          if (!Password::equals($user['LOGIN'], $password, $user['PASSWORD'])) {
            return [
              'result' => false,
              'message' => 'Access is denied. Invalid password.'
            ];
          }
          if (!isset(UserTable::getMap()['PASSWORD'])) {
            unset($user['PASSWORD']);
          }
          $userGroups = UserTable::getUserGroupIds($user['ID']);
          // Whitelist groups
          if (!$this->getSecurity()['login']['whitelist'] && $this->getSecurity()['group']['whitelist'] && !array_intersect($userGroups, $this->get()['group']['whitelist'])) {
            return [
              'result' => false,
              'message' => 'Authorization by login, password failed'
            ];
          }
          // Requests
          $this->requestLimit($login, $userGroups);
          // Prepare user
          foreach ($user as &$value) {
            if (is_string($value) && Helper::getInstance()->isSerialized($value)) {
              $value = unserialize($value);
            }
          }
          // Save user
          Request::getInstance()->setData('_user', $user);
  
          // Return
          return [
            'result' => true
          ];
        }

        /**
         * Проверка ограничения количества запросов
         *
         * @param $clientId
         * @param $groups
         * @return bool
         */
        private function requestLimit($clientId, $groups) {
            if (!Config::getInstance()->getOption('useRequestLimit')) return true;
            $requestLimitGroup = json_decode(Config::getInstance()->getOption('requestLimit'), true);
            if ($intersect = array_intersect($groups, array_keys($requestLimitGroup))) {
                $tmp = [];
                foreach ($intersect as $id) {
                    $tmp[$id] = $requestLimitGroup[$id];
                }
                $requestLimitGroup = Helper::getInstance()->sortByMultipleKey($tmp, ['number' => 'desc', 'period' => 'desc'])[0];
                $dateFrom = date('Y-m-d H:i:s', strtotime('now-' . $requestLimitGroup['period'] . 'seconds'));
                $dateTo = date('Y-m-d H:i:s');
                $number = Journal::getInstance()->getRequestLimitNumber($clientId, $dateFrom, $dateTo);
                if ($number >= $requestLimitGroup['number']) {
                    Response::getInstance()->tooManyRequests();
                }
                Journal::getInstance()->addData('request-limit', ['CLIENT_ID' => $clientId]);
            }
        }

        /**
         * Используется для определения доступа к странице модуля в админ. разделе
         *
         * @param $groups
         * @param $userGroups
         * @return array
         */
        public function checkAccessByGroups($groups, $userGroups) {
            $groups[] = Helper::getInstance()->adminGroupId();
            return array_intersect($groups, $userGroups);
        }

        public function getErrors() {
            return $this->errors;
        }

        public function __call($name, $arguments) {
            Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Security');
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