<?php

function addProductSimilarSort($data) {
    global $PHPShopGUI;

    $Tab3 = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('productsimilar_enabled_new', 1, '�������� � ������ �������', $data['productsimilar_enabled']));

    $PHPShopGUI->addTab(array("������� ������", $Tab3, true));
}

function updateProductSimilarSort() {
    if(!isset($_POST['productsimilar_enabled_new']))
        $_POST['productsimilar_enabled_new']=0;
}


$addHandler = array(
    'actionStart' => 'addProductSimilarSort',
    'actionDelete' => false,
    'actionUpdate' => 'updateProductSimilarSort'
);
?>