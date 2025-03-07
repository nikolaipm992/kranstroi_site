<?php

$TitlePage = __('�������� �������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopSystem, $TitlePage;

    // ��������� ������
    $data['name'] = __('����� ������');
    $data['color'] = '#000000';
    $data['mail_action'] = 1;


    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('������� � �������������', '��������� � �������'));


    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField('����', $PHPShopGUI->setInputColor('color_new', $data['color']));
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(null, "num_new", intval($data['num']), '100'));

    $Tab1 .= $PHPShopGUI->setField("�������������", $PHPShopGUI->setCheckbox('mail_action_new', 1, 'Email �����������', $data['mail_action']) . '<br>' .
            $PHPShopGUI->setCheckbox('sms_action_new', 1, 'SMS �����������', $data['sms_action']) . '<br>' .
            $PHPShopGUI->setCheckbox('bot_action_new', 1, '����������� � �����������', $data['bot_action']) . '<br>' .
            $PHPShopGUI->setCheckbox("sklad_action_new", 1, "�������� �� ������ ������� � ������", $data['sklad_action']) . '<br>' .
            $PHPShopGUI->setCheckbox("cumulative_action_new", 1, "���� ������ ����������", $data['cumulative_action']) . $PHPShopGUI->setHelp(__('����� ������ ������������ ����� ��������� � ������������� �����, ��������� �') . ' <a href="?path=shopusers.status"><span class="glyphicon glyphicon-share-alt"></span>' . __('�������� � ������� �����������') . '</a>', false, false)
    );

    // ������� ���
    $Tab1 .= $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField('������� ���', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    // ���������
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('mail_message_new');
    $oFCKeditor->Height = '350';
    $oFCKeditor->Value = $data['mail_message'];

    $Tab1 .= $PHPShopGUI->setCollapse("����� ������", $oFCKeditor->AddGUI() . $PHPShopGUI->setHelp('����������: <code>@ouid@</code> - ����� ������, <code>@date@</code> - ���� ������, <code>@status@</code> - ����� ������ ������, <code>@fio@</code> - ��� ����������, <code>@sum@</code> - ��������� ������, <code>@manager@</code> - ����������, <code>@tracking@</code> - ����� ��� ������������, <code>@account@</code> - ������ �� ����, <code>@bonus@</code> - ����������� ������ �� �����'));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.order.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;


    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == '������� � �������������')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else if (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);

    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
