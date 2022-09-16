<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
?>
</div>
</article>
</div>

<div class="sliderIndexTwo-block">
	<div class="sliderIndexTwo swiper">
		<div class="wrapper">
			<h2 class="font-caption"><?=Loc::getMessage('CAROUSEL_TITLE');?></h2>
		</div>
		<div class="swiper-wrapper">
			<?php
			foreach ($arResult['ITEMS'] as $item) {
			?>
				<div class="swiper-slide" data-title="<?= Loc::getCurrentLang() == 'ru' ? $item['NAME'] :  $item['ENG_NAME']; ?>" data-description="<?= Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] :  $item['ENG_DESCRIPTION']; ?>">
					<a href="<?= $item['LINK']; ?>"><img src="<?= $item['THUMB']; ?>" /></a>
				</div>
			<?php
			}
			?>
		</div>
		<div class="sliderIndexTwo-textblock">
			<div class="sliderIndexTwo-textblock_item">
				<div class="sliderIndexTwo-text swiper">
					<div class="swiper-wrapper">
						<?php
						foreach ($arResult['ITEMS'] as $item) {
						?>
							<div class="swiper-slide" data-title="<?= Loc::getCurrentLang() == 'ru' ? $item['NAME'] :  $item['ENG_NAME']; ?>" data-description="<?= Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] :  $item['ENG_DESCRIPTION']; ?>">
								<span>New Season</span>
								<a href="<?= $item['LINK']; ?>"><?= Loc::getCurrentLang() == 'ru' ? $item['NAME'] :  $item['ENG_NAME']; ?></a>
								<p class="sliderIndexTwo-img-textInfo"><?= Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] :  $item['ENG_DESCRIPTION']; ?></p>
							</div>
						<?php
						}
						?>

					</div>
				</div>
			</div>
		</div>
		<div class="swiper-pagination"></div>
	</div>
</div>

<div class="wrapper content">
	<article>
		<div data-content>