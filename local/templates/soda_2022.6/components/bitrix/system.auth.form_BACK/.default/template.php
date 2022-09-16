<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;

global $USER;
if (!$USER->IsAuthorized()):?>
  <a class="item hover" href="<?=SITE_DIR?>personal/profile/"><?=Loc::getMessage("AUTH_LOGIN")?></a>
<?else:?>
  <a class="item hover" href="<?=SITE_DIR?>personal/profile/"><?=Loc::getMessage("AUTH_LK")?></a>
<?endif?>