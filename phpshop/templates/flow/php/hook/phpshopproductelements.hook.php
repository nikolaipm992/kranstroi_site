<?php

/**
 * ����� ������ ���������� � ��������������� � ������� �������� �������.
 */
function phpshopproductelements_product_grid_nt_hook($obj, $dataArray) {
    global $PHPShopCart;
    
    // ���������������
    if($dataArray['spec'])
        $obj->set('specIcon', ParseTemplateReturn('product/specIcon.tpl'));
    else
        $obj->set('specIcon', '');
    
    // �������
    if($dataArray['newtip'])
        $obj->set('newtipIcon', ParseTemplateReturn('product/newtipIcon.tpl'));
    else
        $obj->set('newtipIcon', '');
    
    // � �������
    if (empty($PHPShopCart)){
        $PHPShopCart = new PHPShopCart();
    }

    if ($PHPShopCart->isItemInCart($dataArray['id'])) {
        $obj->set('flowProductSale', $obj->lang('productSaleReady'));
    } else {
        $obj->set('flowProductSale', $obj->lang('product_sale'));
    }
    
}

/**
 * ���������� � ������ ��������� ��������������� ������� � 3 ������, ����� 3
 */
$addHandler = array
    (
    'product_grid' => 'phpshopproductelements_product_grid_nt_hook',
);
?>