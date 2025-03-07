<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.alfacredit.alfacredit_system"));

/**
 * ���������� ������ ������
 * @return mixed
 */
function actionBaseUpdate(){
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * ���������� ��������
 * @return mixed
 */
function actionUpdate(){
    global $PHPShopModules, $PHPShopOrm;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (!isset($_POST['prod_mode_new'])) $_POST['prod_mode_new'] = '0';
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

/**
 * ����������� �������� ������
 * @return bool
 */
function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;
    
        // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('* ���', $PHPShopGUI->setInputText(false, 'inn_new', $data['inn'], 300), 1, "��� ��������-��������.");
    $Tab1 .= $PHPShopGUI->setField('* ��� ��������� ������', $PHPShopGUI->setInputText(false, 'category_name_new', $data['category_name'], 300), 1, "��������� �������� ������ ��� ��������� ����� ����� ��� ��������� �� is_support@alfabank.ru ����, ���� ���� ���������, ������������� ���������� �� � ���������� �����.");
    $Tab1 .= $PHPShopGUI->setField('��� ����� (���� ����)', $PHPShopGUI->setInputText(false, 'action_name_new', $data['action_name'], 300), 1, "���/�������� ����� (���������� �������� ����� � ������).");
    $Tab1 .= $PHPShopGUI->setField('* ����������� ����� ������/������ ��� �������', $PHPShopGUI->setInputText(false, 'min_sum_cre_new', $data['min_sum_cre'], 300), 1, "����������� �����, ��� ������� �������� ������� ������ ������/������ � ������.");
    $Tab1 .= $PHPShopGUI->setField('������� ������', $PHPShopGUI->setInputText(false, 'cre_name_new', $data['cre_name'], 300), 1, "������� �� ������/�������� ������ ��� �������� ������ � ������.");
    $Tab1 .= $PHPShopGUI->setField('����������� ����� ������/������ ��� ���������', $PHPShopGUI->setInputText(false, 'min_sum_ras_new', $data['min_sum_ras'], 300), 1, "����������� �����, ��� ������� �������� ������� ������ ������/������ � ��������� (�������� ������ ��� 0 ���� ���).");
    $Tab1 .= $PHPShopGUI->setField('������� ������', $PHPShopGUI->setInputText(false, 'ras_name_new', $data['ras_name'], 300), 1, "������� �� ������/�������� ������ ��� �������� ������ � ���������.");
    $Tab1 .= $PHPShopGUI->setField('����� ������', $PHPShopGUI->setCheckbox("prod_mode_new", 1, "�������� ������ ������� � ������/��������� �� �������� ������", $data["prod_mode"]));
    
    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);

    // ����������
    $info = '
        <h4>���������� � ������</h4>
        <p>������ ��������� ������������� ������ "���� �����" �� �����-����:</p>
        <ol>
        <li><strong>� �������� ������</strong>. ��� ����� ���������� ���������� <kbd>@acredit_product@</kbd> � ������� <code>phpshop/templates/��� �������/product/main_product_forma_full.tpl</code> � ������ ��� �����.</li>
        <li><strong>� ������� ������</strong>. ��� �������� ���� ������ � ������ �� �������</li>
        </ol>

        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://anketa.alfabank.ru/kupilegko/" target="blank">�����-����</a>.</li>
<li>�������� URL, �� ������� ���������� ���������� ������� �� ����� <a href="mailto:is_support@alfabank.ru" target="blank">is_support@alfabank.ru</a> � �������: ��� ��������-�������� (<code>��� ���</code>), ������ (<code>ANY</code>), URL (<code>'.$_SERVER['SERVER_NAME'].'/phpshop/modules/alfacredit/status/accept.php</code>)</li>
<li>�������� �����, ��� ������ � ����������� ���������.</li>
<li>��� �������������� ����� ������ �������������� ������� <code>phpshop/modules/alfacredit/templates/</code></li>
</ol>
';

    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $Tab3), array("������", null, '?path=modules.dir.alfacredit'));

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
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
