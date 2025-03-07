<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.geoipredirect.geoipredirect_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $info = '<p>��� ����������� ������ �� IP ������ ������������ ���������� ������ ���������� <a href="http://sypexgeo.net" target="_blank">Sypex Geo</a>. ��� ����� ������� ����������� ������ ������������� ���������� ������������ ������ ���� ����������. ���� ������ ������� <kbd>SxGeoCity.dat</kbd> ������ ���������� �  <code>phpshop/modules/geoipredirect/class/SxGeoCity.dat</code>. �� ������� �������� ������� <code>SxGeoCity.dat</code> � ������ ���� ���� �� ������� � ������ ���� ������ ��������������. ��� ���������� ����� <code>SxGeoCity.dat</code> ������ ������ <kbd>GeoIP Redirect</kbd> ����������.</p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab2), array("� ������", $Tab3), array("����� �������", null, '?path=modules.dir.geoipredirect'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>