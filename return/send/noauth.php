<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление на возврат');
use \Bitrix\Main\Localization\Loc;

$dateNow = ConvertTimeStamp(time(), "SHORT");
?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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
          <input type="text" name="dateSale" onclick="BX.calendar({node: this, field: this, bTime: false});" style="width: 100px;" />
          <?=Loc::getMessage('SALE_PERSONAL_APPLICATION_ONE')?><input type="text" name="orderId" style="width: 100px;"  />&nbsp;<?=Loc::getMessage('SALE_PERSONAL_APPLICATION_TWO')?> <input type="text" name="totalPrice" style="width: 100px;"  /> <?=(SITE_ID == 's2') ? '$' : 'руб.'?> <?=Loc::getMessage('SALE_PERSONAL_APPLICATION_THREE')?>
        </p>
        <table class="basket-table">
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
              <input name="article[]" value="" />
            </td>
            <td class="desc">
              <input name="nameProduct[]" value="" />
            </td>
            <td class="size">
              <input name="size[]" value="" />
            </td>
            <td class="count">
              <input name="quantity[]" value="" />
            </td>
            <td class="summa">
              <input name="price[]" value="" />
            </td>
            <td class="delete">x</td>
          </tr>
          </tbody>
        </table>
        <button class="ibutton-add-table" type="button"><?=Loc::getMessage('BUTTON_ADD_TABLE_TR')?></button><br />

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
<script>
  $(".ibutton-add-table").click(function () {
    var tr = $("#basket-item-table tr:last").clone();
    tr.find('input').val('');
    $("#basket-item-table tr:last").after(tr);
  });

  $('form[name=returnNotAuth]').submit(function () {
    ajaxQuery($(this));
    return false;
  });
  
  $("#basket-item-table").on("click", ".delete", function () {
    $(this).closest('tr').remove();
  })

  $('input[name=RETURN_TYPE]').change(function(){
    var fieldsTC = $('.fields-return-tc');
    if($(this).data('type') == 'tc' && $(this).is(':checked'))
    {
      fieldsTC.removeClass('display-none');
      fieldsTC.find('input').attr('aria-required', 'true').attr('required', 'required');
    }
    else
    {
      fieldsTC.addClass('display-none');
      fieldsTC.find('input').removeAttr('aria-required').removeAttr('required');
    }
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
      margin-bottom: 10px;
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

    .basket-items-list-item-container td {
      padding-right: 10px !important;
    }

    .ibutton-add-table {
      display: block;
      background-color: #090909;
      vertical-align: middle;
      height: 40px;
      font-size: 14px;
      font-weight: 400;
      line-height: 40px;
      text-align: center;
      text-transform: uppercase;
      text-decoration: none;
      padding: 0 35px;
      border: 1px solid #090909;
      color: #fff;
      cursor: pointer;
      transition: all .2s ease;
    }

    .delete {
      cursor: pointer;
    }

    #basket-item-table tr:first-child .delete {
      display: none !important;
    }
  </style>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>