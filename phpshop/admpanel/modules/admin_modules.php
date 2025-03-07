<?php

// ���������
$TitlePage = __("������");

function getFileInfo($file) {
    $f = parse_ini_file_true("../../license/" . $file, 1);

    if ($f['License']['Pro'] == 'Start')
        $_SESSION['mod_limit'] = 5;
    elseif ($f['License']['Pro'] == 'Enabled') {
        $_SESSION['mod_pro'] = true;
        $_SESSION['mod_limit'] = 100;
    } else
        $_SESSION['mod_limit'] = 50;

    return $f['License']['SupportExpires'];
}

if (!getenv("COMSPEC"))
    define("EXPIRES", PHPShopFile::searchFile("../../license/", 'getFileInfo'));
else {
    define("EXPIRES", time() + 100000);
    $_SESSION['mod_pro'] = true;
}

// ���������� �� ������
function GetModuleInfo($name) {
    $path = "../modules/" . $name . "/install/module.xml";
    return xml2array($path, false, true);
}

function ChekInstallModule($path, $num = false) {
    global $link_db;

    $return = array();
    $sql = 'SELECT a.*, b.key FROM ' . $GLOBALS['SysValue']['base']['modules'] . ' AS a LEFT OUTER JOIN ' . $GLOBALS['SysValue']['base']['modules_key'] . ' AS b ON a.path = b.path where a.path="' . $path . '"';

    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);

    if (empty($row['key']))
        $row['key'] = null;

    if (mysqli_num_rows($result) > 0) {
        $return[0] = "#C0D2EC";
        $return[1] = array('status' => array('enable' => 1, 'align' => 'right', 'caption' => array('����', '���')));
        $return[2] = $row['date'];
        $return[3] = $row['key'];
    } elseif ($num >= $_SESSION['mod_limit']) {

        $return[0] = "white";
        $return[1] = array('name' => '<span class="glyphicon glyphicon-lock pull-right text-muted" data-toggle="tooltip" data-placement="left" title="����� ��������"></span>');
        $return[2] = null;
        $return[3] = $row['key'];
    } else {
        $return[0] = "white";
        $return[1] = array('status' => array('enable' => 0, 'align' => 'right', 'caption' => array('<span class="text-muted">����</span>', '���')));
        $return[2] = null;
        $return[3] = $row['key'];
    }
    return $return;
}

