<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
if (is_array($arResult['SHIPMENT']) && count($arResult['SHIPMENT'])){
  foreach($arResult['SHIPMENT'] as $arItem)
    $arShipment = $arItem;
}
if (is_array($arResult['PAYMENT']) && count($arResult['PAYMENT'])){
  foreach($arResult['PAYMENT'] as $arItem)
    $arPayment = $arItem;
}
if (is_array($arResult['ORDER_PROPS']) && count($arResult['ORDER_PROPS'])){
  foreach($arResult['ORDER_PROPS'] as $arItem)
  {
    if($arItem['CODE'] == 'LOCATION')
      $arResult['DELIVERY_ADDRESS'] = $arItem['VALUE'] .', '. $arResult['DELIVERY_ADDRESS'];
  }
}

//dbgz($arResult['ORDER_PROPS']);
//$isPaySite = false;
//if($arPayment['ID'] == 9) // Оплата на сайте (не курьеру)
//  $isPaySite = true;
$paymentName = '';
switch ($arPayment['PAY_SYSTEM_NAME']){
  case 'Оплата на сайте':
  case 'Payment on Network':
    $paymentName = 'Payment via the website';
    break;
}
$ыршзьутеName = '';
switch ($arShipment['DELIVERY_NAME']){
  case 'Самовывоз':
    $shipmentName = 'Pickup point';
    break;
  case 'UPS (UPS Express Saver)':
    $shipmentName = 'UPS Express Saver';
    break;
}
?>
<h1><?=Loc::getMessage('SALE_PERSONAL_ORDER_ORDER_NUM')?><?=$arResult['ID']?></h1>
<div class="order-detail">
  <div class="status"><?echo ($arResult['CANCELED'] == 'Y') ? Loc::getMessage('SALE_PERSONAL_ORDER_CANCELED') : $arResult['STATUS']['NAME']?></div>
  <br>
  <div class="info">
    <div class="row">
      <div class="cell"><?=Loc::getMessage('SALE_PERSONAL_ORDER_DATE_CREATE')?></div>
      <div class="cell value"><?=$arResult['DATE_INSERT_FORMATED']?></div>
    </div>
    <div class="row">
      <div class="cell"><?=Loc::getMessage('SALE_PERSONAL_ORDER_RECIPPIENT')?></div>
      <div class="cell value"><?=$arResult['USER']['USER_INFO_FORMATTED']?></div>
    </div>
    <?if (!empty($arResult['DELIVERY_ADDRESS'])):?>
      <div class="row">
        <div class="cell"><?=Loc::getMessage('SALE_PERSONAL_ORDER_DELIVERY_ADDRESS')?></div>
        <div class="cell value"><?=$arResult['DELIVERY_ADDRESS']?></div>
      </div>
    <?endif?>
    <div class="row">
      <div class="cell"><?=Loc::getMessage('SALE_PERSONAL_ORDER_METHOD_DELIVERY')?></div>
      <div class="cell value"><?=$shipmentName?></div>
    </div>
    <div class="row">
      <div class="cell"><?=Loc::getMessage('SALE_PERSONAL_ORDER_METHOD_PAYMENT')?></div>
      <div class="cell value"><?=$paymentName?></div>
    </div>
  </div>
    
  <table class="basket-table">
    <tbody>
      <tr class="head">
        <th class="image"><?=Loc::getMessage('SALE_PERSONAL_ORDER_NAME')?></th>
        <th class="desc"></th>
        <th class="check"><?=Loc::getMessage('SALE_PERSONAL_ORDER_SELECT')?></th>
        <th class="size"><?=Loc::getMessage('SALE_PERSONAL_ORDER_SIZE')?></th>
        <th class="count"><?=Loc::getMessage('SALE_PERSONAL_ORDER_QUANTITY')?></th>
        <th class="summa"><?=Loc::getMessage('SALE_PERSONAL_ORDER_PRICE')?></th>
      </tr>
    </tbody>
    <tbody id="basket-item-table">
    <?php
      if (is_array($arResult['BASKET']) && count($arResult['BASKET'])){
        ?>
        <?foreach ($arResult['BASKET'] as $arItem):?>
          <?
          $size = '';
          if (is_array($arItem['PROPS']) && count($arItem['PROPS'])){
            foreach($arItem['PROPS'] as $item)
            {
              if($item['CODE'] === 'SIZES') {
                $size = $item['VALUE'];
              }
            }
          }
          ?>
          <tr class="basket-items-list-item-container" data-id="<?=$arItem['ID']?>">
            <td class="image">
              <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="image hover" alt="<?=$arItem['NAME']?>" style="background-image: url(<?=$arItem['PICTURE']['SRC']?>)" target="_blank"></a>
            </td>
            <td class="desc">
              <div class="name"><a class="hover" target="_blank" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div>
              <div class="mobile-size"><?=$size?></div>
              <div class="props"></div>
            </td>
            <td class="check">
              <div class="field field-checkbox">
                <label>
                  <input type="checkbox" class="js-checked-item" value="<?=$arItem['ID']?>">
                  <div class="label"><?=Loc::getMessage('SALE_PERSONAL_ORDER_MARK')?></div>
                </label>
              </div>
            </td>
            <td class="size"><?=$size?></td>
            <td class="count"><?=$arItem['QUANTITY']?></td>
            <td class="summa"><?=$arItem['PRICE_FORMATED']?></td>
          </tr>
        <?endforeach?>
        <?php
      }
    ?>
    </tbody>
  </table>
  
  <div class="totals-wrap">

    <div class="return-button"><a class="black hover js-return-send" data-order="<?=$arResult['ID']?>" data-items=""><?=Loc::getMessage('SALE_PERSONAL_ORDER_MAKE_RETURN')?></a></div>
    
    <?if($arShipment['PRICE_DELIVERY']):?>
      <div class="total-order"><?=Loc::getMessage('SALE_PERSONAL_ORDER_DELIVERY_PRICE')?> <?=$arShipment['PRICE_DELIVERY_FORMATTED']?></div>
    <?endif?>

    <div class="total-order summa"><?=Loc::getMessage('SALE_PERSONAL_ORDER_TOTAL_PRICE')?> <?=$arResult['PRODUCT_SUM_FORMATED']?></div>
    <div class="total-order">
      <?
        if (is_array($arResult['PAYMENT']) && count($arResult['PAYMENT'])){
          foreach ($arResult['PAYMENT'] as $arPaySystem) {
            if ($arPaySystem['PAID'] == 'N' && !empty($arPaySystem['BUFFERED_OUTPUT']) && empty($arPaySystem['ERROR'])) {
      
              $arPaySystem['BUFFERED_OUTPUT'] = str_replace('btn btn-default btn-buy btn-md', 'ibutton', $arPaySystem['BUFFERED_OUTPUT']);
              $arPaySystem['BUFFERED_OUTPUT'] = str_replace(Loc::getMessage('SALE_PERSONAL_ORDER_REDIRECT_PAYMENT'), Loc::getMessage('SALE_PERSONAL_ORDER_PAY'), $arPaySystem['BUFFERED_OUTPUT']);
              $arPaySystem['BUFFERED_OUTPUT'] = str_replace(Loc::getMessage('SALE_PERSONAL_ORDER_AMOUNT_INVOICE'), Loc::getMessage('SALE_PERSONAL_ORDER_AMOUNT'), $arPaySystem['BUFFERED_OUTPUT']);
              echo $arPaySystem['BUFFERED_OUTPUT'];
            }
          }
        }
      ?>  
    </div>
  </div>
</div>
<div class="section-bottom-info">
  <a class="item black" href="<?=$arResult['URL_TO_LIST']?>"><?=Loc::getMessage('SALE_PERSONAL_ORDER_ORDERS_LIST')?></a>
</div>

