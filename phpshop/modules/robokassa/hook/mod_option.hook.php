<?php

// ��������� ������
PHPShopObj::loadClass("array");

class PHPShopRobokassaArray extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['robokassa']['robokassa_system'];
        parent::__construct("status", "title", 'title_sub', 'merchant_login', 'merchant_key', 'merchant_skey','dev_mode','merchant_country');
    }

    /**
     * ������ ����
     * @param array $message ���������� ������� � �� ��� ���� �������
     * @param string $order_id ����� ������
     * @param string $status ������ ������
     * @param string $type request
     */
    function log($message, $order_id, $status, $type) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['robokassa']['robokassa_log']);
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

}

?>