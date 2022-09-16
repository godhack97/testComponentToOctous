<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;

use tokit\Favorites\Tools;

Loc::loadMessages(__FILE__);

class ToKitFavoritesScriptComponent extends CBitrixComponent
{
	/*
		Component Variables
	*/
	protected static $userCookieId;
	protected $scriptFilter = array("CAN_BUY" => "Y", "DELAY" => "N", "SUBSCRIBE" => "N");
	protected $default_view_field = array(
		"USER_EMAIL",
		"USER_NAME",
		"USER_PHONE",
		"COMMENT"
	);
	protected $default_required_field = array(
		"USER_EMAIL",
	);

	/*
		Arrays for exceptions (for debug)
	*/
	protected $fatalExceptions = array();
	protected $nfatalExceptions = array();

	/* 
		Include Language Files
	*/
	public function onIncludeComponentLang()
	{
		Loc::loadMessages(__FILE__);
	}

	/* 
		Prepare Component Params
	*/
	public function onPrepareComponentParams($arParams)
	{
		return $arParams;
	}
	
	
	/* 
		Check Installed modules
	*/
	protected function checkRequiredModules()
	{
		// if (!Loader::includeModule('tokit.core'))
		// 	throw new Main\SystemException(Loc::getMessage("TOKIT_CORE_MODULE_NOT_INSTALL"));
		if (!Loader::includeModule('tokit.favorites'))
			throw new Main\SystemException(Loc::getMessage("TOKIT_FAVORITES_MODULE_NOT_INSTALL"));
		if (!Loader::includeModule('iblock'))
			throw new Main\SystemException(Loc::getMessage("IBLOCK_MODULE_NOT_INSTALL"));
		
	}
	
	/*
		Send Request
	*/
	protected function processRequest()
	{
		$this->requestData = Context::getCurrent()->getRequest();
	}

	/**
	 * Form Data for Component based on Cookie (if not loggined) or Database;
	 */
	protected function formatResult()
	{
		global $USER;
		$arResult = [];
		$arFields = [
			"ACTIVE" => "Y",
		];

		if($USER->IsAuthorized()) {
			$arSelect = ['ID', 'ELEMENT_ID'];
			$arFilter = ['USER_ID' => $USER->GetId()];
			$arResult['CUR_IN_FAV'] = Tools::getFavList($arSelect, $arFilter);
		}

		$this->arResult = $arResult;
	}

	private function performActions()
	{
		try
		{
			$this->performSyncList();
			$this->performActionList();
		}
		catch (Exception $e)
		{
			$this->errorsNonFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx($e->getMessage());
		}
	}

	protected function performActionList()
	{
		$this->performActionCheckFavorites();
	}
	protected function performSyncList()
	{
		$this->performActionSyncFavorites();
	}

	/**
	 * Perform the following action: add favorites
	 */
	protected function performActionCheckFavorites()
	{
		if(isset($this->requestData['TOKIT_ELEM_ID']) && $this->requestData['TOGGLE_FAV'] == 'Y' && !isset($this->arResult['FATAL_ERROR'])){
			$this->checkFavoritesElement($this->requestData["TOKIT_ELEM_ID"]);
		}
	}
	protected function performActionSyncFavorites()
	{
		if(isset($this->requestData['TOKIT_DIFFS']) && !isset($this->arResult['FATAL_ERROR'])){
			$this->syncFavoritesElements($this->requestData["TOKIT_DIFFS"]);
		}
	}
	/**
	 * Sync Local Storage with DB
	 */
	private function syncFavoritesElements($arItems){
		global $USER, $DB;

		$arDiff = json_decode($arItems);
		$savedItems = [];
		$userId = $USER->GetID();
		foreach($arDiff as $favItem) {
			$arFields = [
				"USER_ID" => $userId,
				"ELEMENT_ID" => $favItem,
			];
			$result = Tools::addToFav($arFields);
			$savedItems[] = $result;
		}
		$this->arResult['SUCCESS'] = $savedItems;
	}

	/**
	 * Add or remove element from favorite list
	 */
	private function checkFavoritesElement($ElementId){
		global $USER, $DB;
		if($ElementId <= 0){
			throw new Main\SystemException(Loc::getMessage('TOKIT_ELEMENT_NOT_FOUND'));
		}

		$arFields = array(
			"ELEMENT_ID" => $ElementId
		);

		$isSaveDb = false;

		if($USER->IsAuthorized()){
			$arFields['USER_ID'] = $USER->GetID();
			$isSaveDb = true;
		}

		if($isSaveDb) {
			// Find Fav in DB
			$favDb = \tokit\Favorites\FavoritesTable::getList([
				'select' => ['ID', 'ELEMENT_ID'],
				'filter' => $arFields,
			]);

			if($favItem = $favDb->fetch()){
				// delete fav
				$result = Tools::removeFromFav($arFields);
				$this->arResult['SUCCESS'] = $result;
			} else {
				// add fav
				$result = Tools::addToFav($arFields);
				$this->arResult['SUCCESS'] = $result;
			}

			$this->arResult['SUCCESS']['COUNT'] = self::getTotalCount($USER->GetID());
		} else {
			$this->arResult['SUCCESS'] = [
				'AUTHORIZED' => false
			];
		}
	}

	protected function getTotalCount($userId)
	{
		$arSelect = ['ID'];
		$arFilter = ['USER_ID' => $userId];
		$total = Tools::getFavList($arSelect, $arFilter);
		return count($total);
	}

	/**
	 * The default action in case of success copying order
	 */
	protected function doAfterDeleteFavor()
	{
		if($_REQUEST['AJAX_FAVORITES_CALL'] != "Y")
			LocalRedirect();
	}

	public function executeComponent()
	{
		global $APPLICATION;
		try{
			$this->checkRequiredModules();
			$this->processRequest();
			$this->formatResult();
			$this->performActions();
		}
		catch (Exception $e){
			$this->errorsFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx($e->getMessage());
		}

		if($this->requestData["AJAX_FAVORITES_CALL"] == "Y")
		{
			$APPLICATION->RestartBuffer();
			if(isset($this->arResult['SUCCESS'])){
				print json_encode($this->arResult['SUCCESS']);
			}
			die();
		}
				
		$this->includeComponentTemplate();
	}
}