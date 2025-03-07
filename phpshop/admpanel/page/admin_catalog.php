<?php

// ���������
$TitlePage = __("��������");
PHPShopObj::loadClass('page');

/**
 * ����� �������
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopCategoryArray = new PHPShopPageCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (empty($_GET['cat']))
        $_GET['cat'] = 0;

    if (!empty($CategoryArray[$_GET['cat']]['name']))
        $catname = " &rarr; " . $CategoryArray[$_GET['cat']]['name'];
    else
        $catname = null;


    $PHPShopInterface->action_select['������������� �������'] = array(
        'name' => '������������� �������',
        'url' => '?path=' . $_GET['path'] . '&id=' . intval($_GET['cat'])
    );

    $PHPShopInterface->action_select['����� �������'] = array(
        'name' => '����� �������',
        'url' => '?path=' . $_GET['path'] . '&action=new',
        'class' => 'enabled'
    );

    $PHPShopInterface->action_select['���������'] = array(
        'name' => '��������� �����',
        'action' => 'option enabled'
    );

    if (empty($_GET['cat']))
        $PHPShopInterface->action_select['������������� �������']['class'] = 'disabled';


    $PHPShopInterface->action_title['copy'] = '������� �����';
    $PHPShopInterface->action_title['url'] = '������� URL';

    $PHPShopInterface->action_button['�������� ��������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="'.__('�������� ��������').'" data-cat="' . $_GET['cat'] . '"'
    );

    // ��������� �����
    if (!empty($_COOKIE['check_memory'])) {
        $memory = json_decode($_COOKIE['check_memory'], true);
    }

    if (empty($memory) or ! is_array($memory['page.option']) or count($memory['page.option']) < 1) {
        $memory['page.option']['link'] = 1;
        $memory['page.option']['name'] = 1;
        $memory['page.option']['server'] = 0;
        $memory['page.option']['menu'] = 1;
        $memory['page.option']['status'] = 1;
    }

    $PHPShopInterface->setActionPanel($TitlePage . $catname, array('����� �������', '���������', '������������� �������', '|', '������� ���������'), array('�������� ��������'));
    $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "40%", array('view' => intval($memory['page.option']['name']))), array("�������", "15%", array('view' => intval($memory['page.option']['server']))), array("", "7%", array('view' => intval($memory['page.option']['menu']))), array("������" . "", "7%", array('align' => 'right', 'view' => intval($memory['page.option']['status']))));

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './js/bootstrap-datetimepicker.min.js', './page/gui/page.gui.js');


    $where = false;
    if (!empty($_GET['cat'])) {
        $where = array('category' => '=' . intval($_GET['cat']));
    }

    // �������
    $PHPShopServerOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
    $data_server = $PHPShopServerOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 1000));

    if (is_array($data_server)) {
        $server_value[1000] = __('������� ����');
        foreach ($data_server as $row) {
            $server_value[$row['id']] = PHPShopString::check_idna($row['host'], true);
        }
    }

    // SSL
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
        $ssl = 'https://';
    else $ssl = 'http://';

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num,id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            // Enabled
            if (empty($row['enabled']))
                $enabled = 'text-muted';
            else
                $enabled = null;

            // �������
            if (!empty($row['servers'])) {
                $servers = preg_split('/i/', $row['servers'], -1, PREG_SPLIT_NO_EMPTY);
                $server = null;
                if (is_array($servers))
                    foreach ($servers as $s)
                        $server .= $server_value[$s] . '<br>';
            } else
                $server = null;

            $PHPShopInterface->path = 'page&return=page.catalog';

            $name = '<a href="?path=page&return=' . $_GET['path'] . '.'.$row['category'].'&id=' . $row['id'] . '" class="' . $enabled . '">' . $row['name'] . '</a><br><span class="text-muted">' . $ssl. $_SERVER['SERVER_NAME'] . '/page/' . $row['link'] . '.html</span>';

            $PHPShopInterface->setRow(
                    $row['id'], array('name' => $name, 'view' => intval($memory['page.option']['name'])), array('name' => $server, 'align' => 'left', 'class' => 'text-muted', 'view' => intval($memory['page.option']['server'])), array('action' => array('edit', 'url', '|', 'delete', 'id' => $row['id']), 'view' => intval($memory['page.option']['menu']), 'align' => 'center'), array('status' => array('enable' => intval($row['enabled']), 'align' => 'right', 'caption' => array('����', '���')), 'view' => intval($memory['page.option']['status']))
            );
        }

    // ����� ������� ������ ���������
    $CategoryArray[0]['name'] = '������';
    $tree_array = array();
    $PHPShopCategoryArrayKey = $PHPShopCategoryArray->getKey('parent_to.id', true);
    if (is_array($PHPShopCategoryArrayKey))
        foreach ($PHPShopCategoryArrayKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = @$CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }

    $GLOBALS['tree_array'] = &$tree_array;

    $PHPShopInterface->path = 'page';

    $tree = '<table class="table table-hover">
         <tr class="treegrid-0">
           <td><a href="?path=' . $_GET['path'] . '">' . __('��� ��������') . '</a></td>
	</tr>';
    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], $k);
            if (empty($check))
                $tree .= '<tr class="treegrid-' . $k . ' data-tree">
		<td><a href="?path=' . $_GET['path'] . '&cat=' . $k . '">' . $v . '</a></td>
	</tr>';
            else
                $tree .= '<tr class="treegrid-' . $k . ' data-tree">
		<td><a href="#" class="treegrid-parent" data-parent="treegrid-' . $k . '">' . $v . '</a></td>
	</tr>';
            $tree .= $check;
        }
    $tree .= '
         <tr class="treegrid-1000 treegrid-parent-100000 data-row">
           <td><a href="?path=' . $_GET['path'] . '&cat=1000"><span class="glyphicon glyphicon-th-list"></span>' . __('������� ���� �����') . '</a></td>
	</tr>
        <tr class="treegrid-2000 treegrid-parent-100000 data-row">
           <td><a href="?path=' . $_GET['path'] . '&cat=2000"><span class="glyphicon glyphicon-bookmark"></span>' . __('��������� ��������') . '</a></td>
	</tr>
</table>
  <script>
    var cat="' . intval($_GET['cat']) . '";
    </script>';

    $sidebarleft[] = array('title' => '���������', 'content' => $tree, 'title-icon' => '<span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('�������� �������') . '"></span>');
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

// ���������� ������ ���������
function treegenerator($array, $parent) {
    global $PHPShopInterface, $tree_array;
    $tree = $check = false;
    $PHPShopInterface->path = $_GET['path'];
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], $k);

            if (empty($check))
                $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=' . $_GET['path'] . '&cat=' . $k . '">' . $v . '</a><span class="pull-right">' . $PHPShopInterface->setDropdownAction(array('edit', '|', 'delete', 'id' => $k)) . '</span></td>
	</tr>';
            else
                $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="#" class="treegrid-parent" data-parent="treegrid-' . $k . '">' . $v . '</a><span class="pull-right">' . $PHPShopInterface->setDropdownAction(array('edit', '|', 'delete', 'id' => $k)) . '</span></td>
	</tr>';

            $tree .= $check;
        }
    }
    return $tree;
}

/**
 * ��������� ����� - 1 ���
 */
