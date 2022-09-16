<?php
	
	namespace Sodamoda\Core\Events;
	
	use \Bitrix\Main\Loader;
	use Bitrix\Main\Mail\Event;
	
	class Main{
		function doBuildGlobalMenu (&$aGlobalMenu, &$aModuleMenu) {
			/*
			foreach ($aModuleMenu as $k => $v) {
				if ($v['text'] == 'Клуб') {
					foreach ($v['items'] as $k1 => $v1) {
						if ($v1['text'] == 'Состав') {
							unset($aModuleMenu[$k]['items'][$k1]);
						}
					}
				}
			}
			
			$arRes = [
				"global_menu_hockey"     => [
					"menu_id"      => "hockey",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "Хоккейный клуб",
					"title"        => "Хоккейный клуб",
					"sort"         => 120,
					"items_id"     => "global_menu_hockey",
					"help_section" => "custom",
					"items"        => []
				],
				"global_menu_hockey_khl" => [
					"menu_id"      => "hockey_khl",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "КХЛ Меню (Test)",
					"title"        => "КХЛ Меню (Test)",
					"sort"         => 120,
					"items_id"     => "global_menu_hockey_khl",
					"help_section" => "custom",
					"items"        => []
				],
				"global_menu_quiz"       => [
					"menu_id"      => "quiz",
					"page_icon"    => "services_title_icon",
					"index_icon"   => "services_page_icon",
					"text"         => "QUIZ",
					"title"        => "QUIZ",
					"sort"         => 120,
					"items_id"     => "global_menu_quiz",
					"help_section" => "custom",
					"items"        => []
				],
			];
			
			return $arRes;
			*/
		}
		function onBeforeUserRegister (&$arFields) {}
		function onBeforeUserAdd(&$arFields) {}
		function onBeforeUserUpdate (&$arFields) {
      $arGroups = \CUser::GetUserGroup($arFields['ID']);
      $isPartner = false;
      if (!in_array(17, $arGroups)) {
        foreach ($arFields['GROUP_ID'] as $group){
          if(intval($group['GROUP_ID']) === 17){
            $isPartner = true;
          }
        }
      }
      if($isPartner){
        $rsUser = \CUser::GetByID($arFields['ID'])->Fetch();
        
        Event::send([
          'EVENT_NAME' => 'USER_PARTNER_ACTIVATE',
          'LID' => 's1',
          'C_FIELDS' => [
            'USER_FIO' => $rsUser['NAME'] . ' ' . $rsUser['LAST_NAME'],
            'USER_EMAIL' => $rsUser['EMAIL'],
          ],
        ]);
      }
    }
		
		function onAfterUserLogin(&$arFields) {}
		
		function onEpilog() {
  
		}
	}