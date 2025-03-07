<?php

include_once dirname(__DIR__) . '/class/Marketplaces.php';
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.marketplaces.marketplaces_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(['version_new' => $new_version]);
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

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem;

    $PHPShopGUI->field_col = 5;

    $data = $PHPShopOrm->select();

    $options = unserialize($data['options']);

    $PHPShopGUI->addJSFiles('../modules/marketplaces/admpanel/gui/marketplaces.gui.js');

    $Tab1 = $PHPShopGUI->setField('������ ������ �����', $PHPShopGUI->setInputText(Marketplaces::getProtocol() . $_SERVER['SERVER_NAME'] . '/yml/?pas=', 'password_new', $data['password'], '100%'));
    $Tab1 .= $PHPShopGUI->setField('����� �������������', $PHPShopGUI->setCheckbox('use_params_new', 1, '�������� ����� ������������� � YML', $data['use_params']));
    $Tab1 .= $PHPShopGUI->setField('������ ��������� ��������', '<div id="marketplacesDescriptionShablon">
<textarea class="form-control marketplace-shablon" name="description_template_new" rows="3" style="width:100%;height: 70px;">' . $data['description_template'] . '</textarea>
    <div class="btn-group" role="group" aria-label="...">
    <input  type="button" value="' . __('��������') . '" onclick="marketplacesShablonAdd(\'@Content@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('������� ��������') . '" onclick="marketplacesShablonAdd(\'@Description@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('��������������') . '" onclick="marketplacesShablonAdd(\'@Attributes@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('�������') . '" onclick="marketplacesShablonAdd(\'@Catalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('����������') . '" onclick="marketplacesShablonAdd(\'@Subcatalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('�����') . '" onclick="marketplacesShablonAdd(\'@Product@\',)" class="btn btn-default btn-sm">
    </div>
</div>
<script>function marketplacesShablonAdd(variable) {
    var shablon = $(".marketplace-shablon").val() + " " + variable;
    $(".marketplace-shablon").val(shablon);
}</script>', 1, '�������������� � �������� ������� �������������� ��������. ������������� ������������ ������ ��� ������ ���������� ���������� �������.');


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

    // �����������
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);

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


    $tree_select_gm = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories_gm[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $tree_select_cm = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories_cm[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $tree_select_ae = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories_ae[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $tree_select_sm = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories_sm[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    // ����� �������� Google Merchant
    $catOption = $PHPShopGUI->setField("����������", $tree_select_gm . $PHPShopGUI->setCheckbox("categories_gm_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("����� � Google Merchant", $PHPShopGUI->setRadio("enabled_gm_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_gm_all", 0, "����.", 1));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('������ ��� Google Merchant', $catOption);

    // ����� �������� ������.������
    $catOption = $PHPShopGUI->setField("����������", $tree_select_cm . $PHPShopGUI->setCheckbox("categories_cm_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("����� � ������.������", $PHPShopGUI->setRadio("enabled_cm_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_cm_all", 0, "����.", 1));

    $Tab1 .= $PHPShopGUI->setCollapse('������ ��� ������.������', $catOption);

    // ����� �������� AliExpress
    $catOption = $PHPShopGUI->setField("����������", $tree_select_ae . $PHPShopGUI->setCheckbox("categories_ae_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("����� � AliExpress", $PHPShopGUI->setRadio("enabled_ae_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_ae_all", 0, "����.", 1));

    $Tab1 .= $PHPShopGUI->setCollapse('������ ��� AliExpress', $catOption);

    // ����� �������� ����������
    $catOption = $PHPShopGUI->setField("����������", $tree_select_sm . $PHPShopGUI->setCheckbox("categories_sm_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("����� � ����������", $PHPShopGUI->setRadio("enabled_sm_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_sm_all", 0, "����.", 1));

    $Tab1 .= $PHPShopGUI->setCollapse('������ ��� ����������', $catOption);
    
    $valuta = $PHPShopSystem->getDefaultValutaCode();

    $Tab1 .= $PHPShopGUI->setCollapse('��������� ���', $PHPShopGUI->setField('������� ��� Google Merchant', $PHPShopGUI->setSelect('options[price_google]', $PHPShopGUI->setSelectValue($options['price_google'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_google_fee]', $options['price_google_fee'], 100, '%').'<br>'.
                  $PHPShopGUI->setInputText(null, 'options[price_google_markup]', $options['price_google_markup'], 100, $valuta)) .
            $PHPShopGUI->setField('������� ��� ������.������', $PHPShopGUI->setSelect('options[price_cdek]', $PHPShopGUI->setSelectValue($options['price_cdek'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_cdek_fee]', $options['price_cdek_fee'], 100, '%').'<br>'.
                    $PHPShopGUI->setInputText(null, 'options[price_cdek_markup]', $options['price_cdek_markup'], 100, $valuta)
                    ) .
            $PHPShopGUI->setField('������� ��� AliExpress', $PHPShopGUI->setSelect('options[price_ali]', $PHPShopGUI->setSelectValue($options['price_ali'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_ali_fee]', $options['price_ali_fee'], 100, '%') .'<br>'.
                    $PHPShopGUI->setInputText(null, 'options[price_ali_markup]', $options['price_ali_markup'], 100, $valuta)) .
            $PHPShopGUI->setField('������� ��� ����������', $PHPShopGUI->setSelect('options[price_sbermarket]', $PHPShopGUI->setSelectValue($options['price_sbermarket'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'options[price_sbermarket_fee]', $options['price_sbermarket_fee'], 100, '%').'<br>'.$PHPShopGUI->setInputText(null, 'options[price_sbermarket_markup]', $options['price_sbermarket_markup'], 100, $valuta)) 
                    
    );

    // ����������
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(["��������", $Tab1, true, false, true], ["����������", $Tab2], ["� ������", $Tab3]);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $_POST['options_new'] = serialize($_POST['options']);
    $PHPShopOrm->debug = false;

    if (empty($_POST["use_params_new"]))
        $_POST["use_params_new"] = 0;

    // ��������� google_merchant
    if (is_array($_POST['categories_gm']) and $_POST['categories_gm'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories_gm'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;
            $PHPShopOrmProducts->update(array('google_merchant_new' => intval($_POST['enabled_gm_all'])), $where);
        }
    }

    // ��������� cdek
    if (is_array($_POST['categories_cm']) and $_POST['categories_cm'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories_cm'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;
            $PHPShopOrmProducts->update(array('cdek_new' => intval($_POST['enabled_cm_all'])), $where);
        }
    }

    // ��������� aliexpress
    if (is_array($_POST['categories_ae']) and $_POST['categories_ae'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories_ae'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;
            $PHPShopOrmProducts->update(array('aliexpress_new' => intval($_POST['enabled_gm_all'])), $where);
        }
    }

    // ��������� sbermarket
    if (is_array($_POST['categories_sm']) and $_POST['categories_sm'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories_sm'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;
            $PHPShopOrmProducts->update(array('sbermarket_new' => intval($_POST['enabled_sm_all'])), $where);
        }
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>