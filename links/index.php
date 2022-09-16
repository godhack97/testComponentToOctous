<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH .'/css/links.css');
?>

<div class="fast-links">
  
  <?
    $page = $APPLICATION->GetCurPageParam('', ['setLanguage']);
    $linken = $_SERVER['HTTP_HOST'] . $page;
    $page2 = str_replace('/en', '', $linken);
  ?>
  
  <?if(SITE_ID == 's2'):?>
    <a class="lang hover" href="https://<?=$page2?>?setLanguage=RU">RU</a>
  <?else:?>
    <a class="lang hover" href="https://<?=$_SERVER['HTTP_HOST']?>/en<?=$page?>?setLanguage=EN">EN</a>
  <?endif?>
  
  <a class="ibutton hollow" href="https://t.me/+TYHDuzXCdg85YjFi">TELEGRAM Канал</a>
    
  <a class="ibutton hollow" href="/">Перейти на сайт</a>
  
  <a class="ibutton hollow" href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="Связаться с нами в WhatsApp">Написать в whatsapp</a>
  
  <a class="ibutton hollow" href="tel:+78003505670">Позвонить&nbsp;&nbsp;8 800 350-5670</a>
  
  <a class="ibutton hollow" href="https://apps.apple.com/in/app/sodamoda/id1582756296"><img class="icon-apple" src="<?=SITE_TEMPLATE_PATH?>/images/icons/icon-apple.svg">App Store</a>
  
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>