<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.errorlog.errorlog_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ������� �������
function actionClean() {
    global $PHPShopModules;
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.errorlog.errorlog_log")); 
    $action = $PHPShopOrm->delete(array('id' => '>0'));
    header('Location: ?path=modules.dir.errorlog');
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->action_button['��������'] = array(
        'name' => __('�������� ������'),
        'action' => 'cleanID',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('��������', '��������� � �������'));

    // �������
    $data = $PHPShopOrm->select();

    switch ($data['enabled']) {
        case 0: $enabled_chek_0 = 'selected';
            break;
        case 1: $enabled_chek_1 = 'selected';
            break;
        default: $enabled_chek_2 = 'selected';
    }

    $option[] = array('�� ���������� ������', 0, $enabled_chek_0);
    $option[] = array('���������� ������ ������', 1, $enabled_chek_1);
    $option[] = array('���������� ������ � �������', 2, $enabled_chek_2);
    $Tab1 = $PHPShopGUI->setField('��� ������', $PHPShopGUI->setSelect('enabled_new', $option,250,true));

    $Info = '��� �������� ���������������� ���������� ���������� � ����� ��� ���������� ������� ��������� ��� � ����� ������� ����� �������:
        <p><code>trigger_error("����� �������", E_USER_NOTICE);</code></p>
        �� ������������� ������� ��� ����� ���������� ������, ��� ��� �� ����������� ���� ������ ���������.
';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // ���������� �������� 2
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2, true), array("� ������", $Tab3), array("������ �������", 0, '?path=modules.dir.errorlog'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "cleanID", "���������", "right", 80, "", "but", "actionClean.modules.edit");
    ;

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>