<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
$TitlePage = __('�������������� ��������') . ' #' . $_GET['id'];

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $_POST['password_new'] = base64_encode($_POST['password_new']);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * ����� ����������
 */
function actionSave() {
    global $PHPShopGUI;


    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    
    $PHPShopGUI->action_select['��� ������'] = array(
        'name' => '��� ������ ��������',
        'url' => '?path=modules.dir.partner.log&where[partner_id]=' . $data['id'],
    );
    
   $PHPShopGUI->setActionPanel(__("�������������� ��������"), array('��� ������', '|', '�������'), array('��������� � �������'));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $PHPShopGUI->field_col = 2;
    $Tab1 = $PHPShopGUI->setField('���', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'money_new', (int) $data['money'], 100,$currency));
    $Tab1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'password_new', base64_decode($data['password']), 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox('enabled_new', 1, '�����������', $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('content_new', $data['content'], true, 400, 100));

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>