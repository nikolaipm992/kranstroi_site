<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.atol.atol_system"));

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
    $Tab1 = $PHPShopGUI->setField('����� � ����', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1.= $PHPShopGUI->setField('������ � ����', $PHPShopGUI->setInputText(false, 'password_new', $data['password'],300));
    $Tab1.= $PHPShopGUI->setField('��� ������ � ����', $PHPShopGUI->setInputText(false, 'group_code_new', $data['group_code'], 300));
    $Tab1.= $PHPShopGUI->setField('URL �������� � ����', $PHPShopGUI->setInputText(false, 'payment_address_new', $data['payment_address'], 300));
    $Tab1.= $PHPShopGUI->setField('��� � ����', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300));
    $Tab1.= $PHPShopGUI->setField('������ ����������', $PHPShopGUI->setCheckbox("manual_control_new", 1, "��������� �������������� �������� �����", $data["manual_control"]));

    // �����������
    $info='<h4>����������� � ���� ������</h4>
        <ol>
        <li>������������������ �� ������� <a href="https://online.atol.ru/lk/Account/Register?partnerUid=deb4b494-75b2-423e-9af0-6b32df3c67a3" target="_blank">���� ������</a>.
        <li>������ ��������� ����������� ����� �� ����������� ���������� <a href="http://www.phpshop.ru/UserFiles/File/atol.pdf" target="_blank">Atol.pdf</a>
        <li>�������� ����� ������� <b>'.$_SERVER['SERVER_NAME'].'</b> � ������ ������������ ��������� � ����� � �������� ��������� ������� � �����.
        </ol>
        
        <h4>��������� ������</h4>
        <ol>
        <li>� ����� "����� � ����","������ � ����" � "��� ������ � ����" ����������� ������, ���������� ����� ����������� � ���� ������.</a>
        <li>���� "URL �������� � ����" ������ <b>��������� ���������</b> � ������� ��������, ��������� � ���� ������.
        <li>���� "��� � ����" ������ ��������� ��������� � ��� ��������, ��������� � ���� ������.
        </ol>
        
        <h4>�������� ����� ������� � ��������</h4>
        <ol>
        <li>���� ������� (�������) ��������� ������������� ��� ������������ ������ ���������� �������� �� �������� ������� ����� ����� ���� ������ ��� ��������� ������ �� ������ ����������� ����� � ����. ��������� �������������� ���������� � �������� ������ �� ���������.
        <li>���������� ��� �������� � �������� <kbd>�����</kbd> � ������� �������������� ������.
        <li>��� ������� ����� �������� � ������ ������ ��� ������ ������ � ������� �������������� ������.
        <li>��� �������� ������� ����� �������� � ������ ������ ��� ������ ������, ������� ��� �������.
        </ol>
        
        <h4>������ ������ �����</h4>
        <ol>
        <li>��� �������� � ������ ��������� � <a href="?path=modules.dir.atol">������ ��������</a>.
        <li>��������� ���������� �� ��������� �������� ����� �������� ��� ����� �� ������ ���� � ������� ��������.
        </ol>
        
        <h4>��������� ��������</h4>
        <ol>
        <li>�������� ������ ��� ��� �������� ����� ��������� � �������� �������������� ��������.
        </ol>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $PHPShopGUI->setInfo($info),true), array("� ������", $Tab3), array("������ ��������", null, '?path=modules.dir.atol'));

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