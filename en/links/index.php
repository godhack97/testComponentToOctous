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
  
  <a class="ibutton hollow" href="https://t.me/+TYHDuzXCdg85YjFi">TELEGRAM Channel</a>
    
  <a class="ibutton hollow" href="/">Go to site</a>
  
  <a class="ibutton hollow" href="whatsapp://send?phone=<?= PHONE_WHATSAPP ?>" target="_blank" title="CONTACT US IN WHATSAPP">CONTACT US IN WHATSAPP</a>
  
  <a class="ibutton hollow" href="tel:+78003505670">CALL 8 800 350-5670</a>
  
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>