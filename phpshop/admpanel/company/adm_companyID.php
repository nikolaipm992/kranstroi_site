<?php

$TitlePage = __('�������������� ������������ ����').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $bank = unserialize($data['bank']);
    
    $PHPShopGUI->setActionPanel(__("��������������") .": ". $data['name'], array('�������'), array('���������', '��������� � �������'));
    $PHPShopGUI->field_col = 3;
    
    $forma_value[] = array("�������������� ���������������", 1, $bank['org_forma']);
    $forma_value[] = array("�������� � ������������ ����������������", 2, $bank['org_forma']);

    $Tab1 = $PHPShopGUI->setField("������������ �����������", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("����� �������������", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value,350,true));
    $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
    $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));
    $Tab1 .= $PHPShopGUI->setField("�������� ���", $PHPShopGUI->setInputText(false, 'bank[nds]', intval($bank['nds']), 100, '%'));
    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));
    
    if($bank['org_forma'] == 1)
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

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));


    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.menu.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.menu.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.menu.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
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

    if (is_array($_POST['bank']))
        foreach ($_POST['bank'] as $key => $val)
            $bank[$key] = $val;

    $bank['org_stamp'] = $_POST['bank_org_stamp'];
    $bank['org_sig'] = $_POST['bank_org_sig'];
    $bank['org_sig_buh'] = $_POST['bank_org_sig_buh'];
    $bank['org_logo'] = $_POST['bank_org_logo'];
    $_POST['bank_new'] = serialize($bank);
    
    if (empty($_POST['enabled_new']))
        $_POST['emabled_new'] = 0;
    
    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>