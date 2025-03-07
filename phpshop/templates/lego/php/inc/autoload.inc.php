<?php

define("SkinName", "lego");

// Цветовые темы CSS
if (isset($_COOKIE[SkinName . '_theme'])) {
    if (PHPShopSecurity::true_skin($_COOKIE[SkinName . '_theme'])) {
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = $_COOKIE[SkinName . '_theme'];
    }
    else
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
} 
elseif (empty($GLOBALS['SysValue']['other'][SkinName . '_theme'])) {
    $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
    setcookie(SkinName . '_theme', 'bootstrap-theme-default');
}
else
    setcookie(SkinName . '_theme', $GLOBALS['SysValue']['other'][SkinName . '_theme']);

function create_theme_menu($file) {

    $current = $GLOBALS['SysValue']['other'][SkinName . '_theme'];
    if (empty($current))
        $current = 'bootstrap-theme-default';

    $color = array(
        'skyblue' => '#88CEEB',
        'red'=>'#FF161D',
        'blue'=>'#576CA8',
        'orchid'=>'#FEA079',
        'default'=>'#F9D400'
     
    );
    if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {
        $icon = $color[$match[1]];
        if (empty($icon))
            $icon = $match[1];

        if ($current == 'bootstrap-theme-' . $match[1])
            $check = '<span class="glyphicon glyphicon-ok"></span>';
        else
            $check = null;

        return '<div class="bootstrap-theme text-center" style="background:' . $icon . '" title="' . $match[1] . '" data-random="?rand=' . time() . '" data-skin="bootstrap-theme-' . $match[1] . '">' . $check . '</div>';
    }
}

