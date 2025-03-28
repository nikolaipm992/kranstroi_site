<?php

$_SESSION['Memory']["rateForComment"]["oneStarWidth"] = 20; // ������ ����� ��������
$_SESSION['Memory']["rateForComment"]["oneSpaceWidth"] = 0;
define("SkinName", "unit");

// �������� ���� CSS
if (isset($_COOKIE[SkinName . '_theme'])) {
    if (PHPShopSecurity::true_skin($_COOKIE[SkinName . '_theme'])) {
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = $_COOKIE[SkinName . '_theme'];
    } else
        $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
} /* elseif (!empty($GLOBALS['SysValue']['other']['template_theme']))
  $GLOBALS['SysValue']['other']['bootstrap_theme'] = $GLOBALS['SysValue']['other']['template_theme']; */
elseif (empty($GLOBALS['SysValue']['other'][SkinName . '_theme'])) {
    $GLOBALS['SysValue']['other'][SkinName . '_theme'] = 'bootstrap-theme-default';
    setcookie(SkinName . '_theme', 'bootstrap-theme-default', time() + 360000, '/');
} else
    setcookie(SkinName . '_theme', $GLOBALS['SysValue']['other'][SkinName . '_theme'], time() + 360000, '/');

function create_theme_menu($file) {

    $current = $GLOBALS['SysValue']['other'][SkinName . '_theme'];
    if (empty($current))
        $current = 'bootstrap-theme-default';

    $color = array(
        'cerulean' => '#8ACBBB',
        'cyborg' => '#000',
        'flatly' => '#D9230F',
        'spacelab' => '#46709D',
        'slate' => '#4E5D6C',
        'yeti' => '#008CBA',
        'simplex' => '#8FBAF3',
        'sardbirds' => '#45B3AF',
        'wordless' => '#468966',
        'wildspot' => '#564267',
        'loving' => '#D7C1E0'
    );
    if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {

        if (!empty($color[$match[1]]))
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

// �������� ��� ����������
if ($GLOBALS['SysValue']['template_theme']['user'] == 'true' or ! empty($GLOBALS['SysValue']['other']['skinSelect'])) {

    // CSS
    $PHPShopCssParser = new PHPShopCssParser($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/' . $GLOBALS['SysValue']['other'][SkinName . '_theme'] . '.css');
    $css_parse = $PHPShopCssParser->parse();

    // XML
    PHPShopObj::loadClass(array('xml', 'admgui'));
    $PHPShopGUI = new PHPShopGUI();

    // bootstrap-
    $PHPShopGUI->addCSSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/editor.css', $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/font-awesome.min.css');
    $PHPShopGUI->addJSFiles($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/js/editor-lite.js');

    $option = xml2array($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/editor/style.xml', false, true);
    $css_edit = $PHPShopGUI->includeJava . $PHPShopGUI->includeCss;

    $option['element'][] = $option['element'];
    if (is_array($option))
        foreach ($option['element'] as $id => $element) {

            if (!empty($element['var']))
                $element_var[0] = $element['var'];

            $theme=$theme_menu=null;
            if (!empty($element_var) and is_array($element_var))
                foreach ($element_var as $var) {

                    // ����
                    if ($var['type'] == 'color') {
                        $css_edit .= $PHPShopGUI->setField($element['description'], $PHPShopGUI->setInputColor($var['name'], str_replace(array('!important'), array(''), $css_parse[$element['name']][$var['name']]), 130, 'color-' . $id, $element['name']), 5, $element['content']);
                    }

                    // ������
                    else if ($var['type'] == 'slider') {
                        $current_filter = $PHPShopCssParser->getParam($element['name'], '-editor-filter');

                        $filter = '<div id="color-slide" data-option="' . $current_filter . '"></div><input type="hidden" name="filter" class="color-filter color-value" value="' . $current_filter . '" data-option="' . $element['name'] . '" id="color-' . $id . '"> ';
                        $css_edit .= $PHPShopGUI->setField($element['description'], $filter, 5, $element['content']);
                    }

                    // ����
                    else if ($var['type'] == 'theme') {

                        if (!empty($_COOKIE['bootstrap_theme']) and $_COOKIE['bootstrap_theme'] == 'bootstrap')
                            $check = '<span class="glyphicon glyphicon-ok"></span>';
                        else
                            $check = null;


                        $theme .= PHPShopFile::searchFile($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/css/', 'create_theme_menu');
                        $css_edit .= $PHPShopGUI->setField($element['description'], $theme, 5, $element['content']);
                    }
                }
        }

    // ���������
    if (!empty($_SESSION['logPHPSHOP'])) {
        $css_edit .= $PHPShopGUI->setButton('���������', 'floppy-disk', 'saveTheme');
        $admin_edit = $PHPShopGUI->setButton('���������', 'cog', 'openAdminModal');

        if (!empty($_COOKIE['debug_template']))
            $debug_active = ' active ';
        else
            $debug_active = null;

        $css_edit .= $PHPShopGUI->setButton('������� �������', 'picture', 'setDebug' . $debug_active);
    }
    else $admin_edit=null;

    // ������ ������ ������
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

    // ������ ��������
    $collapseCSS = $collapseAdmin = null;
    unset($_COOKIE['style_collapse_collapseCSS']);
    unset($_COOKIE['style_collapse_collapseAdmin']);
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

    //if ($collapseCSS == $collapseAdmin)
    //$collapseAdmin = null;

    if (!empty($_SESSION['logPHPSHOP']))
        $admin_help = __('�� ������ ��������� ����������� ������� ��������');
    else
        $admin_help = __('��� ���������� ������� ��������� ���������') . ' <a href="//' . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . 'phpshop/admpanel/" target="_blank"><span class="glyphicon glyphicon-user"></span> ' . __('��������������') . '</a>';
    
     // ����� ��
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
        PHPShopParser::set('leftMenuName', __("������� ����"));
        $GLOBALS['SysValue']['other']['skinSelect'] .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $GLOBALS['SysValue']['templates']['left_menu'], true, true, false);
    }

    $collapse_menu = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseCSS" aria-expanded="true" aria-controls="collapseOne">
          ' . __('����������') . ' <span class="glyphicon ' . $collapseIconCSS . ' pull-right" data-parent="collapseCSS"></span>
        </a>
      </h4>
    </div>
    <div id="collapseCSS" class="panel-collapse collapse ' . $collapseCSS . ' form-horizontal" role="tabpanel">
      <div class="panel-body">
     
         ' . $css_edit . $theme_menu . '

      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading hidde" role="tab" id="adminModalHelp">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseAdmin" aria-expanded="false" aria-controls="collapseTwo">
          ' . __('����������') . ' <span class="glyphicon ' . $collapseIconAdmin . ' pull-right" data-parent="collapseAdmin"></span>
        </a>
      </h4>
    </div>
    <div id="collapseAdmin" class="panel-collapse collapse ' . $collapseAdmin . '" role="tabpanel">
      <div class="panel-body">
      <p class="text-muted">' . $admin_help . '</p>
' . $admin_edit . '
      </div>
    </div>
  </div>
</div>';

    // �������� CSS
    $theme_menu = '
        <div id="style-selector" style="width: 280px; right: ' . $editor['right'] . 'px;">
        <div class="style-toggle ' . $editor['close'] . '" title="' . __('������ ����������') . '"></div>
           <div id="style-selector-container">
              <div class="style-selector-wrapper">
              ' . $GLOBALS['SysValue']['other']['skinSelect'] . $collapse_menu . '
              </div>
           </div>
        </div>';

    // �������� ��
    $edit_frame = ' <!-- Modal admin -->
        <div class="modal bs-example-modal-lg" id="adminModal" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                        <span class="btn btn-default btn-sm pull-left glyphicon glyphicon-fullscreen" id="editorwindow" data-toggle="tooltip" data-placement="bottom" title="{��������� ������}" style="margin-right:10px"></span> 

                        <h4 class="modal-title">{������ ����������}</h4>
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

// ��������� �������
if (!empty($_SESSION['cart'])) {
    $PHPShopCart = new PHPShopCart();
    $GLOBALS['SysValue']['other']['cart_active_num'] = $PHPShopCart->getNum();
    $GLOBALS['SysValue']['other']['cart_active'] = 'active';
}

// ������ �������
if (!empty($_SESSION['UsersId'])) {
    $GLOBALS['SysValue']['other']['user_link'] = 'href="/users/"';
    $GLOBALS['SysValue']['other']['user_active'] = 'active';
} else {
    $GLOBALS['SysValue']['other']['user_link'] = 'href="#"  data-toggle="modal"';
}


// ���� �������
$PHPShopBrandsElement = new PHPShopBrandsElement();
$PHPShopBrandsElement->init('topBrands');
?>