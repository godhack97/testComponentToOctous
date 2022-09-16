<?
use Bitrix\Main\Loader,
    Bitrix\Sale,
    Bitrix\Main;

function generateCoupon($ID, $val)
{
  $sectionSertificateID = 121;
  if($val === 'Y') {
    if(!Loader::includeModule("sale") || !Loader::includeModule('iblock')) return;

    $orderId = $ID;
    $order = Sale\Order::load($orderId);
    $basket = $order->getBasket();

    $arProductIDs = $arBasketItems = [];
    foreach ($basket as $basketItem) {
      $arProductIDs[] = $basketItem->getProductId();
      $arBasketItems[$basketItem->getProductId()] = [
        'PRICE' => $basketItem->getPrice(),
        'QUANTITY' => $basketItem->getQuantity()
      ];
    }

    $rsElements = \Bitrix\Iblock\ElementTable::getList([
      'select' => ['ID'],
      'filter' => ['ID' => $arProductIDs, 'IBLOCK_SECTION_ID' => $sectionSertificateID]
    ]);
    while($arElements = $rsElements->Fetch()){
      $arActions["CLASS_ID"] = "CondGroup";
      $arActions["DATA"]["All"] = "AND";
      $arActions["CLASS_ID"] = "CondGroup";
      $arActions["CHILDREN"][0]["CLASS_ID"] = "ActSaleBsktGrp";
      $arActions["CHILDREN"][0]["DATA"]["Type"] = "Discount";
      $arActions["CHILDREN"][0]["DATA"]["Value"] = $arBasketItems[$arElements['ID']]['PRICE'];
      $arActions["CHILDREN"][0]["DATA"]["Unit"] = "CurAll";
      $arActions["CHILDREN"][0]["DATA"]["All"] = "OR";
      $arActions["CHILDREN"][0]["DATA"]["True"] = "True";

      $arConditions["CLASS_ID"] = "CondGroup";
      $arConditions["DATA"]["All"] = "AND";
      $arConditions["DATA"]["True"] = "True";
      $arConditions["CHILDREN"] = "";

      $arFields = [
        "LID" => "s1",
        "NAME" => 'Скидка по сертификату '. $arBasketItems[$arElements['ID']]['PRICE'] ." руб.",
        "PRIORITY" => 900,
        "CURRENCY" => "RUB",
        "ACTIVE" => "Y",
        "USER_GROUPS" => [2],
        'ACTIONS' => $arActions,
        "CONDITIONS" => $arConditions,
        "LAST_LEVEL_DISCOUNT" => 'Y'
      ];

      $discountID = CSaleDiscount::Add($arFields);
      if($discountID > 0){
        for($i = 0; $i < $arBasketItems[$arElements['ID']]['QUANTITY']; $i++) {
          $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
          $activeFrom = new \Bitrix\Main\Type\DateTime();
          $activeTo = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime("+1 years"));
          $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add([
            'DISCOUNT_ID' => $discountID,
            'COUPON' => $coupon,
            'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
            'ACTIVE_FROM' => '',
            'ACTIVE_TO' => '',
            'MAX_USE' => 1,
            'DESCRIPTION' => 'Для заказа №'.$orderId,
            'ACTIVE_FROM' => $activeFrom,
            'ACTIVE_TO' => $activeTo
          ]);
          if ($addDb->isSuccess()){
            $propertyCollection = $order->getPropertyCollection();
            $email = $propertyCollection->getUserEmail()->getValue();

            $arEventFields = [
              "EMAIL_ORDER" => $email,
              "COUPON" => $coupon,
              "COUPON_PRICE" => number_format($arBasketItems[$arElements['ID']]['PRICE'], 0, '', ' '),
              "IMAGE_URL" => $arBasketItems[$arElements['ID']]['PRICE'],
              'ACTIVE_TO' => FormatDate('d F Y года', $activeTo->getTimestamp(), time()+CTimeZone::GetOffset())
            ];
            CEvent::Send("GENERATE_COUPON", 's1', $arEventFields);
          }
        }
      }
    }
  }
}