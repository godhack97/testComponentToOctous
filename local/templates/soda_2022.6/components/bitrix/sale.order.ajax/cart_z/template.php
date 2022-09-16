<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
  
  /**
   * @var array $arParams
   * @var array $arResult
   * @var CMain $APPLICATION
   * @var CUser $USER
   * @var SaleOrderAjax $component
   * @var string $templateFolder
   */
  
use Bitrix\Main, 
    Bitrix\Main\Localization\Loc;

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$this->addExternalJS($templateFolder."/script_extends.js");


$arDeliveryCurrent = false;
if(is_array($arResult['JS_DATA']['DELIVERY']) && count($arResult['JS_DATA']['DELIVERY'])){
  foreach ($arResult['JS_DATA']['DELIVERY'] as $item) {
    if ($item['CHECKED'] == 'Y')
      $arDeliveryCurrent = $item;
  }
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
    <input type="hidden" name="site_id" value="<?=SITE_ID?>">
    <div class="order cols-order" data-dbg="1">
      <div class="col-order buyer col-buyer type-moscow<?if($arDeliveryCurrent['ID'] == 3):?> mode-pickup<?endif?>">

        <h1><?=Loc::getMessage('SALE_ORDER_CHECKOUT')?></h1>
        <br/>

        <?if(!$USER->IsAuthorized()):?>
          <div class="text intro-text-wrap">
            <a href="<?= $arParams["PATH_TO_AUTH"] ?>" class="black"><?=Loc::getMessage('SALE_ORDER_LOGIN')?></a> <?=Loc::getMessage('SALE_ORDER_VIEW_ORDER')?>
          </div>
        <?endif?>

        <div class="form">

          <?if(count($arResult["JS_DATA"]["PERSON_TYPE"]) == 1):?>

            <input type="hidden" name="PERSON_TYPE" value="<?= $arResult["JS_DATA"]["PERSON_TYPE"][0]["ID"] ?>">

          <?else:
            $personTypeLegalChecked = false;
          ?>

            <ul style="display: none;">
              <? foreach($arResult["JS_DATA"]["PERSON_TYPE"] as $PersonType):
                if ($PersonType['CODE'] === 'LEGAL' && $PersonType["CHECKED"] == "Y") {
                  $personTypeLegalChecked = true;
                }
                ?>
                <li>
                  <input id="user-<?= $PersonType["ID"] ?>" value="<?= $PersonType["ID"] ?>" type="radio" name="PERSON_TYPE" <?if($PersonType["CHECKED"] == "Y"):?>checked<?endif?>>
                  <label for="user-<?= $PersonType["ID"] ?>"><?= $PersonType["NAME"] ?></label>
                </li>
              <?endforeach?>
            </ul>

          <?endif?>

            
          <?if($arParams['USER_SELECTION'] == 'Y'):?>

            <?
              $request = \Bitrix\Main\Context::getCurrent()->getRequest();
              $USER_SELECTION = trim($request->getPost("USER_SELECTION"));
              $USER_SELECTION_ID = (int)$request->getPost("user_selection_id");
            ?>

            <div class="field field-code-user-selection" id="field_user_selection">
              <input type="text" placeholder=" " size="40" name="USER_SELECTION" value="<?=$USER_SELECTION?>" id="USER_SELECTION" aria-required="true" required="" class="field-style">
              <label class="placeholder" for="USER_SELECTION"><?=Loc::getMessage('SALE_ORDER_USER')?></label>
              <a class="add-user-selection black hover"><?=Loc::getMessage('SALE_ORDER_ADD')?></a>
            </div>
            <input type="hidden" name="use_user_selection" value="Y">
            <input type="hidden" id="user_selection_id" name="user_selection_id" value="<?=$USER_SELECTION_ID?>">
            <script>
              $(function () {
                $("#USER_SELECTION").autocomplete({
                  appendTo: "#field_user_selection",
                  source: "/ajax/get_user.php",
                  minLength: 3,
                  select: function( event, ui ) {
                    SetUser(ui.item);
                  }
                }).data("ui-autocomplete")._renderItem = function (ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
                };
              });
            </script>
          <?endif?>
          
          
          <? include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/block/property.php");?>

          <?php
            if(in_array(12, $USER->GetUserGroupArray())){
              ?>
              <div class="field field-checkbox">
                <label>
                  <input type="checkbox" <?=$personTypeLegalChecked ? 'checked="checked"' : ''?> id="PERSON_TYPE_COPY" value="Y" name="PERSON_TYPE_COPY" aria-required="true" required="">
                  <div class="label">
                    <?=Loc::getMessage('SALE_ORDER_PERSON_TYPE_2')?>
                  </div>
                </label>
              </div>
              <?php
            }
          ?>
          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" checked="checked" id="user-agree" value="Y" name="save-user-agree" aria-required="true" required="">
              <div class="label">
                <?=Loc::getMessage('SALE_ORDER_AGREE_WITH_PRIVACY')?> <a href="/rule/" target="_blank" class="black"><?=Loc::getMessage('SALE_ORDER_PRIVACY_POLICY')?></a>
              </div>
            </label>
          </div>

          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" checked="checked" id="user-return" value="Y" name="save-user-return" aria-required="true" required="required">
              <div class="label">
                <?=Loc::getMessage('SALE_ORDER_AGREE_WITH_RETURN')?> <a href="/return/" target="_blank" class="black"><?=Loc::getMessage('SALE_ORDER_RETURN')?></a>
              </div>
            </label>
          </div>

          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" id="js-set-email" <? if(trim($_REQUEST["set-email"]) == "Y"):?>checked<?endif?> value="Y" name="set-email">
              <div class="label">
                <?=Loc::getMessage('SALE_ORDER_OFFERS_EMAIL')?>
              </div>
            </label>
          </div>

          <div class="field field-checkbox">
            <label> 
              <input type="checkbox" id="js-set-bonus" <? if(trim($_REQUEST["set-bonus"]) == "Y" || $USER->IsAuthorized()):?>checked<?endif?> value="Y" name="set-bonus" data-user-confimed="<?=$arResult['PHONE_CONFIRMED'];?>">
              <div class="label">
                <?=Loc::getMessage('SALE_ORDER_BONUS')?>
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
              <?=Loc::getMessage('SALE_ORDER_NOT_DISCOUNT_TOTAL_PRICE')?>
            </td>
            <td>
	            <?php
		            if(!$arResult['USE_EXTEND_CURRENCY']){
			            ?>
			            <?=$arResult["PRICE_WITHOUT_DISCOUNT"] ?>
			            <?php
		            }
		            if($arResult['USE_EXTEND_CURRENCY']){
			            ?>
			            <?=$arResult["PRICE_WITHOUT_DISCOUNT_EXTEND"] ?>
			            <?php
		            }
	            ?>
            </td>
          </tr>
          <tr class="info">
            <td>
              <?=Loc::getMessage('SALE_ORDER_TOTAL_PRICE')?>
            </td>
            <td>
	            <?php
		            if(!$arResult['USE_EXTEND_CURRENCY']){
			            ?>
			            <?=$arResult["ORDER_PRICE_FORMATED"] ?>
			            <?php
		            }
	            ?>
	            <?php
		            if($arResult['USE_EXTEND_CURRENCY']){
			            ?>
			            <?=$arResult["ORDER_PRICE_FORMATED_EXTEND"] ?>
			            <?php
		            }
	            ?>
            </td>
          </tr>
          <tr class="info">
            <td>
              <?=Loc::getMessage('SALE_ORDER_PPRICE_DELIVERY')?>
            </td>
            <td>
              <?if($priceOldDelivery):?>
		            <?php
			            if($arResult['USE_EXTEND_CURRENCY']){
				            ?>
				            <div class="alternative-currency">
				            <?php
			            }
		            ?>
		            <?= number_format($priceOldDelivery, 0, '', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>
		            <?php
			            if($arResult['USE_EXTEND_CURRENCY']){
		            ?>
	    </div>
	  <?php
		  }
	              ?>
	              <?php
	              if($arResult['USE_EXTEND_CURRENCY']){
		              ?>
		              <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency($priceOldDelivery, $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
		              <?php
	              }
	              ?>
              <?else:?>
	              <?php
	              if($arResult['USE_EXTEND_CURRENCY']){
		              ?>
		              <div class="alternative-currency">
		              <?php
	              }
	              ?>
	              <?= $arResult["DELIVERY_PRICE_FORMATED"] ?>
	              <?php
	              if($arResult['USE_EXTEND_CURRENCY']){
		              ?>
		              </div>
		              <?php
	              }
	              ?>
	              <?php
	              if($arResult['USE_EXTEND_CURRENCY']){
		              ?>
		              <?=$arResult["DELIVERY_PRICE_FORMATED_EXTEND"] ?>
		              <?php
	              }
	              ?>
              <?endif?>

            </td>
          </tr>

          <?if($arResult['PHONE_CONFIRMED'] && $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']):?>
            <tr class="alert-domestic-account<?=(!$_REQUEST['PAY_CURRENT_ACCOUNT']=='Y') ? " alert-domestic-account_hiden" : "";?>">
              <td>
                <?=Loc::getMessage('SALE_ORDER_CHARGED_ACCOUNT')?>
              </td>
              <td>
                <?=number_format($arResult['BONUS']['AMOUNT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY')?>
              </td>
            </tr>
          <?endif?>

          <tr class="<?if($priceOldDelivery):?>info info-black<?else:?>total<?endif?>">
            <td>
              <?=Loc::getMessage('SALE_ORDER_TOTAL')?>
            </td>

            <?if($priceOldDelivery):?>
              <td class='order-total-price-delivery' data-bonus-payment="<?= number_format($arResult['BONUS']['PAYMENT'] + $priceOldDelivery, 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>" data-total-price-formated="<?= number_format(($arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] + $priceOldDelivery), 0, '', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>">
	              <?php
		              if($arResult['USE_EXTEND_CURRENCY']){
	              ?>
	              <div class="alternative-currency">
		              <?php
			              }
		            ?>
		            <?= number_format(($arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] + $priceOldDelivery), 0, '', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>
		            <?php
			            if($arResult['USE_EXTEND_CURRENCY']){
		              ?>
	              </div>
              <?php
	              }
                ?>
	              <?php
		              if($arResult['USE_EXTEND_CURRENCY']){
			              ?>
			              <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency(($arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] + $priceOldDelivery), $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
			              <?php
		              }
	              ?>
              </td>
            <?else:?>
              <td class="order-total-price" data-bonus-amount="<?= number_format($arResult['BONUS']['AMOUNT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>" data-bonus-payment="<?= number_format($arResult['BONUS']['PAYMENT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>" data-total-price-formated="<?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>">
                <?php
                  if($arResult["ORDER_TOTAL_PRICE"] < $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']){
                    ?>
	                  <?php
	                  if($arResult['USE_EXTEND_CURRENCY']){
		                  ?>
		                  <div class="alternative-currency">
		                  <?php
	                  }
	                  ?>
	                  <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_PAY_FORMATED"] : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
	                  <?php
	                  if($arResult['USE_EXTEND_CURRENCY']){
		                  ?>
		                  </div>
		                  <?php
	                  }
	                  ?>
                    <?php
	                  if($arResult['USE_EXTEND_CURRENCY']){
		                  ?>
		                  <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency(($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_PAY"] : $arResult["ORDER_TOTAL_PRICE"], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
		                  <?php
	                  }
	                  ?>
	                  <?php
                  }
                  else
                    {
                      ?>
	                    <?php
	                    if($arResult['USE_EXTEND_CURRENCY']){
		                    ?>
		                    <div class="alternative-currency">
		                    <?php
	                    }
	                    ?>
	                    <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT_FORMATED"] : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
	                    <?php
	                    if($arResult['USE_EXTEND_CURRENCY']){
		                    ?>
		                    </div>
		                    <?php
	                    }
	                    ?>
                      <?php
	                    if($arResult['USE_EXTEND_CURRENCY']){
		                    ?>
		                    <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency(($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult["ORDER_TOTAL_PRICE_WITH_DOMESTIC_ACCOINT"] : $arResult["ORDER_TOTAL_PRICE"], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
		                    <?php
	                    }
	                    ?>
	                    <?php
                    }
                ?>
              </td>
            <?endif?>

          </tr>

          <?if($priceOldDelivery):?>

            <tr class="info">
              <td>
                <?=Loc::getMessage('SALE_ORDER_SHIPPING_DISCOUNT')?>
              </td>
              <td>
	              <?php
		              if($arResult['USE_EXTEND_CURRENCY']){
	              ?>
	              <div class="alternative-currency">
		              <?php
			              }
		            ?>
		              –&nbsp;<?= $priceOldDelivery.Loc::getMessage('SALE_ORDER_CURRENCY') ?>
		            <?php
			            if($arResult['USE_EXTEND_CURRENCY']){
		            ?>
			            </div>
		            <?php
	              }
                ?>
	              <?php
		              if($arResult['USE_EXTEND_CURRENCY']){
			              ?>
			              - <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency($priceOldDelivery, $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
			              <?php
		              }
	              ?>
              </td>
            </tr>
            <tr class="total">
              <td>
                <?=Loc::getMessage('SALE_ORDER_TO_PAY')?>
              </td>
              <td class="order-total-price" data-bonus-amount="<?= number_format($arResult['BONUS']['AMOUNT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>" data-bonus-payment="<?= number_format($arResult['BONUS']['PAYMENT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') ?>" data-total-price-formated="<?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>">
                  <?php
                      if($arResult["ORDER_TOTAL_PRICE"] < $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']){
                        ?>
                        <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      <div class="alternative-currency">
		                      <?php
	                      }
	                      ?>
	                      <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? number_format($arResult['BONUS']['AMOUNT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
	                      <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      </div>
		                      <?php
	                      }
	                      ?>
	                      <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency(($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult['BONUS']['AMOUNT'] : $arResult["ORDER_TOTAL_PRICE"], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
		                      <?php
	                      }
	                      ?>
	                      <?php
                      }
                      else
                      {
                        ?>
	                      <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      <div class="alternative-currency">
		                      <?php
	                      }
	                      ?>
	                      <?= ($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? number_format($arResult['BONUS']['AMOUNT'], 0,'', ' ').Loc::getMessage('SALE_ORDER_CURRENCY') : $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?>
	                      <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      </div>
		                      <?php
	                      }
	                      ?>
                        <?php
	                      if($arResult['USE_EXTEND_CURRENCY']){
		                      ?>
		                      <?=SaleFormatCurrency(CCurrencyRates::ConvertCurrency(($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'] && $_REQUEST['PAY_CURRENT_ACCOUNT'] == 'Y') ? $arResult['BONUS']['AMOUNT'] : $arResult["ORDER_TOTAL_PRICE"], $arResult['CURRENCY_ORDER'], $_COOKIE["CATALOG_CURRENCY"]), $_COOKIE["CATALOG_CURRENCY"]) ?>
		                      <?php
	                      }
	                      ?>
	                      <?php
                      }
                  ?>

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

    <div id="js-order-check-bonus" class="wrap-field-check-domestic" style="display:none">
      <?if($arResult['PHONE_CONFIRMED'] && $arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']):?>
        <div class="field field-checkbox field-check-domestic">
          <label>
            <input <?=($_REQUEST['PAY_CURRENT_ACCOUNT']=='Y') ? "checked" : "";?> type="checkbox" value="Y" name="PAY_CURRENT_ACCOUNT" <?=$arResult['BONUS']['CURRENT'] > 0 ? '' : ' disabled';?>>
            <div class="label">
              <span id="active_pay_bonus" style="display: none"><?=Loc::getMessage('SALE_ORDER_PAY_BONUS_ACTIVE', ['#BAL_PAY#' => number_format($arResult['BONUS']['CURRENT'], 0,'', ' '), '#BAL_TOTAL#' => number_format($arResult['BONUS']['TOTAL'], 0,'', ' ')]);?></span>
              <span id="deactive_pay_bonus"><?=Loc::getMessage('SALE_ORDER_PAY_BONUS_DEACTIVE', ['#BAL_TOTAL#' => number_format($arResult['BONUS']['CURRENT'], 0,'', ' ')]);?></span>
            </div>
          </label>
        </div>
      <?endif?>
    </div>
            
    <div class="cols-order buttons">
      <div class="col-order wrap-buttons-between">
        <div class="description-help"><?=Loc::getMessage('SALE_ORDER_HELP_MANAGER')?></div>
        <a class="ibutton hollow group-width js-metrika-order-phone" href="tel:+78003505670"><?=Loc::getMessage('SALE_ORDER_CALL')?> 8 800 350-5670</a>
        <a class="ibutton hollow group-width js-metrika-order-whatsapp" href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" title="<?=Loc::getMessage('SALE_ORDER_WHATSAPP_TITLE')?>"><?=Loc::getMessage('SALE_ORDER_WHATSAPP')?></a>
      </div>
      <div class="col-order wrap-buttons-between">
        <a class="ibutton hollow group-width js-button-back-to-cart" href="<?= $arParams["PATH_TO_BASKET"] ?>"><?=Loc::getMessage('SALE_ORDER_BACK_CART')?></a>
        <a class="ibutton hollow group-width js-button-back-to-order"><?=Loc::getMessage('SALE_ORDER_BACK')?></a>
        <?
          $nameSend = Loc::getMessage('SALE_ORDER_PAY');
          if(count($arResult["JS_DATA"]["PAY_SYSTEM"]) > 1 && $arParams['COL_PAYMENT'] != 'Y')
            $nameSend = Loc::getMessage('SALE_ORDER_CONTINUE');
          
          // Если мы на экране выбора оплаты
          if(count($arResult["JS_DATA"]["PAY_SYSTEM"]) > 1 && $arParams['COL_PAYMENT'] == 'Y')
          {
            foreach($arResult["JS_DATA"]["PAY_SYSTEM"] as $arPaySystem)
            {
              if($arPaySystem['PAY_SYSTEM_ID'] == 10 && $arPaySystem['CHECKED'] == 'Y')
                $nameSend = Loc::getMessage('SALE_ORDER_SEND');
            }
          }
        ?>
        <input class="ibutton group-width js-metrika-order-send" type="submit" value="<?=$nameSend?>" data-name-pay="<?=Loc::getMessage('SALE_ORDER_PAY')?>" data-name-continue="<?=Loc::getMessage('SALE_ORDER_CONTINUE')?>" name="BasketOrder" id="basketOrderButton2">
      </div>
    </div>

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

?>
<script>
  BX.message({
    SALE_ORDER_JS_ENTER_SHIPPING_ADDRESS: '<?=Loc::getMessage('SALE_ORDER_JS_ENTER_SHIPPING_ADDRESS')?>',
    SALE_ORDER_JS_ENTER_PHONE: '<?=Loc::getMessage('SALE_ORDER_JS_ENTER_PHONE')?>',
    SALE_ORDER_JS_ENTER_EMAIL: '<?=Loc::getMessage('SALE_ORDER_JS_ENTER_EMAIL')?>',
    SALE_ORDER_JS_CONSENT_PERSONAL_DATA: '<?=Loc::getMessage('SALE_ORDER_JS_CONSENT_PERSONAL_DATA')?>',
    SALE_ORDER_JS_CONSENT_RULES_RETURN: '<?=Loc::getMessage('SALE_ORDER_JS_CONSENT_RULES_RETURN')?>',
    SALE_ORDER_JS_SELECT_PICKUP_POINT: '<?=Loc::getMessage('SALE_ORDER_JS_SELECT_PICKUP_POINT')?>',
    SALE_ORDER_JS_PAY: '<?=Loc::getMessage('SALE_ORDER_JS_PAY')?>',
    SALE_ORDER_JS_SEND: '<?=Loc::getMessage('SALE_ORDER_SEND')?>',
    SALE_ORDER_JS_INDICATE_NAME: '<?=Loc::getMessage('SALE_ORDER_JS_INDICATE_NAME')?>',
    SALE_ORDER_JS_INDICATE_PHONE: '<?=Loc::getMessage('SALE_ORDER_JS_INDICATE_PHONE')?>',
    SALE_ORDER_JS_INDICATE_EMAIL: '<?=Loc::getMessage('SALE_ORDER_JS_INDICATE_EMAIL')?>',
    SALE_ORDER_JS_INDICATE_VALID_EMAIL: '<?=Loc::getMessage('SALE_ORDER_JS_INDICATE_VALID_EMAIL')?>',
    SALE_ORDER_JS_ADD_USER: '<?=Loc::getMessage('SALE_ORDER_JS_ADD_USER')?>',
    SALE_ORDER_JS_INDICATE_VALID_NAME: '<?=Loc::getMessage('SALE_ORDER_JS_INDICATE_VALID_NAME')?>',
    SALE_ORDER_NOT_AVAILABLE_POPUP: '<?=Loc::getMessage('SALE_ORDER_NOT_AVAILABLE_POPUP')?>',
    SALE_ORDER_AVAILABLE_POPUP: '<?=Loc::getMessage('SALE_ORDER_AVAILABLE_POPUP')?>',
    ERROR_PHONE: '<?=Loc::getMessage('ERROR_PHONE')?>',
  });
</script>
