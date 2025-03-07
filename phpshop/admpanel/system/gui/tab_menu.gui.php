<?php

function tab_menu() {
    global $subpath,$hideSite,$hideCatalog;

    if($subpath[0] == 'system')
    ${'menu_active_' . $subpath[1]} = 'active';
    else ${'menu_actives_' . $subpath[0]} = 'active';
    
    $tree = '
       <ul class="nav nav-pills nav-stacked">
       <li class="' . @$menu_active_system . '"><a href="?path=system">'.__('�������� ���������').'</a></li>
       <li class="' . @$menu_active_company . '"><a href="?path=system.company">'.__('���������').'</a></li>
       <li class="' . @$menu_actives_company . '"><a href="?path=company">'.__('����������� ����').'</a></li>
       <li class="' . @$menu_active_sync .$hideSite. '"><a href="?path=system.sync">'.__('����� �������').'</a></li>
       <li class="' . @$menu_active_seo . '"><a href="?path=system.seo">'.__('SEO ���������').'</a></li>
       <li class="' . @$menu_active_currency .$hideCatalog. '"><a href="?path=system.currency">'.__('������').'</a></li>
       <li class="' . @$menu_active_image . '"><a href="?path=system.image">'.__('�����������').'</a></li>
       <li class="' . @$menu_active_servers . '"><a href="?path=system.servers">'.__('�������').'</a></li> 
       <li class="' . @$menu_active_warehouse .$hideCatalog. '"><a href="?path=system.warehouse">'.__('������').'</a></li>
       <li class="' . @$menu_active_dialog . '"><a href="?path=system.dialog">'.__('�������').'</a></li>
       <li class="' . @$menu_active_integration . '"><a href="?path=system.integration">'.__('����������').'</a></li>     
       <li class="' . @$menu_active_yandexcloud . '"><a href="?path=system.yandexcloud">'.__('Yandex Cloud').'</a></li>     
       <li class="' . @$menu_active_locale . '"><a href="?path=system.locale">'.__('�����������').'</a></li>     
       <li class="' . @$menu_active_service . '"><a href="?path=system.service">'.__('������������').'</a></li>     
       <li><a href="?path=tpleditor">'.__('������� �������').'</a></li>
       </ul>';
    
    return $tree;
}