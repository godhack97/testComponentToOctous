<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление на возврат');
use \Bitrix\Main\Localization\Loc;

$dateNow = ConvertTimeStamp(time(), "SHORT");
?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
  <div class="section-auth mode-single">
    <form name="return">
      <input type="hidden" name="mode" value="return">
      <input type="hidden" name="date_now" value="<?=strtolower(FormatDate('«j» F Y г.', strtotime($dateNow)))?>">
      <div class="form">

        <div class="right-intro">
          <div class="text">
            <?=Loc::getMessage('SALE_PERSONAL_IP')?>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="name" value="" aria-required="true" required="" class="field-style" id="field_name">
            <label class="placeholder" for="field_name"><?=Loc::getMessage('SALE_PERSONAL_FULL_NAME')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="USER_PHONE" value="" aria-required="true" required="" class="field-style" id="field_phone">
            <label class="placeholder" for="field_phone"><?=Loc::getMessage('SALE_PERSONAL_PHONE')?></label>
          </div>

          <div class="text text-caption"><?=Loc::getMessage('PASSPORT_DATA')?></div>
          <div class="field-cols field-cols-2">
            <div class="field">
              <input type="text" placeholder=" " size="40" name="PASSPORT_SERIES" value="" aria-required="true" required="" class="field-style" id="field_passport_series">
              <label class="placeholder" for="field_passport_series"><?=Loc::getMessage('PASSPORT_SERIES')?></label>
            </div>
            <div class="field">
              <input type="text" placeholder=" " size="40" name="PASSPORT_NUMBER" value="" aria-required="true" required="" class="field-style" id="field_passport_number">
              <label class="placeholder" for="field_passport_number"><?=Loc::getMessage('PASSPORT_NUMBER')?></label>
            </div>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="PASSPORT_ISSUED" value="" aria-required="true" required="" class="field-style" id="field_passport_issued">
            <label class="placeholder" for="field_passport_issued"><?=Loc::getMessage('PASSPORT_ISSUED')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="PASSPORT_ADDRESS" value="" aria-required="true" required="" class="field-style" id="field_passport_address">
            <label class="placeholder" for="field_passport_address"><?=Loc::getMessage('PASSPORT_ADDRESS')?></label>
          </div>
        </div>
        <div class="clear"></div>

        <h3><?=Loc::getMessage('SALE_PERSONAL_APPLICATION_RETURN')?></h3>

        <p>
          <?=strtolower(FormatDate('«j» F Y г.', strtotime($arResult['DATE_INSERT_FORMATED'])))?>&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_ONE')?><?=$arResult['ID']?>&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_TWO')?> <?=$arResult['PRICE_FORMATED']?> <?=Loc::getMessage('SALE_PERSONAL_APPLICATION_THREE')?>
        </p>
        <p>Поиск по артикулу:</p>
        <select id="js-data-example-ajax"></select>
        <table class="basket-table">
          <tbody>
          <tr class="head">
            <th class="image"><?=Loc::getMessage('SALE_PERSONAL_ARTICUL')?></th>
            <th class="desc"><?=Loc::getMessage('SALE_PERSONAL_NAME')?></th>
            <th class="count"><?=Loc::getMessage('SALE_PERSONAL_QUANTITY')?></th>
            <th class="summa"><?=Loc::getMessage('SALE_PERSONAL_PRICE')?></th>
          </tr>
          </tbody>
          <tbody id="basket-item-table">
          <?

          $aItems = Array();
?>
              <input type="hidden" name="product[]" value="<?=$arItem['PRODUCT_ID'] .'##'. $arItem['PROPERTY_CML2_ARTICLE_VALUE'] .'##'. $arItem['NAME'] .'##'. $arItem['QUANTITY'] .'##'. $arItem['PRICE']?>">
              <tr class="basket-items-list-item-container" data-id="<?=$arItem['ID']?>">
                <td class="image">
                  <a class="hover" target="_blank" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['PROPERTY_CML2_ARTICLE_VALUE']?></a>
                </td>
                <td class="desc">
                  <div class="name"><a class="hover" target="_blank" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div>
                  <div class="mobile-size"></div>
                </td>
                <td class="size"></td>
                <td class="count"></td>
                <td class="summa"></td>
              </tr>
              <?
