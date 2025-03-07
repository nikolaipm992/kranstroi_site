<?php

function tab_userdata($data, $order) {
    global $PHPShopGUI;
    
    
    $PHPShopGUI->field_col=3;
    
    $help='<p class="text-muted hidden-xs data-row">'.__('�������������� ���� � ������ � ���������� � �� ������������� ���������� ����� ��������� � �������').'  <a href="?path=delivery&id='.$order['Person']['dostavka_metod'].'&tab=1"><span class="glyphicon glyphicon-share-alt"></span>'.__('���������� ���������').'</a></p><hr>';
    
    if(empty($data['fio'])){
        $data['fio']=$order['Person']['name_person'];
    }
    
    if(empty($data['user']))
        $user='<div class="form-group form-group-sm ">
        <label class="col-sm-3 control-label">'.__('���').':</label><div class="col-sm-9">
        <input data-set="3" name="fio_new" maxlength="50" class="search_user form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="'.__('�����...').'" value="'.$data['fio'].'">
        <input name="user_new" type="hidden">
     </div></div> ';
    else $user=$PHPShopGUI->setField("���", $PHPShopGUI->setInputText('', 'fio_new', $data['fio'] ));
    

    $order['Person']=$PHPShopGUI->valid($order['Person'],'adr_name','org_name','mail');

    // ������ ����������
    $disp1 = $user .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setInputText('', 'tel_new', PHPShopSecurity::TotalClean($data['tel'],6))) .
            $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInputText('', 'person[mail]', PHPShopSecurity::TotalClean($order['Person']['mail']))).
            $PHPShopGUI->setField("������", $PHPShopGUI->setInputText('', 'country_new', PHPShopSecurity::TotalClean($data['country']))) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInputText('', 'state_new', PHPShopSecurity::TotalClean($data['state']))) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('', 'city_new', PHPShopSecurity::TotalClean($data['city']))) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInputText('', 'index_new', PHPShopSecurity::TotalClean($data['index']))) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('', 'street_new', PHPShopSecurity::TotalClean($data['street'] . $order['Person']['adr_name']))) .
            $PHPShopGUI->setField("���", $PHPShopGUI->setInputText('', 'house_new', PHPShopSecurity::TotalClean($data['house']))) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setInputText('', 'porch_new', PHPShopSecurity::TotalClean($data['porch']))) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setInputText('', 'door_phone_new', PHPShopSecurity::TotalClean($data['door_phone']))) .
            $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText('', 'flat_new', PHPShopSecurity::TotalClean($data['flat'])));

    // ��. ������ ����������
    $disp2 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText('', 'org_name_new', PHPShopSecurity::TotalClean($data['org_name'] . $order['Person']['org_name'],6))) .
            $PHPShopGUI->setField("���", $PHPShopGUI->setInputText('', 'org_inn_new', PHPShopSecurity::TotalClean($data['org_inn']))) .
            $PHPShopGUI->setField("���", $PHPShopGUI->setInputText('', 'org_kpp_new', PHPShopSecurity::TotalClean($data['org_kpp']))) .
            $PHPShopGUI->setField("��. �����", $PHPShopGUI->setInputText('', 'org_yur_adres_new', PHPShopSecurity::TotalClean($data['org_yur_adres'],6))) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('', 'org_fakt_adres_new', PHPShopSecurity::TotalClean($data['org_fakt_adres']))) .
            $PHPShopGUI->setField("�/�", $PHPShopGUI->setInputText('', 'org_ras_new', PHPShopSecurity::TotalClean($data['org_ras']))) .
            $PHPShopGUI->setField("����", $PHPShopGUI->setInputText('', 'org_bank_new', PHPShopSecurity::TotalClean($data['org_bank']))) .
            $PHPShopGUI->setField("�/�", $PHPShopGUI->setInputText('', 'org_kor_new', PHPShopSecurity::TotalClean($data['org_kor']))) .
            $PHPShopGUI->setField("���", $PHPShopGUI->setInputText('', 'org_bik_new', PHPShopSecurity::TotalClean($data['org_bik']))) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('', 'org_city_new', PHPShopSecurity::TotalClean($data['org_city']))).
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('', 'delivtime_new', PHPShopSecurity::TotalClean($data['delivtime']))).
            $PHPShopGUI->setField("Tracking", $PHPShopGUI->setInputText('', 'tracking_new', PHPShopSecurity::TotalClean($data['tracking'])));

    return $help.$PHPShopGUI->setGrid(array($disp1, 6), array($disp2, 6));
}

?>