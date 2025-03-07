<?php

$TitlePage = __('�������� ��������');

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules, $result_message, $TitlePage;

    // ����� ����
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // �������
    $data = array();
    $PHPShopGUI->field_col = 3;
    $data = $PHPShopGUI->valid($data,'name','content','servers');

    $PHPShopGUI->action_button['��������� � ���������'] = array(
        'name' => '��������� � ���������',
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-ok'
    );


    $PHPShopGUI->action_select['���������'] = array(
        'name' => '��������� �������������',
        'action' => 'send-user'
    );

    // ��� ������
    if (strlen($data['name']) > 50)
        $title_name = substr($data['name'], 0, 70) . '...';
    else
        $title_name = $data['name'];

    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));

    // �����
    if (!empty($result_message))
        $Tab1 = $PHPShopGUI->setField('�����', $result_message);

    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = $data['content'];

    // ���������� �������� 1
    $Tab1=$PHPShopGUI->setField("����", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));

 
    // �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
    $data_page = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array('limit' => 10));

    $value = array();
    $value[] = array(__('�� ������������'), 0, false);
    if (is_array($data_page))
        foreach ($data_page as $val) {
            $value[] = array($val['zag'] . ' &rarr;  ' . $val['datas'], $val['id'], false);
        }

    $Tab1.=$PHPShopGUI->setField('���������� �� �������', $PHPShopGUI->setSelect('template', $value, '100%', false, false, false, false, false, false));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/','100%'));
    
    $Tab1= $PHPShopGUI->setCollapse('����������',$Tab1);
    
    $Tab1.=$PHPShopGUI->setCollapse("����� ������", $oFCKeditor->AddGUI(). $PHPShopGUI->setAIHelpButton('content_new', 300, 'news_sendmail') . $PHPShopGUI->setHelp('����������: <code>@url@</code> - ����� �����, <code>@user@</code> - ��� ����������, <code>@email@</code> - email ����������, <code>@name@</code> - �������� ��������, <code>@tel@</code> - ������� ��������'));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);
    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true,false,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.news.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// ������� ����������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� �������
    if (!empty($_POST['template'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
        $data = $PHPShopOrm->select(array('*'), array('id' => "=" . intval($_POST['template'])), false, array('limit' => 1));
        if (is_array($data)) {
            $_POST['name_new'] = $data['zag'];
            $_POST['content_new'] = $data['podrob'];
        }
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>