<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main, 
    Bitrix\Main\Localization\Loc;

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var SaleOrderAjax $component
 * @var string $templateFolder
 */


$arDeliveryCurrent = false;
foreach($arResult["JS_DATA"]["DELIVERY"] as $item)
{
  if($item['CHECKED'] == 'Y')
    $arDeliveryCurrent = $item;
}


$priceOldDelivery = false;
if(!empty(htmlspecialcharsbx($_SESSION['PRICE_OLD_DELIVERY'])))
  $priceOldDelivery = htmlspecialcharsbx($_SESSION['PRICE_OLD_DELIVERY']);

if(strlen($request->get('ORDER_ID')) > 0)
{
  include(Main\Application::getDocumentRoot() . $templateFolder . '/confirm.php');
}
else
{
  ?>
  <div class="error_message">
    <?
    if(strlen($arResult["ERROR_MESSAGE"]) > 0)
    {
      ?>
      <script>
        $(function () {
          messageShow('<?= $arResult["ERROR_MESSAGE"] ?>', 'error');
        });
      </script>
      <?
      //ShowError($arResult["ERROR_MESSAGE"]);
    }
    ?>
  </div>

  <script>
    $(function () {
      setTimeout(function(){
        if(!$('#PHONE').hasClass('js-mask-inited'))
          $('#PHONE').inputmasks(maskPhoneOpts);
      }, 300);
    });
  </script>

  <form novalidate action="<?= POST_FORM_ACTION_URI ?>" method="POST" name="ORDER_FORM" id="ORDER_FORM"<?if($arParams['COL_PAYMENT'] == 'Y'):?> class="display-col-payment"<?endif?> enctype="multipart/form-data">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="<?= $arParams['ACTION_VARIABLE'] ?>" value="processOrder">
    <input type="hidden" name="location_type" value="code">
    <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $arResult['BUYER_STORE'] ?>">
    <input type="hidden" name="city" value="">
    <div class="order cols-order">
      <div class="col-order buyer col-buyer type-moscow<?if($arDeliveryCurrent['ID'] == 3):?> mode-pickup<?endif?>">

        <h1>Оформление заказа</h1>
        <br/>

        <?if(!$USER->IsAuthorized()):?>
          <div class="text intro-text-wrap">
            <a href="<?= $arParams["PATH_TO_AUTH"] ?>" class="black">Авторизуйтесь</a> для просмотра ваших заказов
          </div>
        <?endif?>

        <div class="form">

          <?if(count($arResult["JS_DATA"]["PERSON_TYPE"]) == 1):?>

            <input type="hidden" name="PERSON_TYPE" value="<?= $arResult["JS_DATA"]["PERSON_TYPE"][0]["ID"] ?>">

          <?else:?>

            <ul>
              <?foreach($arResult["JS_DATA"]["PERSON_TYPE"] as $PersonType):?>
                <li>
                  <input id="user-<?= $PersonType["ID"] ?>" value="<?= $PersonType["ID"] ?>" type="radio" name="PERSON_TYPE" <?if($PersonType["CHECKED"] == "Y"):?>checked<?endif?>>
                  <label for="user-<?= $PersonType["ID"] ?>"><?= $PersonType["NAME"] ?></label>
                </li>
              <?endforeach?>
            </ul>

          <?endif?>


          <? include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/block/property.php"); ?>


          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" checked="checked" id="user-agree" value="Y" name="save-user-agree" aria-required="true" required="">
              <div class="label">
                Cогласен (согласна) с <a href="/rule/" target="_blank" class="black">политикой конфиденциальности</a>
              </div>
            </label>
          </div>

          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" checked="checked" id="user-return" value="Y" name="save-user-return" aria-required="true" required="required">
              <div class="label">
                Cогласен (согласна) с <a href="/return/" target="_blank" class="black">условиями обмена и возврата</a>
              </div>
            </label>
          </div>

          <div class="field field-checkbox">
            <label> 
                <input type="checkbox" id="js-set-email" <? if(trim($_REQUEST["set-email"]) == "Y"):?>checked<?endif?> value="Y" name="set-email">
              <div class="label">
                Получать предложения по акциям на e-mail
              </div>
            </label>
          </div>

        </div>

      </div>
      <div class="col-order methods col-delivery">
        <div class="method method-delivery">

          <? include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/block/delivery.php"); ?>

        </div>
      </div>
      <div class="col-order methods col-payment">
        <div class="method method-pay">

          <? include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/block/pay.php"); ?>

        </div>
      </div>
    </div>
    <div class="total-wrap">
      <table class="total-table">
        <tbody>
          <tr class="info">
            <td>
              Общая стоимость:
            </td>
            <td>
                <?=$arResult["ORDER_PRICE_FORMATED"] ?>
            </td>
          </tr>
          <tr class="info">
            <td>
                Стоимость доставки:
            </td>
            <td>

              <?if($priceOldDelivery):?>
                <?= number_format($priceOldDelivery, 0, '', ' ') ?> руб.
              <?else:?>
                <?= $arResult["DELIVERY_PRICE_FORMATED"] ?>
              <?endif?>

            </td>
          </tr>

          <?if($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']):?>
            <tr class="alert-domestic-account<?=(!$_REQUEST['PAY_CURRENT_ACCOUNT']=='Y') ? " alert-domestic-account_hiden" : "";?>">
              <td>
                  Списано с внутреннего счета
              </td>
              <td>
                  <?=number_format($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'], 0,'', ' ')?> руб.
              </td>
            </tr>
          <?endif?>

          <tr class="<?if($priceOldDelivery):?>info info-black<?else:?>total<?endif?>">
            <td>
              Итого:
            </td>

            <?if($priceOldDelivery):?>
              <td>
                <?= number_format(($arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] + $priceOldDelivery), 0, '', ' ') ?> руб.
              </td>
            <?else:?>
              <td class="order-total-price" data-total-price-with-domestic-account="<?= $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] ?>" data-total-price-formated="<?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>">
                <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
              </td>
            <?endif?>

          </tr>

          <?if($priceOldDelivery):?>

            <tr class="info">
              <td>
                Скидка на доставку:
              </td>
              <td>
                –&nbsp;<?= $priceOldDelivery ?> руб.
              </td>
            </tr>
            <tr class="total">
              <td>
                К оплате:
              </td>
              <td class="order-total-price" data-total-price-with-domestic-account="<?= $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] ?>" data-total-price-formated="<?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>">
                <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
              </td>
            </tr>

          <?endif?>

          <!-- tr class="comment">
            <td>
              * Включая НДС
            </td>
            <td>
            </td>
          </tr -->

        </tbody>
      </table>
    </div>

    <div class="cols-order buttons">
      <div class="col-order wrap-buttons-between">
        <div class="description-help">Нужна помощь? Свяжитесь<br>с&nbsp;нашими менеджерами:</div>
        <a class="ibutton hollow group-width js-metrika-order-phone" href="tel:+78003505670">Позвонить 8 800 350-5670</a>
        <a class="ibutton hollow group-width js-metrika-order-whatsapp" href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" title="Связаться с нами в WhatsApp">Написать в whatsapp</a>
      </div>
      <div class="col-order wrap-buttons-between">
        <a class="ibutton hollow group-width js-button-back-to-cart" href="<?= $arParams["PATH_TO_BASKET"] ?>">Вернуться в корзину</a>
        <a class="ibutton hollow group-width js-button-back-to-order">Назад</a>
        <?
          $nameSend = 'Оплатить';
          if(count($arResult["JS_DATA"]["PAY_SYSTEM"]) > 1 && $arParams['COL_PAYMENT'] != 'Y')
            $nameSend = 'Продолжить';
        ?>
        <input class="ibutton group-width js-metrika-order-send" type="submit" value="<?=$nameSend?>" name="BasketOrder" id="basketOrderButton2">
      </div>
    </div>

    <? /* if($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']){?>
      <div class="field field-checkbox check-domestic-account">
        <label>
          <input <?=($_REQUEST['PAY_CURRENT_ACCOUNT']=='Y') ? "checked" : "";?> type="checkbox" value="Y" name="PAY_CURRENT_ACCOUNT">
          <div class="label">
            Списать с внутреннего счета <?=number_format($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'], 0,'', ' ')?> руб.
          </div>
        </label>
      </div>
    <?} */ ?>

  </form>
  <?
}

unset($arResult["PERSON_TYPE"]);
unset($arResult["PAY_SYSTEM"]);
unset($arResult["ORDER_PROP"]);
unset($arResult["DELIVERY"]);
unset($arResult["JS_DATA"]);
unset($arResult["BASKET_ITEMS"]);
unset($arResult["GRID"]);
unset($arResult["ITEMS_DIMENSIONS"]);
unset($arResult["LOCATIONS"]);
unset($arResult["USER_VALS"]);