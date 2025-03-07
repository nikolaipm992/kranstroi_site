<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';

function addOzonsellerProductTab($data) {
    global $PHPShopGUI;

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_ozon_new', 1, '�������� ������� � OZON', $data['export_ozon']));
    $tab .= $PHPShopGUI->setInput("hidden", "export_ozon_task_id", $data['export_ozon_task_id']);

    // ���� �������� 
    if ($data['export_ozon_task_id'] > 1680000000)
        $date = PHPShopDate::get($data['export_ozon_task_id'], true);
    else
        $date = null;


    $status = ['imported' => '<span class="text-success">' . __('��������') . ' ' . $date . '</span>', 'failed' => '<a class="text-warning" href="?path=modules.dir.ozonseller.product&uid=' . $data['id'] . '" target="_blank">' . __('������') . '</a>', 'pending' => '<span class="text-muted">' . __('� ��������') . '</span>'];

    if (!empty($data['export_ozon_task_status']))
        $tab .= $PHPShopGUI->setField('������ ������', $PHPShopGUI->setText($status[$data['export_ozon_task_status']]));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('���� OZON', $PHPShopGUI->setInputText(null, 'price_ozon_new', $data['price_ozon'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, 'barcode_ozon_new', $data['barcode_ozon'], 150));
    $tab .= $PHPShopGUI->setField("SKU OZON", $PHPShopGUI->setInputText(null, 'sku_ozon_new', $data['sku_ozon'], 150), 1, '������������ ��� ������ �� ����� � OZON');


    if (!empty($data['export_ozon']))
        $tab .= $PHPShopGUI->setField('OZON ID', $PHPShopGUI->setInputText(null, 'export_ozon_id_new', $data['export_ozon_id'], 150), 1, '������������ ��� ���������� ������ � OZON');

    $PHPShopGUI->addTab(array("OZON", $tab, true));
}

function OzonsellerUpdate() {

    // ���������� Ozon
    if (!isset($_POST['export_ozon_new']) and ! empty($_POST['name_new'])) {
        $_POST['export_ozon_new'] = 0;
        //$_POST['export_ozon_task_id_new'] = 0;
        //$_POST['export_ozon_task_status_new'] = '';
    }
    
    //if (isset($_POST['enabled_new']) and empty($_POST['enabled_new']))
       // $_POST['export_ozon_new'] = 0;
}

function OzonsellerSave() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_POST['rowID']]);

    // ����� ��� OZON
    if (!empty($data['export_ozon'])) {

        $OzonSeller = new OzonSeller();

        // ����� ��� �� ��������
        if (empty($data['export_ozon_id'])) {

            // ��������
            if (empty($data['export_ozon_task_id'])) {
                $result = $OzonSeller->sendProducts($data);
                $task_id = $data['export_ozon_task_id'] = $result['result']['task_id'];
                $error = $result['message'];
            } else
                $task_id = $data['export_ozon_task_id'];

            if (!empty($task_id) and empty($error)) {

                // �������� ������� ��������
                $info = $OzonSeller->sendProductsInfo($data)['result']['items'][0];

                // ����� ����������
                if (!empty($info['product_id'])) {

                    // SKU ��� ������ �� ����� OZON
                    $data['sku_ozon'] = $OzonSeller->getProduct($info['product_id'])['items'][0]['sources'][0]['sku'];

                    // �������� ���������
                    if (!empty($data['barcode_ozon']) and ! empty($data['sku_ozon']))
                        $OzonSeller->addBarcode(['barcode_ozon' => $data['barcode_ozon'], 'sku_ozon' => $data['sku_ozon']]);

                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_id_new' => $info['product_id'], 'sku_ozon_new' => $data['sku_ozon']], ['id' => '=' . $data['id']]);
                    $data['export_ozon_id'] = $info['product_id'];
                    $OzonSeller->clean_log($data['id']);
                }
                // ������
                elseif (is_array($info['errors']) and count($info['errors']) > 0) {

                    if (empty($info['status']))
                        $info['status'] = 'error';

                    foreach ($info['errors'] as $k => $er) {

                        // ������
                        $er['description'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">[������]</a>$3', $er['description']);
                        $error .= ($k + 1) . ' - ' . PHPShopString::utf8_win1251($er['description']) . '<br>';
                    }

                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status']], ['id' => '=' . (int) $data['id']]);
                }
                // � ��������
                elseif ($info['status'] == 'pending') {
                    $error = __('����� ��������� � ������� �� ������, ������ OZON �������� �����. ��������� ��������� ����������� ������ ��� ���������� ��������.');
                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_task_id_new' => $task_id], ['id' => '=' . (int) $data['id']]);
                }

                if (!empty($error))
                    $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
            }
            // ������ 
            elseif (!empty($error)) {
                $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
            }
        }
        // ����� ��������, ���������� ��� � ��������
        else {

            // SKU ��� ������ �� ����� OZON
            if (empty($data['sku_ozon'])) {
                $data['sku_ozon']= $OzonSeller->getProduct($data['export_ozon_id'])['items'][0]['sources'][0]['sku'];
            }

            // ��������
            if (!empty($data['barcode_ozon']) and ! empty($data['sku_ozon']))
                $OzonSeller->addBarcode(['barcode_ozon' => $data['barcode_ozon'], 'sku_ozon' => $data['sku_ozon']]);

            // �����
            if (is_array($OzonSeller->warehouse))
                foreach ($OzonSeller->warehouse as $warehouse) {
                    $result = $OzonSeller->setProductStock([$data], $warehouse['id'])['result']['items'][0]['product_id'];
                    //if (!empty($result))
                        //$product_id = $result;
                }

            // ����
            $OzonSeller->setProductPrice([$data]);
        }
    }
}

$addHandler = array(
    'actionStart' => 'addOzonsellerProductTab',
    'actionDelete' => false,
    'actionUpdate' => 'OzonsellerUpdate',
    'actionSave' => 'OzonsellerSave',
    'actionOptionEdit' => 'addOzonsellerProductTab',
);
?>