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

<div class="complect-premiere complect-premiere-bot">
		<div class="wrapper">
			<div class="complect-premiere__title font-caption"><?=$arResult['TITLE'];?></div>
		</div>
		<div class="complect-premiere__content">
			<div class="content-list">
				<?php
				$index = 1;
				foreach ($arResult['ITEMS'] as $item) {
					if ($index > 1) {
          ?>
						<div class="content-list__item content-list__item-<?= $index; ?>">
							<div class="item-info">
								<a href="<?= $item['LINK']; ?>" class="item-title hover"><?= Loc::getCurrentLang() == 'ru' ? $item['NAME'] : $item['ENG_NAME']; ?></a>
								<p class="item-desc"><?= Loc::getCurrentLang() == 'ru' ? $item['DESCRIPTION'] :  $item['ENG_DESCRIPTION']; ?></p>
								<p class="item-price"><?= FormatCurrency($item['PRICE']['PRICE'], $item['PRICE']['CURRENCY']); ?></p>
							</div>
							<a href="<?= $item['LINK']; ?>" class="item-img">
								<div class="item-img-wrapper">
									<img src="<?= $item['THUMB']; ?>" alt="">
								</div>
							</a>
						</div>
          <?php
					}
					$index++;
				}
				?>
			</div>
			<?php
			if (isset($arResult['ITEMS'][3])) {
			?>
				<a href="<?= $arResult['ITEMS'][0]['LINK']; ?>" class="content-main">
					<img src="<?= $arResult['ITEMS'][0]['THUMB']; ?>" alt="">
				</a>
			<?php
			}
			?>
		</div>
</div>

<div class="wrapper content">
<article>
<div data-content>