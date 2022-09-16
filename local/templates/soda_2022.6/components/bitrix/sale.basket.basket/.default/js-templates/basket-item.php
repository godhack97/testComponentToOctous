<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

$restoreColSpan = 2 + $usePriceInAdditionalColumn + $useSumColumn + $useActionColumn;

$positionClassMap = array(
	'left' => 'basket-item-label-left',
	'center' => 'basket-item-label-center',
	'right' => 'basket-item-label-right',
	'bottom' => 'basket-item-label-bottom',
	'middle' => 'basket-item-label-middle',
	'top' => 'basket-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}
?>
<script id="basket-item-template" type="text/html">
	<tr class="basket-items-list-item-container{{#SHOW_RESTORE}} basket-items-list-item-container-expend{{/SHOW_RESTORE}}{{#NOT_AVAILABLE}} not_available{{/NOT_AVAILABLE}}"
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">

        <td class="image">
            <a href="{{DETAIL_PAGE_URL}}" class="image hover" alt="{{NAME}}" style='background-image: url("{{{IMAGE_URL}}}{{^IMAGE_URL}}<?=$templateFolder?>/images/no_photo.png{{/IMAGE_URL}}")' target="_blank"></a>
        </td>

        <td class="desc">
            <div class="name"><a class="hover" target="_blank" href="{{DETAIL_PAGE_URL}}">{{NAME}}</a></div>
            <div class="mobile-size">
              {{#PROPS_ALL}}
                  {{#IS_TEXT}}
                      {{#IS_PROPERTY_SIZES_VALUE}}
                          {{VALUE}}
                      {{/IS_PROPERTY_SIZES_VALUE}}
                  {{/IS_TEXT}}
              {{/PROPS_ALL}}
            </div>
            <div class="props">
                {{#PROPS_ALL}}
                    {{#IS_TEXT}}
                        {{#IS_SHOW_LEFT}}
                            <div class="prop" data-entity="basket-item-property">
                                <span class="title">{{NAME}}</span>
                                {{#IS_PREORDER_PRODUCT_VALUE}}
                                  <span class="value" data-column-property-code="{{CODE}}" data-entity="basket-item-property-column-value">{{VALUE}}</span>
                                {{/IS_PREORDER_PRODUCT_VALUE}}
                            </div>
                        {{/IS_SHOW_LEFT}}
                    {{/IS_TEXT}}
                {{/PROPS_ALL}}
            </div>
        </td>

        <td class="size">
            {{#PROPS_ALL}}
                {{#IS_TEXT}}
                    {{#IS_PROPERTY_SIZES_VALUE}}
                        {{VALUE}}
                    {{/IS_PROPERTY_SIZES_VALUE}}
                {{/IS_TEXT}}
            {{/PROPS_ALL}}
        </td>

        <td class="count">
            <div class="quantity-choice" data-entity="basket-item-quantity-block">
                <div data-entity="basket-item-quantity-minus" class="item hover minus"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/direction-prev.svg"></div>
                <input
                        data-value="{{QUANTITY}}"
                        data-entity="basket-item-quantity-field"
                        id="basket-item-quantity-{{ID}}"
                        type="text"
                        value="{{QUANTITY}}"{{#NOT_AVAILABLE}} disabled="disabled"{{/NOT_AVAILABLE}}
                >
                <div data-entity="basket-item-quantity-plus" class="item hover plus"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/direction-next.svg"></div>
            </div>
        </td>

        <td class="summa" id="basket-item-sum-price-{{ID}}">
          {{#SHOW_DISCOUNT_PRICE}}<span class='basket-item-price-old-text'>{{{FULL_PRICE_FORMATED}}}</span> {{/SHOW_DISCOUNT_PRICE}}
	        {{{SUM_PRICE_FORMATED}}}
        </td>
        <td class="control">
          {{#NOT_AVAILABLE}}
          <div class='basket-items-list-item-warning-container'>
            <div class='alert alert-warning text-center'>
              <?= Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE') ?>.
            </div>
          </div>
          {{/NOT_AVAILABLE}}
            <a class="delete hover" data-entity="basket-item-delete"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/times.svg"></a>
        </td>
	</tr>
</script>