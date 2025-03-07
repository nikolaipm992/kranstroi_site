<?php

PHPShopObj::loadClass('order');
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.dolyame.dolyame_system"));

/**
 * ���������� ������ ������
 * @return mixed
 */
function actionBaseUpdate() {
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
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    // ���������
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;

            $PHPShopOrmProducts->update(array('dolyame_enabled_new' => (int) $_POST['enabled_all']), $where);
        }
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

/**
 * ����������� �������� ������
 * @return bool
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select();
    $PHPShopGUI->addJSFiles('../modules/dolyame/admpanel/gui/dolyame.gui.js');

    $PHPShopGUI->field_col = 4;

    $Tab1 = $PHPShopGUI->setField('����� API ��������', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ API ��������', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab1 .= $PHPShopGUI->setField('Site ID', $PHPShopGUI->setInputText(false, 'site_id_new', $data['site_id'], 100),1,'�������������� ������� ������');
    $Tab1 .= $PHPShopGUI->setField('������������ �����', $PHPShopGUI->setInputText(false, 'max_sum_new', $data['max_sum'], 100, $PHPShopSystem->getValutaIcon()));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_payment_value[] = array($order_status['name'], $order_status['id'], $data['status_payment']);
        }

    // ������ ������
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1 .= $PHPShopGUI->setField('������ ����� ������', $PHPShopGUI->setSelect('status_payment_new', $order_status_payment_value, 300));

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);

            // �����������
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('������', $PHPShopGUI->setField("����������", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.') . $PHPShopGUI->setField("��������� ��������", $PHPShopGUI->setRadio("enabled_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "����.", 1)));


    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://dolyame.ru/business/?utm_medium=ptr.act&utm_campaign=sme.partners&partnerId=5-IV4AJGWE&agentId=5-IVFOVQCN&agentSsoId=14373cd1-2b09-4747-bae6-299a0229aedc&utm_source=partner_rko_a_sme" target="blank">������</a>.</li>
<li>�� �������� ��������� ������ ������ ��������������� ������ <kbd>����� API ��������</kbd> � <kbd>������ API ��������</kbd>.</li>
<li>������� ������ ������ ��� ������ � ������ ������ ����� ������.</li>
<li>������� ������������ ����� ������ ��� ������ ������ (�� ��������� 30 000 ���).</li>
<li>��������� � ����� <code>phpshop/modules/dolyame/cert/</code> �����������, ���������� � ������ �������� ������� ������. ������������� ���� *.pem � <code>certificate.pem</code>, ���� *.key � <code>private.key</code></li>
</ol>

<h4>��������� �������</h4>
 <ol>
   <li>��� ����������� ���������� �������� ������ ������� �� �������� ��������� ������ ������ ��������������� ������ <kbd>Site ID</kbd>. ������� ��������������� � ������������ ������������ ������� ������. ��� ��������� �������� ������� ���������� � ����������� ����� ������ <code>partners@dolyame.ru</code>. ����� ��������� �������� ���������� ��������� ������.</li>
   <li>��� ����������� � ��������� ������ ������ �������� ������ ������ ��� �������� ������������ ���������� <code>@dolyame_product@</code>. </li>
   <li>��� ���������� ���� "Site ID" ������������ ������� ������ ����� ����������� ��� ������������ ������� ������. ������ �������� ������ ������ ������������ ��� ������������� ���� "Site ID" � ������� ������ "����� �����". ��� ������ �������� ������ ������ ���������� �������� ����� ��������������� ������������� ������ � ������ �������� ������� ������.</li>
 </ol>
';

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab1, true, false, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
