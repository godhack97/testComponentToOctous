<?

use Bitrix\Sale;
use Bitrix\Sale\Exchange\Logger\Exchange;

class CSaleExportCustom extends CSaleExport
{
    static function ExportOrders2Xml($arFilter = array(), $nTopCount = 0, $currency = "", $crmMode = false, $time_limit = 0, $version = false, $arOptions = array())
    {
        $lastOrderPrefix = '';
        $arCharSets = array();
        $lastDateUpdateOrders = array();
        $entityMarker = static::getEntityMarker();

        self::setVersionSchema($version);
        self::setCrmMode($crmMode);
        self::setCurrencySchema($currency);

        $count = false;
        if (intval($nTopCount) > 0)
            $count = array("nTopCount" => $nTopCount);

        $end_time = self::getEndTime($time_limit);

        if (intval($time_limit) > 0) {
            if (self::$crmMode) {
                $lastOrderPrefix = md5(serialize($arFilter));
                if (!empty($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && intval($nTopCount) > 0)
                    $count["nTopCount"] = $count["nTopCount"] + count($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]);
            }
        }

        if (!self::$crmMode) {
            $arFilter = static::prepareFilter($arFilter);
            $timeUpdate = isset($arFilter[">=DATE_UPDATE"]) ? $arFilter[">=DATE_UPDATE"] : '';
            $lastDateUpdateOrders = static::getLastOrderExported($timeUpdate);
        }

        self::$arResultStat = array(
            "ORDERS" => 0,
            "CONTACTS" => 0,
            "COMPANIES" => 0,
        );

        $bExportFromCrm = self::isExportFromCRM($arOptions);

        $arStore = self::getCatalogStore();
        $arMeasures = self::getCatalogMeasure();
        self::setCatalogMeasure($arMeasures);
        $arAgent = self::getSaleExport();

        if (self::$crmMode) {
            self::setXmlEncoding("UTF-8");
            $arCharSets = self::getSite();
        }

        echo self::getXmlRootName(); ?>

        <<?= CSaleExport::getTagName("SALE_EXPORT_COM_INFORMATION") ?> <?= self::getCmrXmlRootNameParams() ?>><?

        $arOrder = array("DATE_UPDATE" => "ASC", "ID" => "ASC");

        $arSelect = array(
            "ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED",
            "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID",
            "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE",
            "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION",
            "ADDITIONAL_INFO",
            "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID", "ACCOUNT_NUMBER", "SUM_PAID", "DELIVERY_DOC_DATE", "DELIVERY_DOC_NUM", "TRACKING_NUMBER", "STORE_ID",
            "ID_1C", "VERSION",
            "USER.XML_ID", "USER.TIMESTAMP_X", "RESPONSIBLE"
        );

        $bCrmModuleIncluded = false;
        if ($bExportFromCrm) {
            $arSelect[] = "UF_COMPANY_ID";
            $arSelect[] = "UF_CONTACT_ID";
            if (IsModuleInstalled("crm") && CModule::IncludeModule("crm"))
                $bCrmModuleIncluded = true;
        }

        $arFilter['RUNNING'] = 'N';

        $filter = array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => $arOrder,
            'limit' => $count["nTopCount"]
        );


        if (!empty($arOptions['RUNTIME']) && is_array($arOptions['RUNTIME'])) {
            $filter['runtime'] = $arOptions['RUNTIME'];
        }

        $entity = static::getParentEntityTable();

        //custom для теста импорта
        //$filter['filter'] = ['ID' => 7697];

        $dbOrderList = $entity::getList($filter);

