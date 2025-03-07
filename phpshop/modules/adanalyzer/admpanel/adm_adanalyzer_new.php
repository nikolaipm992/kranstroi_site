<?php

$TitlePage = __('�������� ����� ��������');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.adanalyzer.adanalyzer_campaign"));


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
    global $PHPShopGUI,$PHPShopOrm;


    // �������
    $data['name']=__('����� ��������');
    $data['enabled']=1;
    $data['num']=1;
    

    $PHPShopGUI->field_col = 1;
    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'name_new', $data['name']));
    $Tab1.= $PHPShopGUI->setField('UTM-�����', $PHPShopGUI->setInputText('utm_campaign=', 'utm_new', $data['utm']));
    $Tab1.= $PHPShopGUI->setField('���������', $PHPShopGUI->setInputText('�', 'num_new', $data['num'], '100') .
            $PHPShopGUI->setCheckbox('enabled_new', 1, '���.', $data['enabled']));

    $Tab1.=$PHPShopGUI->setField('��������', $PHPShopGUI->setTextarea('content_new',$data['content']));

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������",$Tab1,true));

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