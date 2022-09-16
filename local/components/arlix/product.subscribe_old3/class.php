<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main,
    \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\ErrorCollection,
    \Bitrix\Iblock,
    \Bitrix\Main\Loader,
    \Bitrix\Catalog\StoreProductTable,
    \Bitrix\Highloadblock\HighloadBlockTable,
    \Bitrix\Main\Application,
    \Bitrix\Main\Web\Cookie;

class LikesComponent extends CBitrixComponent
{
  const INITIAL_LOAD_ACTION = 'initialLoad';
  const STORE_ID = [7, 8, 11, 12];

  public function __construct($component = null)
  {
    parent::__construct($component);
    $this->errorCollection = new ErrorCollection();
  }

  protected function prepareAction()
  {
    $action = (string)$this->arParams['ACTION_VARIABLE'];
    if (empty($action)) {
      $action = self::INITIAL_LOAD_ACTION;
    }
    return $action;
  }

  public function executeComponent()
  {
    if ($this->includeModules())
    {
      $this->setFrameMode(false);

      $this->action = $this->prepareAction();
      $this->doAction($this->action);

    }
  }
  protected function includeModules()
  {
    $success = true;

    if (!Loader::includeModule('highloadblock')) {
      $success = false;
      ShowError(Loc::getMessage('HIGHLOADBLOCK_MODULE_NOT_INSTALL'));
    }

    return $success;
  }

  protected function doAction($action)
  {
    $funcName = $action.'Action';

    if (is_callable(array($this, $funcName))) {
      $this->{$funcName}();
    }
  }

  protected function initialLoadAction()
  {
    //if(empty($this->arParams['OFFERS'])) return;

    $this->arResult['IS_SUBSCRIBE'] = false;

    if($this->arParams['AUTH'] == 'Y') {
      $currentUserEmail = $this->arParams['EMAIL'];
      $hlblock = HighloadBlockTable::getById(HL_SUBSCRIBE)->fetch();
      $entity = HighloadBlockTable::compileEntity($hlblock);
      $hlSubscribeTable = $entity->getDataClass();
      $countElements = $hlSubscribeTable::getlist([
        'filter' => ['UF_PRODUCT_ID' => $this->arParams['PRODUCT_ID'], 'UF_EMAIL' => $currentUserEmail]
      ])->getSelectedRowsCount();
      
      if($countElements > 0) {
        $this->arResult['IS_SUBSCRIBE'] = true;
      }
    }
    else {
      $arProductSession = unserialize($_SESSION['PRODUCT_SUBSCRIBE']);
      foreach($arProductSession as $arProduct) {
        foreach($arProduct as $productId => $email) {
          if($productId == $this->arParams['PRODUCT_ID']) {
            $this->arResult['IS_SUBSCRIBE'] = true;
          }
        }
      }
    }

    //$arOfferId = [];
    //foreach($this->arParams['OFFERS'] as $arOffer) {
    //  $arOfferId[] = $arOffer['ID'];
    //}
    $store = self::checkAvailableOffers($this->arParams['PRODUCT_ID']);
    if(!$store['AVAILABLE'])
      $this->IncludeComponentTemplate();
  }
  
  public function checkAvailableOffers($productID){
    $rsStore = [
      'AMOUNT' => 0,
      'AVAILABLE' => false,
    ];
    if(!empty($productID))
    {
      $rsStoreProduct = StoreProductTable::getList(array(
        'filter' => ['PRODUCT_ID' => $productID, 'STORE.ACTIVE' => 'Y', 'STORE_ID' => self::STORE_ID],
        'select' => ['AMOUNT', 'STORE_ID', 'PRODUCT_ID'],
      ));
      while ($arStoreProduct = $rsStoreProduct->fetch()) {
        $rsStore['AMOUNT'] += $arStoreProduct['AMOUNT'];
      }
      if($rsStore['AMOUNT'] > 0){
        $rsStore['AVAILABLE'] = true;
      }
    }
    
    return $rsStore;
  }
  public function isAvailable($arOfferId)
  {
    if(!empty($arOfferId)) 
    {
      $rsStoreProduct = StoreProductTable::getList(array(
        'filter' => ['PRODUCT_ID' => $arOfferId, 'STORE.ACTIVE' => 'Y', 'STORE_ID' => self::STORE_ID],
        'select' => ['AMOUNT', 'STORE_ID', 'PRODUCT_ID'],
      ));
      while ($arStoreProduct = $rsStoreProduct->fetch()) {
        if ($arStoreProduct['AMOUNT'] > 0) {
          return true;
        }
      }
    }

    return false;
  }
}
?>