        while ($arOrder = $dbOrderList->Fetch()) {

            if (!self::$crmMode && (new Exchange(Sale\Exchange\Logger\ProviderType::ONEC_NAME))->isEffected($arOrder, $lastDateUpdateOrders)) {
                continue;
                //custom для теста импорта
            }

            static::$documentsToLog = array();
            $contentToLog = '';

            $order = static::load($arOrder['ID']);

            if (!$arOrder['SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID']) {
                $arOrder['SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID'] = '6e451a0c-6fea-11ea-8447-b88303f1ae99';
            }
            $arOrder['DATE_STATUS'] = $arOrder['DATE_STATUS']->toString();
            $arOrder['DATE_INSERT'] = $arOrder['DATE_INSERT']->toString();
            $arOrder['DATE_UPDATE'] = $arOrder['DATE_UPDATE']->toString();

            foreach ($arOrder as $field => $value) {
                if (self::isFormattedDateFields('Order', $field)) {
                    $arOrder[$field] = self::getFormatDate($value);
                }
            }

            if (self::$crmMode) {
                if (self::getVersionSchema() > self::DEFAULT_VERSION && is_array($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && in_array($arOrder["ID"], $_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && empty($arFilter["ID"]))
                    continue;
                ob_start();
            }

            self::$arResultStat["ORDERS"]++;

            $agentParams = (array_key_exists($arOrder["PERSON_TYPE_ID"], $arAgent) ? $arAgent[$arOrder["PERSON_TYPE_ID"]] : array());

            $arResultPayment = self::getPayment($arOrder);
            $paySystems = $arResultPayment['paySystems'];
            $arPayment = $arResultPayment['payment'];

            $arResultShipment = self::getShipment($arOrder);
            $arShipment = $arResultShipment['shipment'];
            $delivery = $arResultShipment['deliveryServices'];

            self::setDeliveryAddress('');
            self::setSiteNameByOrder($arOrder);

            $arProp = self::prepareSaleProperty($arOrder, $bExportFromCrm, $bCrmModuleIncluded, $paySystems, $delivery, $locationStreetPropertyValue, $order);
            $agent = self::prepareSalePropertyRekv($order, $agentParams, $arProp, $locationStreetPropertyValue);

            $arOrderTax = CSaleExport::getOrderTax($order);
            $xmlResult['OrderTax'] = self::getXMLOrderTax($arOrderTax);
            self::setOrderSumTaxMoney(self::getOrderSumTaxMoney($arOrderTax));

            $xmlResult['Contragents'] = self::getXmlContragents($arOrder, $arProp, $agent, $bExportFromCrm ? array("EXPORT_FROM_CRM" => "Y") : array());
            $xmlResult['OrderDiscount'] = self::getXmlOrderDiscount($arOrder);
            $xmlResult['SaleStoreList'] = $arStore;
            $xmlResult['ShipmentsStoreList'] = self::getShipmentsStoreList($order);
            // self::getXmlSaleStoreBasket($arOrder,$arStore);
            $basketItems = self::getXmlBasketItems('Order', $arOrder, array('ORDER_ID' => $arOrder['ID']), array(), $arShipment);

            $numberItems = array();
            foreach ($basketItems['result'] as $basketItem) {
                $number = self::getNumberBasketPosition($basketItem["ID"]);

                if (in_array($number, $numberItems)) {
                    $r = new \Bitrix\Sale\Result();
                    $r->addWarning(new \Bitrix\Main\Error(GetMessage("SALE_EXPORT_REASON_MARKED_BASKET_PROPERTY") . '1C_Exchange:Order.export.basket.properties', 'SALE_EXPORT_REASON_MARKED_BASKET_PROPERTY'));
                    $entityMarker::addMarker($order, $order, $r);
                    $order->setField('MARKED', 'Y');
                    $order->setField('DATE_UPDATE', null);
                    $order->save();
                    break;
                } else {
                    $numberItems[] = $number;
                }
            }

            $xmlResult['BasketItems'] = $basketItems['outputXML'];
            $xmlResult['SaleProperties'] = self::getXmlSaleProperties($arOrder, $arShipment, $arPayment, $agent, $agentParams, $bExportFromCrm);
            $xmlResult['RekvProperties'] = self::getXmlRekvProperties($agent, $agentParams);


            if (self::getVersionSchema() >= self::CONTAINER_VERSION) {
                ob_start();
                echo '<' . CSaleExport::getTagName("SALE_EXPORT_CONTAINER") . '>';
            }

            self::OutputXmlDocument('Order', $xmlResult, $arOrder);

            //custom для теста импорта
            //if (self::getVersionSchema() >= self::PARTIAL_VERSION || true)

            if(self::getVersionSchema() >= self::PARTIAL_VERSION)
            {
                self::OutputXmlDocumentsByType('Payment', $xmlResult, $arOrder, $arPayment, $order, $agentParams, $arProp, $locationStreetPropertyValue);
                self::OutputXmlDocumentsByType('Shipment', $xmlResult, $arOrder, $arShipment, $order, $agentParams, $arProp, $locationStreetPropertyValue);
                self::OutputXmlDocumentRemove('Shipment', $arOrder);
            }

            if (self::getVersionSchema() >= self::CONTAINER_VERSION) {
                echo '</' . CSaleExport::getTagName("SALE_EXPORT_CONTAINER") . '>';
                $contentToLog = ob_get_contents();
                ob_end_clean();
                echo $contentToLog;
            }

            if (self::$crmMode) {
                $c = ob_get_clean();
                $c = CharsetConverter::ConvertCharset($c, $arCharSets[$arOrder["LID"]], "utf-8");
                echo $c;
                $_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix][] = $arOrder["ID"];
            } else {
                static::saveExportParams($arOrder);
            }

            ksort(static::$documentsToLog);

            foreach (static::$documentsToLog as $entityTypeId => $documentsToLog) {
                foreach ($documentsToLog as $documentToLog) {
                    $fieldToLog = $documentToLog;
                    $fieldToLog['ENTITY_TYPE_ID'] = $entityTypeId;
                    if (self::getVersionSchema() >= self::CONTAINER_VERSION) {
                        if ($entityTypeId == \Bitrix\Sale\Exchange\EntityType::ORDER)
                            $fieldToLog['MESSAGE'] = $contentToLog;
                    }
                    static::log($fieldToLog);
                }
            }

            if (self::checkTimeIsOver($time_limit, $end_time)) {
                break;
            }
        }
        ?>

        </<?= CSaleExport::getTagName("SALE_EXPORT_COM_INFORMATION") ?>><?

        return self::$arResultStat;
    }

