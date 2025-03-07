<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.countcat.countcat_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ���������� ���
function actionUpdateCount() {

    // ������������
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/countcat/cron/count.php?s=" . $cron_secure;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);

    if (!empty($_POST['clean'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->update(array('count' => '0'), false, false);
    }

    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage, $select_name;

    // �������
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 1;

    $PHPShopGUI->action_button['�����������'] = [
        'name' => __('�����������'),
        'class' => 'btn btn-default btn-sm navbar-btn ',
        'type' => 'submit',
        'action' => 'exportID',
        'icon' => 'glyphicon glyphicon-refresh'
    ];
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['�����������', '��������� � �������']);



    $Tab2 = '
    <h4>���������</h4>
   <ol>
    <li>��� ������ ��������� ������ ����� ���������� �������������� ������ ������� � ������������ � ���������� � ���� ������. 
        ��� ���������� ������������� ����� ��������� ����������� ����������� ���� � �������� ������������� �������� � ����������� � ��������
        <kbd>������</kbd> - <kbd>Count</kbd>.</li>
        <li>��� ��������������� �������� ������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/countcat/cron/count.php</code>.</li>
     </ol> ';

    $Tab1 = $PHPShopGUI->setField('�����', $PHPShopGUI->setCheckbox("enabled_new", 1, '�������� ���������� ������ � ����� ��������', $data['enabled']) . '<br>' .
            $PHPShopGUI->setCheckbox("clean", 1, '�������� ����� ������������� �������� ���-�� ������ � ����������', 0));

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab1), array("����������", $PHPShopGUI->setInfo($Tab2)), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "exportID", "���������", "right", 80, "", "but", "actionUpdateCount.modules.edit");
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� ������� 
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>