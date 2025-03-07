<?php

$TitlePage = __("������ ��������");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name,$PHPShopSystem;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("� ���", "15%"), array("����", "15%"), array("� ������", "15%"), array("�����", "15%"), array("��������", "10%",array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_log"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (is_array($data))
        foreach ($data as $row) {

            if ($row['operation'] == 'registration') {
                $operation = '�������';
            } elseif($row['operation'] == 'return') {
                $operation = '<span class="text-warning">�������</span>';
            }
            else $operation = '<span class="text-danger">������</span>';

            if (empty($row['fiscal']))
                $row['fiscal'] = '������ �' . $row['id'];


            $PHPShopInterface->setRow(array('name' => $row['fiscal'], 'link' => '?path=modules.dir.pechka54&id=' . $row['id']), PHPShopDate::get($row['date'], true), array('name' => $row['order_uid'], 'link' => '?path=order&id=' . $row['order_id']), $row['sum'].$currency,array('name'=>$operation,'align' => 'right'));
        }
    $PHPShopInterface->Compile();
}

?>