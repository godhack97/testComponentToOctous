<?php
  
  namespace Xwild\Restapi\Controllers\Site;
  
  use Bitrix\Main\Loader;
  use CFile;
  use CIBlockElement;
  use Bitrix\Main\Data\Cache;
  
  use Xwild\Restapi\Foundation\Response;
  
  //use Xwild\Restapi\Cache;
  use Xwild\Restapi\Foundation\Request;
  use Xwild\Restapi\Foundation\Config;
  use Xwild\Restapi\Foundation\Journal;
  
  class Shop
  {
    // Время хранения кэша
    private $cacheTtl = 86400;
    
    // Директория хранения кэша относительно /bitrix/cache
    private $cacheDir = 'site/khl';
    
    public function __construct()
    {
      //if (!Config::getInstance()->getOption('useExampleRoute')) {
      //    Response::getInstance()->json('Showing examples is disabled in the settings');
      //}
      Loader::includeModule('iblock');
      Loader::includeModule('sale');
    }
    
    
    // Пример запроса получения данных из инфоблока
    // http://{domain}/{api}/{route}?iblock_id=1&active=Y&name=Шар&color=Белый&fields=preview_text,detail_text,form,preview_picture&sort=id:asc,name:asc&limit=5&page=1
    
    // Пример запроса добавления элементов в инфоблок
    // http://{domain}/{api}/{route}
    // Элементы передаются в теле запроса
    /* Входящий json
    {
        "iblock_id": 1,
        "items": [
                {
                    "name":"test",
                    "color":"blue",
                    "preview_text":"Описание анонса"
                },
                {
                    "name":"test2",
                    "color":"blue",
                    "detail_text":"Детальное описание"
                },
                {
                    "name":"test3",
                    "color":"blue",
                    "preview_picture_url":"https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/1200px-Apple_logo_black.svg.png"
                }
            ]
    }
    */
    public function _post()
    {
      $response = [];
      
      // Если в запросе используется авторизация
      // Тогда дальше в работе методов можно использовать данные пользователя (формируются при авторизации)
      $userId =& Request::getInstance()->getData('_user')['ID'];
      
      foreach (Request::getInstance()->getData('items') as $item) {
        $element = new CIBlockElement;
        $arFields = [
          'IBLOCK_ID' => Request::getInstance()->getData('iblock_id'),
          'NAME' => $item['name'],
          'MODIFIED_BY' => $userId
        ];
        if ($item['preview_text']) $arFields['PREVIEW_TEXT'] = $item['preview_text'];
        if ($item['detail_text']) $arFields['DETAIL_TEXT'] = $item['detail_text'];
        if ($item['color']) $arFields['PROPERTY_VALUES']['COLOR'] = $item['color'];
        if ($item['form']) $arFields['PROPERTY_VALUES']['FORM'] = $item['form'];
        if ($item['preview_picture_url']) $arFields['PREVIEW_PICTURE'] = CFile::MakeFileArray($item['preview_picture_url']);
        if ($id = $element->Add($arFields, false, false, true)) {
          $response['successful'][$id] = [
            'ID' => $id,
            'IBLOCK_ID' => $arFields['IBLOCK_ID'],
            'NAME' => $arFields['NAME'],
          ];
          if ($item['preview_text']) $response['successful'][$id]['PREVIEW_TEXT'] = $item['preview_text'];
          if ($item['detail_text']) $response['successful'][$id]['DETAIL_TEXT'] = $item['detail_text'];
          if ($item['color']) $response['successful'][$id]['PROPERTY_COLOR'] = $item['color'];
          if ($item['form']) $response['successful'][$id]['PROPERTY_FORM'] = $item['form'];
        } else {
          $response['error'] = $element->LAST_ERROR;
        }
      }
      // Очистим кэш для текущего роута чтобы актуализировать данные
      if ($response['successful']) {
        Cache::getInstance()->clearCache($this->cacheDir);
      }
      
      Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE);
    }
    
    // Пример запроса обновления данных элемента
    // http://{domain}/{api}/{route}
    /* Входящий json
    {
        "element_id":35,
        "preview_text":"Описание анонса"
    }
    */
    public function _put()
    {
      $response = [];
      $arFields = [];
      $userId =& Request::getInstance()->getData('_user')['ID'];
      if (Request::getInstance()->getData('name')) $arFields['NAME'] = Request::getInstance()->getData('name');
      if (Request::getInstance()->getData('preview_text')) $arFields['PREVIEW_TEXT'] = Request::getInstance()->getData('preview_text');
      if (Request::getInstance()->getData('detail_text')) $arFields['DETAIL_TEXT'] = Request::getInstance()->getData('detail_text');
      if ($arFields) {
        $element = new CIBlockElement;
        $arFields['MODIFIED_BY'] = $userId;
        if ($element->Update(Request::getInstance()->getData('element_id'), $arFields, false, false, true, false)) {
          if ($userId) {
            $response['successful']['MODIFIED_BY'] = $userId;
          }
          if (Request::getInstance()->getData('name')) $response['successful']['NAME'] = Request::getInstance()->getData('name');
          if (Request::getInstance()->getData('preview_text')) $response['successful']['PREVIEW_TEXT'] = Request::getInstance()->getData('preview_text');
          if (Request::getInstance()->getData('detail_text')) $response['successful']['DETAIL_TEXT'] = Request::getInstance()->getData('detail_text');
        } else {
          $response['error'] = $element->LAST_ERROR;
        }
        // Очистим кэш для текущего роута чтобы актуализировать данные
        if ($response['successful']) {
          Cache::getInstance()->clearCache($this->cacheDir);
        }
      }
      Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE);
    }
    
    // Пример запроса удаления элемента
    // http://{domain}/{api}/{route}
    /* Входящий json
    {
        "element_ids": [1,2,4]
    }
    */
    public function _delete()
    {
      if (is_array(Request::getInstance()->getData('element_ids'))) {
        foreach (Request::getInstance()->getData('element_ids') as $id) {
          CIBlockElement::Delete($id);
        }
      }
      Cache::getInstance()->clear($this->cacheDir);
      $response = [
        'message' => 'Deleted',
        'ids' => Request::getInstance()->getData('element_ids')
      ];
      Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE);
    }
  }