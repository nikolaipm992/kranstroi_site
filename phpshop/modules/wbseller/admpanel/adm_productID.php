<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';

function addWbsellerProductTab($data) {
    global $PHPShopGUI;

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_wb_new', 1, '�������� ������� � WB', $data['export_wb']));
    if (!empty($data['export_wb_task_status']))
        $tab .= $PHPShopGUI->setField('������ ������', $PHPShopGUI->setText('<span class="text-success">�������� ' . PHPShopDate::get($data['export_wb_task_status'], true) . '</span>'));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('���� WB', $PHPShopGUI->setInputText(null, 'price_wb_new', $data['price_wb'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField('������� WB', $PHPShopGUI->setInputText(null, 'export_wb_id_new', $data['export_wb_id'], 150, $PHPShopGUI->setLink('https://www.wildberries.ru/catalog/' . $data['export_wb_id'] . '/detail.aspx', '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('������� �� ���� WB'))));
    $tab .= $PHPShopGUI->setField("������ WB", $PHPShopGUI->setInputText(null, 'barcode_wb_new', $data['barcode_wb'], 150));

    $PHPShopGUI->addTab(array("WB", $tab, true));
}

function WbsellerUpdate(){
    
    // ���������� 
    if (!isset($_POST['export_wb_new']) and ! empty($_POST['name_new'])) {
        $_POST['export_wb_new'] = 0;
        //$_POST['export_wb_task_status_new'] = '';
        // $_POST['export_wb_id_new'] = '';
    }
    
    // if (isset($_POST['enabled_new']) and empty($_POST['enabled_new']))
       // $_POST['export_wb_new'] = 0;
}

function WbsellerSave() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_POST['rowID']]);

    if (!empty($data['export_wb'])) {

        $WbSeller = new WbSeller();

        // ����� ��� �� ��������
        if (empty($data['export_wb_id'])) {


            if (empty($data['export_wb_task_status'])) {

                // ��������
                $result = $WbSeller->sendProducts($data);

                if (is_array($result) and empty($result['error'])) {
                    $PHPShopOrm->update(['export_wb_task_status_new' => time()], ['id' => '=' . (int) $data['id']]);
                }
            }

            // ����������
            $getProduct = $WbSeller->getProduct($data['uid'])['cards'][0];
            $export_wb_id = $getProduct['nmID'];
            $barcode_wb = $getProduct['sizes'][0]['skus'][0];

            if (!empty($export_wb_id)) {
                $data['export_wb_id'] = $export_wb_id;
                $PHPShopOrm->update(['export_wb_id_new' => $export_wb_id, 'barcode_wb_new' => $barcode_wb], ['id' => '=' . (int) $data['id']]);

                // ����
                $WbSeller->sendImages($data);
            }
        }
        // ����� ��������, ���������� ��� � ��������
        else {

            // SKU
            /*
            if (empty($data['barcode_wb'])) {
                $_POST['barcode_wb_new'] = $WbSeller->getProduct($data['uid'])['cards'][0]['sizes'][0]['skus'][0];
            }*/

            // �����
            $WbSeller->setProductStock([$data]);

            // ����
            $price = $data['price'];

            if (!empty($data['price_wb'])) {
                $price = $data['price_wb'];
            } elseif (!empty($data['price' . (int) $data['price']])) {
                $price = $data['price' . (int) $WbSeller->price];
            }

            if ($WbSeller->fee > 0) {
                if ($WbSeller->fee_type == 1) {
                    $price = $price - ($price * $WbSeller->fee / 100);
                } else {
                    $price = $price + ($price * $WbSeller->fee / 100);
                }
            }

            // ����� ������
            if ($WbSeller->discount == 1) {
                $prices[] = [
                    'nmID' => (int) $data['export_wb_id'],
                    'price' => (int) $WbSeller->price($price, $data['baseinputvaluta']),
                    'discount' => (int) 0
                ];
            } else {
                $prices[] = [
                    'nmID' => (int) $data['export_wb_id'],
                    'price' => (int) $WbSeller->price($price, $data['baseinputvaluta'])
                ];
            }

            $WbSeller->sendPrices(['data' => $prices]);
        }
    } 
    //else
       // $PHPShopOrm->update(['export_wb_task_status_new' => '', 'export_wb_id_new' => 0], ['id' => '=' . $data['id']]);
}

$addHandler = array(
    'actionStart' => 'addWbsellerProductTab',
    'actionDelete' => false,
    'actionSave' => 'WbsellerSave',
    'actionUpdate' => 'WbsellerUpdate',
    'actionOptionEdit' => 'addWbsellerProductTab',
);
?>