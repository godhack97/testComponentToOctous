<?php
    namespace Xwild\Restapi\Controllers\Native;

    use Xwild\Restapi\Foundation\Config;
    use Xwild\Restapi\Foundation\Journal;
    use Xwild\Restapi\Foundation\Request;
    use Xwild\Restapi\Foundation\Response;

    class Example {
        public function __construct() {
            if (!Config::getInstance()->getOption('useExampleRoute')) {
                Response::getInstance()->json('Showing examples is disabled in the settings');
            }
        }
        public function _get() {
            $this->response(__FUNCTION__);
        }
        public function _post() {
            $this->response(__FUNCTION__);
        }
        public function _put() {
            $this->response(__FUNCTION__);
        }
        public function _delete() {
            $this->response(__FUNCTION__);
        }
        public function _head() {
            $this->response(__FUNCTION__);
        }

        private function response($action) {
            $response = [
                'message' => 'Запрос выполнен успешно', 'date' => date('Y-m-d H:i:s'), 'controller' => __CLASS__,
                'action'  => $action, 'method' => Request::getInstance()->getMethod(), 'header' => Request::getInstance()->getHeader(),
                'request' => Request::getInstance()->getData(), 'server' => $_SERVER
            ];
            Journal::getInstance()->addData('request-response', ['request' => Request::getInstance()->getData(), 'response' => $response]);
            Response::getInstance()->json($response);
        }
    }
