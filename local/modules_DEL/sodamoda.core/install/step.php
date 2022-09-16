<?php if(!check_bitrix_sessid()) return;?>
<?php echo CAdminMessage::ShowNote('Модуль Ядро Sodamoda успешно установлен!'); ?>
<form action="<?php echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?=LANG; ?>">
    <input type="submit" name="" value="<?=GetMessage("MOD_BACK"); ?>">
<form>
