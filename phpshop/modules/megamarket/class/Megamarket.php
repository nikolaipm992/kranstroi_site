<?php

/**
 * ���������� ������ � Megamarket API
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopModules
 * @todo https://partner.megamarket.ru/documentation/v2/api
 */
class Megamarket {

    const API_URL = 'https://api.megamarket.tech/api/merchantIntegration/v1/offerService/';
    const UPDATE_PRODUCT_PRICES = 'manualPrice/save';
    const UPDATE_PRODUCT_STOCKS = 'stock/update';

    public $api_key;

    public function __construct() {
        global $PHPShopSystem;

        // ��������� ���������
        PHPShopObj::loadClass("valuta");
        $this->PHPShopValuta = (new PHPShopValutaArray())->getArray();
        $this->percent = $PHPShopSystem->getValue('percent');
        $this->defvaluta = $PHPShopSystem->getValue('dengi');
        $this->format = $PHPShopSystem->getSerilizeParam('admoption.price_znak');
        $this->vat = $PHPShopSystem->getParam('nds') / 100;

        // ��������� ������
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_megamarket_system');
        $this->options = $PHPShopOrm->select();
        $this->api_key = $this->options['token'];
        $this->status = $this->options['status'];
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];
        $this->type = $this->options['type'];
        $this->delivery = $this->options['delivery'];
        $this->log = $this->options['log'];
        $this->export = $this->options['export'];

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';
        else
            $this->ssl = 'http://';
    }

    /**
     *  ���� ������
     */
    public function price($price, $baseinputvaluta) {

        // ���� ������ ���������� �� �������
        if ($baseinputvaluta !== $this->defvaluta) {
            $vkurs = $this->PHPShopValuta[$baseinputvaluta]['kurs'];

            // ���� ���� ������� ��� ������ �������
            if (empty($vkurs))
                $vkurs = 1;

            // �������� ���� � ������� ������
            $price = $price / $vkurs;
        }

        $price = ($price + (($price * $this->percent) / 100));
        $price = round($price, intval($this->format));

        return $price;
    }

    /**
     *  ����� ��� ��������?
     */
    public function checkOrderBase($id) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->getOne(['id'], ['megamarket_order_id' => '="' . $id.'"']);
        if (!empty($data['id']))
            return $data['id'];
    }

    /**
     * ������ � ������
     */
    public function log($message, $id, $type) {

        if (!empty($this->log)) {

            $PHPShopOrm = new PHPShopOrm('phpshop_modules_megamarket_log');
            $log = array(
                'message_new' => serialize($message),
                'order_id_new' => $id,
                'type_new' => $type,
                'date_new' => time()
            );

            $PHPShopOrm->insert($log);
        }
    }

    /**
     * ������ � ������ JSON
     */
    public function log_json($message, $id, $type) {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_wbseller_log');

        $log = array(
            'message_new' => $message,
            'order_id_new' => $id,
            'type_new' => $type,
            'date_new' => time()
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * ��������� ������� �� ������
     */
    public function setProductStock($params, $offerId) {

        if ($this->export != 1) {

            $data = [
                'meta' => [],
                'data' => [
                    'token' => $this->api_key,
                    'stocks' => $params
                ]
            ];

            $result = $this->request(self::UPDATE_PRODUCT_STOCKS, $data);

            // ������
            $log['params'] = $data;
            $log['result'] = $result;

            $this->log($log, $offerId, self::UPDATE_PRODUCT_STOCKS);

            return $result;
        }
    }

    /**
     * ��������� ���
     */
    public function setProductPrice($params, $offerId) {

        if ($this->export != 2) {

            $data = [
                'meta' => [],
                'data' => [
                    'token' => $this->api_key,
                    'prices' => $params
                ]
            ];


            $result = $this->request(self::UPDATE_PRODUCT_PRICES, $data);

            // ������
            $log['params'] = $data;
            $log['result'] = $result;

            $this->log($log, $offerId, self::UPDATE_PRODUCT_PRICES);

            return $result;
        }
    }

    /**
     * ������ � API
     * @param string $method ����� ������
     * @param array $params ���������
     * @return array
     */
    public function request($method, $params = []) {

        if (empty($this->api_key))
            return false;


        $ch = curl_init();
        $header = [
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, self::API_URL . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $result = curl_exec($ch);
        curl_close($ch);


        return json_decode($result, true);
    }

    // ����� ������
    function setOrderNum() {

        $PHPShopOrm = new PHPShopOrm();
        $res = $PHPShopOrm->query("select uid from " . $GLOBALS['SysValue']['base']['orders'] . " order by id desc LIMIT 0, 1");
        $row = mysqli_fetch_array($res);
        $last = $row['uid'];
        $all_num = explode("-", $last);
        $ferst_num = $all_num[0];

        if ($ferst_num < 100)
            $ferst_num = 100;
        $order_num = $ferst_num + 1;

        // ����� ������
        $ouid = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, 3);
        return $ouid;
    }

}
