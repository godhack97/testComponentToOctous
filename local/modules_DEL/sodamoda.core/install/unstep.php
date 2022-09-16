<?php if(!check_bitrix_sessid()) return;?>
<?php echo CAdminMessage::ShowNote('Модуль Ядро Sodamoda успешно удален!'); ?>
<form action="<?php echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?php echo LANG; ?>">
    <input type="submit" name="" value="<?php echo GetMessage("MOD_BACK"); ?>">
<form>
