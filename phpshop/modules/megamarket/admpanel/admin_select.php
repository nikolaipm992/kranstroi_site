<?php

function megamarketAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('����������') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_megamarket', 1, '���� ����������', $memory['catalog.option']['price_megamarket']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_megamarket', 1, '����� ����������', $memory['catalog.option']['label_megamarket']) . '<br>';
}

$addHandler = [
    'actionOption' => 'megamarketAddOption'
];
