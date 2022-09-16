<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(count($arResult['ITEMS']) > 0)
{
	?>
	
	<div class="contacts-sections">
	
		<?
		foreach($arResult['ITEMS'] as $arItem)
		{
      if(!empty($arItem['PROPERTIES']['CITY']['VALUE']))
        $arItem['NAME'] .= ', '. $arItem['PROPERTIES']['CITY']['VALUE'];
		?>	
			<div class="block">
				<div class="cols">
					<h2><?=$arItem['NAME']?></h2>
          
          <?if($arItem['DETAIL_TEXT']):?>
            <p><?=$arItem['DETAIL_TEXT']?></p>
          <?endif?>
          
					<?
					foreach($arItem['PROPERTIES'] as $key => $arProperty)
					{
						if(isset($arProperty['VALUE']) && in_array($key, ['RESPONSIBILITIES', 'REQUIREMENTS', 'WE_OFFER']))
						{
						?>
							<h3><?=$arProperty['NAME']?></h3>
							<ul>						
								<?
								foreach($arProperty['VALUE'] as $val)
								{
								?>							
									<li><?=$val?></li>						
								<?
								}
								?>					
							</ul>
						<?
						}
					}
					?>
					
					<span data-comment="span здесь нужен для отступа до кнопок"></span>
				</div>
				
				<div class="cols">
					<button type="button" class="ibutton hollow js-open-popup-name fill-fields" data-form-name="vacancy">Отправить отклик</button>
				</div>
			</div>
		<?	
		}
		?>
		
	</div>

<?
}
else
{
	echo 'В данный момент нет доступных вакансий.';
}
?>

