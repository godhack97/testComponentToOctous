<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$arSizeCropPreload = array('width' => 192, 'height' => 288);
$arSizeCropMobile2 = array('width' => 414, 'height' => 621);
$arSizeCropMobile = array('width' => 620, 'height' => 930);
$arSizeCropTablet = array('width' => 768, 'height' => 1152);
$arSizeCrop = array('width' => 980, 'height' => 1470);
?>

<div class="news">

  <h1 class="font-caption"><?= Loc::getMessage("NEWS_LIST_TITLE") ?></h1>
  <? if (count($arResult["ITEMS"]) > 0): ?>

    <div class="news-content">

      <div class="news-content__left js-sticky-kit-go">
        <div class="news-cards-list">

          <? foreach ($arResult["ITEMS"] as $key => $arItem): ?>
            <?
            if (!empty($arItem['PROPERTIES']['MAIN_ARTICLE']['VALUE'])) {
              $mainArticle = $arItem;
              continue;
            } else {
              $arResult["ITEMS"][$key]["LATEST"] = true;
            }
            ?>

            <?
            $arImagePreload = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
            $arImageTablet = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
            $arImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
            ?>

            <? if ($arResult["ITEMS"][$key]["LATEST"]): ?>
              <div class="news-card card-main">
                <div class="image">
                  <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                    <picture>
                      <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                              data-src="<?= $arImageTablet['src'] ?>"/>
                      <img src="<?= $arImagePreload['src'] ?>" alt="articles img"
                           data-src="<?= $arImage['src'] ?>">
                    </picture>
                  </a>
                </div>
                <div class="description">
                  <span class="subtitle"><?= $arItem['PROPERTIES']['CATEGORY_ARTICLE']['VALUE'] ?></span>
                  <div class="name-block">
                    <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"
                       class="name hover"><?= $arItem['NAME'] ?></a>
                  </div>
                  <span class="creator">By SODAMODA</span>
                  <span class="date"><?= $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
                </div>
              </div>
              <? break; ?>
            <? endif ?>
          <? endforeach; ?>

        </div>
      </div>

      <div class="news-content__right">
        <?
        $startFlag = false;
        $mid = round((count($arResult["ITEMS"]) - 1) / 2);
        $last = array_key_last($arResult["ITEMS"]);

        foreach ($arResult["ITEMS"] as $key => $arItem): ?>
          <?
          if (!empty($arItem['PROPERTIES']['MAIN_ARTICLE']['VALUE']) && !isset($mainArticle))
            $mainArticle = $arItem;

          if (!empty($arItem['PROPERTIES']['MAIN_ARTICLE']['VALUE']) || !empty($arItem["LATEST"]))
            continue;

          $arImagePreload = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
          $arImageMobile2 = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropMobile2, BX_RESIZE_IMAGE_EXACT);
          $arImageMobile = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropMobile, BX_RESIZE_IMAGE_EXACT);
          $arImageTablet = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);

          $class = $key == $mid + 1 ? 'right' : 'left';
          ?>

          <? if (!$startFlag || $key == $mid + 1): ?>
            <? $startFlag = true; ?>
            <div class="news-cards-list <?= $class ?>">
          <? endif ?>

          <div class="news-card">
            <div class="image">
              <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                <picture>
                  <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 768px)"
                          data-src="<?= $arImageMobile2['src'] ?>"/>
                  <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                          data-src="<?= $arImageMobile['src'] ?>"/>
                  <img src="<?= $arImagePreload['src'] ?>" alt="articles img" data-src="<?= $arImageTablet['src'] ?>">
                </picture>
              </a>
            </div>
            <div class="description">
              <span class="subtitle"><?= $arItem['PROPERTIES']['CATEGORY_ARTICLE']['VALUE'] ?></span>
              <div class="name-block">
                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="name hover"><?= $arItem['NAME'] ?></a>
              </div>
              <span class="creator">By SODAMODA</span>
              <span class="date"><?= $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
            </div>
          </div>

          <? if ($key == $mid || $key == $last): ?>
            </div>
          <? endif ?>

        <? endforeach; ?>
      </div>

    </div>
  <? endif ?>

  <? if (!empty($mainArticle)): ?>
    <?
    $arImagePreload = CFile::ResizeImageGet($mainArticle['PREVIEW_PICTURE']['ID'], $arSizeCropPreload, BX_RESIZE_IMAGE_EXACT);
    $arImageTablet = CFile::ResizeImageGet($mainArticle['PREVIEW_PICTURE']['ID'], $arSizeCropTablet, BX_RESIZE_IMAGE_EXACT);
    $arImage = CFile::ResizeImageGet($mainArticle['PREVIEW_PICTURE']['ID'], $arSizeCrop, BX_RESIZE_IMAGE_EXACT);
    ?>

    <div class="news-big">
      <div class="image">
        <a href="<?= $mainArticle["DETAIL_PAGE_URL"] ?>">
          <picture>
            <source srcset="<?= $arImagePreload['src'] ?>" media="(max-width: 1024px)"
                    data-src="<?= $arImageTablet['src'] ?>"/>
            <img src="<?= $arImagePreload['src'] ?>" alt="articles img" data-src="<?= $arImage['src'] ?>">
          </picture>
        </a>
      </div>
      <div class="info">
        <a href="<?= $mainArticle["DETAIL_PAGE_URL"] ?>" class="font-caption hover"><?= $mainArticle['NAME'] ?></a>
        <? if ($mainArticle["PREVIEW_TEXT"]): ?>
          <p class="description">
            <?= $mainArticle["PREVIEW_TEXT"]; ?>
          </p>
        <? endif ?>
        <? if ($mainArticle['PROPERTIES']['BTN_NAME'] && $mainArticle['PROPERTIES']['BTN_LINK']): ?>
          <a href="<?= $mainArticle['PROPERTIES']['BTN_LINK']['VALUE'] ?>" class="ibutton big hollow">
            <?= $mainArticle['PROPERTIES']['BTN_NAME']['VALUE'] ?>
          </a>
        <? endif; ?>
      </div>
    </div>
  <? endif ?>

  <div class="news-trend">
    <p class="font-caption"><?= Loc::getMessage("NEWS_LIST_TRENDING_NOW") ?></p>
    <? $APPLICATION->IncludeComponent(
        "arlix:home.premiere_carousel",
        "news",
        [
            "IBLOCK_ID" => 28,
            "COUNT" => 4,
        ],
        false
    ); ?>
  </div>

</div>
