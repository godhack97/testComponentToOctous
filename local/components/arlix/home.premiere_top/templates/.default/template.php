<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
?>
<div class="slider-premiere">
	<div class="slider-premiere-init">
		<?php
		foreach ($arResult['ITEMS'] as $item) {
		?>
			<div class="item"><a href="<?= $item['LINK']; ?>"><img src="<?= $item['THUMB']; ?>"></a></div>
		<?php
		}
		?>
	</div>
	<div class="caption font-caption"><?=Loc::getMessage('CAPTION');?></div>
	<div class="description font-text-big"><?=Loc::getMessage('DESCRIPTION');?></div>
</div>