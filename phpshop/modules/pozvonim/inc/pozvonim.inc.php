<?php

if (!defined("OBJENABLED")) {
    exit(header('Location: /?error=OBJENABLED'));
}

/**
 * ������� ����� ��������� ������
 */
class AddToTemplatepozvonimElement extends PHPShopElements
{

    var $debug = false;

    /**
     * �����������
     */
    function __construct()
    {
        parent::__construct();
        $this->option();
    }

    /**
     * ���������
     */
    function option()
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pozvonim']['pozvonim_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * �����
     */
    function display()
    {
        if ($this->option['key']) {
            // ���������� ������
            $dis = '<script crossorigin="anonymous" async type="text/javascript" src="//api.pozvonim.com/widget/callback/v3/'
                   . $this->option['key']
                   . '/connect" id="check-code-pozvonim" charset="UTF-8"></script>';
            // ��������� ���������� �������
            $this->set('leftMenu', $dis);
            $this->set('pozvonim', $dis);
        }
    }

}

// ��������� � ������ �������
if ($PHPShopNav->notPath('pozvonim')) {
    $AddToTemplatepozvonimElement = new AddToTemplatepozvonimElement();
    $AddToTemplatepozvonimElement->display();
}
?>