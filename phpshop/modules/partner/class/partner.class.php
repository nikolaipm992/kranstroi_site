<?php

class PHPShopPartnerOrder {

    var $option = null;

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['partner']['partner_log'];

        if (!empty($_SESSION['partner_id'])) {
            $this->partner = $_SESSION['partner_id'];
            $this->path = $_SESSION['partner_path'];
            $this->option = $this->option();
        }
        $this->debug = false;
    }

    /**
     * ��������� ������
     * @return array
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_system']);
        $PHPShopOrm->debug = $this->debug;
        return $PHPShopOrm->select();
    }

    /**
     * ����� ������� ������ ��������
     */
    function updateLog($orderId) {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;

        // �������� ������ ���� ������� �������� �� �����������
        $PHPShopOrm->update(array('enabled_new' => '1'), array('order_id' => '="' . $orderId . '"'));
    }

    /**
     * ��������� ����� ��������
     */
    function addBonus($orderId, $money) {

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->getOne(array('partner_id'), array('order_id' => '=' . $orderId, 'enabled' => "='0'"));
        $partner = $data['partner_id'];


        if (!empty($partner)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_users']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->getOne(array('money'), array('id' => '=' . $partner));

            $bonus = $money * $this->option['percent'] / 100;

            // ��������� ������ ��������
            $PHPShopOrm->update(array('money_new' => intval($bonus + $data['money'])), array('id' => '="' . $partner . '"'));
        }
    }

    /**
     * ������ ������ � ��� ���������
     */
    function writeLog($orderId, $sum) {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->insert(array('date_new' => date("U"),
            'order_uid_new' => $_POST['ouid'],
            'partner_id_new' => $this->partner,
            'path_new' => $this->path,
            'order_id_new' => $orderId,
            'percent_new' => $this->option['percent'],
            'sum_new' => $sum
        ));
    }

    /**
     * ������ ID ��������
     */
    function setPartner($partner) {
        $_SESSION['partner_id'] = $partner;
        $_SESSION['partner_path'] = $_SERVER["HTTP_REFERER"];
        setcookie("ps_partner", $partner, time() + 60 * 60 * 24 * $this->option['cookies_day'], "/", $_SERVER['SERVER_NAME'], 0);
    }

}
