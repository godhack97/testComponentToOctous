<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale;

$access = htmlspecialchars($_GET['access']);
if($access != '5gzk9ghaj')
  exit('Access denied');

CModule::IncludeModule("form");
CModule::IncludeModule("iblock");

$currentDate = new DateTime();
$toDate = $currentDate->format('d.m.Y');
$currentDate->modify('-60 day');
$fromDate =  $currentDate->format('d.m.Y');

$arJson = [];
$arFields[] = array(
  "CODE" => "SIMPLE_QUESTION_144",
  "FILTER_TYPE" => "answer_id",
  "VALUE" => 11,
  "PARAMETER_NAME" => "ANSWER_VALUE"
);
$arFilter = [
  "DATE_CREATE_1" => $fromDate,
  "DATE_CREATE_2" => $toDate,
  "FIELDS" => $arFields
];

$rsResults = CFormResult::GetList(1, $by = 's_date_create', $order = 'desc', $arFilter, $is_filtered, 'N', false);
$count = 0;

$rsMeasure = CCatalogMeasure::getList();
while($measure = $rsMeasure->Fetch())
  $arMeasure[$measure['ID']] = $measure;


while ($arResult = $rsResults->Fetch())
{
  $arAnswer = CFormResult::GetDataByID($arResult['ID'], array(), $arResult, $arAnswer2);

  $ORDER_ID = $arAnswer['SIMPLE_QUESTION_207'][0]['USER_TEXT'];

  $order = Sale\Order::load($ORDER_ID);
  $basket = $order->getBasket();
  $arJsonProducts = [];

  $totalCost = 0;

  $shipmentID = '';
  $collection = $order->getShipmentCollection();
  foreach ($collection as $shipment) {
    if (!$shipment->isSystem()) {
      $shipmentID = $shipment->getField('ID');
    }
  }

  $userResponsibleID = $order->getField('RESPONSIBLE_ID');
  if(!empty($userResponsibleID)) {
    $userResponsibleXML_ID = Bitrix\Main\UserTable::getList([
      "select" => ["XML_ID"],
      "filter" => ['ID' => $userResponsibleID],
    ])->fetch()['XML_ID'];
  }

  $date = new DateTime($order->getDateInsert());

  $arProductsForm = explode('<br>', $arAnswer['SIMPLE_QUESTION_954'][0]['USER_TEXT']);

  foreach($arProductsForm as $product)
  {
    // 0 - id
    // 1 - article
    // 2 - name
    // 3 - quantity
    // 4 - price
    $arProductCart = explode('##', $product);
    $arProduct = CCatalogProduct::GetByIDEx($arProductCart[0]);
    $productSum = $arProductCart[4] * $arProductCart[3];
    $totalCost += $productSum;

    //dbg($arProduct);

//  foreach ($basket as $basketItem)
//  {
    //$productID = $basketItem->getProductId();
    //$arMeasure = \Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($productID);
    //$priceProduct = $basketItem->getPrice();

    $arJsonProducts[] = [
      "Номенклатура" => $arProduct['XML_ID'], //$basketItem->getField("CATALOG_XML_ID"), //$arProduct['XML_ID'], // внешний код товара
      "ЕдиницаИзмерения" => [
        "#type" => "jcfg:CatalogRef.КлассификаторЕдиницИзмерения",
        "#value" => $arMeasure[ $arProduct['PRODUCT']['MEASURE'] ]['CODE'], // $arMeasure[$productID]['MEASURE']['CODE'] // если есть, подставить код ед. изм. (не id)
      ],
      "Характеристика" => "aa357017-bf68-11e9-8430-b88303f1ae99",
      "СерийныеНомера" => "",
      "Цена" => $arProductCart[4], // стоимость товара
      "ПроцентСкидкиНаценки" => 0,
      "СуммаСкидкиНаценки" => 0,
      "Сумма" => $arProductCart[4], // стоимость товара
      "СтавкаНДС" => "58bd2b1b-5459-11e9-8423-b88303f1ae99",
      "СуммаНДС" => 0,
      "Заказ" => [
        "#type" => "jcfg:DocumentRef.ЗаказПокупателя",
        "#value" => (string)$order->getId(), //$order->getField("XML_ID"), // внешний код заказа
      ],
      "Всего" => $productSum, // стоимость товара
      "Себестоимость" => 0,
      "СуммаРасходов" => 0,
      "Содержание" => "",
      "КлючСвязи" => 1,
      "ИдентификаторСтроки" => "",
      "НеобходимостьВводаАкцизнойМарки" => false,
      "Партия" => "00000000-0000-0000-0000-000000000000",
      "Количество" => $arProductCart[3], //$basketItem->getQuantity, // подставляем из заказа
      "СтранаПроисхождения" => "00000000-0000-0000-0000-000000000000",
      "НомерГТД" => "00000000-0000-0000-0000-000000000000",
      "ТоварыДляПроизводства" => false,
      "СтруктурнаяЕдиница" => "11eb0165-6fca-11e9-8425-b88303f1ae99", // код склада (если используется в битриксе)
      "Ячейка" => "00000000-0000-0000-0000-000000000000",
      "НоменклатураНабора" => "00000000-0000-0000-0000-000000000000",
      "ХарактеристикаНабора" => "00000000-0000-0000-0000-000000000000",
      "ДоляСтоимости" => 0,
      "Штрихкод" => "",
      "НоменклатураЕГАИС" => "00000000-0000-0000-0000-000000000000",
      "ЗаказПокупателя" => "00000000-0000-0000-0000-000000000000"
    ];
  }

  $arJson[] = [
    "#type" => "jcfg:DocumentObject.ПриходнаяНакладная",
    "#value" => [
      "Ref" => $arResult['ID'], //$order->getField("XML_ID"), // внешний идентификатор элемента заказа
      "DeletionMark" => false,
      "Date" => $date->format('Y-m-d\TH:i:s'), // подставить дату добавления заказа в инфоблок
      "Number" => "0000-000000",
      "Posted" => true,
      "Автор" => "00000000-0000-0000-0000-000000000000",
      "АдресЧекаЕГАИС" => "",
      "АктПереданВЕГАИС" => false,
      "ВалютаДокумента" => "58bd2b16-5459-11e9-8423-b88303f1ae99",
      "ВидОперации" => "ВозвратОтПокупателя",
      "ВидЦенКонтрагента" => "00000000-0000-0000-0000-000000000000",
      "ВключатьРасходыВСебестоимость" => false,
      "ДатаВходящегоДокумента" => "0001-01-01T00:00:00",
      "ДисконтнаяКарта" => "00000000-0000-0000-0000-000000000000",
      "Договор" => "33b7822a-6f27-11ea-8447-b88303f1ae99",
      "ДокументОснование" => [
        "#type" => "jcfg:DocumentRef.РасходнаяНакладная",
        "#value" => $shipmentID // документ отгрузки, на основании отгрузки заказа
      ],
      "ЕстьМаркируемаяПродукцияГИСМ" => false,
      "УдалитьЕстьРасхождения" => false,
      "Заказ" => [
        "#type" => "jcfg:DocumentRef.ЗаказПокупателя",
        "#value" => (string)$order->getId(), //$order->getField("XML_ID"), //$order->getId() // подставить код заказа
      ],
      "ИдентификаторЧекаШтрихМ" => "00000000-0000-0000-0000-000000000000",
      "Комментарий" => "",
      "КонтактноеЛицоПодписант" => "00000000-0000-0000-0000-000000000000",
      "Контрагент" => "33b78229-6f27-11ea-8447-b88303f1ae99",
      "Кратность" => 1,
      "Курс" => 1,
      "НалогообложениеНДС" => "НеОблагаетсяНДС",
      "НДСВключатьВСтоимость" => true,
      "НомерВходящегоДокумента" => "",
      "Организация" => "bb6a661a-658f-11e9-8425-b88303f1ae99",
      "Ответственный" => $userResponsibleXML_ID, // от пользователя внешний код ответственного по заказу
      "ПодписьКладовщика" => "00000000-0000-0000-0000-000000000000",
      "ПодписьЧекаЕГАИС" => "",
      "Подразделение" => "2eb7a30b-5459-11e9-8423-b88303f1ae99",
      "ПоложениеЗаказаПоставщику" => "ВШапке",
      "ПоложениеСклада" => "ВШапке",
      "ПолученИзЕГАИС" => false,
      "РегистрироватьЦеныПоставщика" => false,
      "СрокХранения" => 0,
      "СтруктурнаяЕдиница" => "11eb0165-6fca-11e9-8425-b88303f1ae99",
      "СуммаВключаетНДС" => false,
      "СуммаДокумента" => $totalCost,
      "УдалитьТоварноТранспортнаяНакладнаяЕГАИС" => "00000000-0000-0000-0000-000000000000",
      "ХозяйственнаяОперация" => "1efcc647-6590-11e9-8425-b88303f1ae99",
      "ЧекККМ" => "00000000-0000-0000-0000-000000000000",
      "Ячейка" => "00000000-0000-0000-0000-000000000000",
      "СпособЗачетаПредоплаты" => "Вручную",
      "НомерСменыККМ" => 0,
      "НомерЧекаККМ" => 0,
      "Телефон" => "",
      "АдресЭП" => "",
      "СпециальныйНалоговыйРежим" => "НеПрименяется",
      "Запасы" => $arJsonProducts, // это товары для возврата
    ]
  ];
}

//dbg($arJson, 1);
exit(json_encode($arJson));

