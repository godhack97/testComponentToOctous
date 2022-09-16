<?php
namespace Sodamoda\Core\Bx;

class Core
{
    /*
        Этот класс для добавления/редактирования/обновления сущностей.
        По конструкту требует url вебхука
    */
    const   url = "https://crmsoda.ru/rest/1/93nhq6l7r6y0i20k/";
    /* методы */
    const   userGet           = self::url . 'user.get'      ;
    const   userCurrent       = self::url . 'user.current'  ;
    const   imNotify          = self::url . 'im.notify'     ;

    const   invoiceGet        = self::url . 'crm.invoice.get';
    const   invoiceList       = self::url . 'crm.invoice.list';
    const   invoiceUpdate     = self::url . 'crm.invoice.update';

    const   imBotRegister       = self::url . 'imbot.register'     ;
    const   imBotMessageAdd     = self::url . 'imbot.message.add'  ;
    const   imBotMessageUpdate  = self::url . 'imbot.message.update';
    const   imBotChatAdd        = self::url . 'imbot.chat.add';
    const   imBotDialogAdd      = self::url . 'imbot.dialog.get';


    const   crmActivityGet    = self::url . 'crm.activity.get';
    const   crmActivityAdd    = self::url . 'crm.activity.add'     ;
    const   crmActivityFields = self::url . 'crm.activity.fields';
    const   crmActivityTypes  = self::url . 'crm.enum.activitytype';
    const   crmActitityTypesList = self::url . 'crm.activity.type.list';
    const   crmTimelineCommentAdd    = self::url . 'crm.timeline.comment.add'     ;
    const   crmTimelineCommentGet = self::url . 'crm.timeline.comment.get';
    const   crmTimelineCommentList = self::url . 'crm.timeline.comment.list';
    const   crmTimelineCommentFields = self::url . 'crm.timeline.comment.fields';


    const   crmStatusList     = self::url . 'crm.status.list';
    const   leadAdd           = self::url . 'crm.lead.add.json'       ;
    const   crmLeadDetailsConfigurationGet           = self::url . 'crm.lead.details.configuration.get'       ;

    const   crmLeadFields           = self::url . 'crm.lead.fields'   ;
    const   crmDealFields           = self::url . 'crm.deal.fields'   ;

    const   leadUpdata        = self::url . 'crm.lead.update.json'    ;
    const   listUserfiledList = self::url . 'crm.lead.userfield.list' ;
    const   leadList          = self::url . 'crm.lead.list'           ;
    const   leadGet           = self::url . 'crm.lead.get'            ;
    const   leadProductrowsGet           = self::url . 'crm.lead.productrows.get'            ;


    const   contactAdd        = self::url . 'crm.contact.add'         ;
    const   contactGet        = self::url . 'crm.contact.get'         ;
    const   contactList       = self::url . 'crm.contact.list'        ;
    const   contactUpdate     = self::url . 'crm.contact.update'      ;

    const   logBlogpostAdd    = self::url . "log.blogpost.add"        ;

    const   dealAdd           = self::url . 'crm.deal.add'            ;
    const   dealUpdata        = self::url . 'crm.deal.update'         ;
    const   dealList          = self::url . 'crm.deal.list'           ;
    const   dealGet           = self::url . 'crm.deal.get'            ;
    const   diskFileGet       = self::url . 'disk.file.get'           ;
    const   dealContactItemsGet     = self::url . 'crm.deal.contact.items.get'      ;
    const   dealProductrowsSet     = self::url . 'crm.deal.productrows.set'      ;
    const   dealProductrowsGet = self::url . 'crm.deal.productrows.get';

    const   sonetGroupGet     = self::url .'sonet_group.get'          ;

    const   departmentGet     = self::url . 'department.get'          ;

    const   tasksTaskList     = self::url . 'tasks.task.list'         ;
    const   tasksTaskAdd      = self::url . 'task.item.add'           ;
    const   tasksTaskGet      = self::url . 'tasks.task.get'          ;

    const   companyList       = self::url . 'crm.company.list'        ;
    const   companyGet        = self::url . 'crm.company.get'         ;