    static function OutputXmlDocument($typeDocument, $xmlResult, $document = array())
    {
        global $DB;
        ?>
        <? ob_start(); ?>
        <<?= CSaleExport::getTagName("SALE_EXPORT_DOCUMENT") ?>><?
        switch ($typeDocument) {
            case 'Order':
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>><?= $document["ID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER") ?>><?= self::getAccountNumberShopPrefix(); ?><?= $document["ACCOUNT_NUMBER"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>><?= $DB->FormatDate($document["DATE_INSERT_FORMAT"], CSite::GetDateFormat("FULL"), "YYYY-MM-DD") ?></<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>><?= CSaleExport::getTagName("SALE_EXPORT_ITEM_ORDER") ?></<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_SELLER") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY") ?>><?= htmlspecialcharsbx(((self::$currency <> '') ? mb_substr(self::$currency, 0, 3) : mb_substr($document["CURRENCY"], 0, 3))) ?></<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY_RATE") ?>>1</<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY_RATE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>><?= $document["PRICE"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>>

                <? if ($document["SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID"] <> '') {
                ?>
                <Ответственный><?= htmlspecialcharsbx($document["SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID"]) ?></Ответственный>
                <?
            } ?>

                <?
                if (self::getVersionSchema() > self::DEFAULT_VERSION) {
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VERSION") ?>><?= (intval($document["VERSION"]) > 0 ? $document["VERSION"] : 0) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VERSION") ?>><?
                    if ($document["ID_1C"] <> '') {
                        ?>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_ID_1C") ?>><?= htmlspecialcharsbx($document["ID_1C"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID_1C") ?>><?
                    }
                }
                if (self::$crmMode) {
                    ?>
                    <DateUpdate><?= $DB->FormatDate($document["DATE_UPDATE"], CSite::GetDateFormat("FULL"), "YYYY-MM-DD HH:MI:SS"); ?></DateUpdate><?
                }
                echo $xmlResult['Contragents'];
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>><?= $DB->FormatDate($document["DATE_INSERT_FORMAT"], CSite::GetDateFormat("FULL"), "HH:MI:SS") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_COMMENTS") ?>><?= htmlspecialcharsbx(self::toText($document["COMMENTS"])) ?></<?= CSaleExport::getTagName("SALE_EXPORT_COMMENTS") ?>>
                <? echo $xmlResult['OrderTax'];
                echo $xmlResult['OrderDiscount'];
                echo self::getXmlSaleStore(array_unique($xmlResult['ShipmentsStoreList'], SORT_NUMERIC), $xmlResult['SaleStoreList']);
                //$storeBasket = self::getXmlSaleStoreBasket($document,$arStore);
                echo $xmlResult['BasketItems'];
                echo $xmlResult['SaleProperties'];
                break;

