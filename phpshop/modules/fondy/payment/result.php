<?php

/**
 * ���������� ���������� � �������
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("parser");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('fondy');

include_once 'phpshop/modules/fondy/class/Signature.php';

class FondyPayment extends PHPShopPaymentResult
{

    function __construct()
    {
        $this->payment_name = 'Fondy';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../class/Fondy.php');
        $this->Fondy = new Fondy();
        parent::__construct();
    }

    /**
     * �������� �������
     * @return boolean
     */
    function check()
    {
        Signature::merchant($this->Fondy->option['merchant_id']);
        Signature::password($this->Fondy->option['password']);
        if (Signature::check($_POST))
            return true;
    }

    /**
     * ���������� ������ �� ������
     */
    function updateorder()
    {
        if ($this->check()) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('*'), array('uid' => "='" . $_POST['Order_ID'] . "'"), false, array('limit' => 1));

            if (empty($row))
                $this->Fondy->log($_POST, $_POST['order_id'], '����� ' . $_POST['Order_ID'] . ' �� ������', '����������� � �������');

            if ($_POST['Status'] === 'authorized' or $_POST['Status'] === 'paid') {
                $this->Fondy->log($_POST, $_POST['order_id'], '����� ' . $_POST['Order_ID'] . ' �������', '����������� � �������');

                // ��� �����
                $PHPShopOrmPayment = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrmPayment->insert(array('uid_new' => $row['uid'], 'name_new' => 'Fondy',
                    'sum_new' => $row['sum'], 'datas_new' => time()));

                // ��������� ������� �������
                $PHPShopOrm->debug = $this->debug;
                $PHPShopOrm->update(array('statusi_new' => $this->set_order_status_101(), 'paid_new' => 1), array('uid' => '="' . $row['uid'] . '"'));

            } else
                $this->Fondy->log($_POST, $_POST['order_id'], '������ ������ ������ ' . $_POST['Order_ID'], '����������� � �������');
        } else {
            $this->Fondy->log($_POST, $_POST['order_id'], '������ �������� Signature', '����������� � �������');
        }
    }
}

new FondyPayment();