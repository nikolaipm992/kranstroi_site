<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_system"));

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
    global $PHPShopGUI, $PHPShopOrm,$_classPath;


    $PHPShopGUI->addJSFiles($_classPath.'modules/pechka54/admpanel/gui/pechka54.gui.js');
    
    // �������
    $data = $PHPShopOrm->select();
    $Tab1.= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText('', 'password_new', $data['password'], 300).$PHPShopGUI->setHelp('URL ������ �������: http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span>'),false,'������ ����������� ������');
    $Tab1.= $PHPShopGUI->setField('� ���', $PHPShopGUI->setInputText(false, 'kkm_new', $data['kkm'], 300,false, false, false, 1234567890));


    // ���
    include_once($_classPath . 'modules/pechka54/class/pechka54.class.php');
    $Pechka54Rest = new Pechka54Rest();
    $nds_array = $Pechka54Rest->taxes;
    if (is_array($nds_array))
        foreach ($nds_array as $val) {
            $tax_product_value[] = array($val['tax_name'], $val['tax_id'], $data['tax_product']);
            $tax_delivery_value[] = array($val['tax_name'], $val['tax_id'], $data['tax_delivery']);
        }


    $Tab1.= $PHPShopGUI->setField('��� ��� �������', $PHPShopGUI->setSelect('tax_product_new', $tax_product_value,300));
    $Tab1.= $PHPShopGUI->setField('��� ��� ��������', $PHPShopGUI->setSelect('tax_delivery_new', $tax_delivery_value,300));

    // �����������
    $info = '
        <p>������ ���� ���������� ������� ������ �� �������� "<b>�������� ���������� ���������</b>" � ID = 101. ����� ������ ����� �������� ������������� ����� �������� ������ ����� ����� ��������� ������ (���������, �������� � ������).</p>
<h4>��� �1 - ��������� ������</h4>
        <ol>
        <li>��������� ���� ������ ����������� ������ ������� � ������ ��� ������ �� �������������������� �������.</li>
        <li>����������� URL ������ ������� <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span></code>
        <li>��������� ���� <kbd>� ���</kbd> ������� ��������� ����������. ��� ������������� ����������� ����� � �������, ������ ��������� ������ ��� ����������� ����������� <code>1234567890</code>.
        </ol>
        
       <h4>��� �2 - ��������� ���������� "�����54"</h4>
        <ol>
        <li>������� ��������� <a href="http://54online.com/?p=56516611" target="_blank">�����54</a> � ����� ������������.
        <li>���������� ��������� ���������� �����54 �� <a href="https://www.youtube.com/watch?v=PVQOX4r4ty8 " target="_blank">�����-����������</a>
        <li>� ���� <kbd>�����</kbd> �������� ���� ���� <b>'.$_SERVER['SERVER_NAME'].'</b> � ��������� ���� "URL ������ �������"  <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/pechka54/api.php?key=<span name="kkm_key">'.$data['password'].'</span></code>
        <li>��������� ������������� ���� ����� ���� <kbd>�����</kbd> - "������� �����" -  <kbd>���������������� � ������</kbd>
        </ol>
        
        <h4>��� �3 - ��������� ������</h4>
        <ol>
        <li>��������� ���� ���������������� ������ ���.
        <li>����������� �������� ���� ���, ���������� ��� �������������, � ��� ������� � �������� � ��������.</li>
        </ol>

        <h4>������ ������ �����</h4>
        <ol>
        <li>��� �������� � ������ ��������� � <a href="?path=modules.dir.pechka54">������ ��������</a>.
        <li>��������� ���������� �� ��������� �������� ����� �������� ��� ����� �� ������ ���� � ������� ��������.
        </ol>
';

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $Tab3), array("������ ��������", null, '?path=modules.dir.pechka54'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $_POST['region_data_new'] = 1;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>