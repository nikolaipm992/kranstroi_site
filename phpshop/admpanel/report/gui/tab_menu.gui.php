<?php

function tab_menu() {
    global $subpath;

    ${'menu_active_' . $subpath[1]} = 'active';
    
    $tree = '
       <ul class="nav nav-pills nav-stacked">
       <li class="' . @$menu_active_statorder . '"><a href="?path=report.statorder">'.__('������� �� �������').'</a></li>
       <li class="' . @$menu_active_statproduct . '"><a href="?path=report.statproduct">'.__('������ �� �������').'</a></li>    
       <li class="' . @$menu_active_statuser . '"><a href="?path=report.statuser">'.__('��� 10 ����������').'</a></li>
       <li class="' . @$menu_active_statpayment . '"><a href="?path=report.statpayment">'.__('������� �������').'</a></li>
       </ul>';
    
    return $tree;
}

?>
