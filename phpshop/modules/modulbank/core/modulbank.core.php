<?php

class PHPShopModulBank extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        // ������ �������
        parent::__construct();
    }

    /**
     * ����� �� ���������
     */
    function index() {
        include_once 'phpshop/modules/modulbank/class/ModulBank.php';

        $Modulbank = new ModulBank();
        $Modulbank->parameters = $_REQUEST;

        if($Modulbank->get_signature() === $_REQUEST['signature']) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('uid'), array('uid' => "='" . $_REQUEST['order_id'] . "'"), false, array('limit' => 1));
            if (!empty($row['uid'])) {
                $PHPShopOrm->query("UPDATE `phpshop_orders` SET `statusi`='101' WHERE `uid`='$row[uid]'");
                $Modulbank->log($_REQUEST, $_REQUEST['order_id'], '�������� ������ �������� �������', '���������� �������');
            } else {
                $Modulbank->log($_REQUEST, $_REQUEST['order_id'], '����� �� ������', '���������� �������');
            }
        } else {
            $Modulbank->log($_REQUEST, $_REQUEST['order_id'], '������ �������� signature', '���������� �������');
        }


    }

}