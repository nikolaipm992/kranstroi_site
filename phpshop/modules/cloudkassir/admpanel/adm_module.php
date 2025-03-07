<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudkassir.cloudkassir_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;


    // �������
    $data = $PHPShopOrm->select();
    $Tab1 = $PHPShopGUI->setField('��� ����������� ��� ��', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300));
    $Tab1.= $PHPShopGUI->setField('Public ID', $PHPShopGUI->setInputText(false, 'publicid_new', $data['publicid'],300));
    $Tab1.= $PHPShopGUI->setField('API Secret', $PHPShopGUI->setInputText(false, 'apisecret_new', $data['apisecret'], 300));

    // ������� ���������������
    $tax_system = array (
        array("����� ������� ���������������", 0, $data["taxationSystem"]),
        array("���������� ������� ��������������� (�����)", 1, $data["taxationSystem"]),
        array("���������� ������� ��������������� (����� ����� ������)", 2, $data["taxationSystem"]),
        array("������ ����� �� ��������� �����", 3, $data["taxationSystem"]),
        array("������ �������������������� �����", 4, $data["taxationSystem"]),
        array("��������� ������� ���������������", 5, $data["taxationSystem"])
    );
    $Tab1.= $PHPShopGUI->setField('C������ ���������������', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // ����������
    $info='<h4>����������� � ������-����� CloudKassir</h4>
        <ol>
        <li><a href="https://cloudpayments.ru/Docs/Connect" target="_blank">������������</a> � ���������� ������� CloudKassir, ���� �� ��� �� ��������� ���������.</li>
        <li>�������� ����������������� ����������� ������� ��� ������ � ������ ���.</li>
        <li>������������������ � ������ �������� ��������� ������:<br>
� <a href="http://lkul.nalog.ru/" target="_blank">��� ����������� ���</a><br>
� <a href="https://lkip.nalog.ru/" target="_blank">��� �������������� ����������������</a></li>
        <li>��������� ������� �� ������-������������.</li>
        <li>����� ���������� �������� � ������ ����� ��� ����� ������������� ����� ��� � �� ��� ����������� � ���.</li>
        </ol>
        
        <h4>��������� ������</h4>
        <ol>
        <li>� ���� "��� ����������� ��� ��" ������� ��� ����� ����������� ��� ��, �� ������� ���������������� �����.</a></li>
        <li>� ���� "Public ID" � "API Secret" ������� Public ID � API Secret �� ������� �������� CloudKassir</li>
        <li>������� ������� ���������������</li>
        <li>� ������ �������� CloudKassir ������� ����� ��� ����������� � �������� ����� <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudkassir/notifications/receipt.php</code> HTTP ����� POST, ��������� Windows-1251</li>
        </ol>
        
        <h4>�������� ����� ������� � ��������</h4>
        <ol>
        <li>���� ������� (�������) ��������� ������������� ��� ������������ ������ ���������� �������� �� �������� ������� ����� ����� ���� ������ ��� ��������� ������ �� ������ ����������� ����� � ����. ��������� �������������� ���������� � �������� ������ �� ���������.</li>
        <li>���������� ��� �������� � �������� <kbd>�����</kbd> � ������� �������������� ������.</li>
        <li>��� ������� ����� �������� � ������ ������ ��� ������ ������ � ������� �������������� ������.</li>
        <li>��� �������� ������� ����� �������� � ������ ������ ��� ������ ������, �������� ��� �������.</li>
        </ol>
        
        <h4>������ ������ �����</h4>
        <ol>
        <li>��� �������� � ������ ��������� � <a href="?path=modules.dir.cloudkassir">������ ��������</a>.</li>
        <li>��������� ���������� �� ��������� �������� ����� �������� ��� ����� �� ������ ���� � ������� ��������.</li>
        </ol>
        
        <h4>��������� ��������</h4>
        <ol>
        <li>�������� ������ ��� ��� �������� ����� ��������� � �������� �������������� ��������.</li>
        </ol>
';
    
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $PHPShopGUI->setInfo($info),true), array("� ������", $Tab3), array("������ ��������", null, '?path=modules.dir.cloudkassir'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $_POST['region_data_new']=1;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>