// Редактор тем оформления
if ($GLOBALS['SysValue']['template_theme']['user'] == 'true' or !empty($_SESSION['logPHPSHOP']) or !empty($GLOBALS['SysValue']['other']['skinSelect'])) {

    // CSS
    $PHPShopCssParser = new PHPShopCssParser($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/' . $GLOBALS['SysValue']['other'][SkinName . '_theme'] . '.css');
    $css_parse = $PHPShopCssParser->parse();

    // XML
    PHPShopObj::loadClass(array('xml', 'admgui'));
    $PHPShopGUI = new PHPShopGUI();
    $PHPShopGUI->collapse_old_style = true;

    // bootstrap-colorpicker
    $PHPShopGUI->addCSSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/bootstrap-colorpicker.min.css', $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/editor.css');
    $PHPShopGUI->addJSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/js/bootstrap-colorpicker.min.js', $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/js/editor.js');

    $option = xml2array($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/editor/style.xml', false, true);
    $css_edit=$PHPShopGUI->includeJava . $PHPShopGUI->includeCss;

    $option['element'][] = $option['element'];
    $css_edit_add = $theme_menu = $admin_edit = null;
    if (is_array($option))
        foreach ($option['element'] as $id => $element) {

            if(!empty($element['var']))
            $element_var[0] = $element['var'];

            if (is_array($element_var))
                foreach ($element_var as $var) {

                    // Цвет
                    if ($var['type'] == 'color') {
                        
                        if(empty($element['description']))
                            $element['description']=null;
                        
                        if(empty($element['content']))
                            $element['content']=null;
                        
                        if(!empty($element['name']))
                        $css_edit_add.=$PHPShopGUI->setField($element['description'], $PHPShopGUI->setInputColor($var['name'], str_replace(array('!important'), array(''), $css_parse[$element['name']][$var['name']]), 130, 'color-' . $id, $element['name']), 5, $element['content']);
                    }

                    // Фильтр
                    else if ($var['type'] == 'slider') {
                        $current_filter = $PHPShopCssParser->getParam($element['name'], '-editor-filter');

                        $filter = '<div id="color-slide" data-option="' . $current_filter . '"></div><input type="hidden" name="filter" class="color-filter color-value" value="' . $current_filter . '" data-option="' . $element['name'] . '" id="color-' . $id . '"> ';
                        $css_edit_add.=$PHPShopGUI->setField($element['description'], $filter, 5, $element['content']);
                    }

                    // Тема
                    else if ($var['type'] == 'theme') {

                        if (!empty($_COOKIE['bootstrap_theme']) and $_COOKIE['bootstrap_theme'] == 'bootstrap')
                            $check = '<span class="glyphicon glyphicon-ok"></span>';
                        else
                            $check = null;

                       
                        $theme= PHPShopFile::searchFile($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/', 'create_theme_menu');
                        $css_edit_theme=$PHPShopGUI->setCollapse($element['description'], $theme.'', 'in', false, false);
                    }
                    // Изображение
                    else if ($var['type'] == 'image') {

                        // Файлменеджер
                        if (!empty($_SESSION['logPHPSHOP'])) {
                            $start_filemanager = 'modal';
                    
                            $css_filemanager = ' 
                            <!-- Modal filemanager -->
        <div class="modal bs-example-modal-lg" id="elfinderModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                        <span class="btn btn-default btn-sm pull-left glyphicon glyphicon-fullscreen" id="filemanagerwindow" data-toggle="tooltip" data-placement="bottom" title="Увеличить размер" style="margin-right:10px"></span>

                        <h4 class="modal-title">{Найти файл}</h4>
                    </div>
                    <div class="modal-body">
                        <iframe class="elfinder-modal-content" data-path="image" frameborder="0" data-option="return=icon_new" id="admin-modal-filemanager"></iframe>

                    </div>
                </div>
            </div>
        </div>
        <!--/ Modal filemanager -->';
                        } else {
                            $css_filemanager = null;
                            $start_filemanager = 'alert';
                        }


                        // Путь к изображению
                        @preg_match("/url\((.*)\) no-repeat center/i", $css_parse[$element['name']][$var['name']], $match);

                        $image_select = '<div class="input-group" style=""><input class="form-control input-sm image-value" value="' . $match[1] . '" name="background" placeholder="" data-option="' . $element['name'] . '" type="text" id="color-' . $id . '"><div class="input-group-addon input-sm"><a href="#" title="Выбрать файл" data-return="return=color-' . $id . '" data-toggle="' . $start_filemanager . '" data-target="#elfinderModal" data-path="image" style="font-size: 14px"><span class="glyphicon glyphicon-picture"></span></a></div></div>';

                        $css_edit_add.=$PHPShopGUI->setField($element['description'], $image_select, 5, $element['content']);
                    }
                
                }
        }
        //$css_edit.=$PHPShopGUI->setLine('<br>');

        ${'h_active'.$_SESSION['editor'][SkinName]['h']}='activeBlock';
        ${'c_active'.$_SESSION['editor'][SkinName]['c']}='activeBlock';
        ${'p_active'.$_SESSION['editor'][SkinName]['p']}='activeBlock';
        ${'s_active'.$_SESSION['editor'][SkinName]['s']}='activeBlock';
        ${'f_active'.$_SESSION['editor'][SkinName]['f']}='activeBlock';

        // Шапка
        $css_edit_theme.=$PHPShopGUI->setCollapse('Шапка',
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon8.svg', 65, 55,null,3,null,null,null,@$h_active1), null, null, 'Шапка 1', 'setBlock','lego_h=1').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon7.svg', 65, 55,null,3,null,null,null,@$h_active2), null, null, 'Шапка 2', 'setBlock','lego_h=2').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon6.svg', 65, 55,null,3,null,null,null,@$h_active3), null, null, 'Шапка 2', 'setBlock','lego_h=3')
                ,'in', false, false);
       
        // Каталог
        $css_edit_theme.=$PHPShopGUI->setCollapse('Каталог',
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon15.svg', 50, 55,null,3,null,null,null,@$c_active1), null, null, 'Каталог 1', 'setBlock','lego_c=1').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon14.svg', 50, 55,null,3,null,null,null,@$c_active2), null, null, 'Каталог 2', 'setBlock','lego_c=2').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon13.svg', 50, 55,null,3,null,null,null,@$c_active3), null, null, 'Каталог 2', 'setBlock','lego_c=3')
                
                ,'in', false, false);
         
        // Товар
        $css_edit_theme.=$PHPShopGUI->setCollapse('Товар',
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon9.svg', 50, 55,null,3,null,null,null,@$p_active1), null, null, 'Товар 1', 'setBlock','lego_p=1').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon10.svg', 50, 55,null,3,null,null,null,@$p_active2), null, null, 'Товар 2', 'setBlock','lego_p=2').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon11.svg', 50, 55,null,3,null,null,null,@$p_active3), null, null, 'Товар 2', 'setBlock','lego_p=3')
            ,'in', false, false);

                
        // Фильтр
        $css_edit_theme.=$PHPShopGUI->setCollapse('Фильтр',
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon4.svg', 50, 55,null,3,null,null,null,@$s_active1), null, null, 'Фильтр 1', 'setBlock','lego_s=1').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon5.svg', 50, 55,null,3,null,null,null,@$s_active2), null, null, 'Фильтр 2', 'setBlock','lego_s=2')
         ,'in', false, false);
        

        // Подвал
        $css_edit_theme.=$PHPShopGUI->setCollapse('Подвал',
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon3.svg', 50, 55,null,3,null,null,null,@$f_active1), null, null, 'Подвал 1', 'setBlock','lego_f=1').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon2.svg', 50, 55,null,3,null,null,null,@$f_active2), null, null, 'Подвал 2', 'setBlock','lego_f=2').
                $PHPShopGUI->setLink('#',$PHPShopGUI->setImage('images/editor/icon2.svg', 50, 55,null,3,null,null,null,@$f_active3), null, null, 'Подвал 2', 'setBlock','lego_f=3')
                ,'in', false, false);
        
            $PHPShopGUI->nav_style = 'nav-tabs';
    $css_edit.=$PHPShopGUI->setTab(array('Темы', $css_edit_theme,false), array('Стили', '<br>'.$css_edit_add,false));

    // Сохранить
    if (!empty($_SESSION['logPHPSHOP'])) {
        
        $css_edit.='<p></p>'.$PHPShopGUI->setButton('Сохранить', 'floppy-disk', 'saveTheme');
        $admin_edit=$PHPShopGUI->setButton('Управлять', 'cog', 'openAdminModal');
        
         if (!empty($_COOKIE['debug_template']))
            $debug_active = ' active ';
        else
            $debug_active = null;

        $css_edit.=$PHPShopGUI->setButton('Отладка', 'picture', 'setDebug' . $debug_active);
    }

 // Память вывода панели
    if (!empty($_COOKIE['style_selector_status'])) {
        if ($_COOKIE['style_selector_status'] == 'enabled') {
            $editor['right'] = 0;
            $editor['close'] = 'ss-close';
        } else {
            $editor['right'] = -280;
            $editor['close'] = false;
        }
    } else if ($GLOBALS['SysValue']['template_theme']['demo'] == 'true') {
        $editor['right'] = 0;
        $editor['close'] = 'ss-close';
    } else {
        $editor['right'] = -280;
        $editor['close'] = false;
    }

    // Память коллапса
    $collapseCSS = $collapseAdmin = null;
    if (isset($_COOKIE['style_collapse_collapseCSS'])) {
        $collapseCSS = null;
        $collapseIconCSS = 'glyphicon-menu-down';
    } else {
        $collapseCSS = 'in';
        $collapseIconCSS = 'glyphicon-menu-up';
    }
    if (isset($_COOKIE['style_collapse_collapseAdmin'])) {
        $collapseAdmin = null;
        $collapseIconAdmin = 'glyphicon-menu-down';
    } else {
        $collapseAdmin = 'in';
        $collapseIconAdmin = 'glyphicon-menu-up';
    }

    if ($collapseCSS == $collapseAdmin)
        $collapseAdmin = null;

    if (!empty($_SESSION['logPHPSHOP']))
        $admin_help = __('Вы можете управлять содержанием текущей страницы');
    else
        $admin_help = __('Для управления текущей страницей требуется').' <a href="//' . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . 'phpshop/admpanel/" target="_blank"><span class="glyphicon glyphicon-user"></span> '.__('авторизоваться').'</a>';
    
     // Выбор БД
    if (is_array($GLOBALS['SysValue']['connect_select'])) {
        foreach ($GLOBALS['SysValue']['connect_select'] as $k => $v) {
            if ($_SESSION['base'] == $k)
                $sel = "selected";
            else
                $sel = null;
            $connect_select[] = array($v, $k, $sel);
        }

        $forma = PHPShopText::div(PHPShopText::form(PHPShopText::select('base', $connect_select, '100%', $float = "none", $caption = false, $onchange = "javascript:document.BdForm.submit()"), 'BdForm', 'get', '/'), 'left', 'padding:10px');
        PHPShopParser::set('leftMenuContent', $forma);
        PHPShopParser::set('leftMenuName', __("Сменить базу"));
        $GLOBALS['SysValue']['other']['skinSelect'] .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $GLOBALS['SysValue']['templates']['left_menu'], true, true, false);
    }
    
    $collapse_menu = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseCSS" aria-expanded="true" aria-controls="collapseOne">
          '.__('Оформление').' <span class="glyphicon ' . $collapseIconCSS . ' pull-right" data-parent="collapseCSS"></span>
        </a>
      </h4>
    </div>
    <div id="collapseCSS" class="panel-collapse collapse ' . $collapseCSS . ' form-horizontal" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
     
         ' . $css_edit . $theme_menu . '

      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading hidde" role="tab" id="adminModalHelp">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseAdmin" aria-expanded="false" aria-controls="collapseTwo">
          '.__('Управление').' <span class="glyphicon ' . $collapseIconAdmin . ' pull-right" data-parent="collapseAdmin"></span>
        </a>
      </h4>
    </div>
    <div id="collapseAdmin" class="panel-collapse collapse ' . $collapseAdmin . '" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      <p class="text-muted">' . $admin_help . '</p>
