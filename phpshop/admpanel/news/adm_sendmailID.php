<?php

$TitlePage = __('�������������� ��������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules, $result_message;

    // ����� ����
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    // �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->field_col = 3;


    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_button['��������� � ���������'] = array(
        'name' => __('��������� � ���������'),
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-ok'
    );


    // ��� ������
    if (strlen($data['name']) > 50)
        $title_name = substr($data['name'], 0, 70) . '...';
    else
        $title_name = $data['name'];

    $PHPShopGUI->setActionPanel(__("��������") . " " . $title_name, array('�������'), array('���������', '��������� � ���������'));

    // �����
    if (!empty($result_message))
        $Tab1 = $PHPShopGUI->setField('�����', $result_message);

    // �������� 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = $data['content'];

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));

    // �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
    $data_page = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array('limit' => 10));

    $value = array();
    $value[] = array(__('�� ������������'), 0, false);
    if (is_array($data_page))
        foreach ($data_page as $val) {
            $value[] = array($val['zag'] . ' &rarr;  ' . $val['datas'], $val['id'], false);
        }

    $Tab1 .= $PHPShopGUI->setField('���������� �� �������', $PHPShopGUI->setSelect('template', $value, '100%', false, false, false, false, false, false));
    $Tab1 .= $PHPShopGUI->setField('����� ��������', $PHPShopGUI->setInputText(null, 'send_limit', '0,300', 150), 1, '������������� c 1 �� 300');
    $Tab1 .= $PHPShopGUI->setField("�������� ���������", $PHPShopGUI->setCheckbox('test', 1, __('��������� �������� ��������� ') . ' ' . $PHPShopSystem->getEmail(), 1, false, false));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', '100%', false));

    if (empty($_POST['time_limit']))
        $_POST['time_limit'] = 15;

    if (empty($_POST['message_limit']))
        $_POST['message_limit'] = 50;

    $Tab2 = $PHPShopGUI->setField('��������� � ��������', $PHPShopGUI->setInputText(null, 'message_limit', $_POST['message_limit'], 150), 1, '�������� ���������');
    $Tab2 .= $PHPShopGUI->setField('��������� ��������', $PHPShopGUI->setInputText(null, 'time_limit', $_POST['time_limit'], 150, __('�����')), 1, '�������� ���������');
    $Tab2 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setCheckbox('smart', 1, __('����� �������� ��� ���������� ������� ����������� �� ��������'), 0, false, false));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    $Tab1 .= $PHPShopGUI->setCollapse('�������������', $Tab2);

    $Tab1 .= $PHPShopGUI->setCollapse("����� ������", $oFCKeditor->AddGUI(). $PHPShopGUI->setAIHelpButton('content_new', 300, 'news_sendmail')  . $PHPShopGUI->setHelp('����������: <code>@url@</code> - ����� �����, <code>@user@</code> - ��� ����������, <code>@email@</code> - email ����������, <code>@name@</code> - �������� ��������, <code>@tel@</code> - ������� ��������'));



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
}

// ���
function actionBot() {
    global $PHPShopBase;

    // ����� �������������
    $total = $PHPShopBase->getNumRows('shopusers', "where sendmail='1'");

    // �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    if ($total >= $_POST['end']) {

        $option = array(
            'start' => $_POST['start'],
            'end' => $_POST['end'],
            'content' => $data['content'],
            'name' => $data['name'],
        );
        $action = actionUpdate($option);
        $action['bar'] = round($_POST['start'] * 100 / $total);

        return $action;
    } else
        return array("success" => 'done', "result" => PHPShopString::win_utf8('������� ��������� �� <strong>' . $total . '</strong> ������� � ������������ ' . ($_POST['end'] - $_POST['start']) . ' e-mail ����� ������ ' . $_POST['time'] . ' ���. ��  ' . round($_POST['performance'] / 60000, 1) . ' ���.'));
}

