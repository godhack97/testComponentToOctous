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
?>
<?if($USER->IsAdmin()):?>
  <div class="share-favorites">
    <a class="label hover js-favorite-share">Поделиться списком желаний</a>
    <!-- img src="/local/templates/sodamoda/images/icons/icon-share.svg" -->
  </div>
<?endif?>

<table class="basket-table">
  <tbody>
    <tr class="head">
      <th class="image">Товар<?=Loc::getMessage('SALE_PERSONAL_ORDER_NAME')?></th>
      <th class="desc"></th>
      <th class="size">Размер<?=Loc::getMessage('SALE_PERSONAL_ORDER_SIZE')?></th>
      <th class="summa">Цена<?=Loc::getMessage('SALE_PERSONAL_ORDER_PRICE')?></th>
      <th class="control">Удалить</th>
    </tr>
  </tbody>
  <tbody id="basket-item-table">

    <?foreach($arResult["ITEMS"] as $arElement):?>
      <?
      $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
      $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
      //dbg($arElement);
      if(!empty($arElement['PROPERTIES']['COLOR']['XML_ID']))
        $arElement['DETAIL_PAGE_URL'] .= '?setColor='. $arElement['PROPERTIES']['COLOR']['VALUE'] .'&offer='. $arElement['ID'];

      $SIZE = $arElement['PROPERTIES']['SIZES']['VALUE'];
      ?>
      <tr class="basket-items-list-item-container" data-id="<?=$arElement['ID']?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
        <td class="image">
          <a href="<?=$arElement['DETAIL_PAGE_URL']?>" class="image hover" alt="<?=$arElement['NAME']?>" style="background-image: url(<?=$arElement['PICTURE']['SRC']?>)" target="_blank"></a>
        </td>
        <td class="desc">
          <div class="name"><a class="hover" target="_blank" href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement['NAME']?></a></div>
          <div class="mobile-size"><?=$SIZE?></div>
          <div class="props">
            <div class="prop" data-entity="basket-item-property">
              <span class="title">Артикул</span>
              <span class="value"><?=$arElement['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span>
            </div>
          </div>
        </td>
        <td class="size"><?=$SIZE?></td>
        <td class="summa">

          <?foreach($arResult["PRICES"] as $code=>$arPrice):?>

            <?if($arPrice = $arElement["PRICES"][$code]):?>
              <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                <s><?=$arPrice["PRINT_VALUE"]?></s> <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
              <?else:?>
                <?=$arPrice["PRINT_VALUE"]?>
              <?endif?>
            <?endif?>

          <?endforeach?>

        </td>
        <td class="control">
          <a class="delete hover js-remove-favorite js-favorite-button" data-id="<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/times.svg"></a>
        </td>
      </tr>
    <?endforeach?>

  </tbody>
</table>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif?>