//              $aTmp = Array(
//                $arItem['PROPERTY_CML2_ARTICLE_VALUE'],
//                $arItem['NAME'],
//                $size,
//                $arItem['QUANTITY'],
//                $arItem['PRICE_FORMATED']
//              );
//              $aItems[] = implode(";", $aTmp);
              ?>
          </tbody>
        </table>

        <input type="hidden" name="items" value="<?=implode("+", $aItems)?>">

        <?=Loc::getMessage('SALE_PERSONAL_SPECIFY_RETURNED')?>
        <div class="reasons">
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_SIZE')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_SIZE')?></div>
            </label>
          </div>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_PRODUCT')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_PRODUCT')?></div>
            </label>
          </div>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_COME_DATE')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_COME_DATE')?></div>
            </label>
          </div>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_NOT_QUALITY')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_NOT_QUALITY')?></div>
            </label>
          </div>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_REFUSE')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_REFUSE')?></div>
            </label>
          </div>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_OTHER')?>" type="radio" name="REASONS" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_OTHER')?></div>
            </label>
          </div>
        </div>

        <h3><?=Loc::getMessage('SALE_PERSONAL_DETAILS_REFUND')?></h3>

        <div class="requisites">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="INN" value="" aria-required="true" required="" class="field-style" id="field_INN">
            <label class="placeholder" for="field_INN"><?=Loc::getMessage('SALE_PERSONAL_INN')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="ACCOUNT" value="" aria-required="true" required="" class="field-style" id="field_ACCOUNT">
            <label class="placeholder" for="field_ACCOUNT"><?=Loc::getMessage('SALE_PERSONAL_PERSONAL_ACCOUNT_NUM')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="BIC" value="" aria-required="true" required="" class="field-style" id="field_BIC">
            <label class="placeholder" for="field_BIC"><?=Loc::getMessage('SALE_PERSONAL_BIK')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="BANK" value="" aria-required="true" required="" class="field-style" id="field_BANK">
            <label class="placeholder" for="field_BANK"><?=Loc::getMessage('SALE_PERSONAL_BANK_NAME')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="CARD" value="" aria-required="true" required="" class="field-style" id="field_CARD">
            <label class="placeholder" for="field_CARD"><?=Loc::getMessage('SALE_PERSONAL_BANK_CARD')?></label>
          </div>
        </div>

        <?=Loc::getMessage('SALE_PERSONAL_RETURN_METHOD')?>
        <div class="reasons mode-width-full">
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_COURIER')?>" type="radio" data-type="curier" name="RETURN_TYPE" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_COURIER')?></div>
            </label>
          </div>
          <?if($isBigTown):?>
            <div class="field-radio">
              <label>
                <input value="<?=Loc::getMessage('SALE_PERSONAL_STORE')?>" type="radio" data-type="shop" name="RETURN_TYPE" aria-required="true" required="">
                <div class="label"><?=Loc::getMessage('SALE_PERSONAL_STORE')?></div>
              </label>
            </div>
          <?endif?>
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_TRANSPORT_COMPANY')?>" type="radio" data-type="tc" name="RETURN_TYPE" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_TRANSPORT_COMPANY')?></div>
            </label>
          </div>
        </div>

        <div class="requisites display-none fields-return-tc">
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_NAME" value="" class="field-style" id="TC_NAME">
            <label class="placeholder" for="TC_NAME"><?=Loc::getMessage('SALE_PERSONAL_NAME_TRANSPORT_COMPANY')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_TRACK" value="" class="field-style" id="TC_TRACK">
            <label class="placeholder" for="TC_TRACK"><?=Loc::getMessage('SALE_PERSONAL_TRACK_NUM')?></label>
          </div>
          <div class="field">
            <input type="text" placeholder=" " size="40" name="TC_DATE" value="" class="field-style" id="TC_DATE">
            <label class="placeholder" for="TC_DATE"><?=Loc::getMessage('SALE_PERSONAL_DEPARTURE_DATE')?></label>
          </div>
        </div>

        <?=Loc::getMessage('SALE_PERSONAL_INFO')?>
        <? //dbgz($arResult); ?>

      </div>
      <br>
      <input class="ibutton" type="submit" value="<?=Loc::getMessage('SALE_PERSONAL_SEND')?>" name="RETURN_SEND">
    </form>
  </div>
