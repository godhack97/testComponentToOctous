<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

class ArlixOrderSearch extends CBitrixComponent implements Controllerable
{
    private $iblockId = (SITE_ID == 's2') ? CATALOG_EN_ID : CATALOG_ID;
    /**
     * кешируемые ключи arResult
     * @var array()
     */
    protected $cacheKeys = array();

    /**
     * дополнительные параметры, от которых должен зависеть кеш
     * @var array
     */
    protected $cacheAddon = array();

    /**
     * парамтеры постраничной навигации
     * @var array
     */
    protected $navParams = array();

    /**
     * вохвращаемые значения
     * @var mixed
     */
    protected $returned;

    /**
     * тегированный кеш
     * @var mixed
     */
    protected $tagCache;

    /**
     * подключает языковые файлы
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array();

        return $result;
    }

    /**
     * проверяет подключение необходиимых модулей
     * @throws LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('iblock'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
    }

    /**
     * проверяет заполнение обязательных параметров
     * @throws SystemException
     */
    protected function checkParams()
    {

    }

    /**
     * выполяет действия перед кешированием
     */
    protected function executeProlog()
    {

    }

    /**
     * получение результатов
     */
    protected function getResult()
    {

    }

    /**
     * выполняет действия после выполения компонента, например установка заголовков из кеша
     */
    protected function executeEpilog()
    {

    }

    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        global $APPLICATION;
        try
        {
            $this->checkModules();
            $this->checkParams();
            $this->executeProlog();
            $this->getResult();
            $this->includeComponentTemplate();
            $this->executeEpilog();

            return $this->returned;
        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'search' => [
                'prefilters' => [
                    new ActionFilter\Authentication()
                ],
                'postfilters' => []
            ]
        ];
    }

    /**
     * @param string $query
     * @return array
     */
    public function searchAction($query = '')
    {
        $result = [];
        $mxResult = CCatalogSKU::GetInfoByProductIBlock(
            $this->iblockId
        );
        $arFilter = [
            "IBLOCK_ID" => $this->iblockId,
            "ACTIVE" => "Y",
            [
                "LOGIC" => "OR",
                "PROPERTY_CML2_ARTICLE" => "%" . $query . "%",
                "NAME" => "%" . $query . "%",
            ],
        ];

        $res = \CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 30]);
        while ($ob = $res->GetNext())
        {
            $item = [
                'id' => (int)$ob["ID"],
                'name' => $ob["NAME"],
                'price' => 0,
                'pict' => '',
            ];
            if (is_array($mxResult))
            {
                $rsOffers = \CIBlockElement::GetList(
                    ["PRICE" => "ASC"],
                    [
                        'IBLOCK_ID' => $mxResult['IBLOCK_ID'],
                        'PROPERTY_' . $mxResult['SKU_PROPERTY_ID'] => $ob["ID"]
                    ], false, ["nPageSize" => 1]
                );
                while ($arOffer = $rsOffers->GetNext())
                {
                    $ar_price = GetCatalogProductPrice($arOffer["ID"], 1);
                    $item["price"] = (float)$ar_price["PRICE"];
                    break;
                }
            }
            $arSelectColor = Array("ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*");
            $arFilterColor = Array(
                "IBLOCK_ID" => COLORS_ID,
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                'PROPERTY_PARENT' => $ob["ID"],
                "!PROPERTY_IMAGES" => false);
            $resColor = CIBlockElement::GetList(Array(), $arFilterColor, false, Array("nPageSize" => 1), $arSelectColor);
            while ($obColor = $resColor->GetNextElement())
            {
                $arFields = $obColor->GetFields();
                $arProps = $obColor->GetProperties();
                $arFields = array_merge($arProps, $arFields);
                $pict = 0;
                if ($arFields["IMAGES"]["VALUE"])
                {
                    if (is_array($arFields["IMAGES"]["VALUE"]))
                    {
                        if (count($arFields["IMAGES"]["VALUE"]) > 0)
                        {
                            $pict = (int)$arFields["IMAGES"]["VALUE"][0];
                        }
                    }
                    else
                    {
                        $pict = (int)$arFields["IMAGES"]["VALUE"];
                    }
                }
                if ($pict > 0)
                {
                    $arFileTmp = \CFile::ResizeImageGet(
                        $pict,
                        array("width" => 124, "height" => 184),
                        BX_RESIZE_IMAGE_EXACT,
                        true
                    );
                    $item["pict"] = "background-image: url(".$arFileTmp["src"].");";
                }
            }
            $result[] = $item;
        }

        return $result;
    }

}