            case 'Payment':
            case 'Shipment':
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>><?= ($document["ID_1C"] <> '' ? $document["ID_1C"] : $document["ID"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER") ?>><?= $document["ID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER") ?>>

                <? if ($document["SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID"] <> '') {
                ?>
                <Ответственный><?= htmlspecialcharsbx($document["SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID"]) ?></Ответственный>
                <?
            } ?>
                <? switch ($typeDocument) {
                case 'Payment':
                    ?>

                    <<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>><?= $DB->FormatDate($document["DATE_BILL"], CSite::GetDateFormat("FULL"), "YYYY-MM-DD") ?></<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>><?= CSaleExport::getTagName("SALE_EXPORT_ITEM_PAYMENT_" . \Bitrix\Sale\PaySystem\Manager::getPsType($document['PAY_SYSTEM_ID'])) ?></<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>>
                    <? break;
                case 'Shipment':
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>><?= $DB->FormatDate($document["DATE_INSERT"], CSite::GetDateFormat("FULL"), "YYYY-MM-DD") ?></<?= CSaleExport::getTagName("SALE_EXPORT_DATE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>><?= CSaleExport::getTagName("SALE_EXPORT_ITEM_SHIPMENT") ?></<?= CSaleExport::getTagName("SALE_EXPORT_HOZ_OPERATION") ?>>
                    <? break;
            } ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_SELLER") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY") ?>><?= htmlspecialcharsbx(((self::$currency <> '') ? mb_substr(self::$currency, 0, 3) : mb_substr($document["CURRENCY"], 0, 3))) ?></<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY_RATE") ?>>1</<?= CSaleExport::getTagName("SALE_EXPORT_CURRENCY_RATE") ?>>
                <? switch ($typeDocument) {
                case 'Payment':
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>><?= $document['SUM'] ?></<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>>
                    <? break;
                case 'Shipment':
                    $price = 0;
                    if (count($document['BasketResult']) > 0) {
                        foreach ($document['BasketResult'] as $basketItem) {
                            $price = $price + $basketItem['PRICE'] * $basketItem['SALE_INTERNALS_BASKET_SHIPMENT_ITEM_QUANTITY'];
                        }
                    }
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>><?= $price + intval($document['PRICE_DELIVERY']) ?></<?= CSaleExport::getTagName("SALE_EXPORT_AMOUNT") ?>>
                    <? break;
            } ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VERSION") ?>><?= (intval($document["VERSION"]) > 0 ? $document["VERSION"] : 0) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VERSION") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER_BASE") ?>><?= $document['ORDER_ID'] ?></<?= CSaleExport::getTagName("SALE_EXPORT_NUMBER_BASE") ?>>
                <? echo $xmlResult['Contragents']; ?>
                <? switch ($typeDocument) {
                case 'Payment':
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>><?= $DB->FormatDate($document["DATE_BILL"], CSite::GetDateFormat("FULL"), "HH:MI:SS") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>>
                    <? break;
                case 'Shipment':
                    ?>
                    <?= $xmlResult['OrderTax']; ?>
                    <?
                    if (isset($xmlResult['ShipmentsStoreList'][$document["ID"]])) {
                        $storId = $xmlResult['ShipmentsStoreList'][$document["ID"]];
                        echo self::getXmlSaleStore(array($document["ID"] => $storId), $xmlResult['SaleStoreList']);
                    } ?>

                    <<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>><?= $DB->FormatDate($document["DATE_INSERT"], CSite::GetDateFormat("FULL"), "HH:MI:SS") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TIME") ?>>
                    <? break;
            } ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_COMMENTS") ?>><?= htmlspecialcharsbx($document["COMMENTS"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_COMMENTS") ?>>

