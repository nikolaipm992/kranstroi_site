<?php

$TitlePage = __('����� ������������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
PHPShopObj::loadClass('user');

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules,$hideCatalog;

    // ��������� ������
    $data['enabled'] = 1;
    

    $data = $PHPShopGUI->valid($data, 'status', 'name', 'login', 'tel', 'dialog_ban', 'cumulative_discount', 'data_adres');

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������', '������� � �������������'));
    $PHPShopGUI->addJSFiles('./js/validator.js');

    // ������� �������������
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    $user_status_value[] = array(__('������������'), 0, $data['status']);
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $data['status']);

    $pasgen = substr(md5(date("U")), 0, 8);

    if (empty($data['servers']))
        $data['servers'] = 1000;

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField("���", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput('email.required.6', "login_new", $data['login'])) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setInput('tel', "tel_new", $data['tel'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInput("password.required.6", "password_new", '', null, false, false, false, false, false, '<a href="#" class="password-gen" data-password="u' . $pasgen . '" data-text="' . __('������������ ������: ') . '"  data-toggle="tooltip" data-placement="top" title="' . __('������������� ������') . '"><span class="glyphicon glyphicon-cog"></span></a>')) .
            $PHPShopGUI->setField("������������� ������", $PHPShopGUI->setInput("password.required.4", "password2_new", null)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']) . '<br>' . $PHPShopGUI->setCheckbox('sendActivationEmail', 1, '���������� ������������', 0)) .
            $PHPShopGUI->setField("���������� ��������", $PHPShopGUI->setCheckbox("dialog_ban_new", 1, null, $data['dialog_ban'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setSelect('status_new', $user_status_value,300)) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', 300, false)) .
            $PHPShopGUI->setField("������������� ������", $PHPShopGUI->setInput('text', "cumulative_discount_new", $data['cumulative_discount'], null, 100, false, false, false, '%'))
    );

    // ������ ��������
    if (empty($hideCatalog))
    $Tab2 = $PHPShopGUI->loadLib('tab_addres', $data['data_adres']);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true), array("�������� � ���������", $Tab2, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.shopusers.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    $_POST['password_new'] = base64_encode($_POST['password_new']);
    $_POST['mail_new'] = $_POST['login_new'];
    $_POST['bot_new'] = md5($_POST['login_new'] . time());

    if (is_array($_POST['mass']))
        foreach ($_POST['mass'] as $k => $v) {

            // ��������� windows 1251
            $mass_decode[$k] = @array_map("urldecode", $v);

            // ���������� ��������
            if (!empty($_POST['mass'][$k]['default']))
                $_POST['data_adres_new']['main'] = $k;

            if (!empty($_POST['mass'][$k]['delete']))
                unset($mass_decode[$k]);
        }

    if (!empty($mass_decode))
        $_POST['data_adres_new']['list'] = $mass_decode;

    if (is_array($_POST['data_adres_new']))
        $_POST['data_adres_new'] = serialize($_POST['data_adres_new']);

    if ($_POST['servers_new'] == 1000)
        $_POST['servers_new'] = 0;


    // ���������� ������������
    if (!empty($_POST['enabled_new']) and ! empty($_POST['sendActivationEmail'])) {

        PHPShopObj::loadClass("parser");
        PHPShopObj::loadClass("mail");

        PHPShopParser::set('user_name', $_POST['name_new']);
        PHPShopParser::set('login', $_POST['login_new']);
        PHPShopParser::set('password', $_POST['password2_new']);

        $zag_adm = __("��� ������� ��� ������� ����������� ���������������");
        $PHPShopMail = new PHPShopMail($_POST['login_new'], $PHPShopSystem->getEmail(), $zag_adm, '', true, true);
        $content_adm = PHPShopParser::file('../lib/templates/users/mail_user_activation_by_admin_success.tpl', true);

        if (!empty($content_adm)) {
            $PHPShopMail->sendMailNow($content_adm);
        }
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == '������� � �������������')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=' . $_GET['path']);

    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>