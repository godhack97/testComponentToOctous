<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>

<div class="products-empty">
	<?=Loc::getMessage("SBB_EMPTY_BASKET_TITLE")?>
	<?
	if (!empty($arParams['EMPTY_BASKET_HINT_PATH']))
	{
		echo '<br>'. Loc::getMessage(
				'SBB_EMPTY_BASKET_HINT',
				[
					'#A1#' => '<a class="black hover" href="'.$arParams['EMPTY_BASKET_HINT_PATH'].'">',
					'#A2#' => '</a>',
				]
    );
	}
	?>
</div>