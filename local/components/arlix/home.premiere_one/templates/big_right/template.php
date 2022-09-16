<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
  }
  /** @var array $arParams */
  /** @var array $arResult */
  ?>
<div class="premiere-one premiere-one-right">
	<div class="indent">
		<?php
			foreach ($arResult['ITEMS'] as $item){
				?>
				<div class="info">
					<div class="caption"><a href="<?=$item['LINK'];?>" class="font-caption hover">Бестселлеры</a></div>
					<div class="description font-text-big"><?=$item['DESCRIPTION'];?></div>
					<a class="ibutton big hollow" href="<?=$item['LINK'];?>">SHOP NOW</a>
				</div>
				<div class="image">
					<a href="<?=$item['LINK'];?>"><img src="<?=$item['THUMB'];?>"></a>
				</div>
				<?php
			}
		?>
	</div>
</div>