function actionStart() {
    global $PHPShopInterface, $PHPShopBase, $TitlePage, $hideCatalog, $hideSite, $shop_type;


    $PHPShopInterface->action_select['��������� ���������'] = array(
        'name' => '��������� ���������',
        'action' => 'module-off-select',
        'class' => 'disabled',
        'url' => '#'
    );

    $PHPShopInterface->action_select['�������� ���������'] = array(
        'name' => '�������� ���������',
        'action' => 'module-on-select',
        'class' => 'disabled',
        'url' => '#'
    );

    if ($PHPShopBase->Rule->CheckedRules('modules', 'remove')) {
        $PHPShopInterface->action_button['���������'] = array(
            'name' => '',
            'action' => '',
            'class' => 'btn btn-default btn-sm navbar-btn load-module',
            'type' => 'button',
            'icon' => 'glyphicon glyphicon-plus',
            'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('��������� ������') . '"'
        );
    }

    $PHPShopInterface->action_title['manual'] = '����������';

    if ($_SESSION['mod_limit'] > 5)
        $PHPShopInterface->setActionPanel($TitlePage, array('��������� ���������', '�������� ���������'), array('���������'));
    else
        $PHPShopInterface->setActionPanel($TitlePage, false);

    $PHPShopInterface->setCaption(
            array(null, "3%", array('class' => 'hidden-xs')), array("��������", "60%"), array("�����������", "15%"), array("", "10%"), array("������" . "", "7%", array('align' => 'right'))
    );

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './modules/gui/modules.gui.js');
    $PHPShopInterface->path = 'modules.action';

    $where = false;
    if (!empty($_GET['cat'])) {
        $where = array('category' => '=' . intval($_GET['cat']));
    } else
        $_GET['cat'] = null;

    // ���������� ������������� �������
    if (empty($_GET['install'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modules']);
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => intval($_SESSION['mod_limit'])));
        $num = count($data);
    }

    $path = "../modules/";
    $i = 1;

    if (isset($_GET['install'])) {

        $active_tree_menu = 'install';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modules']);
        $data = $PHPShopOrm->select(array('*'), false, array('order' => 'date desc'), array('limit' => intval($_SESSION['mod_limit'])));
        $num = count($data);
        if (is_array($data))
            foreach ($data as $row) {
                $ChekInstallModule = ChekInstallModule($row['path']);
                $drop_menu = null;

                // ���������� �� ������
                $Info = GetModuleInfo($row['path']);

                if (!empty($hideSite) and empty($Info['site']))
                    continue;

                if (!empty($hideCatalog) and empty($hideSite) and ( empty($Info['catalog']) and empty($Info['site'])))
                    continue;

                if (!empty($_SESSION['support']) and $_SESSION['support'] < $Info['sign'])
                    continue;

                if (!empty($Info['status']))
                    $new = '<span class="label label-primary">' . $Info['status'] . '</span>';
                else
                    $new = null;

                if (!empty($Info['faqlink']))
                    $wikiPath = $Info['faqlink'];
                else
                    $wikiPath = null;

                if (!empty($Info['trial']) and empty($ChekInstallModule[3])) {
                    $trial = ' (Trial 30 ����)';
                } else
                    $trial = null;

                if (!$PHPShopBase->Rule->CheckedRules('modules', 'edit')) {
                    $status = '<span class="glyphicon glyphicon-lock pull-right"></span>';
                    $drop_menu = null;
                } else {
                    $status = $ChekInstallModule[1];

                    if (!empty($wikiPath))
                        $drop_menu = array('option', 'manual', 'id' => $row['path']);
                    else
                        $drop_menu = array('option', 'id' => $row['path']);

                    // ���� ������
                    if (is_array($Info['adminmenu']['podmenu'][0])) {
                        foreach ($Info['adminmenu']['podmenu'] as $menu_value) {
                            array_push($drop_menu, array('name' => $menu_value['podmenu_name'], 'url' => '?path=modules.' . $menu_value['podmenu_action']));
                        }
                    } else {
                        array_push($drop_menu, array('name' => $Info['adminmenu']['podmenu']['podmenu_name'], 'url' => '?path=modules.' . $Info['adminmenu']['podmenu']['podmenu_action']));
                    }

                    array_push($drop_menu, '|');
                    array_push($drop_menu, 'off');
                }

                if (!empty($Info['pro']) and empty($_SESSION['mod_pro']))
                    $drop_menu = null;

                $name = '<div class="modules-list">
                            <a href="?path=modules&id=' . $row['path'] . '" data-wiki="' . $wikiPath . '">' . __($Info['name']) . ' ' . $Info['version'] . $trial . '</a> ' . $new . '<br>' . __($Info['description']) . '</div>';


                $PHPShopInterface->setRow($row['path'], $name, '<span class="install-date">' . PHPShopDate::get($row['date']) . '</span>', array('action' => $drop_menu, 'align' => 'center'), $status);

                $i++;
            }
    } elseif (@$dh = opendir($path)) {



        $active_tree_menu = $_GET['cat'];

        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != "..") {

                if (is_dir($path . $file)) {

                    // ���������� �� ������
                    $Info = GetModuleInfo($file);

                    if (!empty($_SESSION['support']) and $_SESSION['support'] < $Info['sign'])
                        continue;

                    if (!empty($Info['status']))
                        $new = '<span class="label label-primary">' . $Info['status'] . '</span>';
                    else
                        $new = null;

                    if (empty($Info['sign']))
                        $Info['sign'] = null;

                    if (empty($Info['pro']))
                        $Info['pro'] = null;

                    // ���� ������� ���������
                    if (isset($_GET['cat']) and @ strstr($Info['category'], $_GET['cat']) and empty($Info['hidden'])) {

                        // ������� ������� ��� �����
                        if (!empty($hideSite) and empty($Info['site']))
                            continue;

                        // ������� ������� ��� ��������
                        if (!empty($hideCatalog) and empty($hideSite) and ( empty($Info['catalog']) and empty($Info['site'])))
                            continue;

                        $ChekInstallModule = ChekInstallModule($file, $num);

                        // ����������
                        if (!empty($Info['faqlink']))
                            $wikiPath = $Info['faqlink'];
                        else
                            $wikiPath = null;

                        // ���� ���������
                        if (!empty($ChekInstallModule[2])) {
                            $InstallDate = date("d-m-Y", $ChekInstallModule[2]);
                            $drop_menu = array('option', 'manual', '|', 'off', 'id' => $file);
                        } elseif ($num < $_SESSION['mod_limit']) {
                            $InstallDate = null;
                            if (!empty($wikiPath))
                                $drop_menu = array('manual', '|', 'on', 'id' => $file);
                            else
                                $drop_menu = array('on', 'id' => $file);
                        } else {
                            $InstallDate = null;
                            $drop_menu = null;
                        }

                        if (!empty($Info['trial']) and empty($ChekInstallModule[3])) {
                            $trial = ' (Trial 30 ����)';
                        } else
                            $trial = null;

                        if (!$PHPShopBase->Rule->CheckedRules('modules', 'edit') or EXPIRES < $Info['sign'] or ( !empty($Info['pro']) and empty($_SESSION['mod_pro']))) {
                            $status = '<span class="glyphicon glyphicon-lock pull-right"></span>';

                            if (!empty($Info['pro']) and empty($_SESSION['mod_pro']))
                                $new = '<span class="label label-warning">Pro</span>';

                            unset($drop_menu);
                        } else
                            $status = $ChekInstallModule[1];

                        $name = '<div class="modules-list">
                            <a href="?path=modules&id=' . $file . '" data-wiki="' . $wikiPath . '">' . __($Info['name']) . ' ' . $Info['version'] . $trial . '</a> ' . $new . '<br>' . __($Info['description']) . '</div>';


                        $PHPShopInterface->setRow($file, $name, '<span class="install-date">' . $InstallDate . '</span>', array('action' => $drop_menu, 'align' => 'center'), $status);

                        $i++;
                    }
                    // ����� ���� �������
                    elseif (empty($_GET['cat']) and empty($Info['hidden'])) {

                        // ������� ������� ��� �����
                        if (!empty($hideSite) and empty($Info['site']))
                            continue;

                        // ������� ������� ��� ��������
                        if (!empty($hideCatalog) and empty($hideSite) and ( empty($Info['catalog']) and empty($Info['site'])))
                            continue;

                        $active_tree_menu = 'all';

                        $ChekInstallModule = ChekInstallModule($file, $num);

                        if (!empty($Info['status']))
                            $new = '<span class="label label-primary">' . $Info['status'] . '</span>';
                        else
                            $new = null;

                        // ����������
                        if (!empty($Info['faqlink']))
                            $wikiPath = $Info['faqlink'];
                        else
                            $wikiPath = null;


                        // ���� ���������
                        if (!empty($ChekInstallModule[2])) {
                            $InstallDate = date("d-m-Y", $ChekInstallModule[2]);
                            $drop_menu = array('option', 'manual', '|', 'off', 'id' => $file);
                        } elseif ($num < $_SESSION['mod_limit']) {
                            $InstallDate = null;
                            if (!empty($wikiPath))
                                $drop_menu = array('manual', '|', 'on', 'id' => $file);
                            else
                                $drop_menu = array('on', 'id' => $file);
                        } else {
                            $InstallDate = null;
                            $drop_menu = null;
                        }

                        if (!empty($Info['trial']) and empty($ChekInstallModule[3])) {
                            $trial = ' (Trial 30 ����)';
                        } else
                            $trial = null;

                        if (!$PHPShopBase->Rule->CheckedRules('modules', 'edit') or EXPIRES < $Info['sign'] or ( !empty($Info['pro']) and empty($_SESSION['mod_pro']))) {
                            $status = '<span class="glyphicon glyphicon-lock pull-right"></span> ';
                            if (!empty($Info['pro']) and empty($_SESSION['mod_pro']))
                                $new = '<span class="label label-warning">Pro</span>';

                            unset($drop_menu);
                        } else
                            $status = $ChekInstallModule[1];

                        $name = '<div class="modules-list">
                            <a href="?path=modules&id=' . $file . '" data-wiki="' . $wikiPath . '">' . __($Info['name']) . ' ' . $Info['version'] . $trial . '</a> ' . $new . '<br>' . __($Info['description']) . '</div>';

                        $PHPShopInterface->setRow($file, $name, '<span class="install-date">' . $InstallDate . '</span>', array('action' => $drop_menu, 'align' => 'center'), $status);
                        $i++;
                    }
                }
            }
        }
        closedir($dh);
    }

    if ($num == $_SESSION['mod_limit'])
        $label_class = 'label-warning';
    else
        $label_class = 'label-primary';

    // ���-�� �������
    $count_mod = [
        0 => [
            'pro' => 15,
            'template' => 8,
            'seo' => 5,
            'delivery' => 11,
            'chat' => 7,
            'crm' => 6,
            'marketplaces' => 7,
            'payment' => 28,
            'credit' => 4,
            'yandex' => 3,
            'sale' => 19,
            'develop' => 15,
            'minus' => -16
        ],
        1 => [
            'pro' => 3,
            'template' => 8,
            'seo' => 5,
            'delivery' => 2,
            'chat' => 7,
            'crm' => 2,
            'yandex' => 1,
            'sale' => 8,
            'develop' => 14,
            'minus' => -3
        ],
        2 => [
            'template' => 5,
            'seo' => 4,
            'delivery' => 2,
            'chat' => 7,
            'crm' => 1,
            'yandex' => 1,
            'sale' => 1,
            'develop' => 12
        ]
    ];

    foreach ($count_mod as $k => $count_type) {
        foreach ($count_type as $mod) {
            $count_mod[$k]['all'] += $mod;
        }
    }

    $tree = '<table class="table table-hover">
        <tr class="treegrid-all">
           <td><a href="?path=modules" class="treegrid-parent" data-parent="treegrid-all">' . __('��� ������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['all'] . '</span></td>
	</tr>
        <tr class="treegrid-pro ' . $hideSite . '">
           <td><a href="?path=modules&cat=pro" class="treegrid-parent" data-parent="treegrid-pro">' . __('Pro') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['pro'] . '</span></td>
	</tr>
        <tr class="treegrid-template">
           <td><a href="?path=modules&cat=template" class="treegrid-parent" data-parent="treegrid-template">' . __('������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['template'] . '</span></td>
	</tr>
        <tr class="treegrid-seo">
           <td><a href="?path=modules&cat=seo" class="treegrid-parent" data-parent="treegrid-seo">SEO</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['seo'] . '</span></td>
	</tr>
        <tr class="treegrid-delivery">
           <td><a href="?path=modules&cat=delivery" class="treegrid-parent" data-parent="treegrid-delivery">' . __('��������') . '</a> <span  class="label label-primary pull-right">' . $count_mod[$shop_type]['delivery'] . '</span></td>
	</tr>
        <tr class="treegrid-chat">
           <td><a href="?path=modules&cat=chat" class="treegrid-parent" data-parent="treegrid-delivery">' . __('���� � ������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['chat'] . '</span></td>
	</tr>
        <tr class="treegrid-crm">
           <td><a href="?path=modules&cat=crm" class="treegrid-parent" data-parent="treegrid-crm">CRM</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['crm'] . '</span></td>
	</tr>
        <tr class="treegrid-marketplaces ' . $hideCatalog . '">
           <td><a href="?path=modules&cat=marketplaces" class="treegrid-parent" data-parent="treegrid-payment">' . __('������������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['marketplaces'] . '</span></td>
	</tr>
        <tr class="treegrid-payment ' . $hideCatalog . '">
           <td><a href="?path=modules&cat=payment" class="treegrid-parent" data-parent="treegrid-payment">' . __('��������� �������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['payment'] . '</span></td>
	</tr>
       <tr class="treegrid-credit ' . $hideCatalog . '">
           <td><a href="?path=modules&cat=credit" class="treegrid-parent" data-parent="treegrid-payment">' . __('������������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['credit'] . '</span></td>
	</tr>
        <tr class="treegrid-yandex">
           <td><a href="?path=modules&cat=yandex" class="treegrid-parent" data-parent="treegrid-yandex">' . __('������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['yandex'] . '</span></td>
	</tr>
        <tr class="treegrid-sale">
           <td><a href="?path=modules&cat=sale" class="treegrid-parent" data-parent="treegrid-sale">' . __('�������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['sale'] . '</span></td>
	</tr>
        <tr class="treegrid-develop">
           <td><a href="?path=modules&cat=develop" class="treegrid-parent" data-parent="treegrid-develop">' . __('�������������') . '</a> <span class="label label-primary pull-right">' . $count_mod[$shop_type]['develop'] . '</span></td>
	</tr>
        <tr class="treegrid-install">
           <td><a href="?path=modules&install=check" class="treegrid-parent" data-parent="treegrid-install">' . __('�������������') . '</a> <span id="mod-install-count" class="label ' . $label_class . ' pull-right">' . $num . '</span></td>
	</tr>
    </table>
    <script>
    var modcat="' . $active_tree_menu . '";
    </script>';

    $sidebarleft[] = array('title' => '���������', 'content' => $tree);
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

?>