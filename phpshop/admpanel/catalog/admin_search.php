<?php

$TitlePage = __("�������� ������");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

// ���������� ������ ���������
function treegenerator($array, $i, $curent) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $check = false;
    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $curent);

            if ($k == $curent)
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
                //$i++;
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

function viewCatalog($name = "search_category", $category = 0) {

    if (!empty($category))
        $_GET['cat'] = $category;

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('��������� �������') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }


    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<select id="search-select-category" class="form-control input-sm" name="' . $name . '" style="max-width:280px">
        <option value=""> - ' . __('��� ���������') . ' - </option>';

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $_POST['cat']);

            if ($k == $_POST['cat'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }
    $tree_select .= '</select>';

    return $tree_select;
}

/**
 * ����� ������
 */
function actionSearch() {
    global $PHPShopInterface, $PHPShopOrm, $PHPShopSystem;

    $PHPShopInterface->field_col = 2;

    $PHPShopInterface->_CODE .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'search_name', 'size' => '280px', 'placeholder' => '������������ ������, ������� ��� ID', 'class' => 'pull-left', 'value' => PHPShopSecurity::true_search($_REQUEST['words'])));

    if (empty($_POST['frame'])) {
        $PHPShopInterface->_CODE .= $PHPShopInterface->set_(3);
        $PHPShopInterface->_CODE .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'search_price_start', 'size' => '80px', 'placeholder' => '���� ��', 'class' => 'pull-left', 'value' => $_REQUEST['price_start']));
        $PHPShopInterface->_CODE .= $PHPShopInterface->set_(3);
        $PHPShopInterface->_CODE .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'search_price_end', 'size' => '80px', 'placeholder' => '���� ��', 'class' => 'pull-left', 'value' => $_REQUEST['price_end']));
    }
    $PHPShopInterface->_CODE .= $PHPShopInterface->set_(3);
    $PHPShopInterface->_CODE .= '<div class="pull-left">' . viewCatalog() . '</div> ';
    $PHPShopInterface->_CODE .= $PHPShopInterface->set_(3);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setInput("button", "search_action", __("�����"), "right", 70, "", "btn-sm btn-success pull-left search-action");
    $PHPShopInterface->_CODE .= $PHPShopInterface->setInput("hidden", "frame", $_POST['frame']);
    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';

    // ������
    if ($_POST['selectID'] == 1) {
        $PHPShopInterface->checkbox_action = false;
        $class = 'cart-list';
        $select = $_SESSION['selectCart'];
        $PHPShopInterface->setCaption(array("������������", "70%"), array("����", "12%", array('align' => 'right')), array('���-��', "15%", array('align' => 'center')));
    }
    // ������ �� ID ������
    else {
        $PHPShopInterface->setCaption(array("", "3%"), array("������������", "85%"), array("����", "15%", array('align' => 'right')));
        $class = 'search-list';

        if (strstr($_POST['currentID'], ",")) {
            $current_array = explode(",", $_POST['currentID']);
            if (is_array($current_array)) {
                foreach ($current_array as $v)
                    $select[intval($v)] = intval($v);
            }
        } elseif (!empty($_POST['currentID']))
            $select[$_POST['currentID']] = $_POST['currentID'];
    }


    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    if (!empty($_REQUEST['words'])) {
        $where['( name'] = " REGEXP '\x20*" . PHPShopSecurity::true_search($_REQUEST['words']) . "' or uid  REGEXP '^" . PHPShopSecurity::true_search($_REQUEST['words']) . "' or id='" . intval($_REQUEST['words']) . "' )";
    }

    if (!empty($_GET['cat']))
        $where['category'] = "=" . intval($_GET['cat']);

    if (!empty($_REQUEST['price_end']) and empty($_POST['frame']))
        $where['price'] = " BETWEEN " . intval($_REQUEST['price_start']) . " AND " . intval($_REQUEST['price_end']);

    // ���� ��� ������
    if (empty($where)) {
        $str = null;
        if (is_array($select)) {
            foreach ($select as $k => $v) {
                $str .= intval($k) . ',';
            }
        }
        $is_cart = true;
        $where['id'] = ' IN (' . $str . '0)';
    }

    // ������� ������� ��� ������� �� ID
    if ($_POST['selectID'] != 1)
        $where['parent_enabled'] = "='0'";


    $sklad_status = $PHPShopSystem->getSerilizeParam('admoption.sklad_status');

    $parent_price_enabled = $PHPShopSystem->getSerilizeParam('admoption.parent_price_enabled');

    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'name'), array('limit' => 500));
    if (is_array($data))
        foreach ($data as $row) {

            // �����
            if ($_POST['selectID'] == 1) {

                // ����������� ������� ����� �� ������� � 0 
                if (!empty($is_cart))
                    $add = 'data-cart="true"';
                else
                    $add = null;

                $items = '<div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-sm btn-default item-minus hidden-xs" type="button" data-id="' . $row['id'] . '"><span class="glyphicon glyphicon-minus"></span></button>
      </span>
      <input type="text" class="form-control input-sm" id="select_id_' . $row['id'] . '" name="select[' . $row['id'] . '][item]" data-id="' . $row['id'] . '" value="' . intval($_SESSION['selectCart'][$row['id']]['num']) . '" ' . $add . '  data-parent="' . $row['parent_enabled'] . '">
       <span class="input-group-btn">
        <button class="btn btn-sm btn-default item-plus hidden-xs" type="button" data-id="' . $row['id'] . '"><span class="glyphicon glyphicon-plus"></span></button>
      </span>
    </div>';
                // �� ���������� ������� ����� �������
                if (empty($parent_price_enabled) and empty($row['parent_enabled']) and ! empty($row['parent'])) {
                    continue;
                }

                // �� ���������� ������ � ������� �������
                if ($sklad_status == 2 and $row['items'] < 1 and ! in_array($row['id'], $select)) {
                    continue;
                }

                $PHPShopInterface->setRow(array('name' => $row['name'], 'align' => 'left'), array('name' => $row['price'], 'align' => 'right'), array('name' => $items, 'align' => 'center'));
            }
            // ������ �� ID ������
            else
                $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'align' => 'left', 'class' => 'product-name'), array('name' => $row['price'], 'align' => 'right'));
        }
    @header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    exit('<table class="table table-hover ' . $class . '">' . $PHPShopInterface->getContent() . '</table><p class="clearfix"> </p>');
}

