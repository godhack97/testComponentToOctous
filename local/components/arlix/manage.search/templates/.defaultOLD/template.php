<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
  }

  /** @var array $arParams */
  /** @var array $arResult */

  use \Bitrix\Main\Localization\Loc;

  global $USER;

  $APPLICATION->RestartBuffer();

  if (count($arResult['ITEMS']) > 0) {
    ?>
    <table class='basket-table'>
      <tbody>
      <tr class='head'>
        <th class='image'>Товар</th>
        <th class='desc'>НАЗВАНИЕ</th>
        <th class='count'>Продано</th>
        <th class='summa'>АРТИКУЛ</th>
        <th class='control'>Удалить</th>
      </tr>
      </tbody>
      <?php
        foreach ($arResult['ITEMS'] as $arItem) {
          ?>
          <tbody id="<?= $arItem['ID'] ?>">
          <tr>
            <td class="image">
              <a href="<?= $arParams['IS_OWNER'] !== 'N' ? '/personal' : '/manage'; ?>/products/edit/<?= $arItem['ID'] ?>/" class="image hover" alt="" style="background-image: url(<?= $arItem['PREVIEW_PHOTO'] ?>)"></a>
            </td>
            <td class="desc">
              <div class="name">
                <a class="hover" href="<?= $arParams['IS_OWNER'] !== 'N' ? '/personal' : '/manage'; ?>/products/edit/<?= $arItem['ID'] ?>/"><?= $arItem['NAME'] ?></a>
              </div>
              <div class="props">
                <?php
                  $colorsList = [];
                  $sizesList = [];
                  foreach ($arItem['COLORS_SIZES'] as $val) {
                    if (!in_array($val['COLOR_NAME'], $colorsList)) {
                      $colorsList[] = $val['COLOR_NAME'];
                    }

                    foreach ($val['SIZES'] as $size) {
                      if (!in_array($size, $sizesList)) {
                        $sizesList[] = $size;
                      }
                    }
                  }
                ?>
                <div class="prop"><?= implode(', ', $colorsList) ?></div>
                <div class="prop"><?= implode(', ', $sizesList) ?></div>
              </div>
            </td>
            <td>239</td>
            <td><?= $arItem['ARTICLE'] ?></td>
            <td class="control">
              <a class="delete hover delete-product" data-entity="basket-item-delete" data-product-id="<?= $arItem['ID'] ?>"><img src="/local/templates/sodamoda/images/icons/times.svg"></a>
            </td>
          </tr>
          </tbody>
          <?php
        }
      ?>
    </table>
    <?php
  }
  else {
    ?>
    <div class='product-empty-message'><?= Loc::getMessage('NEWS_LIST_PRODUCT_EMPTY'); ?></div>
    <?php
  }
?>