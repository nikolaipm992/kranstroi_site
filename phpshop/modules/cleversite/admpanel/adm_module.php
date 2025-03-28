<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cleversite.cleversite_system"));

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

    $PHPShopGUI->title = "��������� ������ Cleversite";

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('ID',$PHPShopGUI->setInputText(false, 'client_new', $data['client'], '300'));
    $Tab1.= $PHPShopGUI->setField('ID �����',$PHPShopGUI->setInputText(false, 'site_new', $data['site'], '300'));

    $Info = '<h4>��� ������� ������� ������ �������� ����������:</h4>
        <ol>
        <li> ����������������� �� ����� <a href="https://cleversite.ru/?ref=qD3jt" target="_blank"> cleversite.ru</a>
		<li> �������� �� ����� ������ � ���������������� �������.
		<li> �������� � ������ �������� ����� ������� �� ������ ���������� �� ����� �����.
        <li> ���������� ��� ID � �������� ��� � ���� "ID" �� ������� "��������" �������� ���� ��������� ������.
		<li> ���������� ��� ID ����� ��� ������� ���� � �������� ��� � ���� "ID �����" �� ������� "��������" �������� ���� ��������� ������.
		<li> ��������� ��������� ���� ������.
		</ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info, '200px', '100%');

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay();

    $About = '���� � ��� �������� �������, �� ������ ������ ��������� �� <a href="http://cleversite.ru/" target="_blank">����� �����</a> � ������-����������� ��� ��������� ��������� �� <a href="mailto:help@cleversite.ru">help@cleversite.ru</a>, ��������� ���� ��������� 24 ���� � �����. �� ������� ���������� ��� �� ��� ���� � ������ ������ � �������.';
    $Tab3.=$PHPShopGUI->setInfo($About);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $PHPShopGUI->setCollapse('�����������',$Tab1)), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
