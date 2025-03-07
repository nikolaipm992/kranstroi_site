<?php

function megamarketAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_megamarket'],
        'sort'     => 'price_megamarket',
        'editable' => 'price_megamarket_new',
        'view'     => (int) $memory['catalog.option']['price_megamarket']
    ];
    
}

function megamarketAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // ��������
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['export_megamarket']) && (int) $product['export_megamarket'] === 1 && (int) $memory['catalog.option']['label_megamarket'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('����� � ����������') . '" href="?path=catalog' . $postfix . '&where[export_megamarket]=1">' . __('��') . '</a> ';
}

$addHandler = [
    'grid' => 'megamarketAddOption',
    'labels' => 'megamarketAddLabels'
];