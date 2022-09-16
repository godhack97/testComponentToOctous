<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$access = htmlspecialchars($_REQUEST['access']);
if($access != '5gzk9ghaj')
  exit('Access denied');

CModule::IncludeModule("form");

$formResultID = intval($_REQUEST['return']);
if($formResultID > 0) 
{
  $arVALUE = array();
  $FIELD_SID = "SIMPLE_QUESTION_144";
  $arVALUE[10] = true;
  CFormResult::SetField($formResultID, $FIELD_SID, $arVALUE);
}

