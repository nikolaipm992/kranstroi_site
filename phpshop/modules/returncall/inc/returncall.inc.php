<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * ������� ����� ��������� ������
 */
class AddToTemplateReturnCallElement extends PHPShopElements {

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
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['returncall']['returncall_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ����� �����
     */
    function display() {

        $PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

        $this->set('leftMenuName', $this->option['title']);
        $this->set('returnCallName', $this->option['title']);

        // ���������� ������
        if (empty($this->option['windows'])) {

            if ($this->option['captcha_enabled'] == 1) {
                $this->set('returncall_captcha', $PHPShopRecaptchaElement->captcha('returncall', 'compact'));
            }

            $this->set('leftMenuContent', PHPShopParser::file($GLOBALS['SysValue']['templates']['returncall']['returncall_forma'], true, false, true));
            $dis = $this->parseTemplate($this->getValue('templates.left_menu'));
        } else {

            if ($this->option['captcha_enabled'] == 1) {
                $this->set('returncall_captcha', $PHPShopRecaptchaElement->captcha('returncall'));
            }

            if (empty($this->option['enabled']))
                $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['returncall']['returncall_window_forma'], true, false, true);
            else {
                $this->set('leftMenuContent', PHPShopParser::file($GLOBALS['SysValue']['templates']['returncall']['returncall_window_forma'], true, false, true));
                $dis = $this->parseTemplate($this->getValue('templates.left_menu'));
            }
        }
        
        
        // ��������� ���������� �������
        switch ($this->option['enabled']) {

            case 1:
                $this->set('leftMenu', $dis, true);
                break;

            case 2:
                $this->set('rightMenu', $dis, true);
                break;

            default: $this->set('returncall', $dis);
        }
    }

}

// ��������� � ������ �������
if ($PHPShopNav->notPath('returncall')) {
    $AddToTemplateReturnCallElement = new AddToTemplateReturnCallElement();
    $AddToTemplateReturnCallElement->display();
}
?>