<script type="text/javascript">
  $('#js-data-example-ajax').select2({
    placeholder: "Введите артикул",
    allowClear: true,
    ajax: {
      url: '/ajax/searchByArticle.php',
      dataType: 'json',
      processResults: function (data) {
        $('#basket-item-table tr').remove();
        data.forEach(function(item, i, data) {
          tr = BX.create('tr', {
            props: {className: 'tr-hidden'},
            attrs : {'data-id' : item.id }
          });
          tdArticle = BX.create('td', {
            html: item.article
          });
          tr.appendChild(tdArticle);
          tdName = BX.create('td', {
            html: item.text
          });
          tr.appendChild(tdName);
          tdQuantity = BX.create('td', {
            html: item.text,
            children: [
              BX.create({
                tag: 'input',
                props: {name: 'quantity', type:'text', value: 1}
              }),
            ],
            events: {
              input: function(e) {
                price = $(this).closest('tr').find(".price").data().price;
                quantity = $(e.target).val();
                $(this).closest('tr').find(".price").text(price*quantity);
              }
            }
          });
          tr.appendChild(tdQuantity);
          tdPrice = BX.create('td', {
            props: {className: 'price'},
            attrs : {'data-price' : item.price },
            html: item.price
          });
          tr.appendChild(tdPrice);

          document.getElementById("basket-item-table").appendChild(tr);
        });


        return {
          results: data
        };
      }
    }
  });

  $('#js-data-example-ajax').on('select2:select', function(){

    var self = this;
    $("#basket-item-table tr").each(function(i,elem) {
      if(elem.dataset.id == self.selectedOptions[0].value) {
        elem.className +=" act";
      }
    });
  });
</script>
<style>

  .section-auth.mode-single {
    display: block;
  }

  .mode-single .right-intro {
    float: right;
    text-align: right;
    width: 100%;
    max-width: 500px;
    position: relative;
    overflow: hidden;
  }

  .mode-single .text {
    font-size: 14px;
    line-height: 1.5;
  }

  .mode-single .right-intro .text {
    margin-bottom: 50px;
  }

  .mode-single .right-intro .text-caption {
    margin-top: 50px;
    margin-bottom: 15px;
    text-align: left;
    font-size: 16px;
  }

  .mode-single .field {
    margin-bottom: 20px !important;
  }

  .mode-single h3 {
    margin-bottom: 30px;
    margin-top: 30px;
  }

  .mode-single .basket-table {
    margin-top: 50px;
    margin-bottom: 50px;
  }

  .mode-single .basket-table td.image {
    line-height: 1;
  }

  .mode-single .basket-table .name {
    margin-top: 0;
    margin-bottom: 0;
  }

  .reasons {
    width: 100%;
    max-width: 700px;
    margin-top: 15px;
    margin-bottom: 50px;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-items: stretch;
    align-content: stretch;
  }

  .reasons .field-radio {
    width: 45%;
    margin-right: 5%;
  }

  .reasons .field-radio .label {
    font-size: 12px;
    line-height: 1.2;
    text-transform: uppercase;
    color: #1F1F1F;
  }

  .reasons.mode-width-full .field-radio {
    width: 100%;
    margin-right: 0;
  }

  .mode-single .requisites {
    width: 100%;
    max-width: 500px;
    margin-bottom: 50px;
  }

  .mode-single .requisites.fields-return-tc {
    margin-top: -30px;
  }



  @media only screen and (max-width: 540px) {

    .reasons .field-radio {
      width: 100%;
      margin-right: 0;
    }

    .mode-single .basket-table a {
      display: inline-block;
      min-height: 35px;
    }

  }

  .tr-hidden {
    display: none;
  }
  .tr-hidden.act {
    display: contents;
  }
</style>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>