' . $admin_edit . '
      </div>
    </div>
  </div>
</div>';

    // Редактор CSS
    $theme_menu = '
        <div id="style-selector" style="width: 280px; right: ' . $editor['right'] . 'px;">
        <div class="style-toggle ' . $editor['close'] . '" title="'.__('Панель оформления').'"></div>
           <div id="style-selector-container">
              <div class="style-selector-wrapper">
              ' . $GLOBALS['SysValue']['other']['skinSelect'] . $collapse_menu . '
              </div>
           </div>
        </div>'.$css_filemanager;

    // Редактор БД
    $edit_frame = ' <!-- Modal admin -->
        <div class="modal bs-example-modal-lg" id="adminModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                        <span class="btn btn-default btn-sm pull-left glyphicon glyphicon-fullscreen" id="editorwindow" data-toggle="tooltip" data-placement="bottom" title="Увеличить размер" style="margin-right:10px"></span> 

                        <h4 class="modal-title">'.__('Панель управления').'</h4>
                    </div>
                    <div class="modal-body">
                      
                        <iframe class="admin-modal-content" id="admin-modal" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" width="100%" height="600"></iframe>
                        <div style="height:30px">
                         <!-- Progress -->
                            <div class="progress" style="margin:0px 5px 3px 5px">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                    <span class="sr-only">45% Complete</span>
                                </div>
                            </div>   
                            <!--/ Progress -->
                         </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Modal admin -->';


    if (!empty($GLOBALS['SysValue']['other']['skinSelect']))
        $GLOBALS['SysValue']['other']['editor'] = $theme_menu . $edit_frame;
}

// Мобильная корзина
if (!empty($_SESSION['cart'])) {
    $PHPShopCart = new PHPShopCart();
    $GLOBALS['SysValue']['other']['cart_active_num'] = $PHPShopCart->getNum();
    $GLOBALS['SysValue']['other']['cart_active'] = 'active';
}

// Личный кабинет
if (!empty($_SESSION['UsersId'])) {
    $GLOBALS['SysValue']['other']['user_link'] = 'href="/users/"';
    $GLOBALS['SysValue']['other']['user_active'] = 'active';
} else {
    $GLOBALS['SysValue']['other']['user_link'] = 'href="#"  data-toggle="modal"';
}


// Меню брендов
$PHPShopBrandsElement = new PHPShopBrandsElement();
$PHPShopBrandsElement->init('topBrands');
?>