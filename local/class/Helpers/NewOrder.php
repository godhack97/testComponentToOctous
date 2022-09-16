<?

namespace local\Helpers;

use Bitrix\Main;
use Bitrix\Main\Loader;
use SMSC_Send;
use Bitrix\Sale;


class NewOrder
{
  function OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams)
  {
      logArlix('OnSaleComponentOrderOneStepComplete start $ORDER_ID = '. $ID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
      if($ID){
          $order = Sale\Order::load($ID);

          $request = \Bitrix\Main\Context::getCurrent()->getRequest();
          $USER_SELECTION_ID = (int)$request->getPost("user_selection_id");

          logArlix('OnSaleComponentOrderOneStepComplete $USER_SELECTION_ID = '. $USER_SELECTION_ID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);

          if($USER_SELECTION_ID > 0 && $USER_SELECTION_ID != (int)$order->getField("USER_ID"))
          {
              logArlix('OnSaleComponentOrderOneStepComplete $USER_SELECTION_ID = '. $USER_SELECTION_ID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
              $order->setFieldNoDemand(
                  "USER_ID",
                  $USER_SELECTION_ID
              );
              $order->save();
          }
      }

      logArlix('OnSaleComponentOrderOneStepComplete finish $ID='. $ID, 'log_create_order.txt', __FILE__ .' :'. __LINE__);
  }


  //    function OnSaleOrderSaved(Main\Event $event)
  //    {
  //      /** @var Order $order */
  //      $order = $event->getParameter("ENTITY");
  //      $oldValues = $event->getParameter("VALUES");
  //      $isNew = $event->getParameter("IS_NEW");
  //
  //      logArlix('OnSaleOrderSaved start $isNew='. $isNew, 'log_arlix.txt', __FILE__ .' :'. __LINE__);
  //      
  //      if ($isNew)
  //      {
  //        //            $id = $order->getId();
  //        //            if(Loader::includeModule("smsc.sms")) {
  //        //                $phone = "+79628530160";
  //        //                $test="Осуществлен новый заказ №$id";
  //        //                SMSC_Send::Send_SMS($phone,$test);
  //        //            }
  //      }
  //    }
}