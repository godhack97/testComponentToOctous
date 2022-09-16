<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление на возврат');

$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH."/components/bitrix/sale.personal.order.detail/return/style.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/components/bitrix/sale.personal.order.detail/return/script.js");

$APPLICATION->SetAdditionalCss('//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
$APPLICATION->AddHeadScript('//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js');

CJSCore::Init(array("date")); // Calendar

use \Bitrix\Main\Localization\Loc;

$dateNow = ConvertTimeStamp(time(), "SHORT");
?>
  <div class="section-auth mode-single">
    <form name="returnNotAuth">
      <input type="hidden" name="mode" value="returnNotAuth">
      <input type="hidden" name="date_now" value="<?=strtolower(FormatDate('«j» F Y г.', strtotime($dateNow)))?>">
      <input type="hidden" name="currency" value="<?=(SITE_ID == 's2') ? '$' : 'руб.'?>">
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
          <span class="field field-inline">
            <input onclick="BX.calendar({node: this, field: this, bTime: false});" type="text" placeholder=" " size="20" name="dateSale" value="" aria-required="true" required="" class="field-style" id="field-inline-dateSale" />
            <label class="placeholder" for="field-inline-dateSale"><?=Loc::getMessage('FIELD_INLINE_DATESALE')?></label>
          </span><?=Loc::getMessage('SALE_PERSONAL_APPLICATION_ONE');?><span class="field field-inline">
            <input type="text" placeholder=" " size="20" name="orderId" value="" aria-required="true" required="" class="field-style" id="field-inline-orderId" />
            <label class="placeholder" for="field-inline-orderId"><?=Loc::getMessage('FIELD_INLINE_ORDERID')?></label>
          </span>&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_TWO')?> 
          <span class="field field-inline">
            <input type="text" placeholder=" " size="20" name="totalPrice" value="" aria-required="true" required="" class="field-style" id="field-inline-totalPrice"  /> 
            <label class="placeholder" for="field-inline-totalPrice"><?=Loc::getMessage('FIELD_INLINE_TOTALPRICE')?></label>
          </span>
          <?=(SITE_ID == 's2') ? '$' : 'руб.'?> <?=Loc::getMessage('SALE_PERSONAL_APPLICATION_THREE')?>
        </p>
        <table class="basket-table mode-return-noauth">
          <tbody>
            <tr class="head">
              <th class="image"><?=Loc::getMessage('SALE_PERSONAL_ARTICUL')?></th>
              <th class="desc"><?=Loc::getMessage('SALE_PERSONAL_NAME')?></th>
              <th class="size"><?=Loc::getMessage('SALE_PERSONAL_SIZE')?></th>
              <th class="count"><?=Loc::getMessage('SALE_PERSONAL_QUANTITY')?></th>
              <th class="summa"><?=Loc::getMessage('SALE_PERSONAL_PRICE')?></th>
              <th></th>
            </tr>
          </tbody>
          <tbody id="basket-item-table">
            <?
            $aItems = Array();
            ?>
            <tr class="basket-items-list-item-container">
              <td class="image">
                
                <span class="field field-inline">
                  <input type="text" readonly="readonly" name="article[]" value="" class="field-style" />
                </span>
                
              </td>
              <td class="desc">
                
                <span class="field field-inline">
                  <select id="selectProduct" name="product[]" disabled required aria-required="true">
                    <option value=""><?=Loc::getMessage('FIELD_INLINE_NAMEPRODUCT')?></option>
                  </select>
                  <input type="hidden" name="nameProduct[]" value="" />
                </span>
                
              </td>
              <td class="size">
                
                <span class="field field-inline">
                  <input type="text" readonly="readonly" name="size[]" value="" class="field-style" />
                </span>
                
              </td>
              <td class="count">
                
                <span class="field field-inline">
                  <input type="text" readonly="readonly" name="quantity[]" value="" class="field-style" />
                </span>
                
              </td>
              <td class="summa">
                
                <span class="field field-inline">
                  <input type="text" readonly="readonly" name="price[]" value="" class="field-style" />
                  <input type="hidden" readonly="readonly" name="pricenum[]" value="" class="field-style" />
                </span>
                
              </td>
              <td class="control">
                <a class="delete hover"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/times.svg"></a>
              </td>
            </tr>
          </tbody>
        </table>
        <a class="black hover js-add-product"><?=Loc::getMessage('BUTTON_ADD_TABLE_TR')?></a>
        <div class="return-total-price"><?=Loc::getMessage('TOTAL_RETURN_PRICE')?> <span></span></div>
        <br />

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
          <div class="field-radio">
            <label>
              <input value="<?=Loc::getMessage('SALE_PERSONAL_STORE')?>" type="radio" data-type="shop" name="RETURN_TYPE" aria-required="true" required="">
              <div class="label"><?=Loc::getMessage('SALE_PERSONAL_STORE')?></div>
            </label>
          </div>
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


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>