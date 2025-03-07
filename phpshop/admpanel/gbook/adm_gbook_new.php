<?php

$TitlePage = __('�������� ������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['gbook']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules, $TitlePage;

    $PHPShopGUI->field_col = 3;

    // �������
    $data['datas'] = PHPShopDate::get();
    $data['tema'] = __('����� �� ') . $data['datas'];
    $data['name'] = __('�������������');
    $data = $PHPShopGUI->valid($data, 'otvet', 'mail', 'otsiv', 'flag', 'servers');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));

    // datetimepicker
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');


    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('otvet_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['otvet'];

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get(time())));

    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInput("text", "name_new", $data['name']));

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput("text", "mail_new", $data['mail']));

    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setTextarea("tema_new", $data['tema'])) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setTextarea("otsiv_new", $data['otsiv'], "", '100%', '200') . $PHPShopGUI->setAIHelpButton('otsiv_new', 100, 'gbook_review'));

    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("flag_new", 1, null, $data['flag']));

    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('�����', $Tab1);

    // ���������� �������� 2
    $Tab1 .= $PHPShopGUI->setCollapse('�����', $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('otvet_new', 100, 'gbook_answer', 'otsiv_new'));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.gbook.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['datas_new'] = time();

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>