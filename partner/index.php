<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Раздел менеджера");

use Bitrix\Main\Config\Option;

if($USER->IsAuthorized() && CSite::InGroup(explode(',', Option::get('sodamoda.core', 'group_manage_lists')))){
  LocalRedirect('/partner/products/');
}
else {
    $APPLICATION->AuthForm(GetMessage('FRLM_NEED_AUTH'));
}
?>

<p>На странице <b>Настройка пользователя</b> пользователь имеет возможность редактировать личные данные, регистрационную информацию, информацию о работе и т. д. Вывод данной формы осуществлен с помощью компонента <i>Параметры пользователя</i>. </p>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>