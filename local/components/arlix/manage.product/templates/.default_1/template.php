<?php
  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
  }
  /** @var array $arParams */
  /** @var array $arResult */

  $this->addExternalCss('/local/templates/sodamoda/css/select2/select2.min.css');
  $this->addExternalJs('/local/templates/sodamoda/js/select2/select2.full.min.js');

  $isActionEdit = $arParams['ACTION'] === 'edit';
  if ($isActionEdit){
    $editProduct = $arResult['ID'];
  }
  ?>
  <section class='section-editing'>
    <div class='heading'>
      <h1><?= $isActionEdit ? 'Редактирование товара' : 'Создание товара' ?></h1>
      <?php
        if($isActionEdit){
          ?>
          <div class='add-product'>
            <a target='_blank' href="<?= $arParams['PATH_ADD'] ?>">Добавить товар</a>
          </div>
          <?php
        }
      ?>
    </div>
    <form method="post" name="save_product" class="props-form form js-props-form" enctype="multipart/form-data" data-type-form='<?= $arParams['ACTION'] ?>' data-return="<?=$arParams['PATH_RETURN'];?>">
      <?php
        if ($isActionEdit) {
          ?>
          <input type="hidden" name="product_id" value="<?= $editProduct ?>">
          <?php
        }
      ?>
      <div class="props-wrapper">
        <div class="props-content ru-content">
          <div class="props-item">
            <div class="field field-checkbox">
              <label>
                <input type="checkbox" id="product_active" <?=!$isActionEdit ? 'checked' : ((trim($arResult['ACTIVE']) == 'Y') ? 'checked' : '') ?> value="Y" name="product_active">
                <div class="label">Активность</div>
              </label>
            </div>
          </div>
          <div class="props-item">
            <div class="props-element">
              <div class="field">
                <input class="field-style" type="text" placeholder=" " name="product_name" id="product_name" maxlength="" value="<?= !$isActionEdit ? '' : $arResult['NAME'] ?>" required>
                <label class="placeholder" for="product_name">Название товара</label>
              </div>
            </div>
            <div class="props-element">
              <div class="field">
                <input class="field-style" type="text" placeholder=" " name="article" id="article" maxlength="" value="<?= !$isActionEdit ? '' : $arResult['ARTICLE'] ?>" required>
                <label class="placeholder" for="product_name">Артикул</label>
              </div>
            </div>
          </div>
          <div class="props-item">
            <div class="props-element">
              <div class="field">
                <input class="field-style" type="number" placeholder=" " name="price" id="price" maxlength="" value="<?= !$isActionEdit ? '' : $arResult['PRICE_RUB'] ?>" required>
                <label class="placeholder" for="price">Цена RUB</label>
              </div>
            </div>
            <div class="props-element select-field">
              <select class='new-select' id='section_id' name='section' required>
                <option value='0'>—</option>
                <?php
                  foreach ($arResult['SECTIONS'] as $sectionID => $sectionName) {
                    ?>
                    <option
                      <?= ($arResult['SECTION'] == $sectionID ? 'selected ' : '') ?>value="<?= $sectionID ?>"><?= $sectionName ?></option>
                    <?php
                  }
                ?>
              </select>
              <label class="select-placeholder">Раздел</label>
            </div>
          </div>
          <?php
            if (!$isActionEdit || count($arResult['MATERIALS']) < 1) {
              ?>
              <div class="props-item props-add">
                <div class="props-element select-field">
                  <select name="material-id[]" class="new-select">
                    <option selected value="0">—</option>
                    <?php
                      foreach ($arResult['ENUM']['MATERIALS'] as $eMatID => $eMat) {
                        ?>
                        <option value="<?= $eMatID ?>"><?= (($_SESSION['SESS_COUNTRY_ID'] == 'RU') ? $eMat['NAME'] : $eMat['NAME_EN']) ?></option>
                        <?php
                      }
                    ?>
                  </select>
                  <label class="select-placeholder">Состав материалов</label>
                </div>
                <div class="props-element">
                  <div class="field">
                    <input class="field-style" type="text" placeholder=" " name="material-value[]" id="composition_percent" maxlength="" value="">
                  </div>
                </div>
                <div class="remove-characteristics" title="Удалить материал"></div>
              </div>
              <?
            }
            else {
              $fflag = true;
              foreach ($arResult['MATERIALS'] as $materialID => $arMaterial) {
                ?>
                <div class="props-item <?= (($fflag) ? 'props-add' : '') ?>">
                  <div class="props-element select-field">
                    <select name="material-id[]" class="new-select">
                      <option value="0">—</option>
                      <?php
                        foreach ($arResult['ENUM']['MATERIALS'] as $eMatID => $eMat) {
                          ?>
                          <option <?= (($eMatID == $materialID) ? 'selected' : '') ?> value="<?= $eMatID ?>"><?= (($_SESSION['SESS_COUNTRY_ID'] == 'RU') ? $eMat['NAME'] : $eMat['NAME_EN']) ?></option>
                          <?php
                        }
                      ?>
                    </select>
                    <label class="select-placeholder">Состав материалов</label>
                  </div>
                  <div class="props-element">
                    <div class="field">
                      <input class="field-style" type="text" placeholder=" " name="material-value[]" id="composition_percent" maxlength="" value="<?= $arMaterial['VALUE'] ?>">
                    </div>
                  </div>
                  <div class="remove-characteristics" title="Удалить материал"></div>
                </div>
                <?php
                $fflag = false;
              }
            }
          ?>
          <div class="add-material">добавить материал</div>
        </div>
        <div class="props-content en-content">
          <div class="field">
            <input class="field-style" type="text" placeholder=" " name="product_name_en" id="product_name_en" maxlength="" value="<?= !$isActionEdit ? '' : $arResult['NAME_EN'] ?>" required>
            <label class="placeholder" for="product_name_en">Название товара на англ. яз.</label>
          </div>
          <div class="field">
            <input class="field-style" type="number" placeholder=" " name="price_en" id="price_en" maxlength="" step="0.01" value="<?= !$isActionEdit ? '' : $arResult['PRICE_USD'] ?>" required>
            <label class="placeholder" for="price_en">Цена USD</label>
          </div>
        </div>
      </div>
      <div class="description-wrapper">
        <div class="text-description description-ru field">
          <textarea rows="1" class="area-field" id="description_ru" name="description"><?= !$isActionEdit ? '' : $arResult['DETAIL_TEXT'] ?></textarea>
          <label class="placeholder" for="description_ru">Описание товара</label>
        </div>
        <div class="text-description description-en field">
          <textarea rows="1" name="description_en" id="description_en"><?= !$isActionEdit ? '' : $arResult['DETAIL_TEXT_EN'] ?></textarea>
          <label for="description_en" class="placeholder">Описание товара на англ. яз.</label>
        </div>
      </div>
      <div class="colors-wrapper">
        <?php
          if (!$isActionEdit || $arResult['COLORS_SIZES'] == []) {
            ?>
            <div class="color-block color-element-add">
              <div class="color-element props-item">
                <div class='select-field'>
                  <select id='sel-0' class='new-select color-select'>
                    <?php
                      $fflag = true;
                      $firstColorID = 0;
                      foreach ($arResult['ENUM']['COLORS'] as $colCode => $arColor) {
                        ?>
                        <option <?= (($fflag) ? 'selected' : '') ?>
                          value="<?= $colCode ?>"><?= (($_SESSION['SESS_COUNTRY_ID'] == 'RU') ? $arColor['NAME'] : $arColor['NAME_EN']) ?></option>
                        <?php
                        if ($fflag) {
                          $firstColorID = $colCode;
                        }
                        $fflag = false;
                      }
                    ?>
                  </select>
                  <label class="select-placeholder">Цвет</label>
                  <div class="remove-characteristics" title="Удалить цвет"></div>
                </div>
                <div class="images"></div>
                <div class="upload-image">
                  <input class="upload-field" type="file" id="<?= $firstColorID; ?>" multiple="">
                  Загрузить изображение
                </div>
              </div>
              <div class="sizes-element props-item">
                <div class="props-element select-field">
                  <select id='selsize-0' class="new-select size-select" multiple="multiple" name="sizes[]">
                    <?php
                      foreach ($arResult['ENUM']['SIZES'] as $sizeID => $sizeVal) {
                        ?>
                        <option
                          <?= ((in_array($sizeVal, $arResult['SIZES'])) ? 'selected ' : '') ?>value="<?= $sizeID ?>"><?= $sizeVal ?></option>
                        <?php
                      }
                    ?>
                  </select>
                  <label class="select-placeholder">Размер</label>
                </div>
              </div>
            </div>
            <?php
          }
          else {
            $i = 0;
            $last = count($arResult['COLORS_SIZES']) - 1;
            foreach ($arResult['COLORS_SIZES'] as $colorCode => $arCSP) {
              ?>
              <div class='color-block <?= (($last === $i) ? 'color-element-add' : 'cloned') ?>'>
                <div class='active-element props-item'>
                  <div class='field field-checkbox'>
                    <label>
                      <input type='checkbox' id='active-color' <?=(trim($arCSP['ACTIVE']) == 'Y')? 'checked' : '' ?> value="Y" name="active[<?= $colorCode; ?>]">
                      <div class="label">Активность</div>
                    </label>
                  </div>
                </div>
                <div class='color-element props-item'>
                  <div class='select-field'>
                    <select id="sel-<?= $i ?>" class='new-select color-select'>
                      <?php
                        foreach ($arResult['ENUM']['COLORS'] as $colCode => $arColor) {
                          ?>
                          <option
                            <?= (($colCode == $colorCode) ? 'selected ' : '') ?>value="<?= $colCode ?>"><?= (($_SESSION['SESS_COUNTRY_ID'] == 'RU') ? $arColor['NAME'] : $arColor['NAME_EN']) ?></option>
                          <?php
                        }
                      ?>
                    </select>
                    <label class="select-placeholder">Цвет</label>
                    <div class="remove-characteristics" title="Удалить цвет"></div>
                  </div>
                  <div class='images'>
                    <?php
                      foreach ($arCSP['PHOTOS'] as $arPhoto) {
                        ?>
                        <a class='img' data-fancybox='gallery' href="<?= $arPhoto['PATH'] ?>">
                          <input type="hidden" class="file-<?= $i ?>" name="<?= $colorCode ?>[]"
                                 value="<?= $arPhoto['CODE'] ?>">
                          <img src="<?= $arPhoto['PATH'] ?>" alt="image">
                          <span class="remove-image" data-type="exist"></span>
                        </a>
                        <?php
                      }
                    ?>
                  </div>
                  <div class="upload-image">
                    <input class="upload-field" type="file" id="<?= $colorCode ?>" multiple="">
                    Загрузить изображение
                  </div>
                </div>
                <div class="sizes-element props-item">
                  <div class="props-element select-field">
                    <select id="selsize-<?= $i ?>" class="new-select size-select" multiple="multiple" name="sizes[<?=$colorCode;?>][]">
                      <?php
                        foreach ($arResult['ENUM']['SIZES'] as $sizeID => $sizeVal) {
                          ?>
                          <option <?= ((in_array($sizeVal, $arResult['SIZES'][$colorCode])) ? 'selected ' : '') ?>value="<?= $sizeID ?>"><?= $sizeVal ?></option>
                          <?php
                        }
                      ?>
                    </select>
                    <label class="select-placeholder">Размер</label>
                  </div>
                </div>
              </div>
              <?php
              $i++;
            }
          }
        ?>
        <div class="add-color">добавить цвет</div>
      </div>
      <div class="buttons-wrapper">
        <?php
          if ($isActionEdit) {
            ?>
            <button class="ibutton-white delete-product" type="button" value="<?= $editProduct ?>">удалить</button>
            <button id='create-product' class="ibutton" type="submit" name="partner_save" value="">сохранить</button>
            <?php
          }
          else {
            ?>
            <button class="ibutton-white cancel-product" type="button">отмена</button>
            <button id='create-product' class="ibutton" type="submit" name="partner_save" value="">создать</button>
            <?php
          }
        ?>
      </div>
    </form>
    <?php
      if ($isActionEdit && $arParams['IS_OWNER'] !== 'Y') {
        ?>
        <br />
        <? $APPLICATION->IncludeComponent('arlix:store.page', 'manage', ['QUERY' => $editProduct], false); ?>
        <?php
      }
    ?>
  </section>
  <a data-fancybox="gallery" href=""></a>

