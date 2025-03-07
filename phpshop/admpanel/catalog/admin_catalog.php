<?php

$TitlePage = __("������");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('sort');
unset($_SESSION['jsort']);

/**
 * ����� �������
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage, $PHPShopSystem, $PHPShopBase;

    // ����� ����������
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    if (empty($_GET['cat']))
        $_GET['cat'] = null;

    if (empty($_GET['sub']))
        $_GET['sub'] = null;


    $where['id'] = '=' . intval($_GET['cat']);

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $PHPShopCategoryArray->order = array('order' => 'num, name');
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (!empty($CategoryArray[$_GET['cat']]['name']))
        $catname = '  &rarr;  <span id="catname">' . $CategoryArray[$_GET['cat']]['name'] . '</span>';
    elseif (!empty($CategoryArray[$_GET['sub']]['name']))
        $catname = '  &rarr;  <span id="catname">' . $CategoryArray[$_GET['sub']]['name'] . '</span>';
    elseif (isset($_GET['where']) and !empty($_GET['where']['name']))
        $catname = '  &rarr;  <span id="catname">' . __('�����') . '</span>';
    else
        $catname = '  &rarr;  <span id="catname">' . __('����� ������') . '</span>';

    // ����� ����������
    if ($secure_groups and isset($_GET['cat']) and empty($CategoryArray[$_GET['cat']]['name'])) {
        $catname = " /  <span class='text-danger'><span class='glyphicon glyphicon-lock'></span> " . __('������ ������') . '</span>';
        $_GET['where']['disabled'] = true;
    }

    $PHPShopInterface->action_select['������������'] = array(
        'name' => '������������',
        'class' => 'cat-view hide',
    );

    $PHPShopInterface->action_select['������������� ���������'] = array(
        'name' => '������������� ���������',
        'action' => 'edit-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['����������� ID ���������'] = array(
        'name' => '����������� ID ���������',
        'action' => 'copy-id-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['�������� ���������'] = array(
        'name' => '�������� ���������',
        'action' => 'id-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['������ �� ���������� ���������'] = array(
        'name' => '������ �� ���������� ���������',
        'action' => 'id-select-delete',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['���������'] = array(
        'name' => '��������� �����',
        'action' => 'option enabled'
    );

    $PHPShopInterface->action_select['�����'] = array(
        'name' => '<span class=\'glyphicon glyphicon-search\'></span> ����������� �����',
        'action' => 'search enabled'
    );

    $PHPShopInterface->action_select['������������� �������'] = array(
        'name' => '������������� �������',
        'action' => 'enabled',
        'class' => 'cat-select hide',
        'url' => '?path=' . $_GET['path'] . '&id=' . intval($_COOKIE['cat']) . '&return=catalog.' . intval($_COOKIE['cat'])
    );

    $PHPShopInterface->action_title['copy'] = '������� �����';
    $PHPShopInterface->action_title['url'] = '������� URL';

    $PHPShopInterface->action_button['�������� �����'] = array(
        'name' => '',
        'action' => 'addNewModal',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('�������� �����') . '" data-cat="' . $_GET['cat'] . '"'
    );

    // ���������� �������� ���������
    if ($PHPShopSystem->getSerilizeParam('admoption.fast_view') == 1)
        $PHPShopInterface->action_button['�������� �����']['action'] = 'addNew';

    // ������� ���� ���� ����� �����
    $count_view = 0;

    if (is_array($PHPShopInterface->getProductTableFields()['catalog.option']))
        foreach ($PHPShopInterface->getProductTableFields()['catalog.option'] as $view)
            if (!empty($view))
                $count_view++;

    if ($count_view > 8 and empty($_COOKIE['fullscreen']))
        $function_del = $function_pre_del = null;
    else {
        $function_del = '������� ���������';
        $function_pre_del ='|';
    }


    $PHPShopInterface->setActionPanel($TitlePage . $catname, array('�����', '|', '������������', '���������', '������������� �������', '������������� ���������', 'CSV', '|', '����������� ID ���������', '�������� ���������', '������ �� ���������� ���������', $function_pre_del, $function_del), array('�������� �����'));

    $PHPShopInterface->setCaption(
            ...getTableCaption()
    );

    $PHPShopInterface->addJSFiles('./catalog/gui/catalog.gui.js', './js/bootstrap-treeview.min.js', './js/bootstrap-colorpicker.min.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-treeview.min.css', './css/bootstrap-colorpicker.min.css');
    $PHPShopInterface->path = 'catalog';

    // �����������
    $treebar = '<div class="progress">
  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
    <span class="sr-only">' . __('��������') . '..</span>
  </div>
</div>';

    // ����� ���������
    $search = '<div class="none" id="category-search" style="padding-bottom:5px;"><div class="input-group input-sm">
                <input type="input" class="form-control input-sm" type="search" id="input-category-search" placeholder="' . __('������ � ����������...') . '" value="">
                 <span class="input-group-btn">
                  <a class="btn btn-default btn-sm" id="btn-search" type="submit"><span class="glyphicon glyphicon-search"></span></a>
                 </span>
            </div></div>';

    $sidebarleft[] = array('title' => '���������', 'content' => $search . '<div id="tree">' . $treebar . '</div>', 'title-icon' => '<div class="hidden-xs"><span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('�������� �������') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-down" data-toggle="tooltip" data-placement="top" title="' . __('���������� ���') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-up" data-toggle="tooltip" data-placement="top" title="' . __('��������') . '"></span>&nbsp;<span class="glyphicon glyphicon-search" id="show-category-search" data-toggle="tooltip" data-placement="top" title="' . __('�����') . '"></span></div>');

    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

function getTableCaption() {
    global $PHPShopInterface, $PHPShopModules, $PHPShopSystem;

    $memory = $PHPShopInterface->getProductTableFields();

    // ��������� ������
    if (PHPShopString::is_mobile()) {
        $PHPShopInterface->mobile = true;
    }

    // �������������� �����
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));

    // ������� ���� ���� ����� �����
    $count_view = 0;

    if (is_array($memory['catalog.option']))
        foreach ($memory['catalog.option'] as $view)
            if (!empty($view))
                $count_view++;

    if ($count_view > 8 and empty($_COOKIE['fullscreen']))
        unset($memory['catalog.option']['menu']);

    // ����� ��������
    if ($PHPShopSystem->getParam("shop_type") == 1) {
        $memory['catalog.option']['price'] = 0;
        $memory['catalog.option']['price2'] = 0;
        $memory['catalog.option']['price3'] = 0;
        $memory['catalog.option']['price4'] = 0;
        $memory['catalog.option']['price5'] = 0;
        $memory['catalog.option']['price_n'] = 0;
        $memory['catalog.option']['price_purch'] = 0;
        $memory['catalog.option']['item'] = 0;
    }

    $PHPShopInterface->productTableCaption = [
        [null, "2%"],
        ["������", "5%", ['sort' => 'none', 'view' => (int) $memory['catalog.option']['icon']]],
        ["��������", "40%", ['view' => (int) $memory['catalog.option']['name']]],
        ["�", "10%", ['view' => (int) $memory['catalog.option']['num']]],
        ["ID", "10%", ['view' => (int) $memory['catalog.option']['id']]],
        ["�������", "15%", ['view' => (int) $memory['catalog.option']['uid']]],
        ["����", "10%", ['view' => (int) $memory['catalog.option']['price']]],
        ["���� 2", "10%", ['view' => (int) $memory['catalog.option']['price2']]],
        ["���� 3", "10%", ['view' => (int) $memory['catalog.option']['price3']]],
        ["���� 4", "10%", ['view' => (int) $memory['catalog.option']['price4']]],
        ["���� 5", "10%", ['view' => (int) $memory['catalog.option']['price5']]],
        ["��. ����", "10%", ['view' => (int) $memory['catalog.option']['price_n']]],
        ["���. ����", "10%", ['view' => (int) $memory['catalog.option']['price_purch']]],
        ["���-��", "10%", ['view' => (int) $memory['catalog.option']['item']]],
        [@$dataWarehouse[0]['name'], "10%", ['view' => (int) $memory['catalog.option']['items1']]],
        [@$dataWarehouse[1]['name'], "10%", ['view' => (int) $memory['catalog.option']['items2']]],
        [@$dataWarehouse[2]['name'], "10%", ['view' => (int) $memory['catalog.option']['items3']]],
        ["��������������", "25%", ['view' => (int) $memory['catalog.option']['sort']]]
    ];

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $PHPShopInterface->productTableCaption);

    $PHPShopInterface->productTableCaption[] = ["", "7%", ['view' => (int) $memory['catalog.option']['menu']]];
    $PHPShopInterface->productTableCaption[] = ["������", "7%", ['align' => 'right', 'view' => (int) $memory['catalog.option']['status']]];

    return $PHPShopInterface->productTableCaption;
}

// ��������� �������
$PHPShopGUI->getAction();
?>