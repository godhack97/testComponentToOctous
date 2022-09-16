<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use DhlRu\Delivery\Service\DHLTracking;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

class TrackingComponent extends CBitrixComponent
{
    protected $errorCollection;

    public function onPrepareComponentParams($params)
    {                
        return $params;
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('dhlru.delivery'))
        {
            ShowError(Loc::getMessage('DHLRU_MODULE_NOT_INSTALLED'));
            return false;
        }

        return true;
    }

    protected function getStatuses()
    {
        if (!empty($this->arResult['BILL_NUMBER']))
            return DHLTracking::getStatuses($this->arResult['BILL_NUMBER']);

        return false;
    }

    protected function getBillNumber()
    {
        if(
            $this->request->getRequestMethod() === 'GET' 
            && 
            $this->request->get('BILL_NUMBER')
            &&
            check_bitrix_sessid()
        )
		{
            if (!preg_match('/^[0-9]\\d*$/', $this->request->get('BILL_NUMBER')))
            {
                $this->arResult['ERROR'] = Loc::getMessage('DHLRU_MODULE_ONLY_NUMERIC');
                return false;
            }               
            
            return $this->request->get('BILL_NUMBER');
        }
        return false;
    }


    public function executeComponent()
    {
        $this->includeComponentLang('class.php');
        if($this->checkModules())
        {
            if ($track = $this->getBillNumber())
                $this->arResult['BILL_NUMBER'] = $track;

            $this->arResult['RESPONCE'] = $this->getStatuses();
            $this->includeComponentTemplate();
        }       
    }

   
};