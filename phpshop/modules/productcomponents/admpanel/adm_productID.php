<?php

function addProductIDProductcomponents($data) {
    global $PHPShopGUI, $PHPShopModules;

    if ((new PHPShopOrm($PHPShopModules->getParam("base.productcomponents.productcomponents_system")))->select()['product_search'] == 1) {
        $PHPShopGUI->addJSFiles('../modules/productcomponents/admpanel/gui/productcomponents.gui.js');
        $help = __('������� ID ������� ��� ��������������') .
                ' <a href="#" data-target="#productcomponents_products_new"  class="btn btn-sm btn-default tag-search ' . $class . '"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>';
    } else
        $help = __('������� ID ������� ����� �������');

    $Tab = $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(null, 'productcomponents_discount_new', $data['productcomponents_discount'], 100, '%'));
    $Tab .= $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'productcomponents_markup_new', $data['productcomponents_markup'], 100, '%'));


    $Tab .= $PHPShopGUI->setTextarea('productcomponents_products_new', $data['productcomponents_products'], false, false, false, $help);


    $PHPShopGUI->addTab(array("�������������", $Tab, true));
}

function updateProductIDProductcomponents() {
    
    $PHPShopValutaArray = new PHPShopValutaArray();

    if (!empty($_POST['productcomponents_products_new'])) {

        $ids = explode(",", $_POST['productcomponents_products_new']);
        if (is_array($ids)) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            foreach ($ids as $id) {
                $row[] = $PHPShopOrm->getOne(['*'], ['id=' => (int) $id]);
            }

            $price = $price2 = $price3 = $price4 = $price5 = 0;
            $enabled = 1;
            $items = 100;

            if (is_array($row)) {
                foreach ($row as $data) {


                    if ($data['baseinputvaluta'] != $_POST['baseinputvaluta_new']) {
                        $data['price'] = $data['price'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price2'] = $data['price2'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price3'] = $data['price3'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price4'] = $data['price4'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price5'] = $data['price5'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                    }

                    $price += $data['price'];
                    $price2 += $data['price2'];
                    $price3 += $data['price3'];
                    $price4 += $data['price4'];
                    $price5 += $data['price5'];

                    if ($data['items'] < $items)
                        $items = $data['items'];

                    if (empty($data['items']) or empty($data['enabled'])) {
                        $items = 0;
                        $enabled = 0;
                    }
                }
            }

            // ������
            $price = $price - ($price * $_POST['productcomponents_discount_new'] / 100);
            $price2 = $price2 - ($price2 * $_POST['productcomponents_discount_new'] / 100);
            $price3 = $price3 - ($price3 * $_POST['productcomponents_discount_new'] / 100);
            $price4 = $price4 - ($price4 * $_POST['productcomponents_discount_new'] / 100);
            $price5 = $price5 - ($price5 * $_POST['productcomponents_discount_new'] / 100);


            // �������
            $price = $price + ($price * $_POST['productcomponents_markup_new'] / 100);
            $price2 = $price2 + ($price2 * $_POST['productcomponents_markup_new'] / 100);
            $price3 = $price3 + ($price3 * $_POST['productcomponents_markup_new'] / 100);
            $price4 = $price4 + ($price4 * $_POST['productcomponents_markup_new'] / 100);
            $price5 = $price5 + ($price5 * $_POST['productcomponents_markup_new'] / 100);


            $_POST['price_new'] = $price;
            $_POST['price2_new'] = $price2;
            $_POST['price3_new'] = $price3;
            $_POST['price4_new'] = $price4;
            $_POST['price5_new'] = $price5;
            $_POST['enabled_new'] = $enabled;
            $_POST['items_new'] = $items;
        }
    }
}

$addHandler = array(
    'actionStart' => 'addProductIDProductcomponents',
    'actionDelete' => false,
    'actionUpdate' => 'updateProductIDProductcomponents'
);
?>