<?php

$TitlePage = __('�������� ����� ������');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.button.button_forms"));


// ������� ������
function actionInsert() {
    global $PHPShopOrm;
    if(empty($_POST['num_new'])) $_POST['num_new']=1;
    if(empty($_POST['enabled_new'])) $_POST['enabled_new']=0;

    $action = $PHPShopOrm->insert($_POST);
    
    header('Location: ?path=' . $_GET['path']);
    
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm,$PHPShopModules,$PHPShopSystem;
    
    $PHPShopOrmOption = new PHPShopOrm($PHPShopModules->getParam("base.button.button_system"));
    $option = $PHPShopOrmOption->select();

    // �������
    $data['name']=__('����� ������');
    $data['enabled']=1;
    $data['num']=1;
    

    $PHPShopGUI->field_col = 3;
    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'name_new', $data['name']));

    $Tab1.= $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText('�', 'num_new', $data['num'], '100'));
    $Tab1.= $PHPShopGUI->setField('������',  $PHPShopGUI->setCheckbox('enabled_new', 1, null, $data['enabled']));
    
    // �������� 
    if(empty($option['editor']))
        $editor = 'ace';
    else $editor = $PHPShopSystem->getSerilizeParam("admoption.editor");
    
    $PHPShopGUI->setEditor($editor, true);
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '320';
    $oFCKeditor->Value = $data['content'];

    $Tab1.=$PHPShopGUI->setField('����������', $oFCKeditor->AddGUI());

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������",$Tab1,true,false,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter=$PHPShopGUI->setInput("submit","saveID","���������","right",false,false,false,"actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    
    return true;
}


// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>