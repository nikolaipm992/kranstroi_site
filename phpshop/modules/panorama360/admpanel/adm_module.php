<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.panorama360.panorama360_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);


    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->field_col = 2;

    // �������
    $data = $PHPShopOrm->select();
    
    if(empty($data['frame']))
        $data['frame']=28;
    
    $Tab1 = $PHPShopGUI->setField('������� � ��������', $PHPShopGUI->setInputText(false, 'frame_new', $data['frame'],50));

    $info = '����������� ������ ('.$data['frame'].' ��.) ������ ���� ������� � ����� ������ (<a href="../modules/panorama360/sample/sample.jpg" target="_blank" >������ �������</a>) � ��������� � �������� ������ ����� �������� <kbd>��������</kbd>. <br>'
            . '� ������ ��������� �������� ������ ���������� ���������� ���������� <code>@panorama360@</code> � ������� ��� �� ������ �����.';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true),array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� ������� 
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>