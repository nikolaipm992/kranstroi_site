<?php

function addYandexcart($data) {
    global $PHPShopGUI;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    $Tab3 = null;

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    if (!empty($options['model'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model'], $PHPShopGUI->setCheckbox('yml_new', 1, '�������� ������� � ������.������', $data['yml']), 1, '�������� &#8470;1');
        $Tab3 .= $PHPShopGUI->setField('���� ' . $options['model'], $PHPShopGUI->setInputText(null, 'price_yandex_new', $data['price_yandex'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_2'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model_2'], $PHPShopGUI->setCheckbox('yml_2_new', 1, '�������� ������� � ������.������', $data['yml_2']), 1, '�������� &#8470;2');
        $Tab3 .= $PHPShopGUI->setField('���� ' . $options['model_2'], $PHPShopGUI->setInputText(null, 'price_yandex_2_new', $data['price_yandex_2'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_3'])) {
        $Tab3 .= $PHPShopGUI->setField($options['model_3'], $PHPShopGUI->setCheckbox('yml_3_new', 1, '�������� ������� � ������.������', $data['yml_3']), 1, '�������� &#8470;3');
        $Tab3 .= $PHPShopGUI->setField('���� ' . $options['model_3'], $PHPShopGUI->setInputText(null, 'price_yandex_3_new', $data['price_yandex_3'], 150, $valuta_def_name), 2);
    }


    $Tab3 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setRadio('manufacturer_warranty_new', 1, '��������', $data['manufacturer_warranty']) . $PHPShopGUI->setRadio('manufacturer_warranty_new', 2, '���������', $data['manufacturer_warranty'], false, 'text-muted'), 1, '��� manufacturer_warranty');

    $Tab3 .= $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setInputText(null, 'vendor_name_new', $data['vendor_name'], 300), 1, '��� vendor');

    $Tab3 .= $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setInputText(null, 'vendor_code_new', $data['vendor_code'], 300), 1, '��� vendorCode');

    $Tab3 .= $PHPShopGUI->setField("�������� �������������, ����� � ���. �����", $PHPShopGUI->setInputText(null, 'manufacturer_new', $data['manufacturer'], 300), 1, '��� manufacturer');

    $Tab3 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, 'barcode_new', $data['barcode'], 300), 1, '��� barcode');

    $Tab3 .= $PHPShopGUI->setField("������ ������������", $PHPShopGUI->setInputText(null, 'country_of_origin_new', $data['country_of_origin'], 300), 1, '��� country_of_origin');

    $Tab3 .= $PHPShopGUI->setField("������������� ������ �� �������", $PHPShopGUI->setInputText(null, 'market_sku_new', $data['market_sku'], 300), 1, '��� market-sku ��� ������ FBS, ����� �������� � ������ �������� ������.�������');

    $Tab3 .= $PHPShopGUI->setField('����� ��� ��������', $PHPShopGUI->setRadio('adult_new', 1, '��������', $data['adult']) . $PHPShopGUI->setRadio('adult_new', 2, '���������', $data['adult'], false, 'text-muted'), 1, '��� adult');

    $condition[] = array(__('����� �����'), 1, $data['yandex_condition']);
    $condition[] = array(__('������ � ������������'), 2, $data['yandex_condition']);
    $condition[] = array(__('��������� �������'), 3, $data['yandex_condition']);
    $condition[] = array(__('��������� �����'), 4, $data['yandex_condition']);

    $quality[] = array(__('����� �����'), 1, $data['yandex_quality']);
    $quality[] = array(__('��� �����, ����� � ��������� ���������'), 2, $data['yandex_quality']);
    $quality[] = array(__('��������, ����� ������������� ��� ������� ���� ��������'), 3, $data['yandex_quality']);
    $quality[] = array(__('�������, ���� �������� ����� ������������� ��� �������'), 4, $data['yandex_quality']);

    $Tab3 .= $PHPShopGUI->setField('��������� ������', $PHPShopGUI->setSelect('yandex_condition_new', $condition, 300), 1, '��� condition');
    $Tab3 .= $PHPShopGUI->setField('������� ��� ������', $PHPShopGUI->setSelect('yandex_quality_new', $quality, 300), 1, '��� quality');
    $Tab3 .= $PHPShopGUI->setField('������� ������', $PHPShopGUI->setTextarea('yandex_condition_reason_new', $data['yandex_condition_reason'], true, 300), 1, '��� reason');

    $service_life_days[] = array(__('������ �� �������'), '', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('6 �������'), 'P6M', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('1 ���'), 'P1Y', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('2 ����'), 'P2Y', $data['yandex_service_life_days']);
    $service_life_days[] = array(__('3 ����'), 'P3Y', $data['yandex_service_life_days']);

    $Tab3 .= $PHPShopGUI->setField('���� ��������', $PHPShopGUI->setSelect('yandex_service_life_days_new', $service_life_days, 300), 1, '��� period-of-validity-days');



    $Tab3 .= $PHPShopGUI->setField('���������� ��������', $PHPShopGUI->setRadio('delivery_new', 1, '��������', $data['delivery']) . $PHPShopGUI->setRadio('delivery_new', 2, '���������', $data['delivery'], false, 'text-muted'), 1, '��� delivery');

    $Tab3 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setRadio('pickup_new', 1, '��������', $data['pickup']) . $PHPShopGUI->setRadio('pickup_new', 2, '���������', $data['pickup'], false, 'text-muted'), 1, '��� pickup');

    $Tab3 .= $PHPShopGUI->setField("����������� ����������", $PHPShopGUI->setInputText(null, 'yandex_min_quantity_new', $data['yandex_min_quantity'], 100), 1, ' ����������� ���������� ������ � ����� ������');

    $Tab3 .= $PHPShopGUI->setField("����������� ���", $PHPShopGUI->setInputText(null, 'yandex_step_quantity_new', $data['yandex_step_quantity'], 100), 1, ' ���������� ������, ����������� � ������������');

    $Tab3 .= $PHPShopGUI->setField("������ �� ����� � ������.�������", $PHPShopGUI->setInputText(null, 'yandex_link_new', $data['yandex_link'], '100%'));


    $PHPShopGUI->addTabSeparate(array("������.������", $PHPShopGUI->setPanel(null, $Tab3, 'panel'), true));
}