/**
 * ����� ������ �����������
 */
function actionAdvanceSearch() {
    global $PHPShopInterface,$PHPShopModules;

    $PHPShopInterface->field_col = 3;

    // ������ ����������
    parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $query);

    $searchforma = $PHPShopInterface->setField('�������� ������', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[name]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['name'])));
    $searchforma .= $PHPShopInterface->setField('�������', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[uid]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['uid'])));
    $searchforma .= $PHPShopInterface->setField('ID', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[id]', 'placeholder' => '1005', 'class' => 'pull-left', 'value' => $query['where']['id'])));
    $searchforma .= $PHPShopInterface->setField('������� ���', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[external_code]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['external_code'])));
    $searchforma .= $PHPShopInterface->setField('������', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'parent', 'placeholder' => '52', 'class' => 'pull-left', 'value' => $query['parent'])));
    $searchforma .= $PHPShopInterface->setField('��������������', $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'sort', 'placeholder' => '��������������:��������', 'class' => 'pull-left', 'value' => $query['sort'])));
    $searchforma .= $PHPShopInterface->setField('���������', viewCatalog('where[category]', $query['where']['category']));
    $searchforma .= $PHPShopInterface->setField('�����', $PHPShopInterface->setCheckbox('where[spec]', 1, '����.', intval($query['where']['spec'])) .
            $PHPShopInterface->setCheckbox('where[newtip]', 1, '�������', intval($query['where']['newtip'])) .
            $PHPShopInterface->setCheckbox('where[sklad]', 1, '��� � �������', intval($query['where']['sklad'])) .
            $PHPShopInterface->setCheckbox('where[enabled]', 0, '�� ��������', intval($query['where']['enabled'])));
    $value_search[] = array(__('��������� �����'), 'reg', 'reg');
    $value_search[] = array(__('������ ���������'), 'eq', '');
    $searchforma .= $PHPShopInterface->setField('������', $PHPShopInterface->setSelect('core', $value_search, false, false, false, false, false, false, false, false, 'form-control') . $PHPShopInterface->setHelp('��������� ����� REGEXP [^ - ������, $ - �����]'));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => 'catalog'));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'cat', 'value' => $_GET['cat']));

    $searchforma .= '<div class="clearfix search-sort-list">';

    if (!empty($_POST['cat']) && (int) $_POST['cat'] > 0) {
        PHPShopObj::loadClass("sort");
        $PHPShopSort = new PHPShopSort((int) $_POST['cat'], false, false, 'sorttemplate', false, false, false, false, null, true);
        $searchforma .= $PHPShopSort->disp;
    }

    $searchforma .= '</div>';

    $PHPShopInterface->_CODE .= $searchforma;
    
        // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);
    
    @header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    exit($PHPShopInterface->getContent() . '<p class="clearfix"> </p>');
}

/**
 * ������ ������ �������������
 */
function sorttemplate($value, $n, $title, $vendor) {
    global $PHPShopInterface;
    $i = 1;
    $value_new[0] = array(__('�� ���������'), null, null);
    if (is_array($value)) {
        sort($value);
        foreach ($value as $p) {
            $hash = "i" . $n . "-" . $p[1] . "i";
            $value_new[$i] = array($p[0], $hash, null);
            $i++;
        }
    }

    $value = $PHPShopInterface->setSelect('where[vendor][]', $value_new, 300, null, false, $search = true, false, $size = 1, false, false, 'form-control');

    $disp = $PHPShopInterface->setField($title, $value, 1, false, false, 'control-label', 12, false);

    return $disp;
}

// ��������� �������
$PHPShopInterface->getAction();
?>