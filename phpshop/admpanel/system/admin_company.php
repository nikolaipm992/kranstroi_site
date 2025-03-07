<?php

$TitlePage = __("���������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideCatalog;

    // �������
    $data = $PHPShopOrm->select();
    $bank = unserialize($data['bank']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    $forma_value[] = array("�������������� ���������������", 1, $bank['org_forma']);
    $forma_value[] = array("�������� � ������������ ����������������", 2, $bank['org_forma']);

    $Tab1 = $PHPShopGUI->setField("�������� ��������", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, "company_new", $data['company']));
    $Tab1 .= $PHPShopGUI->setField("������� ��������", $PHPShopGUI->setInputText(null, "tel_new", $data['tel']));
    $Tab1 .= $PHPShopGUI->setField("������� ��������������", $PHPShopGUI->setInputText(null, "bank[org_tel]", $bank['org_tel']));
    $Tab1 .= $PHPShopGUI->setField("����� ������", $PHPShopGUI->setInputText(null, "bank[org_time]", $bank['org_time']));
    $Tab1 .= $PHPShopGUI->setField("������������ �����������", $PHPShopGUI->setInputText(null, "bank[org_name]", $bank['org_name']));
    $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));

    if (empty($hideCatalog)) {
        $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
        $Tab1 .= $PHPShopGUI->setField("����� �������������", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value, 350, true));
        $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));

        if ($bank['org_forma'] == 1)
            $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
        else {
            $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_kpp]", $bank['org_kpp'], 350));
            $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
        }

        $Tab1 .= $PHPShopGUI->setField("� ����� �����������", $PHPShopGUI->setInputText(null, "bank[org_schet]", $bank['org_schet'], 350));
        $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField("������������ ����", $PHPShopGUI->setInputText(null, "bank[org_bank]", $bank['org_bank'], 350));
        $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_bic]", $bank['org_bic'], 350));
        $Tab1 .= $PHPShopGUI->setField("� ����� �����", $PHPShopGUI->setInputText(null, "bank[org_bank_schet]", $bank['org_bank_schet'], 350));
        $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setIcon($bank['org_stamp'], "bank_org_stamp", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("������� ������������", $PHPShopGUI->setIcon($bank['org_sig'], "bank_org_sig", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("������� ����������", $PHPShopGUI->setIcon($bank['org_sig_buh'], "bank_org_sig_buh", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
        $Tab1 .= $PHPShopGUI->setField("������� ��� �������", $PHPShopGUI->setIcon($bank['org_logo'], "bank_org_logo", false, array('load' => false, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));
    }

    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('���������', $Tab1);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // �����
    $PHPShopGUI->Compile(2);
    return true;
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

    // �������
    $data = $PHPShopOrm->select();
    $bank = unserialize($data['bank']);

    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $bank['org_stamp'] = $_POST['bank_org_stamp'];
    $bank['org_sig'] = $_POST['bank_org_sig'];
    $bank['org_sig_buh'] = $_POST['bank_org_sig_buh'];
    $bank['org_logo'] = $_POST['bank_org_logo'];
    $_POST['bank_new'] = serialize($bank);

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();
?>