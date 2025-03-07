<?php
/**
 * ���������� ���������� � �������
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "class/mail.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('uniteller');


class Payment extends PHPShopPaymentResult {

    function __construct() {
        $this->payment_name = 'Uniteller';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../class/Uniteller.php');
        $this->Uniteller = new Uniteller();
        
        parent::__construct();
    }

    /**
     * �������� �������
     * @return boolean
     */
    function check() {

        $signature = strtoupper(md5($_POST['Order_ID'] . $_POST['Status'] . $this->Uniteller->option['password']));

        if ($signature === $_POST['Signature'])
            return true;
    }

    /**
     * ���������� ������ �� ������
     */
    function updateorder() {

        if ($this->check()) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('*'), array('uid' => "='" . $_POST['Order_ID'] . "'"), false, array('limit' => 1));

            if(empty($row))
                $this->Uniteller->log($_POST, $_POST['Order_ID'], '����� ' . $_POST['Order_ID'] . ' �� ������', '����������� � �������');

            if($_POST['Status'] === 'authorized' or $_POST['Status'] === 'paid') {
                $this->Uniteller->log($_POST, $_POST['Order_ID'], '����� ' . $_POST['Order_ID'] . ' �������', '����������� � �������');

                // ��� �����
                $PHPShopOrmPayment = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrmPayment->insert(array('uid_new' => $row['uid'], 'name_new' => 'Uniteller',
                    'sum_new' => $row['sum'], 'datas_new' => time()));

                // ��������� ������� �������
                (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

                $PHPShopSystem = new PHPShopSystem();
                $content = '����� �' . $row['uid'] . ' ������� ��������� �������� Uniteller.';
                new PHPShopMail($PHPShopSystem->getParam('adminmail2'), $PHPShopSystem->getParam('adminmail2'),"������ ������ �".$row['uid'], $content);
            }
            else
                $this->Uniteller->log($_POST, $_POST['Order_ID'], '������ ������ ������ ' . $_POST['Order_ID'], '����������� � �������');
        } else {
            $this->Uniteller->log($_POST, $_POST['Order_ID'], '������ �������� Signature', '����������� � �������');
        }
    }
}

new Payment();