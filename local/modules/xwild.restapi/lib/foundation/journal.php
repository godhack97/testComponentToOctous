<?php
    namespace Xwild\Restapi\Foundation;


    class Journal {
      private static $instance = null;
  
      public function addData($journal, $data = []) {
        switch ($journal) {
          case 'request-response':
            $this->addRequestResponse($data['request'], $data['response']);
            break;
          case 'request-limit':
            $this->addRequestLimit($data);
            break;
        }
      }
      public function getData($type, $filter = [], $sort = []) {
        switch ($type) {
          case 'request-response':
            return $this->getRequestResponse($filter, $sort);
          case 'request-limit':
            return $this->getRequestLimit($filter, $sort);
          default:
            return false;
        }
      }
      public function deleteData($type, $ids) {
        switch ($type) {
          case 'request-response':
            $this->deleteRequestResponse($ids);
            break;
        }
        switch ($type) {
          case 'request-limit':
            $this->deleteRequestLimit($ids);
            break;
        }
      }
  
      // Request-Response
      private function addRequestResponse($request = [], $response = []) {
        if (!Config::getInstance()->getOption('useJournal')) return false;
        $clientId = '';
        if (Security::getInstance()->getSecurity()['auth']['type'] === Helper::getInstance()->login()) {
          $clientId = Request::getInstance()->getHeader('authorization-login');
        }
        else {
          if (Request::getInstance()->getHeader('authorization-token')) {
            $clientId = Request::getInstance()->getHeader('authorization-token');
            if (Config::getInstance()->getOption('tokenKey')) {
              $clientId = explode(':', Request::getInstance()->getHeader('authorization-token'));
              $clientId = $clientId[1];
            }
          }
        }
        if (!$request) {
          $request = [
            'path' => Request::getInstance()->getPath(),
            'params' => Request::getInstance()->getData(),
            'map' => Request::getInstance()->getMap(),
          ];
        }
    
        $data = [
          'METHOD' => Request::getInstance()->getMethod(),
          'IP' => Request::getInstance()->getIp(),
          'CLIENT_ID' => $clientId,
          'DATETIME' => new \Bitrix\Main\Type\DateTime(),
          'REQUEST' => json_encode($request, JSON_UNESCAPED_UNICODE)
        ];
        if ($response) $data['RESPONSE'] = json_encode($response, JSON_UNESCAPED_UNICODE);
        Core::getInstance()->getDB()->add(Settings::getInstance()->getTable()['request-response'], $data);
      }
      private function getRequestResponse($filter = [], $sort = []) {
        $sql = 'SELECT * FROM ' . Settings::getInstance()->getTable()['request-response'];
        if (is_array($filter)) foreach ($filter as $k => $v) if (empty($v)) unset($filter[$k]);
        if ($filter) {
          $sql .= ' WHERE';
          if (!$filter['DATETIME_FROM']) {
            $filter['DATETIME_FROM'] = '2015-12-31';
          }
          if (!$filter['DATETIME_TO']) {
            $filter['DATETIME_TO'] = '2100-12-31';
          }
          $sql .= ' (DATETIME BETWEEN "' . date_format(date_create($filter['DATETIME_FROM']), 'Y-m-d 00:00:00') . '" AND "' . date_format(date_create($filter['DATETIME_TO']), 'Y-m-d 23:59:59') . '")';
          unset($filter['DATETIME_FROM'], $filter['DATETIME_TO']);
          foreach ($filter as $field => $value) {
            $sql .= ' AND ' . $field . '="' . $value . '"';
          }
        }
        if ($sort && $sort['field']) $sql .= ' ORDER BY ' . $sort['field'] . ' ' . $sort['order'];
        return Core::getInstance()->getDB()->query($sql);
      }
      private function deleteRequestResponse($ids) {
        if ($ids === '*') {
          Core::getInstance()->getDB()->truncateTable(Settings::getInstance()->getTable()['request-response']);
        }
        elseif (is_array($ids)) {
          Core::getInstance()->getDB()->queryExecute('DELETE FROM ' . Settings::getInstance()->getTable()['request-response'] . ' WHERE ID IN(' . implode(',', $ids) . ')');
        }
      }
  
      // Request-Limit
      private function addRequestLimit($data = []) {
        $data = [
          'CLIENT_ID' => $data['CLIENT_ID'],
          'DATETIME' => new \Bitrix\Main\Type\DateTime()
        ];
        Core::getInstance()->getDB()->add(Settings::getInstance()->getTable()['request-limit'], $data);
      }
      private function getRequestLimit($filter = [], $sort = []) {
        $sql = 'SELECT * FROM ' . Settings::getInstance()->getTable()['request-limit'];
        if (is_array($filter)) foreach ($filter as $k => $v) if (empty($v)) unset($filter[$k]);
        if ($filter) {
          $sql .= ' WHERE';
          if (!$filter['DATETIME_FROM']) {
            $filter['DATETIME_FROM'] = '2000-12-31';
          }
          if (!$filter['DATETIME_TO']) {
            $filter['DATETIME_TO'] = '2100-12-31';
          }
          $sql .= ' (DATETIME BETWEEN "' . date_format(date_create($filter['DATETIME_FROM']), 'Y-m-d 00:00:00') . '" AND "' . date_format(date_create($filter['DATETIME_TO']), 'Y-m-d 23:59:59') . '")';
          unset($filter['DATETIME_FROM'], $filter['DATETIME_TO']);
          foreach ($filter as $field => $value) {
            $sql .= ' AND ' . $field . '="' . $value . '"';
          }
        }
        if ($sort && $sort['field']) $sql .= ' ORDER BY ' . $sort['field'] . ' ' . $sort['order'];
        return Core::getInstance()->getDB()->query($sql);
      }
      private function deleteRequestLimit($ids) {
        if ($ids === '*') {
          Core::getInstance()->getDB()->truncateTable(Settings::getInstance()->getTable()['request-limit']);
        }
        elseif (is_array($ids)) {
          Core::getInstance()->getDB()->queryExecute('DELETE FROM ' . Settings::getInstance()->getTable()['request-limit'] . ' WHERE ID IN(' . implode(',', $ids) . ')');
        }
      }
      public function getRequestLimitNumber($clientId, $dateForm, $dateTo) {
        return Core::getInstance()->getDB()->query('SELECT COUNT(CLIENT_ID) as COUNT FROM ' . Settings::getInstance()->getTable()['request-limit'] . ' WHERE CLIENT_ID="' . $clientId . '" AND (DATETIME BETWEEN "' . $dateForm . '" AND "' . $dateTo . '")')->fetchRaw()['COUNT'];
      }
  
      //base singleton
      public static function getInstance(){
        if(null === self::$instance){
          self::$instance = new self();
        }
    
        return self::$instance;
      }
  
      public function __call($name, $arguments) {
        Response::getInstance()->internalServerError('Method \'' . $name . '\' is not defined Journal');
      }
      private function __clone(){}
      private function __construct(){}
      private function __sleep(){}
      private function __wakeup(){}
    }