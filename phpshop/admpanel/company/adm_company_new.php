<?php

$TitlePage = __('�������� ������������ ����');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopModules;


    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));
    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $data=$bank=[];
    $data = $PHPShopGUI->valid($data,'name');
    $bank =  $PHPShopGUI->valid($bank ,'org_ur_adre','org_adres','nds','org_inn','org_kpp','org_ogrn','org_schet','org_bank','org_bic','org_bank_schet','org_stamp','org_sig','org_sig_buh','org_logo','org_ur_adres');

    $forma_value[] = array("�������������� ���������������", 1, 0);
    $forma_value[] = array("�������� � ������������ ����������������", 2, 2);

    $Tab1 = $PHPShopGUI->setField("������������ �����������", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("����� �������������", $PHPShopGUI->setSelect('bank[org_forma]', $forma_value, 350,true));
    $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_ur_adres]", $bank['org_ur_adres']));
    $Tab1 .= $PHPShopGUI->setField("����������� �����", $PHPShopGUI->setInputText(null, "bank[org_adres]", $bank['org_adres']));
    $Tab1 .= $PHPShopGUI->setField("�������� ���", $PHPShopGUI->setInputText(false, 'bank[nds]', intval($bank['nds']), 100, '%'));
    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_inn]", $bank['org_inn'], 350));
    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, "bank[org_kpp]", $bank['org_kpp'], 350));
    $Tab1 .= $PHPShopGUI->setField("���� / ������", $PHPShopGUI->setInputText(null, "bank[org_ogrn]", $bank['org_ogrn'], 350));
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
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.menu.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

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
    $PHPShopOrm->debug = true;
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);
}

// ��������� ������� 
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>