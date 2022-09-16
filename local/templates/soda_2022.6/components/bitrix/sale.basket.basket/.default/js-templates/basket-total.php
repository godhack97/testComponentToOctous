<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="basket-checkout-container" data-entity="basket-checkout-aligner">

    <div class="total-wrap total-basket">

      <?if($arParams['HIDE_COUPON'] !== 'Y'):?>
        <div class="basket-coupon-wrapper">
          <div class="basket-coupon-alert-section">
            <div class="basket-coupon-alert-inner">
              {{#COUPON_LIST}}
              <div class="basket-coupon-alert text-{{CLASS}}">
                <span class="basket-coupon-text">
                  <strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
                  {{#DISCOUNT_NAME}}({{{DISCOUNT_NAME}}}){{/DISCOUNT_NAME}}
                </span>
                <a class="close-link hover" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                  <?=Loc::getMessage('SBB_DELETE')?>
                </a>
              </div>
              {{/COUPON_LIST}}
            </div>
          </div>
          <div class="basket-coupon-section">
            <div class="basket-coupon-block-field">
              <div class="form">
                <div class="form-group field">
                  <input type="text" class="form-control field-style" id="field-coupon" placeholder=" " data-entity="basket-coupon-input">
                  <label class="placeholder" for="field-coupon"><?=Loc::getMessage('SBB_COUPON_ENTER')?></label>
                  <span class="basket-coupon-block-coupon-btn"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?endif?>
          
	    <table class="total-table">
		    <tr class="info">
			    <td><?=Loc::getMessage('SALE_BASKET_TOTAL_PRICE')?></td>
			    <td data-entity="basket-total-discount-price">{{PRICE_WITHOUT_DISCOUNT_FORMATED}}</td>
        </tr>
        <tr class="info">
	        <td><?=Loc::getMessage('SALE_BASKET_TOTAL_DISCOUNT_PRICE')?></td>
	        <td data-entity="basket-total-price">{{PRICE_FORMATED}}</td>
        </tr>
	      <tr class="info">
		      <td><?=Loc::getMessage('SALE_BASKET_ESTIMATED_DELIVERY_PRICE')?></td>
		      <td>{{FULL_PRICE_DELIVERY_FORMATED}}</td>
	      </tr>
		    
		    <?if($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET']):?>
			    <tr class="info">
				    <td><?=Loc::getMessage('SALE_BASKET_AVAILABLE')?></td>
				    <td><?=number_format($arResult['DOMESTIC_ACCOUNT']['CURRENT_BUDGET'], 0,'', ' ')?> <?=Loc::getMessage('SALE_BASKET_POINTS')?></td>
			    </tr>
		    <?endif;?>

		    <tr class="total">
			    <td><?=Loc::getMessage('SALE_BASKET_TOTAL')?></td>
			    <td data-entity="basket-total-price-f">{{FULL_PRICE_FORMATED}}</td>
		    </tr>
                <!-- tr class="comment">
                    <td>* Включая НДС</td>
                    <td></td>
                </tr -->
            </table>
        </div>

        <div class="cols-order buttons">
          <div class="col-order wrap-buttons-between">
            <div class="description-help"><?=Loc::getMessage('SALE_BASKET_HELP')?></div>
            <a class="ibutton hollow group-width js-metrika-order-phone" href="tel:+78003505670"><?=Loc::getMessage('SALE_BASKET_CALL')?> 8 800 350-5670</a>
            <a class="ibutton hollow group-width js-metrika-order-whatsapp" href="https://api.whatsapp.com/send?phone=79688700070" target="_blank" title="<?=Loc::getMessage('SALE_BASKET_WHATSAPP_TITLE')?>"><?=Loc::getMessage('SALE_BASKET_WHATSAPP')?></a>
          </div>
          <div class="col-order wrap-buttons-between">
            <a class="ibutton group-width hollow" href="<?=$arParams["EMPTY_BASKET_HINT_PATH"]?>"><?=Loc::getMessage('SALE_BASKET_CONTINUE_SHOPING')?></a>
            <input type="button" class="ibutton group-width js-metrika-order-start" data-entity="basket-checkout-button" value="<?=Loc::getMessage('SBB_ORDER')?>">
          </div>
        </div>
    
    
					</div>
</script>