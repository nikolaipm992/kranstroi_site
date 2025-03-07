<?php

// ��������� ������
PHPShopObj::loadClass("array");

class PHPShopcloudpaymentArray extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->objBase = "phpshop_modules_cloudpayment_system";
        parent::__construct("status", "title", 'title_end', 'publicId', 'description', 'api', 'taxationSystem');
    }

    /**
     * ������ ����
     * @param string $message ���������� ������� � �� ��� ���� �������
     * @param string $order_id ����� ������
     * @param string $status ������ ������
     */
    function log($message, $order_id, $status, $type) {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_cloudpayment_log");
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
