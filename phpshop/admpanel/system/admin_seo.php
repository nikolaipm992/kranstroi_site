<?php

$TitlePage = __("SEO ���������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideSite;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './system/gui/system.gui.js', './system/gui/headers.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('�������� ��������� (Title)', $PHPShopGUI->setTextarea('title_new', $data['title'], false, false, 100) . $PHPShopGUI->setAIHelpButton('title_new', 200, 'site_title'));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('�������� �������� (Description)', $PHPShopGUI->setTextarea('descrip_new', $data['descrip'], false, false, 100). $PHPShopGUI->setAIHelpButton('descrip_new', 200, 'site_descrip'));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('�������� �������� ����� (Keywords)', $PHPShopGUI->setTextarea('keywords_new', $data['keywords'], false, false, 100));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("��������� �����", $PHPShopGUI->setCheckbox('option[safe_links]', 1, '���������� ����������� ������ �� ������ ������� ��� ����������� ������ 404 ������', $option['safe_links']));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("��������� HSTS", $PHPShopGUI->setCheckbox('option[hsts]', 1, '�������� ����� ������ �� ��������� HTTPS', $option['hsts']));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("������ ������", $PHPShopGUI->setCheckbox('option[min]', 1, '������ JS � CSS ������ ��� ���������� ���� �������', $option['min']));
    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('��������', $PHPShopGUI->_CODE);

    if (empty($hideSite)) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ��������', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'catalog'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ �����������', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'podcatalog'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ������', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'product'));
        $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������ ������� � ��������', $PHPShopGUI->loadLib('tab_headers', $data, './system/', 'sort'));
    }

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
    $option = unserialize($data['admoption']);

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('option.safe_links', 'option.hsts', 'option.min');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;


    $_POST['admoption_new'] = serialize($option);


    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();
?>