    const   crmRequisiteList        = self::url . "crm.requisite.list"      ;
    const   crmRequisiteAdd         = self::url . "crm.requisite.add"       ;
    const   crmRequisiteLinkList    = self::url . "crm.requisite.link.list" ;
    const   crmEnumOwnerType        = self::url.  "crm.enum.ownertype"      ;
    const   crmRequisiteLinkGet     = self::url. "crm.requisite.link.get"   ;
    const   crmRequisiteUpdate      = self::url. "crm.requisite.update"     ;

    const   crmAddressAdd           = self::url. "crm.address.add"          ;
    const   crmAddressList          = self::url. "crm.address.list"         ;
    const   crmAddressUpdate        = self::url. "crm.address.update"       ;

    const   crmRequisiteBankdetailList          = self::url. "crm.requisite.bankdetail.list";

    const   duplicateFindbycomm          = self::url. "crm.duplicate.findbycomm";

    const   diskFileGetExternalLink     = self::url . 'disk.file.getExternalLink';

    const   saleShipmentGet       = self::url . 'sale.shipment.get';

    const   catalogProductGet     = self::url . 'catalog.product.get';


    public  $result           = null;

    public static function curlStart($queryUrl, $data){

        $data = http_build_query($data);
        $curl = curl_init(); // создаем ресурс

        curl_setopt_array($curl,
            array(  CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POST           => 1,
                CURLOPT_HEADER         => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL            => $queryUrl,
                CURLOPT_POSTFIELDS     => $data,
            )
        );
        $result = curl_exec($curl)                   ; // Выполняем запрос и записываем ответ
        curl_close($curl)                            ; // Закрываем соединение
        $result = json_decode($result, 1)      ; // Декодируем ответ
        return $result;
    }

    public static function tasksTaskGet(){
        return self::curlStart(self::tasksTaskGet, []);
    }

    public static function duplicateFindbycomm($data){
        return self::curlStart(self::duplicateFindbycomm, $data);
    }

    public static function diskFileGetExternalLink($data)
    {
        return self::curlStart(self::diskFileGetExternalLink, $data);
    }

    public static function crmLeadDetailsConfigurationGet($data){
        return self::curlStart(self::crmLeadDetailsConfigurationGet, $data);
    }

    public static function crmLeadFields(){
        return self::curlStart(self::crmLeadFields, []);
    }

    public static function crmDealFields(){
        return self::curlStart(self::crmDealFields, []);
    }

    public static function crmStatusList(){
        return self::curlStart(self::crmStatusList, []);
    }

    public static function crmInvoiceUpdate($id, $data){
        return self::curlStart(self::invoiceUpdate, [
            'id' => $id,
            'fields' => $data
        ]);
    }

    public static function crmInvoiceList($data){
        return self::curlStart(self::invoiceList, $data);
    }

    public static function crmInvoiceGet($id){
        return self::curlStart(self::invoiceGet, ['id' => $id]);
    }

    public static function getRequiredField__lead(){

        $result = [];
        $data   = self::crmLeadFields();
        if (array_key_exists('result',$data)){

            $data = $data['result'];
            foreach ($data as $key => $value){

                dd($data);
                if($value['isRequired'] == TRUE){
                    $result[] = $key;
                }
            }

        }
        dd($result);
        return $result;
    }



    public static function imBotMessageAdd($data){
        return self::curlStart(self::imBotMessageAdd, $data);
    }

    public static function imBotMessageUpdate($data){
        return self::curlStart(self::imBotMessageUpdate, $data);
    }

    public static function imBotChatAdd($data) {
        return self::curlStart(self::imBotChatAdd, $data);
    }

    public static function imBotDialogAdd($id) {
        $data = [
            'DIALOG_ID' => $id
        ];
        return self::curlStart(self::imBotDialogAdd, $data);
    }

    public static function crmTimelineCommentGet($id) {
        $data = [
            'id' => $id
        ];

        return self::curlStart(self::crmTimelineCommentGet, $data);
    }

