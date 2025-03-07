<?php

/**
 * ���������� ����������� ATOL
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 * @subpackage RestApi
 */
class AtolRest {

    public $token = null;
    public $log = null;
    public $orderId = null;
    public $api_url = "https://online.atol.ru/possystem/v4/";

    /**
     * ����� �������
     * @var bool 
     */
    protected $debug = false;

    /**
     * �����������
     * @param string $OrderId � ������
     */
    public function __construct($OrderId) {
        $this->orderId = $OrderId;
        $this->option();
        $this->getToken();
    }

    /**
     * ��������� ������
     */
    public function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['atol']['atol_system']);
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ������ REST
     * @param array $data ������
     * @param string $rout ������
     * @param bool $post ������������ POST, ����� GET
     * @return string
     */
    public function request($data, $rout, $post = true) {

        $data_string = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url . $rout);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string),
            'Token: ' . $this->token
        ));

        if (!$post)
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $output = curl_exec($ch);

        curl_close($ch);

        $response = explode("\r\n\r\n", $output);
        $responsecontent = $response[1];

        return json_decode($responsecontent, true);
    }

    /**
     * ��������� ������
     */
    public function getToken() {

        $data['login'] = $this->option['login'];
        $data['pass'] = $this->option['password'];

        $result = $this->request($data, 'getToken');

        if ($result['code'] == 1 or $result['code'] == 0)
            $this->token = $result['token'];

        $this->log(__METHOD__, $result);
    }

    /**
     * ��������
     * @param array $data ������ ������ ��� ����
     * @param string $operation �������� [sell|sell_refund]
     * @param bool $post ������������ POST, ����� GET
     */
    public function setOparation($data, $operation = 'sell', $post = true) {

        $data = json_fix_cyr($data);
        $this->log(__METHOD__ . '::' . $operation . ' IN', $data);
        $result = $this->request($data, $this->option['group_code'] . '/' . $operation, $post);
        $this->log(__METHOD__ . '::' . $operation . ' OUT', $result);
        return $result;
    }

    /**
     * ���
     * @param string $path URL �������
     * @param array $data ������
     */
    public function log($path, $data) {

        $data = json_fix_utf($data);
        $this->log[$path] = $data;

        // ����� �������
        if ($this->debug) {
            echo '<pre>';
            print_r($this->log);
            echo '</pre>';
        } 
    }

}

?>