<?php
	
	use Bitrix\Main\Loader;
	use Sodamoda\Core\Bx\Core;
	use Bitrix\Main\Diag\Debug;
  
  require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
  
  //$dealsByTitle = Core::dealList([
  //  'filter' => ['STAGE_ID' => 'NEW', 'TITLE' => 'Заказ с сайта 19540'],
  //  'select' => ['ID']
  //])['result'][0];
  //$deal = Core::dealGet($dealsByTitle['ID'])['result'];
	/*
	
	$rsElement = CIBlockElement::GetList([], ['IBLOCK' => COLORS_ID, '!PROPERTY_PREORDER' => false, '!PROPERTY_PARENT' => false, 'ACTIVE' => 'Y'], false, false, ['ID']);
	
	while ($arElement = $rsElement->Fetch()){
		$props = CIBlockElement::GetByID($arElement['ID'])->GetNextElement()->GetProperties();
		$colorProductXML_ID = $props['COLOR']['VALUE'];
		$parentProduct = $props['PARENT']['VALUE'];
		
		$res = \CCatalogSKU::getOffersList($parentProduct, 0, ['PROPERTY_COLOR' => $colorProductXML_ID, 'ACTIVE' => 'Y']);
		foreach ($res[$parentProduct] as $offer){
			$updateProduct = ['QUANTITY_TRACE' => 'N'];
			\CCatalogProduct::Update($offer['ID'], $updateProduct);
		}
	}
	 */
  CModule::IncludeModule("network.payment");
  
  $json = file_get_contents("php://input");
  $order = json_decode($json);
  
  Debug::writeToFile('request-payment', $json);
  $networkGate = \Network\Payment\Gateway::getInstance();
  
  $record = $networkGate->getDB()
    ->query("select * from network_order where ORDER_ID = '{$order->order->reference}'")
    ->fetch();
  LocalRedirect('https://sodamoda.ru/bitrix/tools/sale_ps_result.php?PAYMENT=NETWORK&ORDER_ID='.$record['BITRIX_ORDER_ID'].'&CALLBACK_REDIRECT=1&PAYMENT_ID='.$record['PAYMENT_ID'].'&ref='.$record['ORDER_ID'].'');
  die();
	if($_REQUEST['paymentFetch']){
    Loader::includeModule('network.payment');
    $networkGate = \Network\Payment\Gateway::getInstance();
    
    $fetchOrder = $networkGate->fetchOrder('ff72970c-325b-4814-9e96-f2dd9da09acc');
    $cancelOrder = $networkGate->cancelOrder($fetchOrder['_links']['cancel']['href']);
    print_r('<pre>');
    print_r('test');
    print_r($cancelOrder);
    print_r('</pre>');
  }
	if(isset($_REQUEST['offer1231231ыфывфв123123'])){
		$elem = CIBlockElement::GetByID(299);
		$arFilter = [
			"=ID"=> IntVal(299),
			"IBLOCK_ID"=> IntVal(8),
		];
		$res = CIBlockElement::GetList([], $arFilter);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$arProperties = $ob->GetProperties();
			var_dump($arFields);
			var_dump($arProperties);
		}
		die();
		if ($arFields['IBLOCK_ID'] == CATALOG_OFFERS_ID) {
			$arFields['NAME'] = str_replace(['  ', ' ))', ' ,'], [' ', '))', ','], trim($arFields['NAME']));
			if(empty($arFields['NAME'])){
				$name = '';
				$mxResult = \CCatalogSku::GetProductInfo($arFields['ID']);
				if (is_array($mxResult)){
					$res = \CIBlockElement::GetByID($mxResult["ID"]);
					if($ar_res = $res->GetNext()) {
						if(!empty($arFields['PROPERTY_VALUES'][78]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][78]['VALUE']);
						}
						elseif(!empty($arFields['PROPERTY_VALUES'][227]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][227]['VALUE']);
						}
						elseif(!empty($arFields['PROPERTY_VALUES'][232]['VALUE'])){
							$name .= $ar_res['NAME']. '('.trim($arFields['PROPERTY_VALUES'][232]['VALUE']).' ';
						}
						
						Loader::includeModule('highloadblock');
						
						$hlblock = HighloadBlockTable::getById(2)->fetch();
						$entity = HighloadBlockTable::compileEntity($hlblock);
						$PsuTaskControl = $entity->getDataClass();
						$reslist = $PsuTaskControl::getList([
							'filter' => [
								'UF_XML_ID' => $arFields['PROPERTIES'][72]['VALUE'],
							],
						]);
						if ($el = $reslist->fetch()) {
							$name .= $el['UF_NAME'].')';
						}
					}
					$arFields['NAME'] = $name;
				}
			}
			//Debug::writeToFile($arFields);
		}
		
		if($arFields['IBLOCK_ID'] == COLORS_ID && isset($arFields['PROPERTY_VALUES'][223])){
			$parentProduct = $arFields['PROPERTY_VALUES'][122][array_key_first($arFields['PROPERTY_VALUES'][122])]['VALUE'];
			$colorProductXML_ID = $arFields['PROPERTY_VALUES'][120][array_key_first($arFields['PROPERTY_VALUES'][120])]['VALUE'];
			
			$res = \CCatalogSKU::getOffersList($parentProduct, 0, ['PROPERTY_COLOR' => $colorProductXML_ID, 'ACTIVE' => 'Y']);
			foreach ($res[$parentProduct] as $offer){
				$updateProduct = ['QUANTITY_TRACE' => isset($arFields['PROPERTY_VALUES'][223][0]) ? 'N' : 'D'];
				\CCatalogProduct::Update($offer['ID'], $updateProduct);
			}
		}
	}
	//var_dump($arOptPrices = \CCatalogProduct::GetByIDEx(738));
  ?>