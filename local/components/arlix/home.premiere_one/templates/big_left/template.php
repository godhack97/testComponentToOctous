<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
  }
  /** @var array $arParams */
  /** @var array $arResult */
  ?>
<div class="premiere-one premiere-one-left">
	<div class="indent">
		<?php
			foreach ($arResult['ITEMS'] as $item){
				?>
				<div class="image">
					<a href="<?=$item['LINK'];?>"><img src="<?=$item['THUMB'];?>"></a>
				</div>
				<div class="info">
					<div class="caption"><a href="<?=$item['LINK'];?>" class="font-caption hover"><?=$item['NAME'];?></a></div>
					<div class="description font-text-big"><?=$item['DESCRIPTION'];?></div>
					<a class="ibutton big hollow" href="<?=$item['LINK'];?>">SHOP NOW</a>
				</div>
				<?php
			}
		?>
	</div>
</div>

