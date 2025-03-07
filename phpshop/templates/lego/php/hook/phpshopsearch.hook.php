<?php

/**
 * ����� ��������� ��� ������
 */
function template_category_select($obj, $data) {
    $dis = null;
    
    // ������������� ���������� ������ �� �������� ������
    //$obj->num_row=10;
    
    // ������  �����
    //$obj->cell=3;
    
    $obj->set('currentSearchCat', __('������� ������� ������'));
    foreach ($obj->value as $val) {
        $dis.='<li><a class="cat-menu-search" data-target="' . $val[1] . '" href="javascript:void(0)">' . $val[0] . '</a></li>';
        if ($val[2] == 'selected')
            $obj->set('currentSearchCat', $val[0]);
    }
    $obj->set('searchPageCategory', $dis);


    if (!empty($_REQUEST['pole']))
        $pole = intval($_REQUEST['pole']);
    else
        $pole = $obj->PHPShopSystem->getSerilizeParam('admoption.search_pole');

    switch ($pole) {
        case 1:
            $obj->set('searchSetCactive', 'active');
            break;
        case 2:
            $obj->set('searchSetDactive', 'active');
            break;
    }

}

$addHandler = array
    (
    'category_select' => 'template_category_select'
);
?>
