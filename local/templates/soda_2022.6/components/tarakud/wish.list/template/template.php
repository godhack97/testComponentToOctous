<?
use Bitrix\Main\Localization\Loc;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->addExternalCss("/bitrix/css/main/bootstrap.min.css");
$this->addExternalCss("/bitrix/css/main/font-awesome.min.css");

if (!empty($arParams["SOCIAL"])) {
	$APPLICATION->AddHeadString('<script src="https://yastatic.net/share2/share.js" async="async"></script>');
}

//dbgz($arResult);
?>

<?if (!empty($arResult["ITEMS"])):?>
	<?if ($arResult["PERSONAL"]["ACCESS_SOCIAL"]):?>
		<div class="row">
			<div class="col-sm-12">
				<?=Loc::getMessage('WISH_SOC_LIST')?> <a href="//<?=$arResult["PERSONAL"]["SHARE_URL"]?>">https://<?=$arResult["PERSONAL"]["SHARE_URL"]?></a>
				<span class="ya-share2" data-services="<?=$arParams["SOCIAL_TEXT"]?>" data-description="<?=Loc::getMessage('WISH_SOC_DESC')?>" data-title="<?=Loc::getMessage('WISH_SOC_TITLE')?>" data-url="<?=$arResult["PERSONAL"]["SHARE_URL"]?>"></span>
			</div>
		</div><br>
	<?endif?>
	

	<div class="table-responsive">
	<table class="table table-condensed table-responsive">
		<thead>
			<tr>
				<td colspan="4" class="wish-record">
					<?
					echo str_replace("#RECORD#", $arResult["RECORD_COUNT"], Loc::getMessage('WISH_RECORD'));
					?>
				</td>
			</tr>
		</thead>
		<?foreach($arResult["ITEMS"] as $k => $arItem):?>
		<tr class="js-wishitem-<?=$arItem["ID"]?>">
			<td class="wish-td-img text-center">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
					<?if ($arItem["PICTURE"]):
						$url = $arItem["PICTURE"]["SRC"];
					else:
						$url = $this->GetFolder().'/images/no_photo.png';
					endif;?>
					<img src="<?=$url?>" alt="<?=$arItem["NAME"]?>">
				</a>
			</td>
			<td>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
			</td>
			<td class="wish-td-price" nowrap>
				<?if (isset($arItem["PRICE"]["MIN_PRICE_FROM"])):?>
					<?=$arItem["PRICE"]["MIN_PRICE_FROM"]?>
				<?else:?>
					<?=$arItem["PRICE"]["PRINT_DISCOUNT_VALUE"]?>
				<?endif;?>
			</td>
			<td class="wish-td-remove">
				<?if ($arResult["PERSONAL"]["ACCESS_DELETE"]):?>
					<a href="<?=$arParams["WISHLIST_URL"]?>?id=<?=$arItem["ID"]?>" title="<?=Loc::getMessage('WISH_DEL')?>" class="js-wish-delete" data-id="<?=$arItem["ID"]?>"><i class="fa fa-times"></i></a>
				<?endif;?>
			</td>
		</tr>
		<?endforeach;?>
	</table>
	</div>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br /><?=$arResult["NAV_STRING"]?>
	<?endif;?>
<?else:?>
	<?=Loc::getMessage('WISH_NULL');?>
<?endif;?>

<?if ($arParams["AJAX_DELETE"]):?>
	<script>
	if (typeof oWishlist == "undefined") {
		let oWishlist = new CWishlist();
	}
	</script>
<?endif;?>