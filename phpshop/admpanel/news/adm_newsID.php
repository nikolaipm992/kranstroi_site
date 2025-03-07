<?php

$TitlePage = __('�������������� �������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    // ����� ����
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './js/jquery.waypoints.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }


    $PHPShopGUI->action_select['���������'] = array(
        'name' => '��������� �������������',
        'action' => 'send-user'
    );

    $PHPShopGUI->action_select['������������'] = array(
        'name' => '������������',
        'url' => '../../news/ID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank',
        'class' => $GLOBALS['isFrame']
    );

    $PHPShopGUI->setActionPanel(__("�������������� ������� ��") . " " . $data['datas'], array('������������', '|', '�������'), array('���������', '��������� � �������'));

    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('kratko_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = $data['kratko'];

    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("datas_new", $data['datas'])) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setInput("text", "zag_new", $data['zag']));

    if (empty($data['date_start']))
        $data['date_start'] = $data['datas'];

    $Tab1 .= $PHPShopGUI->setField("������ ������", $PHPShopGUI->setInputDate("datau_new", PHPShopDate::get($data['datau'])));

    // ������
    $Tab2 .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setIcon($data['icon'], "icon_new", false));
    $Tab2 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    // ������������� ������
    if(empty($hideSite))
    $Tab1 .= $PHPShopGUI->setField('������������� ������', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, 00, __('������� ID ������� ��� ��������������') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>'));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse("�����", $oFCKeditor->AddGUI().$PHPShopGUI->setAIHelpButton('kratko_new',300,'news_description'));

    // �������� 2
    $oFCKeditor2 = new Editor('podrob_new');
    $oFCKeditor2->Height = '470';
    $oFCKeditor2->Value = $data['podrob'];

    $Tab1 .= $PHPShopGUI->setCollapse('�������������', $Tab2);
    $Tab1 .= $PHPShopGUI->setCollapse("��������", '<div>' . $oFCKeditor2->AddGUI().$PHPShopGUI->setAIHelpButton('podrob_new',300,'news_content') . '</div>');

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));


    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.news.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.news.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.news.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
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

    if (!empty($_POST['datau_new']))
        $_POST['datau_new'] = PHPShopDate::GetUnixTime($_POST['datau_new']);
    else
        $_POST['datau_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);

    $_POST['icon_new'] = iconAdd();

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// ���������� ����������� 
function iconAdd() {
    global $PHPShopSystem;

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_new'];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST['icon_new'])) {
        $file = $_POST['icon_new'];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>