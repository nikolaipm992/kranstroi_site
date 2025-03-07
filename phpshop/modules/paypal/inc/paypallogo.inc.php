<?php

/**
 * ������������ ���� PayPal
 */
class AddToTemplatePaypalLogo extends PHPShopElements {

    var $debug = false;

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
        $this->option();
    }

    /**
     * ���������
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['paypal']['paypal_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ����� �����
     */
    function display() {

        $forma = parseTemplateReturn($GLOBALS['SysValue']['templates']['paypal']['paypal_logo'], true);
        $this->set('leftMenuContent', $forma);
        $this->set('leftMenuName', $this->option['title']);

        // ���������� ������
        $dis = $this->parseTemplate($this->getValue('templates.left_menu'));


        // ��������� ���������� �������
        //if ($this->option['operator'] == 1)
            switch ($this->option['logo_enabled']) {

                case 1:
                    $this->set('leftMenu', $dis, true);
                    break;

                case 2:
                    $this->set('rightMenu', $dis, true);
                    break;

                default: $this->set('paypallogo', $dis);
            }
    }

}

// ��������� � ������ �������
$AddToTemplatePaypalLogo = new AddToTemplatePaypalLogo();
$AddToTemplatePaypalLogo->display();
?>