<?php

$TitlePage = __('�������������� �������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel(__("�������������� �������") . ": " . $data['name'], array('�������'), array('���������', '��������� � �������'));

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField('����', $PHPShopGUI->setInputColor('color_new', $data['color']));
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(null, "num_new", $data['num'], '100'));

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
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.order.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.order.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.order.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    if (!empty($_GET['return']))
        header('Location: ?path=' . $_GET['return']);
    else
        header('Location: ?path=' . $_GET['path']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('sklad_action_new', 'cumulative_action_new', 'mail_action_new', 'sms_action_new', 'bot_action_new');

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
