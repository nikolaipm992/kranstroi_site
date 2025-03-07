<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * ������� ������ �� ����������
 */
class AddToTemplateSortselectionElement extends PHPShopElements {

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
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sortselection']['sortselection_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * ����� ������ �������������� ��� ������
     */
    function display() {

        if ($this->option['flag'] == 1 and ! $this->PHPShopNav->index())
            return false;

        if ($this->option['sort'] != "") {


            // ���������� ����������
            $table = null;

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $data_sort_categories = $PHPShopOrm->select(['id,name'], ['id' => ' IN (' . implode(',', unserialize($this->option['sort'])) . ')'], ['order' => 'num,name'], ['limit' => 50]);
            if (is_array($data_sort_categories)) {

                foreach ($data_sort_categories as $cat) {
                    $list = null;
                    $PHPShopOrmVal = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                    $data_sort = $PHPShopOrmVal->select(['id,name'], ['category' => '=' . $cat['id']], ['order' => 'num,name'], ['limit' => 100]);

                    if (is_array($data_sort)) {

                        foreach ($data_sort as $val) {
                            if (!empty($val['name'])) {
                                //$list .= '<label class="checkbox-inline"><input type="checkbox" name="v[' . $cat['id'] . '][]" value="' . $val['id'] . '"> ' . $val['name'] . '</label>';
                                $this->set('sortSelectionId', $cat['id']);
                                $this->set('sortSelectionValue', $val['id']);
                                $this->set('sortSelectionName', $val['name']);

                                // ���������� ������
                                $list .= PHPShopParser::file($GLOBALS['SysValue']['templates']['sortselection']['sortselection_select'], true, false, true);
                            }
                        }
                    }

                    //$table .= PHPShopText::tr($cat['name'], $list);
                    $this->set('sortSelectionSortName', $cat['name']);
                    $this->set('sortSelectionSortList', $list);
                    
                    // ���������� ������
                    $table .= PHPShopParser::file($GLOBALS['SysValue']['templates']['sortselection']['sortselection_sortname'], true, false, true);
                }
            }


            $this->set('sortSelectionContent', $table);
            $this->set('sortSelectionName', $this->option['title']);

            // ���������� ������
            $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['sortselection']['sortselection_forma'], true, false, true);

            // �����
            if ($this->option['enabled'] == 2) {
                $this->set('sortselection', $dis);
            }
            // ����
            else {
                $window = PHPShopParser::file($GLOBALS['SysValue']['templates']['sortselection']['sortselection_window'], true, false, true);
                $this->set('sortselection', $window);
            }
        }
    }

}

// ��������� � ������ �������
$AddToTemplateSortselectionElement = new AddToTemplateSortselectionElement();
$AddToTemplateSortselectionElement->display();
?>