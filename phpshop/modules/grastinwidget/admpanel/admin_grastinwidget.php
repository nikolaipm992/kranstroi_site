<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "50%"), array("№ Заказа", "10%"), array("Дата", "10%"), array("Статус", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.grastinwidget.grastinwidget_log"));
    $PHPShopOrm->debug = false;

    $result = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if(!empty($result['id']))
        $data = array($result);
    else
        $data = $result;

    $orderIds = array();
    foreach ($data as $item)
        $orderIds[] = $item['order_id'];

    $idString = implode("', '", $orderIds);
    $query = $PHPShopOrm->query("SELECT `id`, `uid` FROM " . $GLOBALS['SysValue']['base']['orders'] . " WHERE `uid` in ('$idString')");

    while ($rw = $query->fetch_assoc()) {
        $id[$rw['uid']] = $rw['id'];
    }

    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['order_id']))
                $row['status']=__('Ошибка передачи заказа');
        
            $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.grastinwidget&id=' . $row['id']), array('name' => $row['order_id'], 'link' => '?path=order&id=' . $id[$row['order_id']]), PHPShopDate::get($row['date'], true), $row['status']);
        }
    $PHPShopInterface->Compile();
}

?>