    public static function crmTimelineCommentList($id) {
        $data = [
            'filter' => [
                'ENTITY_ID' => $id,
                'ENTITY_TYPE' => 'lead',
            ],
            'select' => ['ID', 'COMMENT', 'CREATED'],
        ];

        return self::curlStart(self::crmTimelineCommentList, $data);
    }

    public static function crmTimelineCommentFields() {
        return self::curlStart(self::crmTimelineCommentFields, []);
    }

    public static function crmTimelineCommentAdd($data){
        return self::curlStart(self::crmTimelineCommentAdd, $data);
    }



    public static function imBotRegister($data=[]){
        return self::curlStart(self::imBotRegister, $data);
    }

    public static function userGet($data=[]){
        return self::curlStart(self::userGet, $data);
    }


    public static function dealProductrowsSet($dealId,$productRows){
        $data = [

            'ID'    =>  $dealId,
			'rows'	=> $productRows
        ];
        return self::curlStart(self::dealProductrowsSet, $data);
    }

    public static function dealProductrowsGet($deatId)
    {
        $data = [
            'ID' => $deatId
        ];

        return self::curlStart(self::dealProductrowsGet, $data);
    }



    public static function dealContactItemsGet($id){

        $data = [

          'id' => $id
        ];
        return self::curlStart(self::dealContactItemsGet, $data);
    }

    public static function departmentGet($data = []){
        return self::curlStart(self::departmentGet, $data);
    }

    public static function crmActivityGet($id) {
        $data = [
            'id' => $id
        ];
        return self::curlStart(self::crmActivityGet, $data);
    }

    public static function tasksTaskAdd($data = []){
        return self::curlStart(self::tasksTaskAdd, $data);
    }

    public static function crmActivityAdd($data){
        $data = [
            'fields' => $data
        ];
        return self::curlStart(self::crmActivityAdd, $data);
    }

    public static function crmActivityFields(){
        return self::curlStart(self::crmActivityFields, []);
    }

    public static function crmActivityTypes(){
        return self::curlStart(self::crmActivityTypes, []);
    }

    public static function crmActitityTypesList(){
        return self::curlStart(self::crmActitityTypesList, []);
    }

    public static function imNotify($userId,$text){
        $data = [
            'to'        => $userId,
            'message'   => $text,
            'type'      => 'SYSTEM',
        ];
        return self::curlStart(self::imNotify, $data);
    }
    public static function sonetGroupGet($data = []){

        return self::curlStart(self::sonetGroupGet, $data);
    }

    public static function leadProductrowsGet($leadID){

        $data = [
          'ID' => $leadID
        ];
        return self::curlStart(self::leadProductrowsGet, $data);
    }



    public static function crmAddressAdd($data){
        /*
         fields:
        {
            "TYPE_ID": 6,
            "ENTITY_TYPE_ID": 8,
            "ENTITY_ID": 26,
            "ADDRESS_1": "Добавил из REST API TESTER",

        }
         **/
        return self::curlStart(self::crmAddressAdd, $data);
    }

    public static function userCurrent($data=[]){
        return self::curlStart(self::userCurrent, $data);
    }

    public static function tasksTaskList($data){
        return self::curlStart(self::tasksTaskList, $data);
    }

    public static function crmAddressUpdate($data){
        /*
        fields:
        {
            "TYPE_ID": 1,
            "ENTITY_TYPE_ID": 3,
            "ENTITY_ID": 1,
            "ADDRESS_1": "Московский проспект, 261",
            "CITY": "Калининград"
        }
         **/
        return self::curlStart(self::crmAddressUpdate, $data);
    }

    public static function crmRequisiteBankdetailList($data){
        /*
         'order'    => [ "DATE_CREATE"  => "ASC" ],
         'filter'   =>[ "ENTITY_ID" =>  $companyId, 'ENTITY_TYPE_ID' => 4],
         'select'   => [ "ID", "NAME"]
         **/
        return self::curlStart(self::crmRequisiteBankdetailList, $data);
    }

    public static function crmAddressList($data){
        /*
         *
         *   order: { "TYPE_ID": "ASC" },
         *  filter: { "ENTITY_ID": 8},
         *  select: [ "TYPE_ID", "ADDRESS_1", "ADDRESS_2" ]
         *
         **/
        return self::curlStart(self::crmAddressList, $data);
    }

