<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('BX_NO_ACCELERATOR_RESET', true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

exit('stop');

//AddMessage2log('CRON geo_import START');

CModule::IncludeModule('iblock');

/*$res = \Bitrix\Sale\Location\TypeTable::getList(array(
  'select' => array('*', 'NAME_RU' => 'NAME.NAME'),
  'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
));
while($item = $res->fetch())
{
  echo "<pre>";print_r($item);echo "</pre>";
}
*/



$location = [];
$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array(
        '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
        //'NAME.NAME' => $data[4],
        //'TYPE.CODE' => 'COUNTRY',
    ),
    'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE','CODE'),
));
while($item = $res->fetch())
{
  $location[$item['NAME_RU']]=$item['ID'];
  $location[$item['CODE']]=$item['ID'];
}

//print_r($location);


if (($handle = fopen($_SERVER['DOCUMENT_ROOT']."/upload/geo/geo.csv", "r")) !== FALSE)
{
    $count=0;
    $update=0;
    $maxupdate = 50;
    $contry=false;
    $region=false;
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
        if($update>=$maxupdate)
            break;
        $count++;
        switch ($data[0]) {
            case "S":
                if($location[$data[4]])
                {
                    $contry=$location[$data[4]];
                }else{
                    print_r(array(
                        'CODE' => CUtil::translit($data[2],'ru'),
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
                    $res = \Bitrix\Sale\Location\LocationTable::add(array(
                        'CODE' => CUtil::translit($data[2],'ru'),
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
                    $update++;
                    if($res->isSuccess())
                    {
                        $contry=$res->getId();
                    }
                    else
                    {
                        print_r("Ошибка страна: ".implode($res->getErrorMessages(),', '));
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
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                        print_r(array(
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                        $update++;
                        if($res->isSuccess())
                        {
                            $region=$res->getId();
                        }
                        else
                        {
                            print_r("Ошибка регион: ".implode($res->getErrorMessages(),', '));
                            $region=false;
                        }
                    }
                }
                break;
            case "T":
                //город
                if($region){
                    if(!$location[$data[4]] && !$location[CUtil::translit($data[2],'ru')])
                    {
                        $res = \Bitrix\Sale\Location\LocationTable::add(array(
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                        if (!$res->isSuccess())
                        {
                            print_r("Ошибка город: ".implode($res->getErrorMessages(),', '));
                        }else{
                            $update++;
                        }
                        print_r(array(
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                    if(!$location[$data[4]] && !$location[CUtil::translit($data[2],'ru')])
                    {
                        print_r(array(
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                        $res = \Bitrix\Sale\Location\LocationTable::add(array(
                            'CODE' => CUtil::translit($data[2],'ru'),
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
                        if (!$res->isSuccess())
                        {
                            print_r("Ошибка город: ".implode($res->getErrorMessages(),', '));
                        }else{
                            $update++;
                        }

                    }
                }
                break;
            default:
                //echo "<pre>";print_r($data);echo "</pre>";
                break;
        }
    }
    fclose($handle);
    //echo "Обработанно строк: ".$count;
    
    AddMessage2log('CRON geo_import — обработано: '. $count );
    
}

