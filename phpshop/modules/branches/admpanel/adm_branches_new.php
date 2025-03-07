<?php

include_once dirname(__DIR__) . '/class/include.php';

function actionStart() {
    global $PHPShopGUI, $PHPShopModules;

    $Branches = new Branches();

    if(!empty($Branches->options['yandex_api_key'])) {
        $PHPShopGUI->addJSFiles('//api-maps.yandex.ru/2.1/?apikey=' . $Branches->options['yandex_api_key'] . '&lang=ru_RU');
        $PHPShopGUI->addJSFiles('../modules/branches/admpanel/gui/branches.gui.js');
    }

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_branches'));

    $select = (int)$PHPShopOrm->select(['max(id) as end']);

    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'name_new', '', 300));
    $Tab1.= $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('city_id_new', $Branches->getCities(), 300, null, false, true, false, 1, false));
    $Tab1.= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'address_new', '', 300));
    $Tab1.= $PHPShopGUI->setInput('hidden', 'lon_new', '');
    $Tab1.= $PHPShopGUI->setInput('hidden', 'lat_new', '');

    if(!empty($Branches->options['yandex_api_key'])) {
        $map = '<div class="row">
                    <div class="col-sm-12" id="map-container" style="height: 400px; margin-bottom: 50px;"></div>
                </div>';
    } else {
        $map = '<div class="form-group form-group-sm "><div class="col-sm-12 text-info">��� ������� � ����� ������� API ���� ������.���� � ������� "���������" � ���������� ������.</div></div>';
    }

    $Tab1.= $PHPShopGUI->setCollapse('����� ������ ���������� �� �����', $map);


    $PHPShopGUI->setTab(['��������', $Tab1]);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $select['end'] + 1, "right", 70, "", "but") . $PHPShopGUI->setInput("submit", "saveID", "���������", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

function actionInsert() {
    global $PHPShopModules;

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_branches'));

    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);

    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>