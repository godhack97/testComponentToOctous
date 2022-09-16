<?
use Bitrix\Main\Loader,
  Bitrix\Sale,
  Bitrix\Highloadblock as HL;

class BasketSharing {
  const HL_BASKET_SHARING_TABLE_NAME = 'basket_sharing';
  const HL_BASKET_SHARING_PRODUCT_TABLE_NAME = 'basket_sharing_product';

  public $bsFieldUserID = 'UF_USER_ID';

  public $bspFieldBasketID = 'UF_BASKET_ID';
  public $bspFieldQuantity = 'UF_QUANTITY';
  public $bspFieldProductID = 'UF_PRODUCT_ID';
  public $bspFieldProperty = 'UF_PROPERTY';

  private $thisUser;

  public function BasketSharing()
  {
    if (!Loader::includeModule("highloadblock") || !Loader::includeModule("sale"))
      return;

    global $USER;
    $this->setThisUser($USER->GetID());
  }

  /**
   * @return mixed
   */
  public function getThisUser()
  {
    return $this->thisUser;
  }

  /**
   * @param mixed $thisUser
   */
  public function setThisUser($thisUser)
  {
    $this->thisUser = $thisUser;
  }

  //сохранить корзину пользователя в  рд ,kjr
  public function saveUserBasket()
  {
    $basketID = 0;
    //получить текущую корзину
    $fUserId = Sale\Fuser::getId();
    $basket = Sale\Basket::loadItemsForFUser($fUserId, SITE_ID);
    $basketItems = $basket->getBasketItems();

    if(!empty($basketItems)) {
      //создать корзину
      $hlblock = HL\HighloadBlockTable::getList(['filter' => ['=TABLE_NAME' => self::HL_BASKET_SHARING_TABLE_NAME]])->fetch();
      $hlClassName = (HL\HighloadBlockTable::compileEntity($hlblock))->getDataClass();
      $arFields = [
        $this->bsFieldUserID => $fUserId
      ];
      $resBasket = $hlClassName::add($arFields);
      $basketID = $resBasket->getId();

      if($basketID > 0) {
        //сохранить товары
        foreach ($basketItems as $basketItem) {
          $arProperty = $basketItem->getPropertyCollection()->getPropertyValues();
          $hlblock = HL\HighloadBlockTable::getList(['filter' => ['=TABLE_NAME' => self::HL_BASKET_SHARING_PRODUCT_TABLE_NAME]])->fetch();
          $hlClassName = (HL\HighloadBlockTable::compileEntity($hlblock))->getDataClass();
          $arFields = [
            $this->bspFieldBasketID => $basketID,
            $this->bspFieldProductID => $basketItem->getProductId(),
            $this->bspFieldQuantity => $basketItem->getQuantity(),
            $this->bspFieldProperty => json_encode($arProperty),
          ];
          $hlClassName::add($arFields);
        }
      }
    }

    return $basketID;
  }

  //обновить текущую корзину пользователя
  public function updateCurrentBasketUser($basketID)
  {
    $hlblock = HL\HighloadBlockTable::getList(['filter' => ['=TABLE_NAME' => self::HL_BASKET_SHARING_PRODUCT_TABLE_NAME]])->fetch();
    $hlClassName = (HL\HighloadBlockTable::compileEntity($hlblock))->getDataClass();

    //текущая корзигна
    $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    $rsDataBasket = $hlClassName::getList(["select" => ["*"], 'filter' => [$this->bspFieldBasketID => $basketID]]);
    while($arDataBasket = $rsDataBasket->fetch()) {
      //преобразовать объект со свойствами в массив
      $arProperty = json_decode($arDataBasket[$this->bspFieldProperty]);
      $arProperty = json_decode(json_encode($arProperty), true);
      //проверка на наличие товара в корзине
      if (!$basket->getExistsItem('catalog', $arDataBasket[$this->bspFieldProductID], $arProperty)) {
        //if ($item = $basket->getExistsItem('catalog', $arDataBasket[$this->bspFieldProductID], $arProperty)) {
//        $item->setField('QUANTITY', $item->getQuantity() + $arDataBasket[$this->bspFieldQuantity]);
//      } else {
        //добавить товар в корзину
        $item = $basket->createItem('catalog', $arDataBasket[$this->bspFieldProductID]);
        $item->setFields([
          'QUANTITY' => $arDataBasket[$this->bspFieldQuantity],
          'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
          'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
          'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ]);
        $basket->save();

        $basketPropertyCollection = $item->getPropertyCollection();
        if(!empty($arProperty)) {
          $basketPropertyCollection->setProperty($arProperty);
        }

        $basketPropertyCollection->save();
      }
    }
    $basket->save();

    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
    LocalRedirect($uri->getPath());
  }
}
?>