<?php

namespace Sodamoda\Core\Events;

class Highload
{
	function OnBeforeAdd(\Bitrix\Main\Entity\Event $event)
	{
		if (!$event->getParameter("id") && is_array($event->getParameter("id"))) {
			return;
		}

		$arFields = $event->getParameter("fields");

		$result = new \Bitrix\Main\Entity\EventResult();

		$arFields["UF_NAME"] = trim($arFields["UF_NAME"]);
		$arFields["UF_NAME_EN"] = trim($arFields["UF_NAME_EN"]);

		$event->setParameter("fields", $arFields);

		$changedFields = [];
		$changedFields["UF_NAME"] = trim($arFields["UF_NAME"]);
		$changedFields["UF_NAME_EN"] = trim($arFields["UF_NAME_EN"]);
		
		$result->modifyFields($changedFields);

		return $result;
	}
  function OnBeforeUpdate(\Bitrix\Main\Entity\Event $event)
  {
    if (!$event->getParameter("id") && is_array($event->getParameter("id"))) {
      return;
    }
    
    $arFields = $event->getParameter("fields");
    
    $result = new \Bitrix\Main\Entity\EventResult();
    
    $arFields["UF_NAME"] = trim($arFields["UF_NAME"]);
    $arFields["UF_NAME_EN"] = trim($arFields["UF_NAME_EN"]);
    
    $event->setParameter("fields", $arFields);
  
    //$changedFields = [];
    //$changedFields["UF_NAME"] = trim($arFields["UF_NAME"]);
    //$changedFields["UF_NAME_EN"] = trim($arFields["UF_NAME_EN"]);
    
    //$result->modifyFields($changedFields);
    
    return $result;
  }
}
