<?php

PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['webhooks']['webhooks_system']);

function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>� ���� <kbd>URL WebHook</kbd> ������ URL ��� ������ ������.</li>
<li>������� �������� ������������ WebHook (����� �����, ����� ������������ � �.�.).</li>
<li>������� ����� �������� ������.</li>
<li>������ ���������� ������ �������� � �������� <kbd>������ ����������</kbd> �� ����� � ������� "��������".</li>
<li>����� ������������ WebHook ��� ������ ����� �������� �� ��������� "URL WebHook" ��� ���������� ��������� ��������� ������������� <a href="https://apix-drive.com/?p=816b11bc8e756b0cd344fb728e2a2727" target="_blank">APIXDrive</a> � <a href="https://zapier.com" target="_blank">Zapier</a>.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab2), array("� ������", $Tab3), array("����� WebHooks", null, '?path=modules.dir.webhooks'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>