<?php

/**
 * �������������� ���������
 */
function tab_menu_service() {
    global $subpath, $hideSite;

    ${'menu_active_' . $subpath[1]} = 'active';
    
   
    $tree = '
       <ul class="nav nav-pills nav-stacked">
       <li class="' . @$menu_active_service. '"><a href="?path=exchange.service">'.__('������� ���� ������').'</a></li>
       <li class="' . @$menu_active_file.$hideSite. '"><a href="?path=exchange.file">'.__('�������� �����������').'</a></li>
       <li class="' . @$menu_active_product.$hideSite. '"><a href="?path=product">'.__('�������� ���������').'</a></li>
       <li class="' . @$menu_active_uniqname.$hideSite. '"><a href="?path=product.uniqname">'.__('�������� ��������').'</a></li>
       <li class="' . @$menu_active_sql . '"><a href="?path=exchange.sql">'.__('SQL ������ � ����').'</a></li>
       <li class="' . @$menu_active_backup . '"><a href="?path=exchange.backup">'.__('��������� �����������').'</a></li>
       </ul>';
    
    return $tree;
}

?>