<?php

include_once dirname(__FILE__) . '/../class/Avito.php';
$TitlePage = __('����� �� Avito');
PHPShopObj::loadClass("product");
PHPShopObj::loadClass('category');

// ���������� ������ ���������
function treegenerator($array, $i, $curent) {
    global $tree_array, $CategoryArray;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k);
            $disabled = null;

            $selected = null;

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

$Avito = new Avito();

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $Avito, $PHPShopModules;

    $PHPShopGUI->field_col = 4;
    $product_info = $Avito->getProductList($_GET['status'], PHPShopString::utf8_win1251($_GET['id']), null, (int)$_GET['limit'])['resources'][0];
    

    if (!empty($product_info['title']))
        $PHPShopGUI->action_button['��������� �����'] = array(
            'name' => '��������� �����',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('�����') . ': ' . PHPShopString::utf8_win1251($product_info['title']), false, array('��������� �����'));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopCategoryArray = new PHPShopCategoryArray(false, ["id", "name", "parent_to"]);
    $GLOBALS['CategoryArray'] = $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('������� �������') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }

    $GLOBALS['tree_array'] = &$tree_array;


    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $_GET['type_id']);

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $selected = null;

            $tree_select .= '<option value="' . $k . '"  ' . $disabled . ' ' . $selected . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';


    // ��������
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = PHPShopString::utf8_win1251($product_info['description']);

    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setTextarea('name_new', PHPShopString::utf8_win1251($product_info['title'])));
    $Tab1 .= $PHPShopGUI->setField("ID", $PHPShopGUI->setInputText(null, 'uid_new', PHPShopString::utf8_win1251($product_info['id'])));

    $Tab1 .= $PHPShopGUI->setField("��������� � Avito", $PHPShopGUI->setText(PHPShopString::utf8_win1251($product_info['category']['name']), "left", false, false));

    // ����� ��������
    $Tab1 .= $PHPShopGUI->setField("����������", $tree_select);

    // ����� ������
    $Tab1 .= $PHPShopGUI->setField('����� ������', $PHPShopGUI->setCheckbox('enabled_new', 1, '����� � ��������', 1) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, '���������������', 0) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, '�������', 0));
    $Tab1 .= $PHPShopGUI->setField('����������', $PHPShopGUI->setInputText('&#8470;', 'num_new', 0, 150));

    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInputText(null, 'price_new', (float) $product_info['price'], 150));

    $baseinputvaluta = $PHPShopSystem->getDefaultOrderValutaId();

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;

    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $baseinputvaluta, false);
        }

    // ������
    $Tab1 .= $PHPShopGUI->setField('������', $valuta_area);

    $Tab_size = $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, 'weight_new', $product_info['weightDimensions']['weight'], 100, __('�&nbsp;&nbsp;&nbsp;&nbsp;')));
    $Tab_size .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(null, 'height_new', $product_info['weightDimensions']['height'] / 100, 100, __('c�')));
    $Tab_size .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(null, 'width_new', $product_info['weightDimensions']['width'] / 100, 100, __('c�')));
    $Tab_size .= $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText(null, 'length_new', $product_info['weightDimensions']['depth'] / 100, 100, __('c�')));

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('��������', $Tab_size);
    $Tab2 = $PHPShopGUI->setCollapse("��������", $oFCKeditor->AddGUI());


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1 . $Tab2, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = 
            $PHPShopGUI->setInput("hidden", "export_avito_id_new", $_GET['id'], "right", 70, "", "but").
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.catalog.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}


/**
 * ����� �������� ������
 */
function actionSave() {
    global $PHPShopSystem, $YandexMarket;

    // ����� ������������
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];

    // ���� �����������
    $_POST['datas_new'] = time();
    $_POST['export_avito_new'] = 1;
    $_POST['name_avito_new'] = $_POST['name_new'];
    
    // ������������� ������ ��������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=product&return=catalog&id=' . $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>