<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * ������� ������ �� ����������
 */
class AddToTemplateSortElement extends PHPShopElements {

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
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sortbrand']['sortbrand_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ����� ������ �������������� ��� ������
     */
    function display() {

        if ($this->option['sort'] > 0) {

            // ���������� ����������
            PHPShopObj::loadClass('sort');

            $PHPShopSort = new PHPShopSort();
            $PHPShopSort->debug = $this->debug;

            $PHPShopSort->value($this->option['sort'], $this->option['title'], true);

            $value = array();
            if (is_array($PHPShopSort->value_array))
                foreach ($PHPShopSort->value_array as $k => $v) {
                    $value[$k] = array($v[0], $v[1], $v[2]);
                }

            //array_pop($value);
            array_shift($value);

            // ���������� �� �����
            sort($value);
            $size = (strlen($this->option['title']) + 10) * 7;
            $values = PHPShopText::select('v[' . $this->option['sort'] . ']', $value, $size, false, false, false, false, false, false);

            // ������
            if ($this->option['flag'] == 1) {
                $this->set('sortbrand_value', $values);
                //$forma=PHPShopText::p(PHPShopText::form($value.PHPShopText::button('OK','SortSelect.submit()'),'SortSelect','get','/selection/',false,'ok'));
                $forma = PHPShopParser::file($GLOBALS['SysValue']['templates']['sortbrand']['sortbrand_forma'], true, false, true);
            }
            // ������
            else {
                $forma = null;
                if (is_array($value))
                    foreach ($value as $row) {
                        $link = PHPShopText::a('/selection/?v[' . $this->option['sort'] . ']=' . $row[1], $row[0], $row[0], false, false, false, 'sortbrand');
                        $this->set('sortbrand_value', $link);
                        $forma.= PHPShopParser::file($GLOBALS['SysValue']['templates']['sortbrand']['sortbrand_links'], true, false, true);
                    }
            }

            $this->set('leftMenuContent', $forma);
            $this->set('leftMenuName', $this->option['title']);

            // ���������� ������
            $dis = $this->parseTemplate($this->getValue('templates.left_menu'));


            // ��������� ���������� �������
            switch ($this->option['enabled']) {

                case 1:
                    $this->set('leftMenu', $dis, true);
                    break;

                case 2:
                    $this->set('rightMenu', $dis, true);
                    break;

                default: $this->set('brand', $dis);
            }
        }
    }

}

// ��������� � ������ �������
if ($PHPShopNav->notPath(array('order', 'done'))) {
    $AddToTemplateSortElement = new AddToTemplateSortElement();
    $AddToTemplateSortElement->display();
}
?>