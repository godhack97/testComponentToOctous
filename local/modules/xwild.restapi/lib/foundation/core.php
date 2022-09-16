<?php
    namespace Xwild\Restapi\Foundation;

    use Bitrix\Main\EventManager;
    use Bitrix\Main\ObjectPropertyException;
    use Bitrix\Main\SystemException;

    class Core {
      private static $instance = null;

      public function run(): bool
      {
        if (!$this->checkAbilityRun()) {
          return false;
        }
        
        //if (defined('ADMIN_SECTION') || !Config::getInstance()->getOption('useRestApi') || !$this->isPath()){
        //  return false;
        //}

        if (Config::getInstance()->getOption('useLateStart')) {
          EventManager::getInstance()->addEventHandler('main', 'OnPageStart', ['\\Xwild\\Restapi\\Event', 'OnProlog']);
        }
        else {
          $this->start();
        }
        return true;
      }
      
      private function checkAbilityRun(): bool
      {
        if (defined('ADMIN_SECTION') || mb_strpos($_SERVER['REQUEST_URI'], 'bitrix/') !== false || !Config::getInstance()->getOption('useRestApi') || !Config::getInstance()->getOption('pathRestApi')) {
          return false;
        }
        if (Config::getInstance()->getOption('siteList') && defined('SITE_ID') === true) {
          $siteList = explode('|', Config::getInstance()->getOption('siteList'));
          if (!in_array(SITE_ID, $siteList)) {
            return false;
          }
        }
        if (Config::getInstance()->getOption('pathRestApi') === Helper::getInstance()->ROOT()) {
          return true;
        }
        
        $requestUri = $_SERVER['REQUEST_URI'];
        if (defined('SITE_DIR') === true && SITE_DIR !== '/') {
          $requestUri = str_replace(SITE_DIR, '', $requestUri);
        }
        $requestUri = explode('/', trim($requestUri, '/'))[0];
        if (Config::getInstance()->getOption('pathRestApi') === $requestUri) {
          return true;
        }
        return false;
      }
      
      public function start(){
        $this->constants();
        $this->header();
        $this->security();
        $this->controller();
        die;
      }
      //private function isPath() {
      //    return Config::getInstance()->getOption('pathRestApi') && (Config::getInstance()->getOption('pathRestApi') === Helper::getInstance()->ROOT() || Config::getInstance()->getOption('pathRestApi') === explode('/', trim($_SERVER['REQUEST_URI'], '/'))[0]);
      //}
      private function constants(){
        define('SM_SAFE_MODE', true);
        define('PERFMON_STOP', true);
        define('PUBLIC_AJAX_MODE', true);
        define('STOP_STATISTICS', true);
        define('NO_AGENT_STATISTIC', 'Y');
        define('NO_AGENT_CHECK', true);
        define('NO_KEEP_STATISTIC', true);
        define('DisableEventsCheck', true);
      }
      private function header(){
        header($_SERVER['SERVER_PROTOCOL'] . ' 200');
        header('Copyright: 2019-' . date('Y') . ', ' . Settings::getInstance()->getAuthor()['company'] . ', ' . Settings::getInstance()->getAuthor()['website']);
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Authorization, Authorization-Token, Authorization-Login, Authorization-Password');
        if ($_SERVER['HTTP_ORIGIN'] && Config::getInstance()->getOption('useCorsFilter')) {
          $list = Config::getInstance()->getOption('corsListDomains');
          if (strpos($list, '*') !== false) {
            header('Access-Control-Allow-Origin: *');
          }
          else {
            $list = str_replace(["\n", "\r"], '', $list);
            $list = explode(';', $list);
            if (in_array($_SERVER['HTTP_ORIGIN'], $list)) {
              header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            }
          }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
          header('Access-Control-Max-Age: 604800');
          Response::getInstance()->ok();
        }
      }
      private function security(){
        // check controller
        $result = Security::getInstance()->hasController();
        if ($result['result'] === false){
          Response::getInstance()->badRequest($result['message'] ?? '');
        }
        // check content type
        $result = Security::getInstance()->isValidContentType();
        if ($result['result'] === false){
          Response::getInstance()->badRequest($result['message'] ?? '');
        }
        // check active
        $result = Security::getInstance()->isActive();
        if ($result['result'] === false){
          Response::getInstance()->requestedHostUnavailable($result['message'] ?? '');
        }
        // check parameters
        if (!Security::getInstance()->isValidParameters()){
          Response::getInstance()->badRequest(Security::getInstance()->getErrors());
        }
        // check authorization
        try {
          $result = Security::getInstance()->isAuthorized();
          if ($result['result'] === false) {
            Response::getInstance()->unauthorized($result['message'] ?? '');
          }
        }
        catch (ObjectPropertyException | SystemException $e) {}
        //if (!Security::getInstance()->isAuthorized()){
        //  Response::getInstance()->unauthorized();
        //}
      }
      private function controller(){
        Controller::getInstance()->run();
      }
      public function getDB(){
        return \Bitrix\Main\Application::getConnection();
      }

      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
        }

        return self::$instance;
      }
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Core');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }