<?php
	namespace Sodamoda\Core\Sms;
	
use Sodamoda\Core\Sms\Api\SmsActionBalance;
use Sodamoda\Core\Sms\Api\SmsActionInbox;
use Sodamoda\Core\Sms\Api\SmsActionPostSms;
use Sodamoda\Core\Sms\Api\SmsActionStatus;
use Sodamoda\Core\Sms\Api\SmsClient;

class QTSMS
{
    /** @var SmsClient */
    private $smsClient;

    /** @var string Путь к файлу с сертификатом по-умолчанию */
    private $pathToCertPem = './Src/cacert.pem';

    public function __construct(string $user, string $password, string $host)
    {
        $this->smsClient = new SmsClient(
            'POST',
            'https',
            $host
        );
        $this->smsClient->setAuth($user, $password);

        // по-умолчанию проверка сертификата узла для CURL отключена
          $this->smsClient->setCurlCertificateCheck(FALSE);
        // указать CURL путь к файлу с сертификатом (если требуется включить проверку)
        // см. https://curl.se/docs/caextract.html
        // $realpath = realpath(__DIR__ . $this->pathToCertPem);
        // $this->smsClient->setCurlCertificatePath($realpath);

        // установите параметры прокси-сервера, если он у вас есть
        // $this->set_proxy('<ip>:<port>');
    }

    /**
     * Установить параметры прокси-сервера
     * @param string $proxyData Адрес прокси-сервера в виде "ip:port"
     */
    public function set_proxy(string $proxyData)
    {
        $this->smsClient->setProxy($proxyData);
    }

    /**
     * Установить параметры прокси-сервера
     * @param string $proxyData Логин/пароль к прокси-серверу в виде "username:password"
     */
    public function set_proxy_user_pwd(string $proxyData)
    {
        $this->smsClient->setProxyUserPwd($proxyData);
    }

    /**
     * Работа с методами старого интерфейса
     */
    public function start_multipost()
    {
        $this->smsClient->startMultipost();
    }

    public function process()
    {
        return $this->smsClient->sendRequest();
    }

    /****************************************
     ***        отправка сообщений        ***
     ****************************************/

    public function post_mes(
        string $mes,
        string $target,
        string $phl_codename,
        string $sender,
        string $post_id=NULL,
        string $period=NULL)
    {
        $action = new SmsActionPostSms();
        $action->setParams([
            'message' => $mes,
            'target' => $target ?? NULL,
            'phl_codename' => $phl_codename ?? NULL,
            'sender' => $sender,
            'post_id' => $post_id,
            'period' => $period,
        ]);
        $this->smsClient->setAction($action);
        if (!$this->smsClient->isMultipost()) {
            return $this->smsClient->sendRequest();
        }
    }

    public function post_message($mes, $target, $sender = NULL, $post_id = NULL, $period = FALSE)
    {
        if (is_array($target)) {
            $target = implode(',', $target);
        }
        return $this->post_mes($mes, $target, FALSE, $sender, $post_id, $period);
    }

    public function post_message_phl($mes, $phl_codename, $sender = NULL, $post_id = NULL, $period = FALSE)
    {
        return $this->post_mes($mes, FALSE, $phl_codename, $sender, $post_id, $period);
    }

    /****************************************
     ***         статус сообщений         ***
     ****************************************/

    public function status_sms(
        string $date_from,
        string $date_to,
        string $smstype,
        string $sms_group_id,
        string $sms_id)
    {
        $action = new SmsActionStatus();
        $action->setParams([
            'date_from' => $date_from,
            'date_to' => $date_to,
            'smstype' => $smstype,
            'sms_group_id' => $sms_group_id,
            'sms_id' => $sms_id,
        ]);
        $this->smsClient->setAction($action);
        if (!$this->smsClient->isMultipost()) {
            return $this->smsClient->sendRequest();
        }
    }

    public function status_sms_id($sms_id)
    {
        return $this->status_sms(FALSE, FALSE, FALSE, FALSE, $sms_id);
    }

    public function status_sms_group_id($sms_group_id)
    {
        return $this->status_sms(FALSE, FALSE, FALSE, $sms_group_id, FALSE);
    }

    public function status_sms_date($date_from, $date_to, $smstype = 'SENDSMS')
    {
        return $this->status_sms($date_from, $date_to, $smstype, FALSE, FALSE);
    }

    /****************************************
     ***         проверка баланса         ***
     ****************************************/

    public function get_balance()
    {
        $action = new SmsActionBalance();
        $this->smsClient->setAction($action);
        if (!$this->smsClient->isMultipost()) {
            return $this->smsClient->sendRequest();
        }
    }

    /****************************************
     ***        получение входящих        ***
     ****************************************/

    public function inbox_sms(
        $new_only = FALSE,
        $sib_num = FALSE,
        $date_from = FALSE,
        $date_to = FALSE,
        $phone = FALSE,
        $prefix = FALSE)
    {
        $action = new SmsActionInbox();
        $action->setParams([
            'new_only' => $new_only ?? NULL,
            'sib_num' => $sib_num ?? NULL,
            'date_from' => $date_from ?? NULL,
            'date_to' => $date_to ?? NULL,
            'phone' => $phone ?? NULL,
            'prefix' => $prefix ?? NULL,
        ]);
        $this->smsClient->setAction($action);
        if (!$this->smsClient->isMultipost()) {
            return $this->smsClient->sendRequest();
        }
    }

}

if(!function_exists('http_build_query')) {
    function http_build_query($data,$prefix='',$sep='',$key='') {
        $ret = array();
        foreach ((array)$data as $k => $v) {
            if (is_int($k) && $prefix != null) $k = urlencode($prefix . $k);
            if ((!empty($key)) || ($key === 0))  $k = $key.'['.urlencode($k).']';
            if (is_array($v) || is_object($v)) array_push($ret, \Qtsms\http_build_query($v, '', $sep, $k));
            else array_push($ret, $k.'='.urlencode($v));
        }
        if (empty($sep)) $sep = ini_get('arg_separator.output');
        return implode($sep, $ret);
    };
};
