<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * ������� ������ �� ����������
 */
class AddToTemplateSortProductElement extends PHPShopElements {

    var $debug = false;

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
        $this->option();

        // ���� ������ SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
            PHPShopObj::loadClass('string');
            $this->option['seourl_enabled'] = true;
        }
        else
            $this->option['seourl_enabled'] = false;
    }

    /**
     * ���������
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sortproduct']['sortproduct_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ����� �������
     */
    function product($where, $limit) {

        // ���� ������ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $seourlpro_enabled = true;
        }

        $forma = null;
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array('limit' => $limit));
        if (is_array($data))
            foreach ($data as $row) {

                if ($seourlpro_enabled) {

                    if (empty($row['prod_seo_name']))
                        $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'];
                    else
                        $url = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];

                    $link = PHPShopText::a($this->getValue('dir.dir') . $url . '.html', $row['name'], $row['name'], false, false, false, 'sortproduct');
                }
                else
                    $link = PHPShopText::a($this->getValue('dir.dir') . '/shop/UID_' . $row['id'] . '.html', $row['name'], $row['name'], false, false, false, 'sortproduct');

                $this->set('sortproduct_value', $link);
                $forma.=parseTemplateReturn($GLOBALS['SysValue']['templates']['sortproduct']['sortproduct_links'], true);
            }
        return $forma;
    }

    /**
     * ����� ������ �������������� ��� ������
     */
    function display() {
        $forma = null;

        // ������ ������������� ��� ������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sortproduct']['sortproduct_forms']);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($data))
            foreach ($data as $row) {
                $hash = $row['sort'] . '-' . $row['value_id'];
                $where = array('vendor' => " REGEXP 'i" . $hash . "i'");

                $this->set('sortproduct_title', $row['value_name']);
                $this->set('sortproduct_value_list', $this->product($where, $row['items']));
                $forma.=parseTemplateReturn($GLOBALS['SysValue']['templates']['sortproduct']['sortproduct_forma'], true);
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

            default: $this->set('brandproduct', $dis);
        }
    }

}

// ��������� � ������ �������
if ($PHPShopNav->notPath(array('order', 'done'))) {
    $AddToTemplateSortProductElement = new AddToTemplateSortProductElement();
    $AddToTemplateSortProductElement->display();
}
?>