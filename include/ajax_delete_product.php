<?php
  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
  
  use \Bitrix\Main\Localization\Loc;
  
  global $USER;
  
  $userID = $USER->GetID();
  
  if(!isset($_POST['product_id'])){
    $_POST = json_decode(file_get_contents('php://input'), true);
  }
  // Получим данные товара для удаления из $_POST
  $siteID = htmlspecialcharsbx($_POST['site_id']);
  $productID = htmlspecialcharsbx($_POST['product_id']);
  $owner = htmlspecialcharsbx($_POST['owner']);
  
  if($USER->IsAdmin()){
    if(!empty($productID)){
      if($owner !== 'N'){
        $productsObj = CIBlockElement::GetList([], ['IBLOCK_ID' => CATALOG_ID, 'PROPERTY_PRODUCT_OWNER' => $userID, '=ID' => $productID], false, false, ['ID', 'IBLOCK_ID']);
        if($productsObj->result->num_rows == 0) {
          die(json_encode(['status' => false, 'validation_messages' => 'Это не ваш товар! Вы не можете отредактировать чужой товар.']));
        }
      }
      $materialObj = CIBlockElement::GetList([], ['IBLOCK_ID' => COMPOSITION_ID, '=PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID']);
      while($arMaterial = $materialObj->GetNext()){
        CIBlockElement::Delete($arMaterial['ID']);
      }
      $photoObj = CIBlockElement::GetList([], ['IBLOCK_ID' => COLORS_ID, '=PROPERTY_PARENT' => $productID], false, false, ['ID', 'IBLOCK_ID']);
      while($arPhoto = $photoObj->GetNext()) {
        CIBlockElement::Delete($arPhoto['ID']);
      }
      $offersObj = CIBlockElement::GetList([], ['IBLOCK_ID' => CATALOG_OFFERS_ID, '=PROPERTY_CML2_LINK' => $productID], false, false, ['ID', 'IBLOCK_ID']);
      while($arOffer = $offersObj->GetNext()){
        CIBlockElement::Delete($arOffer['ID']);
      }
      
      CIBlockElement::Delete($productID);
      
      echo json_encode(['status' => true]);
      die();
    }
  }
?>