// ������� ����������
function actionUpdate($option = false) {
    global $PHPShopModules, $PHPShopSystem, $PHPShopGUI, $result_message, $PHPShopBase;

    $_POST['date_new'] = time();

    PHPShopObj::loadClass("parser");

    PHPShopParser::set('url', $_SERVER['SERVER_NAME']);
    PHPShopParser::set('name', $PHPShopSystem->getValue('name'));
    PHPShopParser::set('tel', $PHPShopSystem->getValue('tel'));
    PHPShopParser::set('title', $_POST['name_new']);
    PHPShopParser::set('logo', $PHPShopSystem->getLogo());
    $from = $PHPShopSystem->getEmail();

    // ����������
    if (!empty($_POST['servers_new']) and $_POST['servers_new'] != 1000) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
        $PHPShopOrm->debug = false;
        $showcaseData = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'id' => "=" . (int) $_POST['servers_new']), false, array('limit' => 1));
        if (is_array($showcaseData)) {

            if (!empty($showcaseData['tel']))
                $PHPShopSystem->setParam("tel", $showcaseData['tel']);

            if (!empty($showcaseData['adminmail']))
                $from = $showcaseData['adminmail'];

            if (!empty($showcaseData['name']))
                $PHPShopSystem->setParam('name', $showcaseData['name']);

            if (!empty($showcaseData['title']))
                $PHPShopSystem->setParam('title', $showcaseData['title']);

            if (!empty($showcaseData['logo']))
                $PHPShopSystem->setParam('logo', $showcaseData['logo']);

            if (!empty($showcaseData['icon']))
                $PHPShopSystem->setParam('url', $showcaseData['host']);
        }
    }


    // �������� �������
    if (!empty($_POST['template'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
        $data = $PHPShopOrm->select(array('*'), array('id' => "=" . intval($_POST['template'])), false, array('limit' => 1));
        if (is_array($data)) {
            $_POST['name_new'] = $data['zag'];
            $_POST['content_new'] = $data['podrob'];
        }
    }

    $n = $error = 0;

    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
        $ssl = 'https://';
    else
        $ssl = 'http://';

    // ���������� http
    if (!strstr($_POST['content_new'], "http:") and ! strstr($_POST['content_new'], "https:")) {
        $_POST['content_new'] = str_replace('../../UserFiles/', "/UserFiles/", $_POST['content_new']);
        $_POST['content_new'] = str_replace("/UserFiles/", $ssl . $_SERVER['SERVER_NAME'] . "/UserFiles/", $_POST['content_new']);
    }

    // ����
    if (!empty($_POST['test'])) {
        
        if (!empty($_POST['saveID'])) {
            PHPShopParser::set('user', $_SESSION['logPHPSHOP']);
            PHPShopParser::set('email', $from);
            PHPShopParser::set('content', preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $_POST['content_new']));

            $PHPShopMail = new PHPShopMail($from, $from, $_POST['name_new'], '', true, true);
            $content = PHPShopParser::file('tpl/sendmail.mail.tpl', true);

            if (!empty($content)) {
                if ($PHPShopMail->sendMailNow($content))
                    $n++;
                else
                    $error++;
            }
        }
        
    } else {

        // �������������
        if (is_array($option)) {
            $limit = $option['start'] . ',' . $option['end'];
            $title = $option['name'];
            $content = $option['content'];
        } elseif (!empty($_POST['smart'])) {
            $limit = '0,' . $_POST['message_limit'];
            $content = $_POST['content_new'];
            $title = $_POST['name_new'];
        } else {
            $limit = $_POST['send_limit'];
            $content = $_POST['content_new'];
            $title = $_POST['name_new'];
        }

        // �������� �������������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $PHPShopOrm->debug = false;
        $where['sendmail'] = "='1'";

        // ����������
        if ($_POST['servers_new'] == 1000)
            $where['servers'] = '=' . (int) $_POST['servers_new'] . ' or servers=0';
        else
            $where['servers'] = '=' . (int) $_POST['servers_new'];

        $data = $PHPShopOrm->select(array('id', 'mail', 'name', 'password'), $where, array('order' => 'id desc'), array('limit' => $limit));

        if (is_array($data))
            foreach ($data as $row) {

                PHPShopParser::set('user', $row['name']);
                PHPShopParser::set('email', $row['mail']);
                PHPShopParser::set('content', preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $content));
                $unsubscribe = '<p>��� �� ���������� �� ��������� �������� <a href="http://' . $_SERVER['SERVER_NAME'] . '/unsubscribe/?id=' . $row['id'] . '&hash=' . md5($row['mail'] . $row['password']) . '" target="_blank">��������� �� ������.</a></p>';
                PHPShopParser::set('unsubscribe', $unsubscribe);

                $PHPShopMail = new PHPShopMail($row['mail'], $from, $title, '', true, true);
                $content_message = PHPShopParser::file('tpl/sendmail.mail.tpl', true);

                if (!empty($content_message)) {
                    if ($PHPShopMail->sendMailNow($content_message))
                        $n++;
                    else
                        $error++;
                }
            }
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // �������������
    if (!empty($_POST['smart']) and empty($_POST['test'])) {

        // ����� �������������
        $total = $PHPShopBase->getNumRows('shopusers', "where sendmail='1'");

        $bar = round($_POST['message_limit'] * 100 / $total);
        $action = true;
        $result_message = $PHPShopGUI->setAlert('<div id="bot_result">������� ��������� �� <strong>' . $n . '</strong> ������� � ������������ ' . $limit . ' �������. ������ <strong>' . $error . '</strong>.</div>
<div class="progress">
  <div class="progress-bar progress-bar-striped  progress-bar-success active" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: ' . $bar . '%"> ' . $bar . '% 
  </div>
</div>');
    } else {
        $result_ajax = '������� ��������� �� <strong>' . $n . '</strong> ������� � ������������ ' . $limit . ' �������. ������ <strong>' . $error . '</strong>.';
        $result_message = $PHPShopGUI->setAlert($result_ajax);
        $action = true;
    }

    if (empty($option)) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
        $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    }

    return array("success" => $action, "result" => PHPShopString::win_utf8($result_ajax), 'limit' => $limit);
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