function actionOption() {
    global $PHPShopInterface;

    // ������ ��������� �����
    if (!empty($_COOKIE['check_memory'])) {
        $memory = json_decode($_COOKIE['check_memory'], true);
    }
    if (!is_array($memory['page.option']) or count($memory['page.option']) < 1) {
        $memory['page.option']['link'] = 1;
        $memory['page.option']['name'] = 1;
        $memory['page.option']['server'] = 0;
        $memory['page.option']['menu'] = 1;
        $memory['page.option']['status'] = 1;
    }

    $message = '<p class="text-muted">' . __('�� ������ �������� �������� ����� � ������� ����������� �������') . '.</p>';

    $searchforma = $message .
            $PHPShopInterface->setCheckbox('name', 1, '��������', $memory['page.option']['name']) .
            $PHPShopInterface->setCheckbox('server', 1, '�������', $memory['page.option']['server']) .
            $PHPShopInterface->setCheckbox('menu', 1, '����� ����', $memory['page.option']['menu']) .
            $PHPShopInterface->setCheckbox('status', 1, '������', $memory['page.option']['status']);


    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => 'page.catalog'));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'cat', 'value' => $_REQUEST['cat']));

    $searchforma .= '<p class="clearfix"> </p>';


    $PHPShopInterface->_CODE .= $searchforma;

    exit($PHPShopInterface->getContent() . '<p class="clearfix"> </p>');
}

/**
 * ��������� ����� - 2 ���
 */
function actionOptionSave() {

    // ������ ��������� �����
    if (is_array($_POST['option'])) {

        $memory = json_decode($_COOKIE['check_memory'], true);
        unset($memory['page.option']);
        foreach ($_POST['option'] as $k => $v) {
            $memory['page.option'][$k] = $v;
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000 * 6, $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/admpanel/');
    }

    return array('success' => true);
}

// ��������� �������
$PHPShopGUI->getAction();
?>