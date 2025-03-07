<?php

function marketplacesAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('������������') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_google', 1, '���� Google Merchant', $memory['catalog.option']['price_google']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_cdek', 1, '���� ������.������', $memory['catalog.option']['price_cdek']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_aliexpress', 1, '���� AliExpress', $memory['catalog.option']['price_aliexpress']). '<br>' ;
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_sbermarket', 1, '���� ����������', $memory['catalog.option']['price_sbermarket']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_google_merchant', 1, '����� Google Merchant', $memory['catalog.option']['label_google_merchant']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_cdek', 1, '����� ������.������', $memory['catalog.option']['label_cdek']) . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_aliexpress', 1, '����� AliExpress', $memory['catalog.option']['label_aliexpress']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_sbermarket', 1, '����� ����������', $memory['catalog.option']['label_sbermarket']);
}

$addHandler = [
    'actionOption' => 'marketplacesAddOption'
];
