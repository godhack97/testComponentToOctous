<?php
  namespace Sodamoda\Core\Partners\Personal;
  
  use Bitrix\Main\Config\Option;
  use Bitrix\Main\Loader;
  use Bitrix\Highloadblock\HighloadBlockTable;
  use Bitrix\Main\Localization\Loc;
  use Bitrix\Main\Diag\Debug;
  
  class Product{
    private static $instance = null;
    private $userID;
    private $isManage;
    private $requestData = [];
    private $productFields = [];
    private $materials = [];
    private $updateData = [];
    private $colors = [
      'sizes' => [],
      'photos' => [],
    ];
    private $translateParams = [
      'max_len' => '100', 				// обрезает символьный код до 100 символов
      'change_case' => 'L', 				// буквы преобразуются к нижнему регистру
      'replace_space' => '_', 			// меняем пробелы на нижнее подчеркивание
      'replace_other' => '_', 			// меняем левые символы на нижнее подчеркивание
      'delete_repeat_replace' => 'true', 	// удаляем повторяющиеся нижние подчеркивания
      'use_google' => 'true'
    ];
    private $uploadPath;
    private $uploadAbsolutePath;
    
    public function productAction($requestData, $requestFiles = []){
      global $USER;
      
      $this->isManage = \CSite::InGroup(explode(',', Option::get('sodamoda.core', 'group_manage_lists')));
  
      $this->userID = $USER->GetID();
      $this->userUploadDir();
      $this->prepareRequestData($requestData);
      $this->prepareRequestFiles($requestFiles);
  
      if(count($this->colors['sizes']) < 1){
        die(json_encode(['status' => false, 'validation_messages' => 'Нельзя создать товар без указания размеров! Укажите все имеющиеся размеры для данного товара.']));
      }
      $this->checkSizesList();
      
      if(isset($this->materials[0])) {
        unset($this->materials[0]);
      }
      $this->checkMaterialsList();
      if(empty($this->requestData['product']['id'])){
        $this->checkColorPhoto();
      }
      
      $this->prepareIblockFields();
      
      if(isset($this->requestData['product']['id']) && !empty($this->requestData['product']['id'])){
        if(!$this->isManage){
          $this->checkOwnerProduct();
        }
        $rsAction = $this->updateProduct();
      }
      else{
        $rsAction = $this->createProduct();
      }
  
      return json_encode(['status' => $rsAction['status'], 'message' => $rsAction['message'], 'validation_messages' => '']);
    }
    public function createProduct(): array {
      $iblockObj = new \CIBlockElement;
      $arErrors = [];
      
      $this->productFields['CODE'] = \CUtil::translit($this->requestData['product']['ru']['name'], 'ru', $this->translateParams);
      
      $productID = $iblockObj->Add($this->productFields);
      if($productID){
        // Создадим новые материалы по входящим данными
        $rsMaterials = $this->createMaterials($productID);
        if($rsMaterials !== true){
          $arErrors[] = 'Ошибка создания материалов для нового товара ID '.$productID.': '.json_encode($rsMaterials);
        }
  
        // Создадим новые фото по входящим данными
        $rsColors = $this->createColors($productID);
        if($rsColors !== true){
          $arErrors[] = 'Ошибка создания цветов для нового товара ID '.$productID.': '.json_encode($rsColors);
        }
  
        // Создадим новые оферы по входящим данными
        $rsOffers = $this->createOffers($productID);
        if($rsOffers !== true){
          $arErrors[] = 'Ошибка создания оферов для нового товара ID '.$productID.': '.json_encode($rsOffers);
        }
  
        // Создадим цену товара
        //$upPriceFlag = updatePrice($productID, 1, $price);
        //$upPriceFlag = \CPrice::SetBasePrice($productID, $this->requestData['product']['ru']['price'], 'RUB');
        //$upPriceFlagEn = $this->updatePrice($productID, 6, $this->requestData['product']['en']['price']);
  
        //if(!$upPriceFlag || !$upPriceFlagEn){
        //  $arErrors[] = 'Ошибка изменения цены для нового товара ID '.$productID;
        //}
  
        if(count($arErrors) > 0) {
          return [
            'message' => json_encode($arErrors),
            'status' => false,
          ];
        }
        else{
          return [
            'message' => '',
            'status' => true,
          ];
        }
      }
      else{
        $arErrors[] = $iblockObj->LAST_ERROR;
        
        return [
          'message' => json_encode($arErrors),
          'status' => false,
        ];
      }
    }
    public function createMaterials($productID){
      $iblockObj = new \CIBlockElement;
      $rsErrors = [];
      foreach ($this->materials as $mID => $mPercent){
        $arFields = [
          'IBLOCK_ID' => COMPOSITION_ID,
          'NAME' => $this->requestData['product']['ru']['name'],
          'ACTIVE' => 'Y',
          'PROPERTY_VALUES'	=> [
            'MATERIAL'  => $mID,
            'VALUE'  => $mPercent,
            'PARENT' => $productID
          ]
        ];
  
        $newID = $iblockObj->Add($arFields);
  
        if(!$newID){
          $rsErrors[] = $iblockObj->LAST_ERROR;
        }
      }
      
      return count($rsErrors) > 0 ? $rsErrors : true;
    }
    public function createColors($productID){
      $iblockObj = new \CIBlockElement;
      $rsErrors = [];
      foreach ($this->colors['photos'] as $cID => $cPhotos){
        $addedPhotos = [];
        foreach($cPhotos as $photoPath){
          $addedPhotos[] = ['VALUE' => \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$photoPath), 'DESCRIPTION' => ''];
        }
  
        $arFields = [
          'IBLOCK_ID' => COLORS_ID,
          'NAME'  => $this->requestData['product']['ru']['name'],
          'ACTIVE' => $this->requestData['colors']['active'][$cID],
          'PROPERTY_VALUES'	=> [
            'COLOR' 		=> $cID,
            'IMAGES' 		=> $addedPhotos,
            'CML2_ARTICLE'	=> $this->requestData['product']['article'],
            'PARENT' 		=> $productID
          ]
        ];
        $newID = $iblockObj->Add($arFields);
    
        if(!$newID){
          $rsErrors[] = $iblockObj->LAST_ERROR;
        }
      }
  
      return count($rsErrors) > 0 ? $rsErrors : true;
    }
    public function createOffers($productID){
      $iblockObj = new \CIBlockElement;
      $rsErrors = [];
      //$this->getHLObject()
  
      foreach ($this->colors['sizes'] as $cID => $cSizes){
        // Найдём имя цвета
        $rsItemColor = $this->getHLObject()::getList(['filter' => ['UF_XML_ID' => $cID]])->Fetch();
        $colorName = $rsItemColor['UF_NAME'];
        
        if(isset($_SESSION['SESS_COUNTRY_ID']) && $_SESSION['SESS_COUNTRY_ID'] != 'RU'){
          $colorName = $rsItemColor['UF_NAME_EN'];
        }
  
        foreach($cSizes as $sizeId) {
          // Найдём значение размера
          $sizesObj = \CIBlockPropertyEnum::GetList(
            ['VALUE' => 'ASC' , 'ID' => 'DESC'],
            ['IBLOCK_ID' => CATALOG_OFFERS_ID, 'CODE' => 'SIZES', 'ID' => $sizeId]
          );
          $item = $sizesObj->GetNext();
  
          $arFields = [
            'IBLOCK_ID' => CATALOG_OFFERS_ID,
            'NAME' => $this->requestData['product']['ru']['name'].' ('.$colorName.' ('.$item['VALUE'].'))',
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES'	=> [
              'COLOR' => $cID,
              'SIZES' => $sizeId,
              'CML2_LINK' => $productID
            ]
          ];
    
          // Добавление элемента ТП в инфоблок
          $newID = $iblockObj->Add($arFields);
  
          if(!$newID){
            $rsErrors['add_tp_element'][] = $iblockObj->LAST_ERROR;
          }
  
          $offerFields = [
            'ID' => $newID,
            'VAT_INCLUDED' => 'Y', 		//НДС входит в стоимость
          ];
          $propRes = \CCatalogProduct::Add($offerFields, false);
          if(!$propRes){
            $rsErrors['add_tp_prop'][] = 'Ошибка добавления свойств для ТП ID '.$newID;
          }
    
          // Добавление базовой цены ТП
          $rsPrice = \CPrice::SetBasePrice($newID, $this->requestData['product']['ru']['price'], 'RUB');
          if(!$rsPrice){
            $rsErrors['add_tp_price'][] = 'Ошибка добавления базовой цены для ТП ID '.$newID;
          }
    
          // Добавление долларовой цены ТП
          $arFieldsPrice = ['PRODUCT_ID' => $newID, 'CATALOG_GROUP_ID' => 6, 'PRICE' => $this->requestData['product']['en']['price'], 'CURRENCY' => 'USD'];
          $priceEnRes = \CPrice::Add($arFieldsPrice);
    
          if(!$priceEnRes){
            $rsErrors['add_tp_price_en'][] = 'Ошибка добавления долларовой цены для ТП ID '.$newID;
          }
        }
      }
  
      return count($rsErrors) > 0 ? $rsErrors : true;
    }
    
    public function updateProduct(): array {
      $iblockObj = new \CIBlockElement;
      $arErrors = [];
      
      $materialObj = \CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => COMPOSITION_ID, 'PROPERTY_PARENT' => $this->requestData['product']['id']],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_MATERIAL', 'PROPERTY_VALUE']
      );
      while($arMaterial = $materialObj->GetNext()){
        $this->updateData['materials'][] = $arMaterial;
      }
  
      $rsMaterials = $this->updateMaterials();
      if($rsMaterials !== true){
        $arErrors[] = 'Ошибка создания материалов для старого товара ID '.$this->requestData['product']['id'].': '.json_encode($rsMaterials);
      }
      
      $rsColors = $this->updateColors();
      if($rsColors !== true){
        $arErrors[] = 'Ошибка создания цветов для старого товара ID '.$this->requestData['product']['id'].': '.json_encode($rsColors);
      }
  
      // Удалим все оферы из CATALOG_OFFERS_ID, где PROPERTY_CML2_LINK - текущий товар
      $offersObj = \CIBlockElement::GetList(
        [], ['IBLOCK_ID' => CATALOG_OFFERS_ID, 'PROPERTY_CML2_LINK' => $this->requestData['product']['id']],
        false, false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_COLOR', 'PROPERTY_SIZES']
      );
      while($arOffer = $offersObj->GetNext()){
        $this->updateData['offers'][] = $arOffer;
      }
  
      // Создадим новые оферы по входящим данными
      $rsOffers = $this->updateOffers();
      if($rsOffers !== true){
        $arErrors[] = 'Ошибка создания оферов для старого товара ID '.$this->requestData['product']['id'].': '.json_encode($rsOffers);
      }
  
      // Получим цену товара, сравним и поменяем, если не равна
      $upPriceFlag = $this->updatePrice($this->requestData['product']['id'], 1, $this->requestData['product']['ru']['price']);
      $upPriceFlagEn = $this->updatePrice($this->requestData['product']['id'], 6, $this->requestData['product']['en']['price']);
  
      if(!$upPriceFlag || !$upPriceFlagEn){
        $arErrors[] = 'Ошибка изменения цены для старого товара ID '.$this->requestData['product']['id'];
      }
  
      // АПДЕЙТ
      $result = $iblockObj->Update($this->requestData['product']['id'], $this->productFields);
      
      if(!$result || count($arErrors) > 0) {
          return [
            'message' => json_encode($arErrors),
            'status' => false,
          ];
      }
      else{
        return [
          'message' => '',
          'status' => true,
        ];
      }
    }
    private function updateMaterials(){
      global $USER;
      
      $iblockObj = new \CIBlockElement;
      $rsErrors = [];
      $materialObj = \CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => COMPOSITION_ID, 'PROPERTY_PARENT' => $this->requestData['product']['id']],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_MATERIAL', 'PROPERTY_VALUE']
      );
      while($arMaterial = $materialObj->GetNext()){
        \CIBlockElement::Delete($arMaterial['ID']);
        //$this->updateData['materials'][] = $arMaterial;
      }
  
      return $this->createMaterials($this->requestData['product']['id']);
      //if(count($this->materials) > count($this->updateData['materials'])){
      
      //}
      $isExist = false;
      foreach ($this->materials as $mID => $mPercent) {
        foreach ($this->updateData['materials'] as $material) {
          if ($material['PROPERTY_MATERIAL_VALUE'] == $mID) {
            $isExist = true;
          }
        }
      }
      if($isExist){
        foreach ($this->materials as $mID => $mPercent){
          foreach ($this->updateData['materials'] as $material) {
            if($material['PROPERTY_MATERIAL_VALUE'] == $mID){
              if($material['PROPERTY_VALUE_VALUE'] != $mPercent){
                $PROP[205] = $mPercent;
                $arLoadProductArray = [
                  'MODIFIED_BY' => $USER->GetID(), // элемент изменен текущим пользователем
                  'PROPERTY_VALUES' => $PROP,
                ];
                $updated = $iblockObj->Update($material['ID'], $arLoadProductArray);
                
                if(!$updated){
                  $rsErrors[] = $iblockObj->LAST_ERROR;
                }
              }
            }
          }
        }
      }
      return count($rsErrors) > 0 ? $rsErrors : true;
    }
    private function updateColors(){
      // Удалим все фото из COLORS_ID, где PROPERTY_PARENT - текущий товар
      $photoObj = \CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, 'PROPERTY_PARENT' => $this->requestData['product']['id']], false, false, ['ID', 'IBLOCK_ID']);
      while($arPhoto = $photoObj->GetNext()){
        \CIBlockElement::Delete($arPhoto['ID']);
      }
  
      return $this->createColors($this->requestData['product']['id']);
    }
    private function updateOffers(){
      $iblockObj = new \CIBlockElement;
      $rsErrors = [];
      $updateList = [];
      $existOfferList = [];
      if(count($this->updateData['offers']) > 0){
        foreach ($this->updateData['offers'] as $offer) {
          $existOfferList[$offer['ID']] = [
            'id' => $offer['ID'],
            'color' => $offer['PROPERTY_COLOR_VALUE'],
            'size' => $offer['PROPERTY_SIZES_ENUM_ID'],
            'delete' => true,
          ];
        }
      }
      if(count($existOfferList) > 0){
        foreach ($this->colors['sizes'] as $cID => $cSizes) {
          foreach ($cSizes as $sizeId) {
            if(!isset($updateList[$cID][$sizeId])){
              $updateList[$cID][$sizeId] = [
                'color' => $cID,
                'size' => $sizeId,
                'exist' => false,
              ];
            }
          }
        }
        foreach ($updateList as $colorID => $sizes){
          foreach ($sizes as $sizeID => $value) {
            foreach ($existOfferList as $offer){
              if($offer['color'] === $colorID && intval($offer['size']) === intval($sizeID)){
                $updateList[$colorID][$sizeID]['id'] = $offer['id'];
                $updateList[$colorID][$sizeID]['exist'] = true;
                $existOfferList[$offer['id']]['delete'] = false;
              }
            }
          }
        }
        foreach ($updateList as $colorID => $sizes){
          foreach ($sizes as $sizeID => $value){
            if(!$value['exist']){
              // Найдём имя цвета
              $rsItemColor = $this->getHLObject()::getList(['filter' => ['UF_XML_ID' => $colorID]])->Fetch();
              $colorName = $rsItemColor['UF_NAME'];
  
              if(isset($_SESSION['SESS_COUNTRY_ID']) && $_SESSION['SESS_COUNTRY_ID'] != 'RU'){
                $colorName = $rsItemColor['UF_NAME_EN'];
              }
  
              // Найдём значение размера
              $sizesObj = \CIBlockPropertyEnum::GetList(['VALUE' => 'ASC' , 'ID' => 'DESC'], ['IBLOCK_ID' => CATALOG_OFFERS_ID, 'CODE' => 'SIZES', 'ID' => $sizeID]);
              $item = $sizesObj->GetNext();
  
              $arFields = [
                'IBLOCK_ID' => CATALOG_OFFERS_ID,
                'NAME' => $this->requestData['product']['ru']['name'].' ('.$colorName.' ('.$item['VALUE'].'))',
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES'	=> [
                  'COLOR' => $colorID,
                  'SIZES' => $sizeID,
                  'CML2_LINK' => $this->requestData['product']['id']
                ]
              ];
  
              // Добавление элемента ТП в инфоблок
              $newID = $iblockObj->Add($arFields);
  
              if(!$newID){
                $rsErrors['add_tp_element'][] = $iblockObj->LAST_ERROR;
              }
              $offerFields = [
                'ID' => $newID,
                'VAT_INCLUDED' => 'Y', 		//НДС входит в стоимость
              ];
              $propRes = \CCatalogProduct::Add($offerFields, false);
              if(!$propRes){
                $rsErrors['add_tp_prop'][] = 'Ошибка добавления свойств для ТП ID '.$newID;
              }
  
              // Добавление базовой цены ТП
              $rsPrice = \CPrice::SetBasePrice($newID, $this->requestData['product']['ru']['price'], 'RUB');
              if(!$rsPrice){
                $rsErrors['add_tp_price'][] = 'Ошибка добавления базовой цены для ТП ID '.$newID;
              }
  
              // Добавление долларовой цены ТП
              $arFieldsPrice = ['PRODUCT_ID' => $newID, 'CATALOG_GROUP_ID' => 6, 'PRICE' => $this->requestData['product']['en']['price'], 'CURRENCY' => 'USD'];
              $priceEnRes = \CPrice::Add($arFieldsPrice);
  
              if(!$priceEnRes){
                $rsErrors['add_tp_price_en'][] = 'Ошибка добавления долларовой цены для ТП ID '.$newID;
              }
            }
            else{
              // Получим цену товара, сравним и поменяем, если не равна
              $upPriceFlag = $this->updatePrice($value['id'], 1, $this->requestData['product']['ru']['price']);
              $upPriceFlagEn = $this->updatePrice($value['id'], 6, $this->requestData['product']['en']['price']);
  
              if(!$upPriceFlag || !$upPriceFlagEn){
                $arErrors[] = 'Ошибка изменения цены для ТП ID '.$this->requestData['product']['id'];
              }
            }
          }
        }
        foreach ($existOfferList as $existOffer){
          if ($existOffer['delete']){
            \CIBlockElement::Delete($existOffer['id']);
          }
        }
      }
      else{
        return $this->createOffers($this->requestData['product']['id']);
      }
      
      return count($rsErrors) > 0 ? $rsErrors : true;
    }
    private function updatePrice($productID, $groupPrice, $price){
      $priceObj = \CPrice::GetListEx(
        [], ['PRODUCT_ID' => $productID, 'CATALOG_GROUP_ID' => $groupPrice],
        false, false, ['ID', 'PRICE', 'CURRENCY']
      );
      $arOldPrice = $priceObj->GetNext();
      $res = true;
  
      $arFields = ['PRODUCT_ID' => $productID, 'CATALOG_GROUP_ID' => $groupPrice, 'PRICE' => $price, 'CURRENCY' => $arOldPrice['CURRENCY']];
      if(isset($arOldPrice['PRICE']) && $price != $arOldPrice['PRICE']) {
        $res = \CPrice::Update($arOldPrice['ID'], $arFields);
      }
      else{
        $res = \CPrice::Add($arFields);
      }
  
      return $res;
    }
    
    //Helper
    public function userUploadDir() {
      $this->uploadPath = '/upload/product_photos/'.$this->userID.'/';
      $this->uploadAbsolutePath = $_SERVER['DOCUMENT_ROOT'].$this->uploadPath;
    }
    private function getHLObject(){
      $arHLBlock = HighloadBlockTable::getById(HL_COLOR_ID)->fetch();
      $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
      return $obEntity->getDataClass();
    }
    private function uploadImage($file){
      $legalExt = ['gif', 'jpg', 'jpeg', 'png'];
      $existExt = false;
    
      if(!empty($file['tmp_name'][0])) {
        $name = $file['name'][0];
        $ext = explode('.', $name);
        $ext = end($ext);
      
        if(in_array($ext, $legalExt)){
          $existExt = true;
        }
      
        // Проверяем расширение (текстовый файл прошёл при тесте)
        if($existExt) {
          if(!file_exists($this->uploadAbsolutePath)){
            mkdir($this->uploadAbsolutePath, 0755);
          }
        
          if(move_uploaded_file($file['tmp_name'][0], $this->uploadAbsolutePath.$name)) {
            return $this->uploadPath.$name;
          }
        }
      }
    
      return false;
    }
    
    private function checkOwnerProduct(){
      $productsObj = \CIBlockElement::GetList(
        [], ['IBLOCK_ID' => CATALOG_ID, 'ID' => intval($this->requestData['product']['id'])],
        false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_PRODUCT_OWNER']
      );
      if($productsObj->result->num_rows == 0){
        die(json_encode(['status' => false, 'validation_messages' => 'Это не ваш товар! Вы не можете отредактировать чужой товар.']));
      }
    }
    private function checkSizesList(){
      foreach ($this->requestData['sizes'] as $colorID => $sizeList){
        $countSizesObj = \CIBlockPropertyEnum::GetList(['VALUE' => 'ASC' , 'ID' => 'DESC'], ['IBLOCK_ID' => CATALOG_OFFERS_ID, 'CODE' => 'SIZES', 'ID' => $sizeList]);
        if($countSizesObj->result->num_rows < count($sizeList)){
          die(json_encode(['status' => false, 'validation_messages' => 'Ошибка! Недействительные размеры. Цвет = '.$colorID]));
        }
      }
    }
    private function checkMaterialsList(){
      $countMaterialsObj = \CIBlockElement::getList([], ['IBLOCK_ID' => MATERIALS_ID, 'ID' => array_keys($this->materials)], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
      if($countMaterialsObj->result->num_rows < count($this->materials)){
        die(json_encode(['status' => false, 'validation_messages' => 'Ошибка! Недействительные материалы.']));
      }
      foreach($this->materials as $val){
        if(trim($val) == ''){
          die(json_encode(['status' => false, 'validation_messages' => 'Есть материалы, у которых не указано содержание! Укажите % содержания материалов в товаре.']));
        }
      }
    }
    private function checkColorPhoto(){
      if(count($this->colors['photos']) < 1){
        die(json_encode(['status' => false, 'validation_messages' => 'Нельзя создать товар без фотографий! Загрузите все фото для данного товара.']));
      }
      
      $countColorsObj = $this->getHLObject()::getList(['filter' => ['UF_XML_ID' => array_keys($this->colors['photos'])]]);
      if($countColorsObj->getSelectedRowsCount() < count(array_keys($this->colors['photos']))){
        die(json_encode(['status' => false, 'validation_messages' => 'Ошибка! Недействительные цвета.']));
      }
    }
    
    public function prepareRequestData($request) {
      $codeReplaces = ["replace_space"=>"-","replace_other"=>"-"];
      $this->requestData = [
        'global' => [
          'siteID' => htmlspecialcharsbx($request['site_id']),
        ],
        'product' => [
          'id' => htmlspecialcharsbx($request['product_id']),
          'active' => htmlspecialcharsbx($request['product_active']),
          'section' => htmlspecialcharsbx($request['section']),
          'article' => htmlspecialcharsbx($request['article']),
          'ru' => [
            'name' => htmlspecialcharsbx($request['product_name']),
            'price' => str_replace(['-', '+'], '', htmlspecialcharsbx($request['price'])),
            'description' => htmlspecialcharsbx($request['description']),
          ],
          'en' => [
            'name' => htmlspecialcharsbx($request['product_name_en']),
            'price' => str_replace(['-', '+'], '', htmlspecialcharsbx($request['price_en'])),
            'description' => htmlspecialcharsbx($request['description_en']),
          ],
        ],
        'sizes' => $request['sizes'],
        'materials' => [
          'ids' => array_map('htmlspecialcharsbx', $request['material-id']),
          'values' => array_map('htmlspecialcharsbx', $request['material-value']),
        ],
        'colors' => [
          'active' => $request['active'],
          'sizes' => $request['sizes'],
          'photos' => [],
        ]
      ];
      
      foreach ($request as $key => $value) {
        switch ($key){
          case 'site_id':
          case 'product_id':
          case 'section':
          case 'article':
          case 'product_active':
          case 'product_name':
          case 'product_name_en':
          case 'price':
          case 'price_en':
          case 'description':
          case 'description_en':
          case 'sizes':
          case 'active':
          case 'material-id':
          case 'material-value':
            break;
          default:
            $this->requestData['colors']['photos'][$key] = array_map('htmlspecialcharsbx', $value);
            break;
        }
      }
      
      $this->prepareMaterialList();
    }
    public function prepareRequestFiles($files) {
      foreach ($this->requestData['colors']['photos'] as $kColor => $photos){
        foreach ($photos as $photoID) {
          $fileData = \CFile::GetByID($photoID)->Fetch();
          $photoAbsolutePath = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$fileData['SUBDIR'].'/'.$fileData['FILE_NAME'];
  
          if(!file_exists($this->uploadAbsolutePath)){
            mkdir($this->uploadAbsolutePath, 0755);
          }
  
          copy($photoAbsolutePath, $this->uploadAbsolutePath.$fileData['FILE_NAME']);
          $this->colors['photos'][$kColor][] = $this->uploadPath.$fileData['FILE_NAME'];
          $this->colors['sizes'][$kColor] = $this->requestData['sizes'][$kColor];
        }
      }
      
      foreach ($files as $kColor => $file){
        $colorKeyFile = explode('_', $kColor);
        $pathPhoto = $this->uploadImage($file);
        $this->colors['photos'][$colorKeyFile[0]][] = $pathPhoto;
        $this->colors['sizes'][$colorKeyFile[0]] = $this->requestData['sizes'][$colorKeyFile[0]];
      }
    }
    public function prepareMaterialList() {
      foreach($this->requestData['materials']['ids'] as $index => $id){
        $this->materials[$id] = $this->requestData['materials']['values'][$index];
      }
    }
    public function prepareIblockFields(){
      
      $this->productFields = [
        'IBLOCK_ID' => CATALOG_ID,
        'NAME' => $this->requestData['product']['ru']['name'],
        'IBLOCK_SECTION' => $this->requestData['product']['section'],
        'DETAIL_TEXT' => $this->requestData['product']['ru']['description'],
        'ACTIVE' => $this->requestData['product']['active'],
        'PROPERTY_VALUES'	=> [
          'ENG_NAIMENOVANIE' => $this->requestData['product']['en']['name'],
          'CML2_ARTICLE' => $this->requestData['product']['article'],
          'ENG_DESCRIPTION' => $this->requestData['product']['en']['description'],
          'PRODUCT_OWNER' => $this->userID,
        ]
      ];
      if(!$this->isManage){
        $this->productFields['PROPERTY_VALUES']['PRODUCT_OWNER'] = $this->userID;
      }
      else{
        $this->productFields['MODIFIED_BY'] = $this->userID;
      }
    }
    
    //base singleton
    public static function getInstance(){
      if (null === self::$instance){
        self::$instance = new self();
      }
      return self::$instance;
    }
    private function __clone() {}
    private function __construct() {}
    private function __sleep(){}
    private function __wakeup(){}
  }
