<?php

/**
 * �������������� ���������
 */
function tab_menu() {
    global $subpath, $hideCatalog;

    ${'menu_active_' . $subpath[2]} = 'active';
    
   
    $tree = '
       <ul class="nav nav-pills nav-stacked">
       <li class="' . @$menu_active_ . '"><a href="?path=exchange.'.$subpath[1].'">'.__('������').'</a></li>
       <li class="' . @$menu_active_catalog . '"><a href="?path=exchange.'.$subpath[1].'.catalog">'.__('��������').'</a></li>
       <li class="' . @$menu_active_user . '"><a href="?path=exchange.'.$subpath[1].'.user">'.__('������������').'</a></li>
       <li class="' . @$menu_active_order .$hideCatalog. '"><a href="?path=exchange.'.$subpath[1].'.order">'.__('������').'</a></li>
       </ul>';
    
    return $tree;
}

?>