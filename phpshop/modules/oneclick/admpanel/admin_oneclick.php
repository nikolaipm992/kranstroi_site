<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name, $PHPShopSystem;

    $PHPShopInterface->setActionPanel($TitlePage, $select_name, null);

    $PHPShopInterface->setCaption(array("", "1%"), array("������", "5%", array('sort' => 'none')), array("��������", "30%"), array("����", "10%"), array("���", "20%"), array("����", "10%"),  array(null, "10%"), array("������", "10%"));
    $PHPShopInterface->dropdown_action_form = true;

        // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $status_array = array(
        1 => array('name'=>'����� ������','color'=>'#35A6E8'),
        2 => array('name'=>'�����������','color'=>'#EC971F'),
        3 => array('name'=>'����c�����','color'=>'red'),
        4 => array('name'=>'��������','color'=>'#70BD1B'),
    );

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.oneclick.oneclick_jurnal"));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {

            if (!empty($row['product_image']))
                $icon = '<img src="' . $row['product_image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';
            
            if(!empty($row['mail']))
              $mail= '<br>'.$row['mail'];
            else $mail=null;

            $PHPShopInterface->setRow($row['id'], array('name' => $icon, 'link' => '?path=modules.dir.oneclick&id=' . $row['id'], 'align' => 'left'), array('name' => $row['product_name'], 'link' => '?path=modules.dir.oneclick&id=' . $row['id']),$row['product_price'].' '.$currency,array('name' => $row['name'], 'link' => '?path=modules.dir.oneclick&id=' . $row['id'],'addon'=>$mail), PHPShopDate::get($row['date'], false),   array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), '<span style="color:'.$status_array[$row['status']]['color'].'">'.$status_array[$row['status']]['name'].'</span>');
        }

    $PHPShopInterface->Compile();
}

?>