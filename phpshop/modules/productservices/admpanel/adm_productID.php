<?php

function addProductIDProductservices($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(null, 'productservices_discount_new', $data['productservices_discount'], 100,'%'));
    $Tab .= $PHPShopGUI->setTextarea('productservices_products_new', $data['productservices_products'], false, false, false,
        __('������� ID ������� ��� ��������������') .
        ' <a href="#" data-target="#productservices_products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>');

    $PHPShopGUI->addJSFiles('../modules/productservices/admpanel/gui/productservices.gui.js');

    $PHPShopGUI->addTab(array("������", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addProductIDProductservices',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>