<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.1c.1c_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    // �������
    $data = $PHPShopOrm->select();

    // �����������
    $info='<p>
        <p><h4>��� ������������ � 1� ���������� ������� ������� CommerceML?</h4>
        <ol>
        <li>���� 1� ��������� ��� � ������ - <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/commerceml" target="_blank">���������� �� �����������</a>
        </ol>
        </p>
        <p><h4>��� ������������ � �������� ���������� ������� ������� CommerceML?</h4>
        <ol>
        <li>�������������� <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/sinkhronizaciya-s-moi-sklad" target="_blank">����������� �� �����������</a>
        </ol>
        <h4>��� ������������ � 1� ������� ������������ PHPShop (��� ������ 1�)?</h4>
        <ol>
        <li>���� 1� ��������� - <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/ustanovka-i-aktivaciya-1s-sinkhronizacii#1s-na-kompyutere" target="_blank">���������� �� �����������</a>
        <li>���� 1� � ������ - <a href="https://docs.phpshop.ru/sinkhronizaciya-s-1s/ustanovka-i-aktivaciya-1s-sinkhronizacii#1s-v-oblake" target="_blank">���������� �� �����������</a>
        </ol></p>
        </p>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $PHPShopGUI->setCollapse('',$info),true), array("����� �������", null, '?path=system.sync'), array("������ ��������", null, '?path=report.crm'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $_POST['region_data_new']=1;

    if (empty($_POST["manual_control_new"]))
        $_POST["manual_control_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>