function addYandexCartOptions($data) {
    global $PHPShopGUI;

    $PHPShopGUI->field_col = 5;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $Tab = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, 'barcode_new', $data['barcode']), 1, '��� barcode');
    $Tab .= $PHPShopGUI->setField("��� �������������", $PHPShopGUI->setInputText(null, 'vendor_code_new', $data['vendor_code']), 1, '��� vendorCode');

    if (!empty($options['model'])) {
        $Tab .= $PHPShopGUI->setField($options['model'], $PHPShopGUI->setCheckbox('yml_new', 1, '�������� ������� � ������.������', $data['yml']), 1, '�������� &#8470;1');
        $Tab .= $PHPShopGUI->setField('���� ' . $options['model'], $PHPShopGUI->setInputText(null, 'price_yandex_new', $data['price_yandex'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_2'])) {
        $Tab .= $PHPShopGUI->setField($options['model_2'], $PHPShopGUI->setCheckbox('yml_2_new', 1, '�������� ������� � ������.������', $data['yml_2']), 1, '�������� &#8470;2');
        $Tab .= $PHPShopGUI->setField('���� ' . $options['model_2'], $PHPShopGUI->setInputText(null, 'price_yandex_2_new', $data['price_yandex_2'], 150, $valuta_def_name), 2);
    }

    if (!empty($options['model_3'])) {
        $Tab .= $PHPShopGUI->setField($options['model_3'], $PHPShopGUI->setCheckbox('yml_3_new', 1, '�������� ������� � ������.������', $data['yml_3']), 1, '�������� &#8470;3');
        $Tab .= $PHPShopGUI->setField('���� ' . $options['model_3'], $PHPShopGUI->setInputText(null, 'price_yandex_3_new', $data['price_yandex_3'], 150, $valuta_def_name), 2);
    }

    $PHPShopGUI->addTab(["������", $Tab, true]);
}

function YandexcartUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->updateZeroVars('yml_2_new', 'yml_3_new');
}

function YandexcartSave() {
    global $PHPShopOrm;

    // ���������� ��� � ��������
    include_once dirname(__FILE__) . '/../class/YandexMarket.php';
    $Market = new YandexMarket();
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    // �������� 1
    $products = $PHPShopOrm->getOne(['*'], ['yml' => "='1'", 'id' => '=' . $_POST['rowID']]);
    if (count($products) > 0) {
        $Market->updateStocks([$products], false);
        $Market->updatePrices([$products], false);
    }

    // �������� 2
    $products_2 = $PHPShopOrm->getOne(['*'], ['yml_2' => "='1'", 'id' => '=' . $_POST['rowID']]);
    if (count($products_2) > 0) {
        $Market->updateStocks([$products_2], 2);
        $Market->updatePrices([$products_2], 2);
    }

    // �������� 3
    $products_3 = $PHPShopOrm->getOne(['*'], ['yml_3' => "='1'", 'id' => '=' . $_POST['rowID']]);
    if (count($products_3) > 0) {
        $Market->updateStocks([$products_3], 3);
        $Market->updatePrices([$products_3], 3);
    }
}

$addHandler = array(
    'actionStart' => 'addYandexcart',
    'actionDelete' => false,
    'actionUpdate' => 'YandexcartUpdate',
    'actionSave' => 'YandexcartSave',
    'actionOptionEdit' => 'addYandexCartOptions'
);
