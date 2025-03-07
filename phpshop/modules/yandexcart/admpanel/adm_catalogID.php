<?php

function addYandexcartCPA($data) {
    global $PHPShopGUI;

    // ������ ��� �������� ���������
    if (!is_array($GLOBALS['tree_array'][$data['id']]) and isset($data['icon'])) {

        // ��������� �������� � ������� actionStart
        if (empty($data['prod_seo_name'])) {
            PHPShopObj::loadClass("string");
            $data['prod_seo_name'] = PHPShopString::toLatin($data['name']);
            $data['prod_seo_name'] = str_replace("_", "-", $data['prod_seo_name']);
        }
        $Tab3 = $PHPShopGUI->setField("������ (Fee)", $PHPShopGUI->setInputText(null, 'fee_new', null, 100), 1, '1% �������� ������������� �������� 100');


        $Tab3.=$PHPShopGUI->setField(__('CPA ������'), 
                $PHPShopGUI->setRadio('cpa_new', 1, __('��������'), 0) . 
                $PHPShopGUI->setRadio('cpa_new', 2, __('���������'), 0, false, 'text-warning').
                $PHPShopGUI->setRadio('cpa_new', 0, __('�� ��������'), 0, false, 'text-muted') );

        $PHPShopGUI->addTab(array("������", $Tab3, true));
    }
}

function updateYandexcartCPA() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    if(!empty($_POST['cpa_new']))
    $action = $PHPShopOrm->update(array('cpa_new' => $_POST['cpa_new'], 'fee_new' => $_POST['fee_new']), array('category' => '=' . intval($_POST['rowID'])));
}

$addHandler = array(
    'actionStart' => 'addYandexcartCPA',
    'actionDelete' => false,
    'actionUpdate' => 'updateYandexcartCPA'
);
?>