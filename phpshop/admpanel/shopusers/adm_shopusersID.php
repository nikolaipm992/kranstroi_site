<?php

$TitlePage = __('�������������� ������������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
PHPShopObj::loadClass('user');

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem, $hideCatalog;
    
    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_select['������� �����'] = array(
        'name' => '������� �����',
        'url' => '?path=order&action=new&user=' . $data['id'],
        'class'=> $hideCatalog
    );

    $PHPShopGUI->action_select['������ ������������'] = array(
        'name' => '������ ������������',
        'url' => '?path=order&where[a.user]=' . $data['id'],
        'class'=> $hideCatalog
    );

    $PHPShopGUI->action_select['������� ������������'] = array(
        'name' => '������� ������������',
        'url' => '?path=dialog&uid=' . $data['id']
    );

    $PHPShopGUI->action_select['��������� ������'] = array(
        'name' => '��������� ������',
        'url' => 'mailto:' . $data['login']
    );

    $PHPShopGUI->action_select['������� ������'] = array(
        'name' => '������� ������',
        'url' => '?path=dialog&new&user=' . $data['id'] . '&bot=message&id=' . $data['id'] . '&return=dialog'
    );

    // ������.�����
    $yandex_apikey = $PHPShopSystem->getSerilizeParam("admoption.yandex_apikey");
    if (empty($yandex_apikey))
        $yandex_apikey = 'cb432a8b-21b9-4444-a0c4-3475b674a958';

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel(__("������������") . '<span class="hidden-xs">: ' . $data['name'] . '</span>', array('������� �����', '������ ������������', '������� ������������', '������� ������', '|', '�������'), array('���������', '��������� � �������'));
    $PHPShopGUI->addJSFiles('./js/validator.js');

    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions_utf.min.js', './order/gui/dadata.gui.js');
    else
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions.min.js', './order/gui/dadata.gui.js');

    $PHPShopGUI->addCSSFiles('./css/suggestions.min.css');

    // ������� �������������
    $PHPShopUserStatus = new PHPShopUserStatusArray();
    $PHPShopUserStatusArray = $PHPShopUserStatus->getArray();
    $user_status_value[] = array(__('������������'), 0, $data['status']);
    if (is_array($PHPShopUserStatusArray))
        foreach ($PHPShopUserStatusArray as $user_status)
            $user_status_value[] = array($user_status['name'], $user_status['id'], $data['status']);

    if (empty($data['servers']))
        $data['servers'] = 1000;

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField("���", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput('email.required.6', "login_new", $data['login'])) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setInput('tel', "tel_new", $data['tel'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInput("password.required.4", "password_new", base64_decode($data['password']), null, false, false, false, false, false, '<a href="#" class="password-view"  data-toggle="tooltip" data-placement="top" title="' . __('�������� ������') . '"><span class="glyphicon glyphicon-eye-open"></span></a>')) .
            $PHPShopGUI->setField("������������� ������", $PHPShopGUI->setInput("password.required.4", "password2_new", base64_decode($data['password']))) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']) . '<br>' . $PHPShopGUI->setCheckbox('sendActivationEmail', 1, '���������� ������������', 0)) .
            $PHPShopGUI->setField("���������� ��������", $PHPShopGUI->setCheckbox("dialog_ban_new", 1, null, $data['dialog_ban'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setSelect('status_new', $user_status_value, 300)) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', 300, false)) .
            $PHPShopGUI->setField("������������� ������", $PHPShopGUI->setInput('text', "cumulative_discount_new", $data['cumulative_discount'], null, 100, false, false, false, '%'))
    );
    

    // ������ ��������
    if (empty($hideCatalog)) {
        $Tab2 = $PHPShopGUI->loadLib('tab_addres', $data['data_adres']);

        // ������
        $Tab3 = $PHPShopGUI->loadLib('tab_bonus', $data['id']);


        // ������
        $_GET['user'] = $data['id'];
        $tab_order = $PHPShopGUI->loadLib('tab_order', false, './dialog/');
        if (!empty($tab_order))
            $sidebarright[] = array('title' => '������', 'content' => $tab_order);

        // �������
        $tab_cart = $PHPShopGUI->loadLib('tab_cart', false, './dialog/');
        if (!empty($tab_cart))
            $sidebarright[] = array('title' => '�������', 'content' => $tab_cart);
    }

    // �������
    $_GET['user_id'] = $data['id'];
    $tab_dialog = $PHPShopGUI->loadLib('tab_dialog', false, './dialog/');

    if (!empty($tab_dialog))
        $sidebarright[] = array('title' => '�������', 'content' => $tab_dialog);
    
    // ������
    $tab_comment = $PHPShopGUI->loadLib('tab_comment',false);

     if (!empty($tab_comment))
        $sidebarright[] = array('title' => '������', 'content' => $tab_comment);
    
    // �����
    $mass = unserialize($data['data_adres']);
    if ($PHPShopSystem->ifSerilizeParam('admoption.yandexmap_enabled')) {
        if (!empty($mass['main']) and ! empty($mass['list'][$mass['main']]['street_new'])) {
            $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js', '//api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU&apikey=' . $yandex_apikey);
            $map = '<div id="map" data-geocode="' . $mass['list'][$mass['main']]['city_new'] . ', ' . $mass['list'][$mass['main']]['street_new'] . ' ' . $mass['list'][$mass['main']]['house_new'] . '" style="width: 280px;height:280px;"></div>';

            $sidebarright[] = array('title' => '����� �������� �� �����', 'content' => array($map));
        }
    }

    // ������ �������
    if (!empty($sidebarright) and empty($hideCatalog)) {
        $PHPShopGUI->setSidebarRight($sidebarright, 3, 'hidden-xs');
        $PHPShopGUI->sidebarLeftRight = 3;
    }

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ������
    if ($PHPShopSystem->getSerilizeParam('admoption.bonus') > 0)
        $PHPShopGUI->addTabSeparate(array("������ <span class=badge>" . $data['bonus'] . "</span>", $Tab3, true));

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("�������� � ���������", $Tab2, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "bonus_new", $data['bonus']) .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.shopusers.edit");

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
    return array("success" => $action);
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
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

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

    if ($_POST['servers_new'] == 1000)
        $_POST['servers_new'] = 0;

    $_POST['mail_new'] = $_POST['login_new'];

    // ���������� ������������
    if (!empty($_POST['enabled_new']) and ! empty($_POST['sendActivationEmail'])) {

        PHPShopObj::loadClass("parser");
        PHPShopObj::loadClass("mail");

        PHPShopParser::set('user_name', $_POST['name_new']);
        PHPShopParser::set('login', $_POST['login_new']);
        PHPShopParser::set('password', $_POST['password_new']);

        $zag_adm = __("��� ������� ��� ������� ����������� ���������������");
        $PHPShopMail = new PHPShopMail($_POST['login_new'], $PHPShopSystem->getParam('adminmail2'), $zag_adm, '', true, true);
        $content_adm = PHPShopParser::file('../lib/templates/users/mail_user_activation_by_admin_success.tpl', true);

        if (!empty($content_adm)) {
            $PHPShopMail->sendMailNow($content_adm);
        }
    }

    if (!empty($mass_decode))
        $_POST['data_adres_new']['list'] = $mass_decode;

    if (is_array($_POST['data_adres_new']))
        $_POST['data_adres_new'] = serialize($_POST['data_adres_new']);

    if (!empty($_POST['password_new']))
        $_POST['password_new'] = base64_encode($_POST['password_new']);

    // ������
    if (!empty($_POST['comment_new'])) {

        $PHPShopOrm->query("
	INSERT INTO `" . $GLOBALS['SysValue']['base']['bonus'] . "` 
	(`date`, `comment`, `user_id`, `bonus_operation`) VALUES 
	('" . time() . "','" . $_POST['comment_new'] . "','" . $_POST['rowID'] . "','" . intval($_POST['bonus_operation_new']) . "')");

        if (intval($_POST['bonus_operation_new']) != 0) {
            $_POST['bonus_new'] = $_POST['bonus_operation_new'] + $_POST['bonus_new'];
        }
    }

    // ������������� ������ ��������
    if (empty($_POST['ajax']))
        $PHPShopOrm->updateZeroVars('enabled_new', 'dialog_ban_new');

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>