                <? switch ($typeDocument) {
                case 'Payment':

                    $checkData = false;
                    $cashBoxOneCId = self::getCashBoxOneCId();
                    if (isset($cashBoxOneCId) && $cashBoxOneCId > 0) {
                        $checks = \Bitrix\Sale\Cashbox\CheckManager::getPrintableChecks(array($cashBoxOneCId), array($document['ORDER_ID']));
                        foreach ($checks as $checkId => $check) {
                            if ($check['PAYMENT_ID'] == $document["ID"]) {
                                $checkData = $check;
                                break;
                            }
                        }
                    }
                    ?>
                    <?
                    if ($checkData) {
                        ?>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_CASHBOX_CHECKS") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_CASHBOX_CHECK") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>><?= ($checkData['ID']) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_PROP_VALUES") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_PROP_VALUE") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>PRINT_CHECK</<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>
                        <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>true</<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                        </<?= CSaleExport::getTagName("SALE_EXPORT_PROP_VALUE") ?>>
                        </<?= CSaleExport::getTagName("SALE_EXPORT_PROP_VALUES") ?>>
                        </<?= CSaleExport::getTagName("SALE_EXPORT_CASHBOX_CHECK") ?>>
                        </<?= CSaleExport::getTagName("SALE_EXPORT_CASHBOX_CHECKS") ?>>
                        <?
                    }
                    ?>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DATE_PAID") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DATE_PAID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_CANCELED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["CANCELED"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PAY_SYSTEM_ID") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["PAY_SYSTEM_ID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PAY_SYSTEM") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["PAY_SYSTEM_NAME"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PAY_PAID") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["PAID"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PAY_RETURN") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["IS_RETURN"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PAY_RETURN_REASON") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["PAY_RETURN_COMMENT"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <? self::OutputXmlSiteName($document); ?>
                    <? if (isset($xmlResult['RekvProperties']) && $xmlResult['RekvProperties'] <> '') echo $xmlResult['RekvProperties']; ?>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES") ?>>
                    <? break;

                case 'Shipment':
                    ?>

                    <?
                    echo $xmlResult['BasketItems'];
                    ?>

                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_PRICE_DELIVERY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["PRICE_DELIVERY"] <> '' ? $document["PRICE_DELIVERY"] : "0.0000") ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DATE_ALLOW_DELIVERY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DATE_ALLOW_DELIVERY"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY_LOCATION") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DELIVERY_LOCATION"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY_STATUS") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["STATUS_ID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY_DEDUCTED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["DEDUCTED"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DATE_DEDUCTED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DATE_DEDUCTED"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_REASON_UNDO_DEDUCTED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["REASON_UNDO_DEDUCTED"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_RESERVED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["RESERVED"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY_ID") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DELIVERY_ID"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DELIVERY_NAME"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_CANCELED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= ($document["CANCELED"] == 'Y' ? 'true' : 'false') ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_DELIVERY_DATE_CANCEL") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["DATE_CANCELED"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= GetMessage("SALE_EXPORT_CANCEL_REASON") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["REASON_CANCELED"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_REASON_MARKED") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["REASON_MARKED"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= CSaleExport::getTagName("SALE_EXPORT_TRACKING_NUMBER") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= $document["TRACKING_NUMBER"] ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE") ?>>

                    <? self::OutputXmlSiteName($document); ?>
                    <? self::OutputXmlDeliveryAddress(); ?>

                    <? if (isset($xmlResult['RekvProperties']) && $xmlResult['RekvProperties'] <> '') echo $xmlResult['RekvProperties']; ?>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES") ?>>
                    <?
                    break;
            }
        }
        ?>
        </<?= CSaleExport::getTagName("SALE_EXPORT_DOCUMENT") ?>>
        <? $c = ob_get_contents();
        ob_end_clean();
        echo $c;

        $typeEntityId = static::resolveEntityTypeId($typeDocument, $document);

        if (intval($typeEntityId) > 0) {
            $filedsTolog = array(
                'ENTITY_ID' => $document["ID"],
                'XML_ID' => $document["ID_1C"]
            );

            if (self::getVersionSchema() < self::CONTAINER_VERSION)
                $filedsTolog['MESSAGE'] = $c;

            switch ($typeDocument) {
                case 'Order':
                    $filedsTolog['ENTITY_DATE_UPDATE'] = new \Bitrix\Main\Type\DateTime(\CAllDatabase::FormatDate($document['DATE_UPDATE']));
                    if (self::getVersionSchema() >= self::CONTAINER_VERSION)
                        $filedsTolog['PARENT_ID'] = $document["ID"];
                    break;
                case 'Payment':
                case 'Shipment':
                    $filedsTolog['OWNER_ENTITY_ID'] = $document["ORDER_ID"];

                    if (self::getVersionSchema() >= self::CONTAINER_VERSION)
                        $filedsTolog['PARENT_ID'] = $document["ORDER_ID"];
                    break;
            }

            static::$documentsToLog[$typeEntityId][] = $filedsTolog;
        }
    }

    static function OutputXmlDocumentsByType($typeDocument, $xmlResult, $arOrder, $documents, \Bitrix\Sale\Order $order = null, $agentParams, $arProp, $locationStreetPropertyValue)
    {
        if (is_array($documents) && count($documents) > 0) {
            foreach ($documents as $document) {
                $document['LID'] = $arOrder['LID'];
                $document['VERSION'] = $arOrder['VERSION'];

                switch ($typeDocument) {
                    case 'Payment':

                        if ($document['DATE_BILL'] == '')
                            $document['DATE_BILL'] = $arOrder['DATE_INSERT_FORMAT'];

                        if (($paymentCollection = $order->getPaymentCollection())) {
                            foreach ($paymentCollection as $payment) {
                                if ($payment->getId() == $document['ID']) {
                                    $agent = self::prepareSalePropertyRekv($payment, $agentParams, $arProp, $locationStreetPropertyValue);
                                    $xmlResult['RekvProperties'] = self::getXmlRekvProperties($agent, $agentParams);
                                    break;
                                }
                            }
                        }

                        self::OutputXmlDocument('Payment', $xmlResult, $document);
                        break;
                    case 'Shipment':

                        if (($shipmentCollection = $order->getShipmentCollection())) {
                            foreach ($shipmentCollection as $shipment) {
                                if ($shipment->getId() == $document['ID']) {
                                    $agent = self::prepareSalePropertyRekv($shipment, $agentParams, $arProp, $locationStreetPropertyValue);
                                    $xmlResult['RekvProperties'] = self::getXmlRekvProperties($agent, $agentParams);
                                    break;
                                }
                            }
                        }

                        $basketItems = self::getXmlBasketItems('Shipment', $document, array(
                            'ORDER_ID' => $document['ORDER_ID'],
                            'SHIPMENT_ITEM.ORDER_DELIVERY_ID' => $document['ID'],
                        ),
                            array(
                                'SHIPMENT_ITEM.QUANTITY',
                                'SHIPMENT_ITEM.ID'
                            ),
                            array(
                                array('PRICE_DELIVERY' => $document['PRICE_DELIVERY'])
                            ),
                            $order
                        );
                        $xmlResult['BasketItems'] = $basketItems['outputXML'];
                        $document['BasketResult'] = $basketItems['result'];
                        $document['SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID'] = $arOrder['SALE_INTERNALS_ORDER_RESPONSIBLE_XML_ID'];

                        self::OutputXmlDocument('Shipment', $xmlResult, $document);
                        break;
                }
            }
        }
    }

    static function getXmlContragents($arOrder = array(), $arProp = array(), $agent = array(), $arOptions = array())
    {
        ob_start();
        self::ExportContragents($arOrder, $arProp, $agent, $arOptions);
        $ec_bufer = ob_get_clean();
        return $ec_bufer;
    }
    /*
     * Добавление номера телефона в поле контрагенты
     * */
    static function ExportContragents($arOrder = array(), $arProp = array(), $agent = array(), $arOptions = array())
    {
        $agent['PHONE'] = '';

        $phoneAuthTable = \Bitrix\Main\UserPhoneAuthTable::getList($parameters = array(
            'filter' => array('USER_ID' => $arProp['USER']['ID'])
        ));

        if ($item = $phoneAuthTable->fetch()) {
            $agent['PHONE'] = $item['PHONE_NUMBER'];
        }

        $bExportFromCrm = (isset($arOptions["EXPORT_FROM_CRM"]) && $arOptions["EXPORT_FROM_CRM"] === "Y");
        ?>
        <<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENTS") ?>>
        <<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT") ?>>
        <?
        if ($bExportFromCrm):
            $xmlId = htmlspecialcharsbx(mb_substr($arProp["CRM"]["CLIENT_ID"] . "#" . $arProp["CRM"]["CLIENT"]["LOGIN"] . "#" . $arProp["CRM"]["CLIENT"]["LAST_NAME"] . " " . $arProp["CRM"]["CLIENT"]["NAME"] . " " . $arProp["CRM"]["CLIENT"]["SECOND_NAME"], 0, 40));
        else:
            $xmlId = static::getUserXmlId($arOrder, $arProp);
        endif; ?>
        <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>><?= $xmlId ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>

        <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= htmlspecialcharsbx($agent["AGENT_NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
        <?
        self::setDeliveryAddress($agent["ADDRESS_FULL"]);

        //region address
        $address = "";
        if ($agent["ADDRESS_FULL"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") . ">" . htmlspecialcharsbx($agent["ADDRESS_FULL"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") . ">";
        } else {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") . "></" . CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") . ">";
        }
        if ($agent["INDEX"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_POST_CODE") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["INDEX"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["COUNTRY"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
									<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_COUNTRY") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
									<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["COUNTRY"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
								</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["REGION"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_REGION") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["REGION"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["STATE"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_STATE") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["STATE"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["TOWN"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["TOWN"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["CITY"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_CITY") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["CITY"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["STREET"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_STREET") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["STREET"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["HOUSE"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_HOUSE") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["HOUSE"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["BUILDING"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_BUILDING") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["BUILDING"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        if ($agent["FLAT"] <> '') {
            $address .= "<" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">" . CSaleExport::getTagName("SALE_EXPORT_FLAT") . "</" . CSaleExport::getTagName("SALE_EXPORT_TYPE") . ">
								<" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">" . htmlspecialcharsbx($agent["FLAT"]) . "</" . CSaleExport::getTagName("SALE_EXPORT_VALUE") . ">
							</" . CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") . ">";
        }
        //endregion

        if ($agent["IS_FIZ"] == "Y") {
            self::$arResultStat["CONTACTS"]++;
            ?>
            <<?= CSaleExport::getTagName("SALE_EXPORT_FULL_NAME") ?>><?= htmlspecialcharsbx($agent["FULL_NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_FULL_NAME") ?>>
            <?
            if ($agent["SURNAME"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_SURNAME") ?>><?= htmlspecialcharsbx($agent["SURNAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_SURNAME") ?>><?
            }
            if ($agent["NAME"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_NAME") ?>><?= htmlspecialcharsbx($agent["NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_NAME") ?>><?
            }
            if ($agent["SECOND_NAME"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_MIDDLE_NAME") ?>><?= htmlspecialcharsbx($agent["SECOND_NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_MIDDLE_NAME") ?>><?
            }
            if ($agent["BIRTHDAY"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_BIRTHDAY") ?>><?= htmlspecialcharsbx($agent["BIRTHDAY"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_BIRTHDAY") ?>><?
            }
            if ($agent["MALE"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_SEX") ?>><?= htmlspecialcharsbx($agent["MALE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_SEX") ?>><?
            }
            if ($agent["INN"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_INN") ?>><?= htmlspecialcharsbx($agent["INN"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_INN") ?>><?
            }
            if ($agent["KPP"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_KPP") ?>><?= htmlspecialcharsbx($agent["KPP"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_KPP") ?>><?
            }
            if ($address <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_REGISTRATION_ADDRESS") ?>>
                <?= $address ?>
                </<?= CSaleExport::getTagName("SALE_EXPORT_REGISTRATION_ADDRESS") ?>>
                <?
            }
        } else {
            self::$arResultStat["COMPANIES"]++;
            ?>
            <<?= CSaleExport::getTagName("SALE_EXPORT_OFICIAL_NAME") ?>><?= htmlspecialcharsbx($agent["FULL_NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OFICIAL_NAME") ?>>
            <?
            if ($address <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_UR_ADDRESS") ?>>
                <?= $address ?>
                </<?= CSaleExport::getTagName("SALE_EXPORT_UR_ADDRESS") ?>><?
            }
            if ($agent["INN"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_INN") ?>><?= htmlspecialcharsbx($agent["INN"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_INN") ?>><?
            }
            if ($agent["KPP"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_KPP") ?>><?= htmlspecialcharsbx($agent["KPP"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_KPP") ?>><?
            }
            if ($agent["EGRPO"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_EGRPO") ?>><?= htmlspecialcharsbx($agent["EGRPO"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_EGRPO") ?>><?
            }
            if ($agent["OKVED"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKVED") ?>><?= htmlspecialcharsbx($agent["OKVED"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKVED") ?>><?
            }
            if ($agent["OKDP"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKDP") ?>><?= htmlspecialcharsbx($agent["OKDP"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKDP") ?>><?
            }
            if ($agent["OKOPF"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKOPF") ?>><?= htmlspecialcharsbx($agent["OKOPF"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKOPF") ?>><?
            }
            if ($agent["OKFC"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKFC") ?>><?= htmlspecialcharsbx($agent["OKFC"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKFC") ?>><?
            }
            if ($agent["OKPO"] <> '') {
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKPO") ?>><?= htmlspecialcharsbx($agent["OKPO"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKPO") ?>><?
                ?><<?= CSaleExport::getTagName("SALE_EXPORT_OKPO_CODE") ?>><?= htmlspecialcharsbx($agent["OKPO"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_OKPO_CODE") ?>><?
            }
            if ($agent["ACCOUNT_NUMBER"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNTS") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNT") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ACCOUNT_NUMBER") ?>><?= htmlspecialcharsbx($agent["ACCOUNT_NUMBER"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ACCOUNT_NUMBER") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_BANK") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= htmlspecialcharsbx($agent["B_NAME"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") ?>><?= htmlspecialcharsbx($agent["B_ADDRESS_FULL"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") ?>>
                <?
                if ($agent["B_INDEX"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_POST_CODE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_INDEX"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_COUNTRY"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_COUNTRY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_COUNTRY"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_REGION"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_REGION") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_REGION"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_STATE"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_STATE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_STATE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_TOWN"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_TOWN"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_CITY"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_CITY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_CITY"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_STREET"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_STREET") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_STREET"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_HOUSE"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_HOUSE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_HOUSE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_BUILDING"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_BUILDING") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_BUILDING"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                if ($agent["B_FLAT"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_FLAT") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                    <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["B_FLAT"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                    </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>><?
                }
                ?>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS") ?>>
                <?
                if ($agent["B_BIK"] <> '') {
                    ?><<?= CSaleExport::getTagName("SALE_EXPORT_BIC") ?>><?= htmlspecialcharsbx($agent["B_BIK"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_BIC") ?>><?
                }
                ?>
                </<?= CSaleExport::getTagName("SALE_EXPORT_BANK") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNT") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNTS") ?>>
                <?
            }
        }

        if ($agent["F_ADDRESS_FULL"] <> '') {
            self::setDeliveryAddress($agent["F_ADDRESS_FULL"]);
            ?>
            <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") ?>><?= htmlspecialcharsbx($agent["F_ADDRESS_FULL"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_PRESENTATION") ?>>
            <?
            if ($agent["F_INDEX"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_POST_CODE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_INDEX"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_COUNTRY"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_COUNTRY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_COUNTRY"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_REGION"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_REGION") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_REGION"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_STATE"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_STATE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_STATE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_TOWN"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_TOWN"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_CITY"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_CITY") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_CITY"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_STREET"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_STREET") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_STREET"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_HOUSE"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_HOUSE") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_HOUSE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_BUILDING"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_BUILDING") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_BUILDING"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            if ($agent["F_FLAT"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_FLAT") ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["F_FLAT"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD") ?>>
                <?
            }
            ?>
            </<?= CSaleExport::getTagName("SALE_EXPORT_ADDRESS") ?>>
            <?
        }

        if ($agent["PHONE"] <> '' || $agent["EMAIL"] <> '') {
            ?>
            <<?= CSaleExport::getTagName("SALE_EXPORT_CONTACTS") ?>>
            <?
            if ($agent["PHONE"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CONTACT") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= (self::getVersionSchema() > self::DEFAULT_VERSION ? CSaleExport::getTagName("SALE_EXPORT_WORK_PHONE_NEW") : CSaleExport::getTagName("SALE_EXPORT_WORK_PHONE")) ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["PHONE"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_CONTACT") ?>>
                <?
            }
            if ($agent["EMAIL"] <> '') {
                ?>
                <<?= CSaleExport::getTagName("SALE_EXPORT_CONTACT") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>><?= (self::getVersionSchema() > self::DEFAULT_VERSION ? CSaleExport::getTagName("SALE_EXPORT_MAIL_NEW") : CSaleExport::getTagName("SALE_EXPORT_MAIL")) ?></<?= CSaleExport::getTagName("SALE_EXPORT_TYPE") ?>>
                <<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>><?= htmlspecialcharsbx($agent["EMAIL"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_VALUE") ?>>
                </<?= CSaleExport::getTagName("SALE_EXPORT_CONTACT") ?>>
                <?
            }
            ?>
            </<?= CSaleExport::getTagName("SALE_EXPORT_CONTACTS") ?>>
            <?
        }
        if ($agent["CONTACT_PERSON"] <> '') {
            ?>
            <<?= CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVES") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVE") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_RELATION") ?>><?= CSaleExport::getTagName("SALE_EXPORT_CONTACT_PERSON") ?></<?= CSaleExport::getTagName("SALE_EXPORT_RELATION") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>><?= md5($agent["CONTACT_PERSON"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ID") ?>>
            <<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>><?= htmlspecialcharsbx($agent["CONTACT_PERSON"]) ?></<?= CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME") ?>>
            </<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT") ?>>
            </<?= CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVE") ?>>
            </<?= CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVES") ?>>
            <?
        } ?>
        <<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>><?= CSaleExport::getTagName("SALE_EXPORT_BUYER") ?></<?= CSaleExport::getTagName("SALE_EXPORT_ROLE") ?>>
        </<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT") ?>>
        </<?= CSaleExport::getTagName("SALE_EXPORT_CONTRAGENTS") ?>>
        <?

        $filedsTolog = array(
            'ENTITY_ID' => $arOrder["USER_ID"],
            'PARENT_ID' => $arOrder['ID'],
            'ENTITY_DATE_UPDATE' => static::getUserTimeStapmX($arOrder),
            'XML_ID' => $xmlId
        );

        static::$documentsToLog[\Bitrix\Sale\Exchange\EntityType::USER_PROFILE][] = $filedsTolog;
    }
}