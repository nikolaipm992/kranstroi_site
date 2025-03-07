<?php

$TitlePage = __("��������");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('sort');
unset($_SESSION['jsort']);

/**
 * ����� �������
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage, $PHPShopBase, $PHPShopGUI;


    $PHPShopInterface->sort_action = false;
    $PHPShopInterface->action_button['�������� �������'] = array(
        'name' => '',
        'action' => 'addNewCat',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('�������� �������') . '"'
    );

    $PHPShopInterface->setActionPanel($TitlePage, false, array('�������� �������'));


    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './catalog/gui/catalog.gui.js', './js/bootstrap-treeview.min.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-treeview.min.css');

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

    // ����� �������
    $sidebarleft[] = array('title' => '���������', 'content' => $search . '<div id="tree">' . $treebar . '</div>', 'title-icon' => '<span class="glyphicon glyphicon-plus addNewCat" data-toggle="tooltip" data-placement="top" title="'.__('�������� �������').'"></span>&nbsp;<span class="glyphicon glyphicon-chevron-down" data-toggle="tooltip" data-placement="top" title="'.__('����������').'"></span>&nbsp;<span class="glyphicon glyphicon-chevron-up" data-toggle="tooltip" data-placement="top" title="'.__('��������').'"></span>&nbsp;<span class="glyphicon glyphicon-search" id="show-category-search" data-toggle="tooltip" data-placement="top" title="'.__('�����').'"></span>');

    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);
    $PHPShopInterface->sidebarLeftCell = 3;


    $PHPShopInterface->_CODE .= '   
    <div class="row intro-row">
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-eye-open"></span> ' . __('������� ��������') . '</div>
             <div class="panel-body text-right panel-intro">
                 <a>' . $PHPShopBase->getNumRows('categories', "where skin_enabled='0'") . '</a>
             </div>
          </div>
       </div>
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-eye-close"></span> ' . __('������� ��������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a>' . $PHPShopBase->getNumRows('categories', "where skin_enabled='1'") . '</a>
               </div>
          </div>
       </div>
        <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> ' . __('�������� � ��� ����') . '</div>
                <div class="panel-body text-right panel-intro">
                <a>' . $PHPShopBase->getNumRows('categories', "where menu='1'") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-th-large"></span> ' . __('������ �� �������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a>' . $PHPShopBase->getNumRows('categories', "where tile='1'") . '</a>
               </div>
          </div>
       </div>
   </div>';

    $PHPShopInterface->_CODE .= '   
    <div class="row intro-row">
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-thumbs-up"></span> ' . __('��������� ������') . '</div>
             <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog&where[spec]=1&where[newtip]=1">' . $PHPShopBase->getNumRows('products', "where spec='1' and newtip='1' and parent_enabled='0'") . '</a>
             </div>
          </div>
       </div>
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-open"></span> ' . __('������ ������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog&where[yml]=1">' . $PHPShopBase->getNumRows('products', "where yml='1'") . '</a>
               </div>
          </div>
       </div>
        <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-flash"></span> ' . __('������ ��� ���') . '</div>
                <div class="panel-body text-right panel-intro">
                <a href="?path=catalog&where[price]=0&core=eq">' . $PHPShopBase->getNumRows('products', "where price='0' and parent_enabled='0'") . '</a>
               </div>
          </div>
       </div>
       <div class="col-md-3 col-xs-6 col-panel">
          <div class="panel panel-default">
             <div class="panel-heading"><span class="glyphicon glyphicon-picture"></span> ' . __('������ ��� ��������') . '</div>
                <div class="panel-body text-right panel-intro">
                 <a href="?path=catalog&where[pic_small]=null&core=eq">' . $PHPShopBase->getNumRows('products', "where pic_small='' and parent_enabled='0'") . '</a>
               </div>
          </div>
       </div>
   </div>';

    $fixVariants = [
        [__('��������� � �������������� ������'), 1, 1],
        [__('�������'), 2, 1]
    ];

    $PHPShopInterface->_CODE .= '<div class="row intro-row">';
    $PHPShopInterface->_CODE .= '<div class="col-md-6 text-center col-panel">';
    $PHPShopInterface->_CODE .= '<div class="panel panel-default fix-products-block">';
    $PHPShopInterface->_CODE .= '<div class="panel-heading"><span class="glyphicon glyphicon-transfer"></span> ' . __('������ � ���������� �����������') . '</div>';
    $PHPShopInterface->_CODE .= '<div class="panel-body text-left panel-intro text-center">';
    $PHPShopInterface->_CODE .= $PHPShopGUI->setSelect('fix_products', $fixVariants, 270);
    $PHPShopInterface->_CODE .= $PHPShopGUI->setButton('���������', 'ok', 'fix-products');
    $PHPShopInterface->_CODE .= '</div>';
    $PHPShopInterface->_CODE .= '</div>';
    $PHPShopInterface->_CODE .= '</div>';

    $fixCategory = [
        [__('������'), 1, 1],
        [__('�������'), 2, 1]
    ];

    $PHPShopInterface->_CODE .= '<div class="col-md-6 text-center col-panel">';
    $PHPShopInterface->_CODE .= '<div class="panel panel-default fix-products-block">';
    $PHPShopInterface->_CODE .= '<div class="panel-heading"><span class="glyphicon glyphicon-transfer"></span> ' . __('��������� � ���������� ��������') . '</div>';
    $PHPShopInterface->_CODE .= '<div class="panel-body text-left panel-intro text-center">';
    $PHPShopInterface->_CODE .= $PHPShopGUI->setSelect('fix_category', $fixCategory, 270);
    $PHPShopInterface->_CODE .= $PHPShopGUI->setButton('���������', 'ok', 'fix-category');
    $PHPShopInterface->_CODE .= '</div>';
    $PHPShopInterface->_CODE .= '</div>';
    $PHPShopInterface->_CODE .= '</div>';
    $PHPShopInterface->_CODE .= '</div>';

    $PHPShopInterface->Compile(3);
}

function actionDeleteProducts() {
    $mode = (int) $_REQUEST['mode'];

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $categories = array_column($orm->select(['id', 'parent_to'], false, false, ['limit' => 1000000]), 'parent_to', 'id');

    // ���� � ����� ��������� ������� ������������, �������� � ������ id ����� ��������� � ���� �� ������������
    $childrens = [];
    foreach ($categories as $id => $parentId) {
        if ((int) $parentId > 0 && !isset($categories[$parentId])) {
            $category = new PHPShopCategory((int) $id);
            $childrens[] = (int) $id;
            foreach (array_column($category->getChildrenCategories(1000, ['id'], false), 'id') as $child) {
                $childrens[] = (int) $child;
            }
        }
    }

    // ������� �� � �� � ������� ���������
    $orm->delete(['id' => sprintf(' IN (%s)', implode(',', $childrens))]);
    foreach ($childrens as $children) {
        unset($categories[$children]);
    }

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $count = $orm->select(["COUNT('id') as count"], ['category' => sprintf(' NOT IN (%s)', implode(',', array_keys($categories)))]);
    if ((int) $count['count'] > 0) {
        if ($mode === 1) {
            $orm->update(['category_new' => 1000004], ['category' => sprintf(' NOT IN (%s)', implode(',', array_keys($categories)))]);
        } else {
            $orm->delete(['category' => sprintf(' NOT IN (%s)', implode(',', array_keys($categories)))]);
        }
    }

    return ['success' => 1, 'count' => $count['count']];
}

function actionDeleteCategory() {
    $mode = (int) $_REQUEST['mode'];
    $count=0;

    $PHPShopProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

    $data = $PHPShopCat->getList(['id', 'name'], false, ['order' => 'id']);
    if (is_array($data))
        foreach ($data as $row) {

            $check_parent = $PHPShopCat->getOne(['id'], ['parent_to' => '=' . $row['id']]);

            if (empty($check_parent)) {

                $where['category'] = "=" . $row['id'] . ' OR dop_cat LIKE \'%#' . $row['id'] . '#%\' ';
                $check_product = $PHPShopProduct->getOne(['id'], $where);

                if (empty($check_product)) {

                    if($mode == 2)
                    $PHPShopCat->delete(['id' => '=' . $row['id']]);
                    else 
                        $PHPShopCat->update(['skin_enabled_new'=>1],['id' => '=' . $row['id']]);
                    
                    $count++;
                }
            }
        }

    return ['success' => 1, 'count' => $count];
}

// ��������� �������
$PHPShopGUI->getAction();
?>