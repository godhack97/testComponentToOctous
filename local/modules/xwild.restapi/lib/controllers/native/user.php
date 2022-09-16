<?php
    namespace Xwild\Restapi\Controllers\Native;

    use Bitrix\Main\UserTable;
    use CUser;
    use CFile;
    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Journal;
    use Xwild\Restapi\Foundation\Request;
    use Xwild\Restapi\Foundation\Response;
    use Xwild\Restapi\Foundation\Settings;

    class User {
        private $user;

        public function __construct() {
            if (!Config::getInstance()->getOption('useNativeRoute')) {
                Response::getInstance()->json('The use of native routes is disabled in the settings');
            }
        }

        /**
         * Получить данные пользователя по ID, логину или токену
         *
         * @param int $id
         * @return mixed
         */
        private function user($id = 0) {
            if (!$this->user) {
                $filter = [];

                if ($id) {
                    $filter = ['=ID' => $id];
                }
                elseif (Request::getInstance()->getData('id')) {
                    $filter = ['=ID' => Request::getInstance()->getData('id')];
                }
                elseif (Request::getInstance()->getData('login')) {
                    $filter = ['=LOGIN' => Request::getInstance()->getData('login')];
                }
                elseif (Request::getInstance()->getData('token')) {
                    $filter = ['='.Settings::getInstance()->getTokenField()['code'] => Request::getInstance()->getData('token')];
                }

                if ($filter){
                    $this->user = UserTable::getList([
                        'select' => ['*', 'UF_*'], 'filter' => $filter, 'limit' => 1
                    ])->fetchRaw();
                }
                // Завершить процесс
                if (!$this->user['ID']) {
                    Journal::getInstance()->addData('request-response', [
                        'request' => Request::getInstance()->getData(), 'response' => [
                            'error' => 'User not found'
                        ]
                    ]);
                    Response::getInstance()->notFound();
                }
            }
            return $this->user;
        }

        /**
         * Создать нового пользователя
         */
        public function create() {
            $user = new CUser;
            $arFields = array_change_key_case(Request::getInstance()->getData(), CASE_UPPER);
            if ($id = $user->Add($arFields)) {
                $response = ['user' => $this->user($id)];
                Journal::getInstance()->addData('request-response', [
                    'request' => Request::getInstance()->getData(), 'response' => $response
                ]);
                Response::getInstance()->created($response);
            }
            else {
                $response = ['error' => $user->LAST_ERROR];
                Journal::getInstance()->addData('request-response', [
                    'request' => Request::getInstance()->getData(), 'response' => $response
                ]);
                Response::getInstance()->json($response);
            }
        }

        /**
         * Получить данные пользователя по ID, логину или токену
         */
        public function get() {
            $response = $this->user();
            Journal::getInstance()->addData('request-response', [
                'request' => Request::getInstance()->getData(), 'response' => $response
            ]);
            Response::getInstance()->json($response);
        }

        /**
         * Обновить данные пользователя
         */
        public function update() {
            $arFields = array_change_key_case(Request::getInstance()->getData(), CASE_UPPER);
            $arFields = array_intersect_key($arFields, $this->user());
            $user = new CUser;
            $user->Update($this->user()['ID'], $arFields);
            $response = $user->LAST_ERROR ? ['error' => $user->LAST_ERROR] : ['user' => array_merge($this->user(), $arFields)];
            Journal::getInstance()->addData('request-response', [
                'request' => Request::getInstance()->getData(), 'response' => $response
            ]);
            Response::getInstance()->json($response);
        }

        /**
         * Удалить пользователя
         */
        public function delete() {
            global $DB;
            $id = $this->user()['ID'];
            $response = [];
            $strSql = 'SELECT F.ID FROM	b_user U, b_file F WHERE U.ID='.$id.' and (F.ID=U.PERSONAL_PHOTO or F.ID=U.WORK_LOGO)';
            $z = $DB->Query($strSql, false, 'FILE: '.__FILE__.' LINE:'.__LINE__);
            while ($zr = $z->fetch()) {
                CFile::Delete($zr['ID']);
            }
            if (!$DB->Query('DELETE FROM b_user_group WHERE USER_ID='.$id)) {
                $response['error'][] = 'Failed attempt to delete from table: b_user_group';
            }
            if (!$DB->Query('DELETE FROM b_user_digest WHERE USER_ID='.$id)) {
                $response['error'][] = 'Failed attempt to delete from table: b_user_digest';
            }
            if (!$DB->Query('DELETE FROM b_app_password WHERE USER_ID='.$id)) {
                $response['error'][] = 'Failed attempt to delete from table: b_app_password';
            }
            if (!$DB->Query('DELETE FROM b_user WHERE ID='.$id.' AND ID<>1')) {
                $response['error'][] = 'Failed attempt to delete from table: b_app_password';
            }
            if ($response['error']) {
                Journal::getInstance()->addData('request-response', [
                    'request' => Request::getInstance()->getData(), 'response' => $response
                ]);
                Response::getInstance()->json($response);
            }
            else {
                $response['successful'] = true;
                Journal::getInstance()->addData('request-response', [
                    'request' => Request::getInstance()->getData(), 'response' => $response
                ]);
                Response::getInstance()->ok();
            }
        }
    }
