<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Config;
use Bitrix\Main\Data;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Application;

use tokit\Favorites\Tools;

Loc::loadMessages(__FILE__);

class ToKitFavoritesLineComponent extends CBitrixComponent
{
	protected $errorsFatal = array();
	protected $errorsNonFatal = array();

	protected $requestData = array();
	protected $options = array();

	protected $useCatalog = true;

	private $dateFormat = '';
	private $navString = "";
	private $navObject;

	protected $filter = array();

	protected $sortBy = false;

	protected $sortOrder = false;

	protected $dbResult = array();
	private $dbQueryResult = array();

	protected $currentCache = null;

	protected $application;
	
	public function __construct($component = null)
	{
		parent::__construct($component);

		$this->dateFormat = CSite::GetDateFormat("FULL", SITE_ID);
		global $APPLICATION;
		$this->application = $APPLICATION;
		Loc::loadMessages(__FILE__);
	}

	public function onPrepareComponentParams($arParams)
	{

		$arParams["FAVORITES_COUNT"] = intval($arParams["FAVORITES_COUNT"]);
		if($arParams["FAVORITES_COUNT"]<=0)
			$arParams["FAVORITES_COUNT"] = 20;

		$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
		if(!$arParams["CACHE_FILTER"])
			$arParams["CACHE_TIME"] = 0;

		$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
		$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
		$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
		$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');


		$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
		$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
		$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
		$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
		$arParams["NAV_TEMPLATE"] = trim($arParams["NAV_TEMPLATE"]);
		$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
		$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
		$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
		$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";
		$arParams["DATE_FORMAT"] = trim($arParams["DATE_FORMAT"]);
		$arParams['DATE_FORMAT'] = ($arParams['DATE_FORMAT'] != ""?$arParams['DATE_FORMAT']:"d.m.Y");

		$arParams["SORT_BY"] = trim($arParams["SORT_BY"]);
		if(strlen($arParams["SORT_BY"])<=0)
			$arParams["SORT_BY"] = "ID";
		if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER"]))
			$arParams["SORT_ORDER"]="DESC";


		return $arParams;
	}

	protected function checkRequiredModules()
	{
		if (!Loader::includeModule('tokit.favorites'))
			throw new Main\SystemException(Loc::getMessage("TOKIT_FAVORITES_MODULE_NOT_INSTALL"));
		if (!Loader::includeModule('iblock'))
			throw new Main\SystemException(Loc::getMessage("IBLOCK_MODULE_NOT_INSTALL"));

	}

	protected function obtainData()
	{
		$this->obtainDataReferences();
		$this->obtainDataFavoritess();
	}

	protected function obtainDataReferences()
	{
		if ($this->startCache(array('tokit-count')))
		{
			try
			{
				$cachedData = array();
			}
			catch (Exception $e)
			{
				$this->abortCache();
				throw $e;
			}

			$this->endCache($cachedData);

		}
		else
			$cachedData = $this->getCacheData();

		$this->dbResult = array_merge($this->dbResult, $cachedData);
	}

	protected function obtainDataFavoritess()
	{
		global $USER;

		if($USER->IsAuthorized()) {
			$this->dbQueryResult['FAVORITES'] = \tokit\Favorites\FavoritesTable::getList(
				array(
					"filter" => ['USER_ID' => $USER->GetId()],
					"count_total" => true,
				)
			);
			$this->dbResult['COUNT'] = $this->dbQueryResult['FAVORITES']->getCount();
		} else {
			$favList = json_decode($_COOKIE['tokit_favs']);
			$this->dbResult['COUNT'] = is_array($favList) ? count($favList) : 0;
		}
	} 

	/**
	 * Move data read from database to a specially formatted $arResult
	 * @return void
	 */
	protected function formatResult()
	{
		$arResult = array();
		$arResult["COUNT"] = $this->dbResult['COUNT'];
		$this->arResult = $arResult;
	}


	////////////////////////
	// Cache functions
	////////////////////////
	final protected function getCacheNeed()
	{
		return	intval($this->arParams['CACHE_TIME']) > 0 &&
				$this->arParams['CACHE_TYPE'] != 'N' &&
				Config\Option::get("main", "component_cache_on", "Y") == "Y";
	}

	final protected function startCache($cacheId = array())
	{
		if(!$this->getCacheNeed())
			return true;

		$this->currentCache = Data\Cache::createInstance();

		return $this->currentCache->startDataCache(intval($this->arParams['CACHE_TIME']), $this->getCacheKey($cacheId));
	}

	final protected function endCache($data = false)
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		$this->currentCache->endDataCache($data);
		$this->currentCache = null;
	}

	final protected function abortCache()
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		$this->currentCache->abortDataCache();
		$this->currentCache = null;
	}

	final protected function getCacheData()
	{
		if(!$this->getCacheNeed())
			return;

		if($this->currentCache == 'null')
			throw new Main\SystemException('Cache were not started');

		return $this->currentCache->getVars();
	}

	final protected function getCacheKey($cacheId = array())
	{
		if(!is_array($cacheId))
			$cacheId = array((string) $cacheId);

		$cacheId['SITE_ID'] = SITE_ID;
		$cacheId['LANGUAGE_ID'] = LANGUAGE_ID;
		// if there are two or more caches with the same id, but with different cache_time, make them separate
		$cacheId['CACHE_TIME'] = intval($this->arResult['CACHE_TIME']);

		if(defined("SITE_TEMPLATE_ID"))
			$cacheId['SITE_TEMPLATE_ID'] = SITE_TEMPLATE_ID;

		return implode('|', $cacheId);
	}

	public function executeComponent()
	{
		try
		{
			$this->checkRequiredModules();

			$this->obtainData();
			$this->formatResult();
		}
		catch (Exception $e)
		{
			$this->errorsFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx($e->getMessage());
		}

		if($this->requestData["AJAX_CALL_FAV_COUNT"] == "Y")
		{
			$this->application->RestartBuffer();
		}
		$this->includeComponentTemplate();
		if ($this->requestData["AJAX_CALL_FAV_COUNT"] == "Y")
		{
			die();
		}
	}
	
}