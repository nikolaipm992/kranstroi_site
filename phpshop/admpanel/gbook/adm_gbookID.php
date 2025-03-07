<?php

$TitlePage = __('�������������� ������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['gbook']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    $PHPShopGUI->field_col = 3;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // datetimepicker
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->action_select['������������'] = array(
        'name' => '������������',
        'url' => '../../gbook/ID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank'
    );

    $PHPShopGUI->setActionPanel(__("�������������� ������ ��") . " " . $data['name'], array('������������', '|', '�������'), array('���������', '��������� � �������'));

    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('otvet_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['otvet'];

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get($data['datas'])));

    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInput("text", "name_new", $data['name']));

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput("text", "mail_new", $data['mail']));

    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setTextarea("tema_new", $data['tema'])) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setTextarea("otsiv_new", $data['otsiv'], "", '100%', '200') . $PHPShopGUI->setAIHelpButton('otsiv_new', 100, 'gbook_review'));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("flag_new", 1, null, $data['flag']));

    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('�����', $Tab1);

    // ���������� �������� 2
    $Tab1 .= $PHPShopGUI->setCollapse('�����', $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('otvet_new', 200, 'gbook_answer', 'otsiv_new'));


    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.gbook.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.gbook.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.gbook.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� �������� �����
function sendMail($name, $mail) {
    global $PHPShopSystem, $PHPShopBase;

    // ���������� ���������� �������� �����
    PHPShopObj::loadClass("mail");

    $zag = __("��� ����� �������� �� ����") . " " . $PHPShopSystem->getValue('name');
    $message = __("���������") . " " . $name . ",

" . __("��� ����� �������� �� ���� �� ������") . ": http://" . $_SERVER['SERVER_NAME'] . $PHPShopBase->getParam('dir.dir') . "/gbook/

" . __("������� �� ����������� �������.");
    new PHPShopMail($mail, $PHPShopSystem->getEmail(), $zag, $message);
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('flag_new');

    if (empty($_POST['ajax'])) {
        $_POST['datas_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);
    }
    if (empty($_POST['flag_new']))
        $_POST['flag_new'] = 0;
    else if (!empty($_POST['mail_new']))
        sendMail($_POST['name_new'], $_POST['mail_new']);

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }


    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>