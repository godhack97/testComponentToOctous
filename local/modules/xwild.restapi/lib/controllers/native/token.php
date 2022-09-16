<?php
    namespace Xwild\Restapi\Controllers\Native;

    use Bitrix\Main\UserTable;
    use CUser;
    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Helper;
    use Xwild\Restapi\Foundation\Journal;
    use Xwild\Restapi\Foundation\Request;
    use Xwild\Restapi\Foundation\Response;
    use Xwild\Restapi\Foundation\Settings;

    class Token {
        private $user;

        public function __construct() {
            if (!Config::getInstance()->getOption('useNativeRoute')) {
                Response::getInstance()->json('The use of native routes is disabled in the settings');
            }
        }

        /**
         * Создать токен для пользователя
         */
        public function create() {
            $user = new CUser();
  
            $token = Helper::getInstance()->generateToken($this->user()['ID'], $this->user()['LOGIN']);
            $expire = Settings::getInstance()->getTokenExpire();
            $user->update($this->user()['ID'], [
                Settings::getInstance()->getTokenField()['code'] => $token,  Settings::getInstance()->getTokenExpireField()['code'] => $expire
            ]);
            $response = [
                'token' => $token, 'expire' => $expire
            ];
            Journal::getInstance()->addData('request-response', [
                'request' => Request::getInstance()->getData(), 'response' => $response
            ]);
            Response::getInstance()->created($response);
        }

        /**
         * Получить информацию о токене пользователя
         */
        public function get() {
            $response = [
                'token' => $this->user()[Settings::getInstance()->getTokenField()['code']] ? $this->user()[Settings::getInstance()->getTokenField()['code']] : '',
                'expire' => $this->user()[Settings::getInstance()->getTokenExpireField()['code']] ? $this->user()[Settings::getInstance()->getTokenExpireField()['code']] : ''
            ];
            Journal::getInstance()->addData('request-response', [
                'request' => Request::getInstance()->getData(), 'response' => $response
            ]);
            Response::getInstance()->json($response);
        }

        /**
         * Удалить токен пользователя
         */
        public function delete() {
            $user = new CUser();
            $user->update($this->user()['ID'], [
                Settings::getInstance()->getTokenField()['code'] => '', Settings::getInstance()->getTokenExpireField()['code'] => ''
            ]);
            $response['successful'] = true;
            Journal::getInstance()->addData('request-response', [
                'request' => Request::getInstance()->getData(), 'response' => $response
            ]);
            Response::getInstance()->ok();
        }

        /**
         * Проверка логина и пароля пользователя в базе данных
         * В случае успеха, получаем данные пользователя
         *
         * @return bool
         */
        private function user() {
            if (!$this->user) {
              $select = [
                'ID', 'LOGIN', 'PASSWORD', Settings::getInstance()->getTokenField()['code'], Settings::getInstance()->getTokenExpireField()['code']
              ];
              
              if (isset(UserTable::getMap()['PASSWORD'])) {
                $user = UserTable::getList(['select' => $select, 'filter' => ['=LOGIN' => Request::getInstance()->getData('login')], 'limit' => 1]);
              }
              else {
                $user = UserTable::getList(['select' => $select, 'filter' => ['=LOGIN' => Request::getInstance()->getData('login')], 'private_fields' => ['PASSWORD'], 'limit' => 1]);
              }
              
              if ($user->getSelectedRowsCount() === 0) {
                Journal::getInstance()->addData('request-response', [
                  'request' => Request::getInstance()->getData(),
                  'response' => [
                    'error' => 'Login not found'
                  ]
                ]);
                Response::getInstance()->notFound();
              }
            }
            return $this->user;
        }
    }
