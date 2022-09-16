<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
		die();
	}
	use Bitrix\Main\Localization\Loc;

	/** @var array $arParams */
	/** @var array $arResult */

	$view = 'right';
	foreach ($arResult['ITEMS'] as $item){
		switch ($view){
			case 'left':
				$view = 'right';
				?>
				<div class="premiere-one premiere-one-left">
					<div class="indent">
						<div class="image">
							<a href="<?=$item['LINK'];?>"><img src="<?=$item['THUMB'];?>"></a>
						</div>
						<div class="info">
							<div class="caption"><a href="<?=$item['LINK'];?>" class="font-caption hover"><?=Loc::getCurrentLang() == 'ru' ? $item['NAME'] : $item['ENG_NAME'];?></a></div>
							<div class="description font-text-big"><?=Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] : $item['ENG_DESCRIPTION'];?></div>
							<a href="<?=$item['LINK'];?>" class="ibutton big hollow"><?=Loc::getMessage('MORE');?></a>
						</div>
					</div>
				</div>

				<?php
				break;
			case 'right':
				$view = 'left';
				?>
				<!-- <div class="premiere-one premiere-one-right">
					<div class="indent">
						<div class="info">
							<div class="caption"><a href="<?=$item['LINK'];?>" class="font-caption hover"><?=Loc::getCurrentLang() == 'ru' ? $item['NAME'] : $item['ENG_NAME'];?></a></div>
							<div class="description font-text-big"><?=Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] : $item['ENG_DESCRIPTION'];?></div>
							<a href="<?=$item['LINK'];?>" class="ibutton big hollow"><?=Loc::getMessage('MORE');?></a>
						</div>
						<div class="image">
							<a href="<?=$item['LINK'];?>"><img src="<?=$item['THUMB'];?>"></a>
						</div>
					</div>
				</div> -->

				<div class="premiere-one premiere-one-right">
					<div class="indent">
						<div class="info">
							<div class="caption"><a href="<?=$item['LINK'];?>" class="font-caption hover"><?=Loc::getCurrentLang() == 'ru' ? $item['NAME'] : $item['ENG_NAME'];?></a></div>
							<div class="description font-text-big"><?=Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] : $item['ENG_DESCRIPTION'];?></div>
							<a href="<?=$item['LINK'];?>" class="ibutton big hollow"><?=Loc::getMessage('MORE');?></a>
						</div>
						<div class="images">
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-1.jpg"></a>
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-2.jpg"></a>
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-3.jpg"></a>
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-4.jpg"></a>
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-5.jpg"></a>
							<a href="/"><img src="<?= SITE_TEMPLATE_PATH ?>/images/bestsellers/bestseller-6.jpg"></a>
						</div>
					</div>
				</div>
				<?php
				break;
		}
	}
?>

