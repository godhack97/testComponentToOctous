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
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Highloadblock\HighloadBlockTable;

    class Mobileapp {
        // Время хранения кэша
        private $cacheTtl = 1800;
        private $activeCache = true;

        // Директория хранения кэша относительно /bitrix/cache
        private $cacheDir = 'site/mobileapp';

        public function __construct() {
            //if (!Config::getInstance()->getOption('useExampleRoute')) {
            //    Response::getInstance()->json('Showing examples is disabled in the settings');
            //}
            Loader::includeModule('iblock');
        }


        // Пример запроса получения данных из инфоблока
        // http://{domain}/{api}/{route}?iblock_id=1&active=Y&name=Шар&color=Белый&fields=preview_text,detail_text,form,preview_picture&sort=id:asc,name:asc&limit=5&page=1
        public function getNewsList() {
            $cache = Cache::createInstance();

            // Дополнительные заголовки ответа - здесь используются только для демонстрации такой возможности
            $headers = [
                //    'Header-One' => 'Value of header one',
                //    'Header-Two' => 'Value of header two',
                //    'Header-Three' => 'Value of header three'
            ];

            // Уникальный ключ для кэша
            $cacheId = __CLASS__ . ':' . __FUNCTION__;

            // Отдадим данные из кэша если они в нем имеются
            // Иначе, получим данные из базы и запишем в кэш
            // Срок кэша - 7 дней
            // Место хранения /bitrix/cache/example/check

            //if ($cache->initCache($this->cacheTtl, $cacheId, $this->cacheDir) && $this->activeCache) {
            //    $response = $cache->getVars();
            //}
            //elseif ($cache->startDataCache()) {
                $response = [];

                $res = CIBlockElement::GetList(
                    ['ACTIVE_FROM' => 'DESC'],
                    ['IBLOCK_ID' => '2','ACTIVE' => 'Y'],
                    false,
                    ["nPageSize" => 50],
                    [],//['ID', 'NAME', 'IBLOCK_ID', 'ACTIVE_FROM', 'SECTION_ID', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL']
                );

                while ($ob = $res->GetNext()) {
                    $ob['PREVIEW_PICTURE'] = \CFile::GetPath($ob['PREVIEW_PICTURE']);
                    $response[] = [
                        'id' => $ob['ID'],
                        'subject' => $ob['NAME'],
                        'created_at' => date('Y-m-d H:i:s', strtotime($ob["ACTIVE_FROM"])),
                        'text' => $ob['PREVIEW_TEXT'],
                        'full_text' => $ob['DETAIL_TEXT'],
                        'banner_url' => 'https://rocketboy.ru'.$ob['PREVIEW_PICTURE'],
                        'original_post_url' => 'https://rocketboy.ru'.$ob['DETAIL_PAGE_URL'],
                    ];
                }
                //$cache->endDataCache($response);
           // }

            // Запишем информацию в Журнал: Запрос/Ответ
            Journal::getInstance()->addData('request-response', ['request' => Request::getInstance()->getData(), 'response' => $response]);

            // Возвращаем результат клиенту
            Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE, $headers);
        }

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
        public function getSectionsList(){
          $siteDomain = 'https://sodamoda.ru';
          $response = [
            'items' => [],
            'page' => 1,
            'total_page' => 1,
            'total_items' => 0,
          ];
          // Дополнительные заголовки ответа - здесь используются только для демонстрации такой возможности
          $headers = [
            //    'Header-One' => 'Value of header one',
            //    'Header-Two' => 'Value of header two',
            //    'Header-Three' => 'Value of header three'
          ];
          $sections = [];
          $rsParentSection = \CIBlockSection::GetList(['sort' => 'asc'], ['IBLOCK_ID' => 2, 'DEPTH_LEVEL' => '1', 'ACTIVE' => 'Y']);
          while ($arParentSection = $rsParentSection->GetNext()) {
            $sections[$arParentSection['ID']] = [
              'id' => $arParentSection['ID'],
              'name' => $arParentSection['NAME'],
              'url' => $siteDomain.$arParentSection['SECTION_PAGE_URL'],
            ];
          }
          
          foreach ($sections as $section){
            $response['items'][] = $section;
          }
          $response['total_items'] = count($response['items']);
          
          // Запишем информацию в Журнал: Запрос/Ответ
          Journal::getInstance()->addData('request-response', ['request' => Request::getInstance()->getData(), 'response' => $response]);
  
          // Возвращаем результат клиенту
          Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE, $headers);
        }
        public function getSectionProductList(){
          $siteDomain = 'https://sodamoda.ru';
          $arImageCrop = Array('width' => 800, 'height' => 1200);
  
          Loader::includeModule('highloadblock');
          Loader::includeModule('catalog');
  
          $hlblock = HighloadBlockTable::getById(2)->fetch();
          $entity = HighloadBlockTable::compileEntity($hlblock);
          $PsuTaskControl = $entity->getDataClass();
          
          $cache = Cache::createInstance();
  
          // Уникальный ключ для кэша
          $cacheId = __CLASS__ . ':' . __FUNCTION__;
          if ($cache->initCache($this->cacheTtl, $cacheId, $this->cacheDir) && $this->activeCache) {
            $rsItemsNew = $cache->getVars();
          }
          elseif ($cache->startDataCache()) {
            $sourceItems = [];
            $parentsListProduct = [];
            $photosList = [];
            $rsParentSection = \CIBlockSection::GetList(['sort' => 'asc'], ['IBLOCK_ID' => 2, 'ID' => Request::getInstance()->getData('section_id'), 'ACTIVE' => 'Y']);
            while ($arParentSection = $rsParentSection->GetNext()) {
              $response['id'] = $arParentSection['ID'];
              $response['name'] = $arParentSection['NAME'];
              $response['url'] = $siteDomain.$arParentSection['SECTION_PAGE_URL'];
              $arFilter = [
                'IBLOCK_ID' => 2,
                'ACTIVE_DATE' => 'Y',
                'ACTIVE' => 'Y',
                'SECTION_ID' => Request::getInstance()->getData('section_id'),
                'INCLUDE_SUBSECTIONS' => 'Y',
              ];
              $rs = \CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'SORT', 'CODE', 'DETAIL_PAGE_URL', 'PROPERTY_CML2_ARTICLE']);
              while ($item = $rs->fetch()) {
                //$item['PROPERTIES'] = $item->GetProperties();
                if(\CCatalogSKU::getExistOffers($item['ID'])[$item['ID']]){
                  $res = \CCatalogSKU::getOffersList($item['ID'], 0, ['CATALOG_AVAILABLE' => 'Y'], ['CODE' => 'CATALOG_PRICE_1'], ['CODE' => ['COLOR', 'SIZE', 'CML2_LINK', 'MORE_PHOTO', 'VIDEO_FILES', 'VIDEO_IMAGE', 'FULL', 'FULL_IMAGE']]);
                  $item['OFFERS'] = $res[$item['ID']];
                  foreach ($item['OFFERS'] as &$offer) {
                    if ($offer['PROPERTIES']['COLOR']['VALUE']) {
                      $offer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
                      $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] = $offer['PROPERTIES']['COLOR']['VALUE'];
                      $reslist = $PsuTaskControl::getList([
                        'filter' => [
                          'UF_XML_ID' => $offer['PROPERTIES']['COLOR']['VALUE'],
                        ],
                      ]);
                      if ($el = $reslist->fetch()) {
                        $offer['PROPERTIES']['COLOR']['VALUE'] = (Loc::getCurrentLang() == 'en') ? $el['UF_NAME_EN'] : $el['UF_NAME'];
                      }
                    }
                  }
                  unset($offer);
                }
                $item['DETAIL_PAGE_URL'] = $siteDomain.\CIBlock::ReplaceDetailUrl($item['DETAIL_PAGE_URL'], ['SECTION_CODE' => $arParentSection['CODE'], 'ELEMENT_CODE' => $item['CODE'], ], true, false);
                $sourceItems[] = $item;
                $parentsListProduct[] = $item['ID'];
              }
    
              if (!empty($parentsListProduct)) {
                $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'SORT', 'DATE_ACTIVE_FROM', 'PROPERTY_*'];
                $arFilter = [
                  'IBLOCK_ID' => 11,
                  'ACTIVE_DATE' => 'Y',
                  'ACTIVE' => 'Y',
                  'PROPERTY_PARENT' => $parentsListProduct,
                  '!PROPERTY_IMAGES' => false
                ];
                $res = \CIBlockElement::GetList([], $arFilter, false, array('nPageSize' => 500), $arSelect);
                while ($ob = $res->GetNextElement()) {
                  $arFields = $ob->GetFields();
                  $arProps = $ob->GetProperties();
                  $arFields = array_merge($arProps, $arFields);
                  $photosList[$arFields['PARENT']['VALUE']][$arFields['COLOR']['VALUE']] = $arFields;
                }
              }
              //$response['items'] = $arParentSection['items'];
            }
  
            foreach ($sourceItems as $arItem) {
              $arOffers = [];
    
              if (!empty($arItem['OFFERS'])) {
                $arSetPhoto = [];
                foreach ($arItem['OFFERS'] as $offer) {
                  $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = false;
        
                  if ($photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] && !$arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]) {
                    $offer['PROPERTIES']['VIDEO_FILES'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_FILES'];
                    $offer['PROPERTIES']['VIDEO_IMAGE'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['VIDEO_IMAGE'];
          
                    $offer['PROPERTIES']['FULL'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL'];
                    $offer['PROPERTIES']['FULL_IMAGE'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['FULL_IMAGE'];
                    $arSetPhoto[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']] = $offer['ID'];
                    $offerFindedPhotos = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['IMAGES']['VALUE'];
                    $offer['SORT'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT'];
                    $offer['SORT_NEW'] = $photosList[$arItem['ID']][$offer['PROPERTIES']['COLOR']['VALUE_XML_ID']]['SORT_NEW']['VALUE'];
          
                    if (!empty($offerFindedPhotos)) {
                      $offer['PROPERTIES']['MORE_PHOTO']['VALUE'] = $offerFindedPhotos;
                    }
                  }
        
                  if (!empty($offer['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
                    $isUse = false;
                    foreach ($arOffers as $arOffer) {
                      if ($arOffer['PROPERTIES']['COLOR']['VALUE_XML_ID'] == $offer['PROPERTIES']['COLOR']['VALUE_XML_ID'] &&
                        $arOffer['PROPERTIES']['CML2_LINK']['VALUE'] == $offer['PROPERTIES']['CML2_LINK']['VALUE']) {
                        $isUse = true;
                      }
                    }
                    if (!$isUse) {
                      $arOffers[] = $offer;
                    }
                  }
                }
      
                if (count($arOffers) < 1) {
                  $rsItemsNew[] = $arItem;
                }
                else {
                  foreach ($arOffers as $arOffer) {
                    if (!empty($arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72']) && !in_array($arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'], $arResult['ORIGINAL_PARAMETERS']['GLOBAL_FILTER']['OFFERS']['=PROPERTY_72'])) {
                      continue;
                    }
          
                    $nameOffer = (Loc::getCurrentLang() == 'en') ? $arItem['PROPERTIES']['ENG_NAIMENOVANIE']['VALUE'] : $arItem['NAME'];
          
                    $arOfferInfo = array(
                      'SORT' => $arOffer['SORT'],
                      'SORT_NEW' => $arOffer['SORT_NEW'],
                      'ARTICLE' => $arItem['PROPERTY_CML2_ARTICLE_VALUE'],
                      'NAME' => trim($nameOffer) . ', ' . $arOffer['PROPERTIES']['COLOR']['VALUE'],
                      'CODE' => $arItem['CODE'],
                      'PRICE' => \CCatalogProduct::GetOptimalPrice($arOffer['ID'])['RESULT_PRICE'],
                      'DETAIL_PICTURE' => $arOffer['PROPERTIES']['MORE_PHOTO']['VALUE'][0],
                      'DETAIL_PAGE_URL' => $arItem['DETAIL_PAGE_URL'] . '?setColor=' . $arOffer['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] . '&offer=' . $arOffer['ID'],
                    );
          
                    $rsItemsNew[] = array_merge($arOffer, $arOfferInfo);
                  }
                }
              }
              else {
                $rsItemsNew[] = $arItem;
              }
            }
            uasort($rsItemsNew, function ($a, $b) {
              //return $a['SORT'] - $b['SORT'];
              if ($a['SORT'] == $b) {
                return 0;
              }
    
              return ($a['SORT'] > $b['SORT']) ? -1 : 1;
            });
            $cache->endDataCache($rsItemsNew);
          }
          $response = [
            'page' => intval(Request::getInstance()->getData('page')),
            'total_page' => 1,
            'total_items' => 0,
          ];
          // Дополнительные заголовки ответа - здесь используются только для демонстрации такой возможности
          $headers = [
            //    'Header-One' => 'Value of header one',
            //    'Header-Two' => 'Value of header two',
            //    'Header-Three' => 'Value of header three'
          ];
          
          foreach (array_slice($rsItemsNew, $response['page'] === 1 ? 0 : ($response['page'] - 1) * 6, 6) as $item) {
            $response['items'][] = [
              'id' => $item['ID'],
              'name' => $item['NAME'],
              'url' => $item['DETAIL_PAGE_URL'],
              'picture' => $siteDomain.\CFile::ResizeImageGet($item['DETAIL_PICTURE'], $arImageCrop, BX_RESIZE_IMAGE_EXACT)['src'],
              'article' => $item['ARTICLE'],
              'price' => $item['PRICE']['BASE_PRICE'],
              'price_old' => $item['PRICE']['DISCOUNT_PRICE'],
              'discount' => $item['PRICE']['DISCOUNT'] > 0,
              'sort' => $item['SORT'],
            ];
          }
          
          $response['total_items'] = count($rsItemsNew);
  
          $listChunk = array_chunk($rsItemsNew, 6);
          $response['total_page'] = count($listChunk);
          // Запишем информацию в Журнал: Запрос/Ответ
          Journal::getInstance()->addData('request-response', ['request' => Request::getInstance()->getData(), 'response' => $response]);
      
          // Возвращаем результат клиенту
          Response::getInstance()->json($response, 200, JSON_UNESCAPED_UNICODE, $headers);
        }
        
        public function _post() {
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
        public function _put() {
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
        public function _delete() {
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