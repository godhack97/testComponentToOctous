<?php

exit('stop');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('BX_NO_ACCELERATOR_RESET', true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule('iblock');

/*$res = \Bitrix\Sale\Location\TypeTable::getList(array(
    'select' => array('*', 'NAME_RU' => 'NAME.NAME'),
    'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
));
while($item = $res->fetch())
{
    echo "<pre>";print_r($item);echo "</pre>";
}*/
$location=[];
$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array(
        '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
        //'NAME.NAME' => $data[4],
        //'TYPE.CODE' => 'COUNTRY',
    ),
    'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
));
while($item = $res->fetch())
{
    $location[$item['NAME_RU']]=$item['NAME_RU'];
}


if (($handle = fopen($_SERVER['DOCUMENT_ROOT']."/upload/geo/geo.csv", "r")) !== FALSE)
{
    $contry=false;
    $region=false;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
        switch ($data[0]) {
            case "S":
                if($location[$data[4]])
                {
                    $contry=$location[$data[4]];
                }else{
                    $res = \Bitrix\Sale\Location\LocationTable::add(array(
                        'CODE' => $data[2],
                        'SORT' => '100',
                        //'PARENT_ID' => 1,
                        'TYPE_ID' => 2, // ID типа
                        'NAME' => array( // языковые названия
                            'ru' => array(
                                'NAME' => $data[4]
                            ),
                            'en' => array(
                                'NAME' => $data[2]
                            ),
                        )
                    ));
                    if($res->isSuccess())
                    {
                        $contry=$res->getId();
                    }
                    else
                    {
                        $contry=false;
                    }
                }
                break;
            case "R":
                //регион
                if($contry){
                    if($location[$data[4]])
                    {
                        $region=$location[$data[4]];
                    }else{
                        $res = \Bitrix\Sale\Location\LocationTable::add(array(
                            'CODE' => $data[2],
                            'SORT' => '100',
                            'PARENT_ID' => $contry,
                            'TYPE_ID' => 4, // ID типа
                            'NAME' => array( // языковые названия
                                'ru' => array(
                                    'NAME' => $data[4]
                                ),
                                'en' => array(
                                    'NAME' => $data[2]
                                ),
                            )
                        ));
                        if($res->isSuccess())
                        {
                            $region=$res->getId();
                        }
                        else
                        {
                            $region=false;
                        }
                    }
                }
                break;
            case "T":
                //город
                if($region){
                    if(!$location[$data[4]])
                    {
                        $res = \Bitrix\Sale\Location\LocationTable::add(array(
                            'CODE' => $data[2],
                            'SORT' => '100',
                            'PARENT_ID' => $region,
                            'TYPE_ID' => 1, // ID типа
                            'NAME' => array( // языковые названия
                                'ru' => array(
                                    'NAME' => $data[4]
                                ),
                                'en' => array(
                                    'NAME' => $data[2]
                                ),
                            )
                        ));
                    }
                }elseif($contry){
                    if(!$location[$data[4]])
                    {
                        $res = \Bitrix\Sale\Location\LocationTable::add(array(
                            'CODE' => $data[2],
                            'SORT' => '100',
                            'PARENT_ID' => $contry,
                            'TYPE_ID' => 1, // ID типа
                            'NAME' => array( // языковые названия
                                'ru' => array(
                                    'NAME' => $data[4]
                                ),
                                'en' => array(
                                    'NAME' => $data[2]
                                ),
                            )
                        ));
                    }
                }
                break;
            default:
                //echo "<pre>";print_r($data);echo "</pre>";
                break;
        }
    }
    fclose($handle);
}

