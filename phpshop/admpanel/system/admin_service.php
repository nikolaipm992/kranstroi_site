<?php

$TitlePage = __("������������");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopBase, $hideCatalog, $hideSite, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select();
    $option = unserialize($data['admoption']);

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('service_content');
    $oFCKeditor->Height = '350';
    $oFCKeditor->Value = $option['service_content'];

    // ����� ������������
    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������', $PHPShopGUI->setField("����� ������������", $PHPShopGUI->setCheckbox('option[service_enabled]', 1, '�������� ����� ��������� � ���������� ����������� ����� �� �����', $option['service_enabled'])) .
            $PHPShopGUI->setField('��������� IP ������', $PHPShopGUI->setTextarea('option[service_ip]', $option['service_ip'], false, $width = false, 50), 1, '������� IP ������ ����� �������') .
            $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText(null, 'option[service_title]', $option['service_title'])) .        
            $PHPShopGUI->setField('���������', $oFCKeditor->AddGUI())
    );
    
     $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('������', $PHPShopGUI->setField('��������������� IP ������', $PHPShopGUI->setTextarea('option[block_ip]', $option['block_ip'], false, $width = false, 100), 1, '������� IP ������ ����� �������') 
    );
    
    // Robots.txt
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/robots.txt'))
        $robots = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt');
    else $robots;

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('��������������', $PHPShopGUI->setField('Robots.txt', $PHPShopGUI->setTextarea('service_robots', $robots, false, $width = false, 500))
    );



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
    $PHPShopOrm->updateZeroVars('option.service_enabled');

    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    $option['service_content'] = $_POST['service_content'];
    
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/robots.txt',$_POST['service_robots']);

    $_POST['admoption_new'] = serialize($option);


    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));


    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();
?>