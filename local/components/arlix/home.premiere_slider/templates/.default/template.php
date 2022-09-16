<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
		die();
	}
	/** @var array $arParams */
	/** @var array $arResult */
	use Bitrix\Main\Localization\Loc;
?>
<div class="slider-premiere">
	<div class="indent">
		<?php
			foreach ($arResult['ITEMS'] as $item){
				?>
				<div class="item"><a href="/catalog/vyazannyy-trikotazh/<? // =$item['LINK'];?>"><img src="<?=$item['THUMB'];?>"></a></div>
				<?php
			}
		?>
	</div>
	<div class="caption"><a href="/catalog/vyazannyy-trikotazh/" class="font-caption hover"><?=Loc::getMessage('SLIDER_TITLE');?></a></div>
	<div class="description"><a href="/catalog/vyazannyy-trikotazh/" class="font-text-big hover"><?=Loc::getMessage('SLIDER_TEXT');?></a></div>
	<div class="nav">
		<div class="nav-item active"></div>
		<div class="nav-item"></div>
		<div class="nav-item"></div>
		<div class="nav-item"></div>
		<div class="nav-item"></div>
	</div>
</div>