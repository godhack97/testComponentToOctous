<?php
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);?>
<div class="footer-menu footer-item" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
	<?php if (!empty($arResult)):?>
		<?php foreach($arResult as $arItem):?>
			<div class="footer-menu-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
				<a class="hover" href="<?=$arItem["LINK"]?>" itemprop="url">
					<?=$arItem["TEXT"]?>
					<meta itemprop="name" content="<?=$arItem["TEXT"]?>" />
				</a>
			</div>
		<?php endforeach;?>
	<?php endif?>
</div>