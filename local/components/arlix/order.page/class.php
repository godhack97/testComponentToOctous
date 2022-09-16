<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Highloadblock\HighloadBlockTable;

class ArlixOrderPage extends CBitrixComponent implements Controllerable
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
        if (!Main\Loader::includeModule('highloadblock'))
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
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    protected function _sortSizes($a, $b)
    {
        $aUpper = strtoupper($a["VALUE"]);
        $bUpper = strtoupper($b["VALUE"]);

        $sizes = array(
            'XXXS',
            'XXXS/XXS',
            'XXS/XXXS',
            'XXS',
            'XXS/XS',
            'XS/XXS',
            'XS',
            'XS/S',
            'S/SX',
            'S',
            'S/M',
            'M/S',
            'M',
            'M/L',
            'L/M',
            'L',
            'XL/L',
            'L/XL',
            'XL',
            'XL/XXL',
            'XXL/XL',
            'XXL',
            'XXXL/XXL',
            'XXL/XXXL',
            'XXXL',
        );

        $asize = 100;
        $apos = -1;
        $bsize = 100;
        $bpos = -1;
        foreach ($sizes as $val => $str)
        {
            $pos = ($aUpper == $str) ? 1 : -1;
            if ($pos > 0 && ($apos < 0 || $pos < $apos))
            {
                $asize = $val;
                $apos = $pos;
            }
            $pos = ($bUpper == $str) ? 1 : -1;
            if ($pos > 0 && ($bpos < 0 || $pos < $bpos))
            {
                $bsize = $val;
                $bpos = $pos;
            }
        }
        if ($apos < 0)
        {
            if (is_numeric($a) && is_numeric($b))
            {
                return ($a == $b) ? 0 : ($a > $b) ? 1 : -1;
            }
            else
            {
                return (is_numeric($a)) ? -1 : 1;
            }
        }
        if ($bpos < 0)
        {
            if (is_numeric($a) && is_numeric($b))
            {
                return ($a == $b) ? 0 : ($a > $b) ? 1 : -1;
            }
            else
            {
                return (is_numeric($b)) ? 1 : -1;
            }
        }

        return ($asize == $bsize ? 0 : ($asize > $bsize ? 1 : -1));
    }


    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'getelement' => [
                'prefilters' => [
                    new ActionFilter\Authentication()
                ],
                'postfilters' => []
            ]
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    
    public function getelementAction($id = 0)
    {
        $result = [];

        if ($id <= 0)
            return $result;

        if (!Main\Loader::includeModule('iblock'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
        if (!Main\Loader::includeModule('highloadblock'))
            throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));


        $select_fields = Array();
        $filter = Array("ACTIVE" => "Y", "ID" => [7, 8, 11, 12, 22, 25]);
        $resStore = \CCatalogStore::GetList(array(), $filter, false, false, $select_fields);
        while ($sklad = $resStore->Fetch())
            $stores[] = $sklad;
        $result['stores'] = $stores;

        $hlblock = HighloadBlockTable::getById(2)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $PsuTaskControl = $entity->getDataClass();

        $res = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_TYPE' => 'catalogs',
                'IBLOCK_ID' => IBLOCK_MEDIA,
                'PROPERTY_PARENT' => $id,
                'ACTIVE' => 'Y',
            ],
            false,
            false,
            [
                "IBLOCK_TYPE",
                "IBLOCK_ID",
                "NAME",
                "PROPERTY_COLOR",
                "PROPERTY_PARENT",
                "ID",
            ]
        );
        while ($ar_res = $res->GetNext())
            $COLOR_ID[$ar_res["PROPERTY_COLOR_VALUE"]] = $ar_res["ID"];

        $mxResult = CCatalogSKU::GetInfoByProductIBlock(
            $this->iblockId
        );

        $arFilter = [
            "IBLOCK_ID" => $this->iblockId,
            "ACTIVE" => "Y",
            "ID" => $id,
        ];

        $res = \CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 1], ["ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE"]);
        if ($ob = $res->GetNext())
        {
            $result['id'] = (int)$ob["ID"];
            $result['name'] = $ob["NAME"];
            $result['prop'][] = [
                'name' => "Артикул",
                'value' => $ob["PROPERTY_CML2_ARTICLE_VALUE"],
            ];

            $result['article']=$ob["PROPERTY_CML2_ARTICLE_VALUE"];

            $result['offer'] = [];

            if (is_array($mxResult))
            {
                $rsOffers = \CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $mxResult['IBLOCK_ID'],
                        'PROPERTY_' . $mxResult['SKU_PROPERTY_ID'] => $ob["ID"]
                    ]
                );
                while ($obColor = $rsOffers->GetNextElement())
                {
                    $arFields = $obColor->GetFields();
                    $arProps = $obColor->GetProperties();
                    $arOffer = array_merge($arProps, $arFields);
                    $ar_price = \GetCatalogProductPrice($arOffer["ID"], 1);
                    $arOffer["price"] = (float)$ar_price["PRICE"];
                    $result['offer'][] = $arOffer;
                }

                if ($result['offer'] && is_array($result['offer']))
                {
                    foreach ($result['offer'] as &$offer)
                    {
                        if ($COLOR_ID[$offer["COLOR"]['VALUE_XML_ID']])
                        {
                            $PHOTO_OFFER_ID[$offer["ID"]] = $offer["COLOR"]['VALUE_ENUM_ID'];
                            $COLOR_ID[$COLOR_ID[$offer["COLOR"]['VALUE_XML_ID']]] = $offer["COLOR"]['VALUE_ENUM_ID'];
                        }
                    }
                    unset($offer);
                }


                $COLOR_SIZES = [];
                $arColorsWithoutImages = [];

                if ($result["offer"] && is_array($result["offer"]))
                {

                    foreach ($result["offer"] as &$offer)
                    {
                        if ($offer["COLOR"]["VALUE"])
                        {
                            $offer["COLOR"]["VALUE_ENUM_ID"] = $offer["COLOR"]["VALUE"];
                            $offer["COLOR"]["VALUE_XML_ID"] = $offer["COLOR"]["VALUE"];
                            $reslist = $PsuTaskControl::getList([
                                'filter' => [
                                    "UF_XML_ID" => $offer["COLOR"]["VALUE"],
                                ],
                            ]);
                            if ($el = $reslist->fetch())
                            {
                                $offer["COLOR"]["VALUE"] = $el["UF_NAME"];
                            }
                        }
                    }
                    unset($offer);


                    // Двумя циклами собираем цвета с их размерами
                    foreach ($result["offer"] as $offer)
                    {
                      if ($offer["COLOR"]['VALUE'])
                      {
                        $arColor = $offer["COLOR"];

                        if (!$COLOR_SIZES[$arColor["VALUE_ENUM_ID"]])
                        {
                          $COLOR_SIZES[$arColor["VALUE_ENUM_ID"]] = [
                              'NAME' => $arColor['NAME'],
                              'SORT' => $arColor['SORT'],
                              'CODE' => $arColor['CODE'],
                              'XML_ID' => $arColor['XML_ID'],
                              'PHOTO_ID' => $arColor['PHOTO_ID'],
                              'VALUE_ENUM' => $arColor['VALUE_ENUM'],
                              'VALUE_XML_ID' => $arColor['VALUE_XML_ID'],
                              'VALUE_SORT' => $arColor['VALUE_SORT'],
                              'VALUE' => $arColor['VALUE'],
                              'PROPERTY_VALUE_ID' => $arColor['PROPERTY_VALUE_ID'],
                              'VALUE_ENUM_ID' => $arColor['VALUE_ENUM_ID'],
                              'DESCRIPTION' => $arColor['DESCRIPTION'],
                              'IMAGES' => $offer['IMAGES']['VALUE'],
                              'SIZES' => [],
                          ];
                        }
                      }
                    }


                    foreach ($result["offer"] as $offer)
                    { 
                        if ($offer["COLOR"]['VALUE'] && $offer["SIZES"]['VALUE'])
                        {

                            $colorEnumID = $offer["COLOR"]["VALUE_ENUM_ID"];
                            $arSizes = $offer["SIZES"];
                            $arRost = $offer['ROST'];
                            

                            $ROST = '';
                            if(!empty($arRost['VALUE']))
                              $ROST = ' - '. $arRost['VALUE'];

                            if ($COLOR_SIZES[$colorEnumID])
                            {
                                $COLOR_SIZES[$colorEnumID]['SIZES'][] = [
                                    'OFFER_ID' => $offer['ID'],
                                    'price' => $offer['price'],
                                    'NAME' => $arSizes['NAME'],
                                    'SORT' => $arSizes['SORT'],
                                    'CODE' => $arSizes['CODE'],
                                    'XML_ID' => $arSizes['XML_ID'],
                                    'PHOTO_ID' => $arColor['PHOTO_ID'],
                                    'VALUE_ENUM' => $arSizes['VALUE_ENUM'],
                                    'VALUE_XML_ID' => $arSizes['VALUE_XML_ID'],
                                    'VALUE_SORT' => $arSizes['VALUE_SORT'],
                                    'VALUE' => $arSizes['VALUE'] . $ROST,
                                    'PROPERTY_VALUE_ID' => $arSizes['PROPERTY_VALUE_ID'],
                                    'VALUE_ENUM_ID' => $arSizes['VALUE_ENUM_ID'],
                                    'DESCRIPTION' => $arSizes['DESCRIPTION'],
                                ];
                            }
                        }
                        if ($offer["COLOR"]['VALUE'] && $offer["SIZES_JEANS"]['VALUE'])
                        {

                            $colorEnumID = $offer["COLOR"]["VALUE_ENUM_ID"];
                            $arSizes = $offer["SIZES_JEANS"];
                            $arRost = $offer['ROST'];
                            

                            $ROST = '';
                            if(!empty($arRost['VALUE']))
                              $ROST = ' - '. $arRost['VALUE'];

                            if ($COLOR_SIZES[$colorEnumID])
                            {
                                $COLOR_SIZES[$colorEnumID]['SIZES'][] = [
                                    'OFFER_ID' => $offer['ID'],
                                    'price' => $offer['price'],
                                    'NAME' => $arSizes['NAME'],
                                    'SORT' => $arSizes['SORT'],
                                    'CODE' => $arSizes['CODE'],
                                    'XML_ID' => $arSizes['XML_ID'],
                                    'PHOTO_ID' => $arColor['PHOTO_ID'],
                                    'VALUE_ENUM' => $arSizes['VALUE_ENUM'],
                                    'VALUE_XML_ID' => $arSizes['VALUE_XML_ID'],
                                    'VALUE_SORT' => $arSizes['VALUE_SORT'],
                                    'VALUE' => $arSizes['VALUE'] . $ROST,
                                    'PROPERTY_VALUE_ID' => $arSizes['PROPERTY_VALUE_ID'],
                                    'VALUE_ENUM_ID' => $arSizes['VALUE_ENUM_ID'],
                                    'DESCRIPTION' => $arSizes['DESCRIPTION'],
                                ];
                            }
                        }
                    }

                    

                    // Собираем фотографии с торговых предложений в соответствии с цветом
                    $OFFERS_IMAGES = [];
                    foreach ($result['offer'] as $arOffer)
                    {
                        if (!empty($arOffer['MORE_PHOTO']['VALUE']))
                        {
                            $colorEnumID = $arOffer['COLOR']['VALUE_ENUM_ID'];
                            $arTmpImages = $OFFERS_IMAGES[$colorEnumID];

                            if (count($arTmpImages))
                                $OFFERS_IMAGES[$colorEnumID] = array_merge($arOffer['MORE_PHOTO']['VALUE'], $arTmpImages);
                            else
                                $OFFERS_IMAGES[$colorEnumID] = $arOffer['MORE_PHOTO']['VALUE'];
                        }
                    }


                    $arNewPhotos = [];
                    $arSelect = Array("ID", "IBLOCK_ID", "NAME", "SORT", "DATE_ACTIVE_FROM", "PROPERTY_*");
                    $arFilter = Array("IBLOCK_ID" => COLORS_ID, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'PROPERTY_PARENT' => $id, "!PROPERTY_IMAGES" => false);
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 300), $arSelect);
                    while ($ob = $res->GetNextElement())
                    {
                        $arFields = $ob->GetFields();
                        $arProps = $ob->GetProperties();
                        $arFields = array_merge($arProps, $arFields);
                        $arNewPhotos[$arFields['COLOR']['VALUE']] = $arFields;
                    }


                    // Массив цветов у которых есть картинки
                    $OFFERS_COLOR_LIST = [];
                    foreach ($COLOR_SIZES as $colorEnumID => $item)
                    {
                        if (array_key_exists($item["VALUE_XML_ID"], $arNewPhotos))
                        {
                            $OFFERS_COLOR_LIST[$colorEnumID] = [
                                'NAME' => $item['VALUE'],
                                'IMAGES' => $arNewPhotos[$item["VALUE_XML_ID"]]["IMAGES"]["VALUE"],
                            ];
                        }
                        elseif (array_key_exists($colorEnumID, $OFFERS_IMAGES))
                        {
                            $OFFERS_COLOR_LIST[$colorEnumID] = [
                                'NAME' => $item['VALUE'],
                                'IMAGES' => $OFFERS_IMAGES[$colorEnumID]
                            ];
                        }
                        else
                            $arColorsWithoutImages[$colorEnumID] = $colorEnumID;
                    }

                    if (count($arColorsWithoutImages))
                    {
                        foreach ($COLOR_SIZES as $colorEnumID => $item)
                        {
                            if (array_key_exists($colorEnumID, $arColorsWithoutImages))
                                unset($COLOR_SIZES[$colorEnumID]);
                        }
                    }

                    foreach ($OFFERS_COLOR_LIST as $key => $value)
                    {
                        $arFileTmp = \CFile::ResizeImageGet(
                            current($value["IMAGES"]),
                            array("width" => 493, "height" => 818),
                            BX_RESIZE_IMAGE_EXACT,
                            true
                        );
                        $newPict = [
                            "ID" => $key,
                            "SRC" => $arFileTmp["src"], //"background-image: url(" . $arFileTmp["src"] . ");",
                            "URL" => $arFileTmp["src"],
                        ];
                        $result['offers_color_list'][] = $newPict;
                    }

                    $result['color_sizes'] = array_values($COLOR_SIZES);
                    foreach ($result['color_sizes'] as &$color)
                    {
                        foreach ($color["SIZES"] as &$size)
                        {
                            $size["VALUE"] = str_replace(["RU", "(", ")", "/"], "", $size["VALUE"]);
                            //$size["VALUE"] = preg_replace('#[0-9 ]*#', '', $size["VALUE"]);
                            $size["VALUE"] = trim($size["VALUE"]);
                            $size["stores"] = [];
                            foreach ($result['stores'] as $store)
                            {
                                $arFilterStore = ["PRODUCT_ID" => $size["OFFER_ID"], "STORE_ID" => $store["ID"]];
                                $resStore = \CCatalogStoreProduct::GetList([], $arFilterStore, false, false, []);
                                if ($arResStore = $resStore->GetNext())
                                {
                                    $size["stores"][] = (int)$arResStore['AMOUNT'];
                                }else{
                                    $size["stores"][] = 0;
                                }
                            }
                        }
                        unset($size);
                        usort($color["SIZES"], [$this, '_sortSizes']);
                    }
                    unset($color);
                    $result['select_color_sizes'] = $result['color_sizes'][0]["VALUE_XML_ID"];
                    $result['select_offer_id'] = $result['color_sizes'][0]["SIZES"][0]["OFFER_ID"];
                    $result['select_stores'] = $result['stores'][0]["ID"];
                }
            }
        }
        return $result;
    }
}
