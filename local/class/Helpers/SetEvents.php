<?

namespace local\Helpers;

use Bitrix\Main\EventManager;

class SetEvents
{
  public static function init()
  {
    EventManager::getInstance()->addEventHandlerCompatible (
      "sale",
      "OnSaleComponentOrderOneStepComplete",
      array(
        "local\\Helpers\\NewOrder",
        "OnSaleComponentOrderOneStepComplete"
      )
    );

    EventManager::getInstance()->addEventHandler(
      "sale",
      "OnSaleOrderSaved",
      array(
        "local\\Helpers\\NewOrder",
        "OnSaleOrderSaved"
      )
    );

    EventManager::getInstance()->addEventHandler(
      "main",
      "OnBeforeUserRegister",
      array(
        "local\\Helpers\\NewUser",
        "OnBeforeUserRegisterHandler"
      )
    );

    EventManager::getInstance()->addEventHandler(
      "iblock",
      "OnBeforeIBlockElementAdd",
      array(
        "local\\Helpers\\ElementAddTags",
        "AddTagsFromSectionTitle"
      )
    );

    EventManager::getInstance()->addEventHandler(
      "iblock",
      "OnBeforeIBlockElementUpdate",
      array(
        "local\\Helpers\\ElementAddTags",
        "AddTagsFromSectionTitle"
      )
    );

    EventManager::getInstance()->addEventHandler(
      'iblock',
      'OnIBlockPropertyBuildList',
      array(
        "local\\Helpers\\UserTypeElemColors",
        "GetUserTypeDescription"
      )
    );

    EventManager::getInstance()->addEventHandler(
      'sale',
      'OnSalePayOrder',
      'generateCoupon'
    );

    EventManager::getInstance()->addEventHandler(
      'catalog',
      'OnStoreProductUpdate',
      'OnStoreProductUpdateHandler'
    );

    EventManager::getInstance()->addEventHandler(
      'sale',
      'onSaleAdminOrderInfoBlockShow',
      'onSaleAdminOrderInfoBlockShowHandler'
    );

    EventManager::getInstance()->addEventHandler(
      'messageservice',
      'onGetSmsSenders',
      'registerSmscService'
    );
  }
}