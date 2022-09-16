<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
	<div class="basket-checkout-container" data-entity="basket-checkout-aligner">
		<?
		if ($arParams['HIDE_COUPON'] !== 'Y')
		{
			?>
			<div class="basket-coupon-section form">
				<div class="basket-coupon-block-field">
          <div class="field">
            <input type="text" class="field-style" placeholder=" " size="40" value="" id="field-order-create-basket-coupon" aria-required="true" required="" data-entity="basket-coupon-input">
            <label class="placeholder" for="field-order-create-basket-coupon"><?=Loc::getMessage('SBB_COUPON_ENTER')?></label>
          </div>
				</div>
			</div>
			<?
		}
		?>

        <div class="total-wrap">
            <table class="total-table">

                <tr class="total">
                    <td><?=Loc::getMessage('SALE_BASKET_FULL_PRICE')?></td>
                    <td data-entity="basket-total-price-f">{{FULL_PRICE_FORMATED}}</td>
                </tr>
                <!-- tr class="comment">
                    <td>* Включая НДС</td>
                    <td></td>
                </tr -->
            </table>
        </div>



		<?
		if ($arParams['HIDE_COUPON'] !== 'Y')
		{
		?>
			<div class="basket-coupon-alert-section">
				<div class="basket-coupon-alert-inner">
					{{#COUPON_LIST}}
					<div class="basket-coupon-alert text-{{CLASS}}">
						<span class="basket-coupon-text">
							<strong>{{COUPON}}</strong> - <?=Loc::getMessage('SBB_COUPON')?> {{JS_CHECK_CODE}}
							{{#DISCOUNT_NAME}}({{{DISCOUNT_NAME}}}){{/DISCOUNT_NAME}}
						</span>
						<span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
							<?=Loc::getMessage('SBB_DELETE')?>
						</span>
					</div>
					{{/COUPON_LIST}}
				</div>
			</div>
			<?
		}
		?>
	</div>
</script>