<?php

include_once dirname(__DIR__) . '/class/include.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_branches'));

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $Branches = new Branches();

    if(!empty($Branches->options['yandex_api_key'])) {
        $PHPShopGUI->addJSFiles('//api-maps.yandex.ru/2.1/?apikey=' . $Branches->options['yandex_api_key'] . '&lang=ru_RU');
        $PHPShopGUI->addJSFiles('../modules/branches/admpanel/gui/branches.gui.js');
    }

    $branch = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_GET['id']]);

    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'name_new', $branch['name'], 300));
    $Tab1.= $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('city_id_new', $Branches->getCities($branch['city_id']), 300, null, false, true, false, 1, false));
    $Tab1.= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'address_new', $branch['address'], 300));
    $Tab1.= $PHPShopGUI->setInput('hidden', 'lon_new', $branch['lon']);
    $Tab1.= $PHPShopGUI->setInput('hidden', 'lat_new', $branch['lat']);

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
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $branch['id'], "right", 70, "", "but") .
        $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
        $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
        $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

function actionUpdate() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->update($_POST, ['id' => '=' . (int) $_GET['id']]);

    return ['success' => $action];
}

function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;

    $PHPShopOrm->delete(['id' => '=' . (int) $_GET['id']]);

    return ["success" => true];
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>