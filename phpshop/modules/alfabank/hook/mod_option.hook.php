<?php

PHPShopObj::loadClass("array");
/**
 * ����� ��������� �������� ������
 */
class PHPShopAlfabankArray extends PHPShopArray
{

    function __construct()
    {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['alfabank']['alfabank_system'];
        parent::__construct("login", "password", "dev_mode", "status", "title_sub", "taxationSystem","api_url");
    }

    /**
     * ������ ����
     * @param string $message ���������� ������� � �� ��� ���� �������
     * @param string $order_id ����� ������
     * @param string $status ������ ������
     */
    function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_alfabank_log");
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