    public static function crmEnumOwnerType(){
        return self::curlStart(self::crmEnumOwnerType, []);
    }

    public static function crmRequisiteLinkGet($data){
        return self::curlStart(self::crmRequisiteLinkGet, $data);
    }

    public static function crmRequisiteLinkList($data){
        return self::curlStart(self::crmRequisiteLinkList, $data);
    }

    public static function logBlogpostAdd($data){
        /*
         *
            [
                'POST_TITLE'    => 'Заголовок',
                POST_MESSAGE    => 'text',
                DEST            => ['UA'] // User All
            ]
        */
        return self::curlStart(self::logBlogpostAdd, $data);
    }



    /*
     * Для поиска реквизита
     *
     * оrder: { "DATE_CREATE": "ASC" },
     * filter: [{"ENTITY_TYPE_ID": 4},{"ENTITY_ID" :2}],
     * select: [ "ID", "NAME"]
     *
     */
    public static function crmRequisiteList($data){
        return self::curlStart(self::crmRequisiteList, $data);
    }


    public static function crmRequisiteAdd($data){
        return self::curlStart(self::crmRequisiteAdd, $data);
    }




    /*
     * Обновляет реквизиты
     *    [
     *       ['id': id],
     *       [
     *           "NAME"   =>  "Реквизит (архив)",
     *           "SORT"   =>  200,
     *           "ACTIVE" =>  "N"
     *       ]
     *   ]
     * */
    public static function crmRequisiteUpdate($data){
        return self::curlStart(self::crmRequisiteUpdate, $data);
    }

    public static function companyList($data){
        return self::curlStart(self::companyList, $data);
    }

    public static  function dealUpdata($id, $arr){
        $data = array(
            'id'        =>  $id,
            'fields'    =>  $arr,
            'params'    =>  array("REGISTER_SONET_EVENT" => "Y")
        );
        return self::curlStart(self::dealUpdata, $data);
    }
    public static  function dealList($data){
        return self::curlStart(self::dealList, $data);
    }
    public static  function leadList($data){
        return self::curlStart(self::leadList, $data);
    }

    public static  function companyGet($id){
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::companyGet, $data);
    }

    public static  function leadGet($id){
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::leadGet, $data);
    }

    public static  function diskFileGet($id){
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::diskFileGet, $data);
    }
    public static  function contactGet($id){
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::contactGet, $data);
    }
    public static  function contactAdd($data){
        $data = array(
            "fields" => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );
        return self::curlStart(self::contactAdd, $data);
    }
    public static  function leadAdd($data){
        $data = array(
            "fields" => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );
        return self::curlStart(self::leadAdd, $data);
    }
    public static  function leadUF(){
        $data = array(
            'order'     => array( "SORT"        => "ASC" ),
            'filter'    => array( "MANDATORY"   => "N"   )
        );
        //return  self::curlStart(self::userFieldList, $data)  ;
    }
    public static  function contactUpdate($id, $arr){
        $data = array(
            'id'        =>  $id,
            'fields'    =>  $arr,
            'params'    =>  array("REGISTER_SONET_EVENT" => "Y")
        );
        return self::curlStart(self::contactUpdate, $data);
    }
    public static  function leadUpdata($id, $arr){
        $data = array(
            'id'        =>  $id,
            'fields'    =>  $arr,
            'params'    =>  array("REGISTER_SONET_EVENT" => "Y")
        );
        return self::curlStart(self::leadUpdata, $data);
    }
    public static  function dealAdd($data){
        $data = array(
            'fields' => $data,
            'params' => array("REGISTER_SONET_EVENT" => "Y")
        );
        $result =  self::curlStart(self::dealAdd, $data);
        if(!$result['result'] > 1){
            $result = FALSE;
        }
        return $result;
    }
    public static  function dealGet($id){
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::dealGet, $data);
    }

    public static function catalogProductGet($id) {
        $data = array(
            'id'    => $id,
        );
        return self::curlStart(self::catalogProductGet, $data);
    }

    public static  function contactList($data){
        return self::curlStart(self::contactList, $data);
    }
}
