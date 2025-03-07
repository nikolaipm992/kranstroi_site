<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.bitrix24.bitrix24_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['statuses_new'] = serialize($_POST['statuses']);

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    include_once '../modules/bitrix24/class/Bitrix24.php';
    $Bitrix24 = new Bitrix24();
    $data = $PHPShopOrm->select();

    $statusesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
    $statusesResult = $statusesOrm->select(array('*'));
    $statuses = array(
        array('id' => 0, 'name' => '����� �����')
    );
    if (isset($statusesResult['id']))
        $statuses[] = $statusesResult;
    else
        $statuses = array_merge($statuses, $statusesResult);

    if (!empty($data['statuses']))
        $statusSettings = unserialize($data['statuses']);
    else
        $statusSettings = array();

    $dealStages = $Bitrix24->getDealStages();

    $fieldStatuses = '';
    if (is_array($dealStages['result']))
        foreach ($dealStages['result'] as $dealStage) {
            $selectStatuses = array();

            foreach ($statuses as $status) {
                $selectStatuses[] = array($status['name'], $status['id'], $statusSettings[$dealStage['STATUS_ID']]);
            }

            $fieldStatuses .= $PHPShopGUI->setField(PHPShopString::utf8_win1251($dealStage['NAME']), $PHPShopGUI->setSelect('statuses[' . $dealStage['STATUS_ID'] . ']', $selectStatuses));
        }

    $Tab1 = $PHPShopGUI->setField('URL ������� �������24', $PHPShopGUI->setInputText(false, 'webhook_url_new', $data['webhook_url'], 500));
    $Tab1 .= $PHPShopGUI->setField('��� ����������� ���������� ������', $PHPShopGUI->setInputText(false, 'update_delivery_token_new', $data['update_delivery_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('��� ����������� �������� ������', $PHPShopGUI->setInputText(false, 'delete_product_token_new', $data['delete_product_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('��� ����������� �������� ��������', $PHPShopGUI->setInputText(false, 'delete_contact_token_new', $data['delete_contact_token'], 500));
    $Tab1 .= $PHPShopGUI->setField('��� ����������� �������� ��������', $PHPShopGUI->setInputText(false, 'delete_company_token_new', $data['delete_company_token'], 500));

    if (empty($data['webhook_url']))
        $Tab1 .= $PHPShopGUI->setCollapse('�������', $PHPShopGUI->setAlert('��� ������������� �������� ������ � ������ ������ ������� "URL ������� �������24" � ������� "���������"', 'warning'));
    else
        $Tab1 .= $PHPShopGUI->setCollapse('�������', $fieldStatuses);

    $info = '
<h4>��� ������������ � �������24?</h4>
<ol>
 <li>������������������ �� ����� <a href="https://www.bitrix24.ru/create.php?p=9003557" target="_blank">�������24</a>
</li></ol> 

<h4>�������� ��������� ������� � �������24</h4>
        <ol>
<li>�������� ����������, ������� ������� URL �����: <code>https://�����_������_�������24/marketplace/hook/</code></li>
<li>������� ������ "�������� ������", � ���������� ������ �������� "�������� ������".</li>
<li>�������� ������� "������������� �������".</li>
<li>����� ������� �������� �������� "CRM".</li>
<li>���������� "������ URL ��� ������ REST" �� "/profile/", ������� "���������".</li>
<li>������������� URL �������� � ���� "URL ������� �������24" � ���������� ������.</a></li>
</ol>
<h4>�������� ��������� �������� � �������24</h4>
        <ol>
<li>�������� ����������, ������� ������� URL �����: <code>https://�����_������_�������24/marketplace/hook/</code></li>
<li>������� ������ "�������� ������", � ���������� ������ �������� "��������� ������".</li>
<li>����� ����������� ������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>�������� ������� "���������� ������� ������".</li>
<li>��� ������� �������� �������� "���������� ������", ������� ������ "���������".</li>
<li>���������� "��� �����������" ������ � ���� "��� ����������� ���������� ������" � ���������� ������.</li>
<li>������� ������ "�������� ������", � ���������� ������ �������� "��������� ������".</li>
<li>����� ����������� ������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>�������� ������� "�������� ������ � �������24".</li>
<li>��� ������� �������� �������� "�������� ������", ������� ������ "���������".</li>
<li>���������� "��� �����������" ������ � ���� "��� ����������� �������� ������" � ���������� ������.</li>
<li>������� ������ "�������� ������", � ���������� ������ �������� "��������� ������".</li>
<li>����� ����������� ������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>�������� ������� "�������� �������� � �������24".</li>
<li>��� ������� �������� �������� "�������� ��������", ������� ������ "���������".</li>
<li>���������� "��� �����������" ������ � ���� "��� ����������� �������� ��������" � ���������� ������.</li>
<li>������� ������ "�������� ������", � ���������� ������ �������� "��������� ������".</li>
<li>����� ����������� ������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/bitrix24/api/api.php</code>.</li>
<li>�������� ������� "�������� �������� � �������24".</li>
<li>��� ������� �������� �������� "�������� ��������", ������� ������ "���������".</li>
<li>���������� "��� �����������" ������ � ���� "��� ����������� �������� ��������" � ���������� ������.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>