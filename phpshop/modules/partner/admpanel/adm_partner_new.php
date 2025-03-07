<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
$TitlePage = __('�������� ��������');


// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem,$TitlePage;
    
    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));


    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $PHPShopGUI->field_col = 2;
    $Tab1 = $PHPShopGUI->setField('���', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'money_new', 0, 100,$currency));
    $Tab1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'password_new', base64_decode($data['password']), 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox('enabled_new', 1, '�����������', 1));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('content_new', $data['content'], true, 400, 100));

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.news.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionInsert() {
    global $PHPShopOrm;
    
    $_POST['date_new'] =  date("d-m-y");

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>