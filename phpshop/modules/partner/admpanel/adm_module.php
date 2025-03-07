<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    if (empty($_POST['key_enabled_new']))
        $_POST['key_enabled_new'] = 0;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function getStatus($status_id) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {
            if ($row['id'] == $status_id)
                $sel = 'selected';
            else
                $sel = null;
            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('order_status_new', $value);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������ �����', $PHPShopGUI->setCheckbox('enabled_new', 1, '���� ��������� ���������', $data['enabled']));
    
    $Tab1.=$PHPShopGUI->setField('���������� ���������', $PHPShopGUI->setInputText('%', 'percent_new', $data['percent'], '150', '�� ������'));
    $Tab1.=$PHPShopGUI->setField('�������� cookies', $PHPShopGUI->setInputText(null, 'cookies_day_new', $data['cookies_day'], '150', '����'));
    $Tab1.=$PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'stat_day_new', $data['stat_day'], '150', '����'));
    $Tab1.=$PHPShopGUI->setField('������ ������ �������',getStatus($data['order_status']));
    $Info = '�������� ����� � ����������� ������ ��������� �� ������: <a href="../../partner/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/partner/</a>. ���������� �� ����� ����� �������� ��� ������ ��� �������������.
        <p>������� ����������� � ����������� ��������� �������� �� ������
        <a href="../../rulepartner/" target="_blank">http://' . $_SERVER['SERVER_NAME'] . '/rulepartner/</a>.
     <p>
     ������� ���������� ��������� � ����� <code>/phpshop/modules/partner/templates/</code><br>
     �������� ���� �� ������ <code>/phpshop/modules/partner/inc/config.ini</code> � ����� <kbd>[lang]</kbd>
     </p>
     <p>��� ���������� �������� ������� �� <kbd>CSV</kbd> ����� �� <kbd>URL</kbd> � ������ ���������� �������������� ����� ������������ ���� ���������� ��� ������ "������" �� ������: <code>phpshop/modules/partner/cron/status.php</code></p>
     ';

    // �������� 
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('rule_new');
    $oFCKeditor->Height = '520';
    $oFCKeditor->Value = $data['rule'];
    
    $Tab4=$PHPShopGUI->setInfo($Info);

    // ���������� �������� 2
    $Tab2 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    $Tab3 = $oFCKeditor->AddGUI();


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab1,true), array("����� ������� �������", $Tab3), array("����������", $Tab4), array("� ������", $Tab2));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>


