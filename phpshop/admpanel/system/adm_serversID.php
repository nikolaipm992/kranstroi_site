<?php

$TitlePage = __('�������������� �������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);

// ����� ������� �������
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value);
}

// ����� �����
function GetLocaleList($skin) {
    global $PHPShopGUI;
    $dir = "../locale/";

    $locale_array = array(
        'russian' => __('�������'),
        'ukrainian' => __('����������'),
        'belarusian' => __('��������'),
        'english' => __('English')
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                $name = $locale_array[$file];
                if (empty($name))
                    $name = $file;

                if ($skin == $file)
                    $sel = "selected";
                else
                    $sel = "";

                if ($file != "." and $file != ".." and ! strpos($file, '.'))
                    $value[] = array($name, $file, $sel, 'data-content="<img src=\'' . $dir . '/' . $file . '/icon.png\'/> ' . $name . '"');
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('lang_new', $value);
}

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem, $PHPShopBase,$hideCatalog;

    PHPShopObj::loadClass(array('valuta', 'user'));

    $PHPShopGUI->field_col = 2;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $option = unserialize($data['admoption']);

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->setActionPanel(__("�������������� �������") . ": " . $data['name'] . ' [ID ' . intval($_GET['id']) . ']', array('�������'), array('���������', '��������� � �������'));

    $shop_type_value[] = array('��������-�������', 0, $data['shop_type']);
    $shop_type_value[] = array('������� ���������', 1, $data['shop_type']);
    $shop_type_value[] = array('���� ��������', 2, $data['shop_type']);

    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText('https://', "host_new", $data['host']));
    $Tab1 .= $PHPShopGUI->setField(
            array("������� ��������", "������� ��������������"), array($PHPShopGUI->setInputText(null, "tel_new", $data['tel']),
        $PHPShopGUI->setInputText(null, "option[org_tel]", $option['org_tel'])), array(array(2, 4), array(2, 4)));
    $Tab1 .= $PHPShopGUI->setField(
            array("����� ������", "�����"), array($PHPShopGUI->setInputText(null, "option[org_time]", $option['org_time']),
        $PHPShopGUI->setInputText(null, "option[org_adres]", $option['org_adres'])), array(array(2, 4), array(2, 4)));
    $Tab1 .= $PHPShopGUI->setField(
            array("������������", "E-mail ����������"), array($PHPShopGUI->setSelect('shop_type_new', $shop_type_value,'100%',true),
        $PHPShopGUI->setInputText(null, "adminmail_new", $data['adminmail'])), array(array(2, 4), array(2, 4)));
    
   
    $Tab1 .= $PHPShopGUI->setField(
            array("SMTP ������������", "������"), array($PHPShopGUI->setInputText(null, "option[smtp_user]", $option['smtp_user'], false, false, false, false, 'user@yandex.ru'),
        $PHPShopGUI->setInput('password', "option[smtp_password]", $option['smtp_password'])), array(array(2, 4), array(2, 4)));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField(array("�������", "Favicon"), array($PHPShopGUI->setIcon($data['logo'], "logo_new", false), $PHPShopGUI->setIcon($data['icon'], "icon_new", false, array('load' => false, 'server' => true, 'url' => true, 'multi' => false, 'view' => false))), array(array(2, 4), array(2, 4)));
    $Tab1 .= $PHPShopGUI->setField('��������� (Title)', $PHPShopGUI->setTextarea('title_new', $data['title'], false, false, 100));
    $Tab1 .= $PHPShopGUI->setField('�������� (Description)', $PHPShopGUI->setTextarea('descrip_new', $data['descrip'], false, false, 100));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $currency_value[] = array($val['name'], $val['id'], $data['currency']);
        }

    if (empty($data['skin']))
        $data['skin'] = $PHPShopSystem->getParam('skin');

    if (empty($data['lang']))
        $data['lang'] = $PHPShopSystem->getSerilizeParam('admoption.lang');

    $Tab2 .= $PHPShopGUI->setField(array('������', '������', '����'), array($PHPShopGUI->setSelect('currency_new', $currency_value,false,false,false,false,$hideCatalog), GetSkinList($data['skin']), GetLocaleList($data['lang'])), array(array(2, 2), array(1, 2), array(2, 2)));

    $sql_value[] = array('�� �������', 0, 0);
    $sql_value[] = array('�������� ������ �������', 'on', 1);
    $sql_value[] = array('��������� ������ �������', 'off', 1);
    $sql_value[] = array('�������� ��� ��������', 1, 0);
    $sql_value[] = array('��������� ��� ��������', 2, 0);
    $sql_value[] = array('�������� ��� ��������', 3, 0);
    $sql_value[] = array('��������� ��� ��������', 4, 0);
    $sql_value[] = array('�������� ��� ����', 5, 0);
    $sql_value[] = array('��������� ��� ����', 6, 0);
    $sql_value[] = array('�������� ��� ��������', 7, 0);
    $sql_value[] = array('��������� ��� ��������', 8, 0);
    $sql_value[] = array('�������� ��� �������', 9, 0);
    $sql_value[] = array('��������� ��� �������', 10, 0);
    $sql_value[] = array('�������� ��� ��������', 11, 0);
    $sql_value[] = array('��������� ��� ��������', 12, 0);

    // ������
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    $warehouse_value[] = array('����� �����', 0, $data['warehouse']);
    if (is_array($dataWarehouse)) {
        foreach ($dataWarehouse as $val) {
            $warehouse_value[] = array($val['name'], $val['id'], $data['warehouse']);
        }
    }

    // ���������
    if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data_manager = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'id' => '!=' . $_SESSION['idPHPSHOP']), array('order' => 'id DESC'), array('limit' => 100));
        $manager_status_value[] = array(__('��� ���������'), '', '');
        if (is_array($data_manager))
            foreach ($data_manager as $manager_status)
                $manager_status_value[] = array($manager_status['name'], $manager_status['id'], $data['admin']);
    }

    $Tab2 .= $PHPShopGUI->setField(array("�������� ���������", '�����', '������� ���'), array($PHPShopGUI->setSelect('sql', $sql_value, false, true), $PHPShopGUI->setSelect('admin_new', $manager_status_value, false, true), $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5),false,false,false,false,$hideCatalog)), array(array(2, 2), array(1, 2), array(2, 2)));

    // �������
    $PHPShopUserStatusArray = new PHPShopUserStatusArray();
    $userstatus_array = $PHPShopUserStatusArray->getArray();

    $userstatus_value[] = array(__('�������������� ������������'), 0, $option['user_status']);
    if (is_array($userstatus_array))
        foreach ($userstatus_array as $val) {
            $userstatus_value[] = array($val['name'], $val['id'], $option['user_status']);
        }

    $Tab2 .= $PHPShopGUI->setField("����������� �������������", $PHPShopGUI->setCheckbox('option[user_mail_activate]', 1, '��������� ����� E-mail', $option['user_mail_activate']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_mail_activate_pre]', 1, '������ ��������� ���������������', $option['user_mail_activate_pre']) . '<br>' . $PHPShopGUI->setCheckbox('option[user_price_activate]', 1, '����������� ��� ��������� ���', $option['user_price_activate'],$hideCatalog)) . $PHPShopGUI->setField("������ ����� �����������", $PHPShopGUI->setSelect('option[user_status]', $userstatus_value));

    // ����������� ����
    $PHPShopCompany = new PHPShopCompanyArray();
    $PHPShopCompanyArray = $PHPShopCompany->getArray();
    $company_value[] = array($PHPShopSystem->getSerilizeParam("bank.org_name"), 0, $data['company_id']);
    if (is_array($PHPShopCompanyArray))
        foreach ($PHPShopCompanyArray as $company)
            $company_value[] = array($company['name'], $company['id'], $data['company_id']);

    $Tab2 .= $PHPShopGUI->setField("����������� ����", $PHPShopGUI->setSelect('company_id_new', $company_value));
    $Tab2 .= $PHPShopGUI->setField("�������� ����", $PHPShopGUI->setInputText(false, 'option[fee]', $option['fee'], 100, '%'),1,null,$hideCatalog);

    $Tab2 = $PHPShopGUI->setCollapse("�������������", $Tab2);

    $Tab2 .= $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('���������� ���������', $PHPShopGUI->setField('ID ����� ������.�������', $PHPShopGUI->setInputText(null, 'option[metrica_id]', $option['metrica_id'], 230, false, false, false, 'XXXXXXXX')) . $PHPShopGUI->setField('ID ����� Google', $PHPShopGUI->setInputText('UA-', 'option[google_id]', $option['google_id'], 230, false, false, false, 'XXXXX-Y')), 'in', true
    );

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    $Tab1 = $PHPShopGUI->setCollapse("��������", $Tab1);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("�������������", $Tab2, true), array("����������", $PHPShopGUI->loadLib('tab_showcase', false, './system/')));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.servers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.servers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.servers.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ���������� ����������� 
function iconAdd($name = 'icon_new') {

    // ����� ����������
    $path = '/UserFiles/Image/';

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST[$name];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST[$name])) {
        $file = $_POST[$name];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// �������� �������
function serversClean() {

    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['categories'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['news'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['page'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['menu'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['page_categories'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['modules'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['slider'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
    $PHPShopOrm->query('update ' . $GLOBALS['SysValue']['base']['payment_systems'] . ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")');
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� �������
    serversClean();

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
    global $PHPShopOrm, $PHPShopModules, $PHPShopBase;

    if (!empty($_POST['host_new'])) {
        $License = @parse_ini_file_true("../../license/" . PHPShopFile::searchFile("../../license/", 'getLicense'), 1);
        $_POST['code_new'] = md5($License['License']['Serial'] . $License['License']['DomenLocked'] . $_POST['host_new'] . $PHPShopBase->getParam("connect.host") . $PHPShopBase->getParam("connect.user_db") . $PHPShopBase->getParam("connect.pass_db"));
    }

    if (empty($_POST['ajax'])) {

        // �������
        $_POST['logo_new'] = iconAdd('logo_new');

        // ������������� ������ ��������
        $PHPShopOrm->updateZeroVars('option.user_mail_activate', 'option.user_mail_activate_pre', 'option.user_price_activate');

        if (is_array($_POST['option']))
            foreach ($_POST['option'] as $key => $val)
                $option[$key] = $val;

        $_POST['admoption_new'] = serialize($option);
        $_POST['host_new'] = trim(mb_strtolower($_POST['host_new'], 'windows-1251'));
    }


    // �������
    $set_on = ' set `servers`=CONCAT("i' . $_POST['rowID'] . 'ii1000i", `servers` )';
    $set_off = ' set `servers`=REPLACE(`servers`,"i' . $_POST['rowID'] . 'i",  "")';
    switch ($_POST['sql']) {

        case 1:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['categories'] . $set_on);
            break;

        case 2:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['categories'] . $set_off);
            break;

        case 3:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['page'] . $set_on);
            break;

        case 4:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['page'] . $set_off);
            break;
        case 5:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['menu'] . $set_on);
            break;

        case 6:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['menu'] . $set_off);
            break;

        case 7:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['slider'] . $set_on);
            break;

        case 8:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['slider'] . $set_off);
            break;

        case 9:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['news'] . $set_on);
            break;

        case 10:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['news'] . $set_off);
            break;

        case 11:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['delivery'] . $set_on);
            break;

        case 12:
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['delivery'] . $set_off);
            break;

        case "on":
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['categories'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['page'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['menu'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['slider'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['news'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['delivery'] . $set_on);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['payment_systems'] . $set_on);
            break;

        case "off":
            $PHPShopOrmCat = new $PHPShopOrm();
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['categories'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['page'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['menu'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['slider'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['news'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['delivery'] . $set_off);
            $PHPShopOrmCat->query('update ' . $GLOBALS['SysValue']['base']['payment_systems'] . $set_off);
            break;
    }

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
