<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("������", "7%"), array("��������", "40%"), array("������", "30%"), array("������", "20%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), array('export_wb' => "='1'"), array('order' => 'export_wb_task_status'), array('limit' => 10000));
    $WbSeller = new WbSeller();

    if (is_array($data)) {


        foreach ($data as $row) {

            if (!empty($row['pic_small']))
                $icon = '<img src="' . $row['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            if (!empty($row['export_wb_task_status'])) {

                $status = '<span class="text-success">' . __('��������') . ' ' . PHPShopDate::get($data['export_wb_task_status'], true) . '</span>';
                $error = null;

                if (empty($row['export_wb_id'])) {

                    // ����
                    $WbSeller->sendImages($row);

                    // �����
                    $WbSeller->setProductStock([$row]);
                    
                    // ����������
                    $export_wb_id = $WbSeller->getProduct([$row['uid']])['data'][0]['nmID'];

                    if (!empty($export_wb_id))
                        $PHPShopOrm->update(['export_wb_id_new' => $export_wb_id], ['id' => '=' . (int) $row['id']]);
                    
                }
            } else {

                // ��������
                $result = $WbSeller->sendProducts([$row]);

                if (is_array($result) and empty($result['error'])) {
                    $PHPShopOrm->update(['export_wb_task_status_new' => time(), 'export_wb_id_new' => 0], ['id' => '=' . (int) $row['id']]);
                    $error = null;
                    $status = '<span class="text-success">' . __('��������') . ' ' . PHPShopDate::get(time(), true) . '</span>';
                } else {
                    $error = PHPShopString::utf8_win1251($result['errorText']);
                    $status = '<span class="text-warning">' . __('������') . '</span>';
                }
            }

            // �������
            if (!empty($row['uid']))
                $uid = '<div class="text-muted">' . __('���') . ' ' . $row['uid'] . '</div>';
            else
                $uid = null;

            $PHPShopInterface->setRow($icon, array('name' => $row['name'], 'addon' => $uid, 'link' => '?path=product&id=' . $row['id']), $error, $status);
        }
    }
    $PHPShopInterface->Compile();
}
