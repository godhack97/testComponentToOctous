<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Main\UserPhoneAuthTable;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//if (isset($templateData['TEMPLATE_THEME']))
//{
//	$APPLICATION->SetAdditionalCSS($templateFolder.'/themes/'.$templateData['TEMPLATE_THEME'].'/style.css');
//	$APPLICATION->SetAdditionalCSS('/bitrix/css/main/themes/'.$templateData['TEMPLATE_THEME'].'/style.css', true);
//}

$APPLICATION->SetPageProperty('og:image', SITE_SERVER_PROTOCOL . SITE_SERVER_NAME . $arResult['OG_IMAGE']);

if (!empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;

	if (!empty($templateData['CURRENCIES']))
	{
		$loadCurrency = Loader::includeModule('currency');
	}

	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
		?>
		<script>
			BX.Currency.setCurrencies(<?=$templateData['CURRENCIES']?>);
		</script>
		<?
	}
}

if (isset($templateData['JS_OBJ']))
{
	?>
	<script>
		BX.ready(BX.defer(function(){
			if (!!window.<?=$templateData['JS_OBJ']?>)
			{
				window.<?=$templateData['JS_OBJ']?>.allowViewedCount(true);
			}
		}));
	</script>

	<?
	// check compared state
	if ($arParams['DISPLAY_COMPARE'])
	{
		$compared = false;
		$comparedIds = array();
		$item = $templateData['ITEM'];

		if (!empty($_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]))
		{
			if (!empty($item['JS_OFFERS']))
			{
				foreach ($item['JS_OFFERS'] as $key => $offer)
				{
					if (array_key_exists($offer['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS']))
					{
						if ($key == $item['OFFERS_SELECTED'])
						{
							$compared = true;
						}

						$comparedIds[] = $offer['ID'];
					}
				}
			}
			elseif (array_key_exists($item['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS']))
			{
				$compared = true;
			}
		}

		if ($templateData['JS_OBJ'])
		{
			?>
			<script>
				BX.ready(BX.defer(function(){
					if (!!window.<?=$templateData['JS_OBJ']?>)
					{
						window.<?=$templateData['JS_OBJ']?>.setCompared('<?=$compared?>');

						<? if (!empty($comparedIds)): ?>
						window.<?=$templateData['JS_OBJ']?>.setCompareInfo(<?=CUtil::PhpToJSObject($comparedIds, false, true)?>);
						<? endif ?>
					}
				}));
			</script>
			<?
		}
	}

	// select target offer
	$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
	$offerNum = false;
	$offerId = (int)$this->request->get('OFFER_ID');
	$offerCode = $this->request->get('OFFER_CODE');

	if ($offerId > 0 && !empty($templateData['OFFER_IDS']) && is_array($templateData['OFFER_IDS']))
	{
		$offerNum = array_search($offerId, $templateData['OFFER_IDS']);
	}
	elseif (!empty($offerCode) && !empty($templateData['OFFER_CODES']) && is_array($templateData['OFFER_CODES']))
	{
		$offerNum = array_search($offerCode, $templateData['OFFER_CODES']);
	}

	if (!empty($offerNum))
	{
		?>
		<script>
			BX.ready(function(){
				if (!!window.<?=$templateData['JS_OBJ']?>)
				{
					window.<?=$templateData['JS_OBJ']?>.setOffer(<?=$offerNum?>);
				}
			});
		</script>
		<?
	}
}

$userPhone = UserPhoneAuthTable::getList(['filter' => ['USER_ID' => $USER->GetID()]])->fetch();

$arResult['USER_EMAIL'] = $USER->GetEmail();
if(isset($userPhone['PHONE_NUMBER'])){
  $arResult['USER_PHONE'] = $userPhone['PHONE_NUMBER'];
}
else{
  $arResult['USER_PHONE'] = false;
}

echo preg_replace_callback(

  "/#APS_(.*?)#/is".BX_UTF_PCRE_MODIFIER,

  function ($matches) use ($arResult)
  {
    $staticHTMLCache = \Bitrix\Main\Data\StaticHTMLCache::getInstance();
    $staticHTMLCache->disableVoting();
    ob_start();
    $typeSplit = explode('_', $matches[1]);
    $isMobile = ($typeSplit == 'MOBILE') ? true : false;

    $GLOBALS["APPLICATION"]->IncludeComponent(
      "arlix:product.subscribe",
      "",
      array(
        "PRODUCT_ID" => $typeSplit[1],
        //"PRODUCT_ID" => $arResult['ID'],
        //"OFFERS" => $arResult['OFFERS'],
        "ARTICLE" => $arResult['ARTICLE'],
        "AUTH" => $arResult['IS_AUTH'],
        "EMAIL" => $arResult['USER_EMAIL'],
        "PHONE" => $arResult['USER_PHONE'],
        "IS_MOBILE" => $isMobile,
      )
    );

    $retrunStr = @ob_get_contents();
    ob_get_clean();
    $staticHTMLCache->enableVoting();
    return $retrunStr;
  },

  $arResult["CACHED_TPL"], -1);


if(LANGUAGE_ID == 'en') 
{
  $arResult['IPROPERTY_VALUES']['ELEMENT'] = "detail";
  $_SERVER["META_DATA"] = $arResult['IPROPERTY_VALUES'];
}
