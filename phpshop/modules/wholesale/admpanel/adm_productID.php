<?php

function addModWholesale($data) {
    global $PHPShopGUI, $ed_izm;

    include_once '../.' . $GLOBALS['SysValue']['class']['wholesale'];
    $GLOBALS['PHPShopWholesale'] = new PHPShopWholesale();
    $opt = $GLOBALS['PHPShopWholesale']->getOpt($data);

    // ����� ��������� � �����
    if (is_array($opt)) {

        // ������� ���������
        if (empty($data['ed_izm']))
            $ed_izm = '��.';
        else
            $ed_izm = $data['ed_izm'];

        // ������
        if ($opt['tip'] == 0) {
            $Tab = $PHPShopGUI->setField('���������� ������', $PHPShopGUI->setInputText(null, 'wholesale_check_new', $data['wholesale_check'], 100, $ed_izm));
            $Tab .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(null, 'wholesale_discount_new', $data['wholesale_discount'], 100, '%'));
        } 
        // �������
        else{
            $Tab = $PHPShopGUI->setField('���������� ������', $PHPShopGUI->setInputText(null, 'wholesale_check_new', $data['wholesale_check'], 100, $ed_izm));
            $Tab .= $PHPShopGUI->setField("������� ���", $PHPShopGUI->setSelect('wholesale_price_new', $PHPShopGUI->setSelectValue($data['wholesale_price'], 5), 100));
        }

        $PHPShopGUI->addTab(array("������� ����", $Tab, true));
    }
}

$addHandler = array(
    'actionStart' => 'addModWholesale',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>