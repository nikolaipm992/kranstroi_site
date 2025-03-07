<?php

function template_CID_Product($obj, $data, $rout) {
    global $PHPShopNav;


    if ($rout == 'START') {

        // Фасетный фильтр
        $obj->sort_template = 'sorttemplatehook';

        // Виртуальные каталоги
        $obj->cat_template = 'sortсattemplatehook';

        if (!empty($_GET['gridChange']))
            switch ($_GET['gridChange']) {
                case 1:
                    $obj->set('gridSetAactive', 'active');
                    break;
                case 2:
                    $obj->set('gridSetBactive', 'active');
                    break;
                default: if ($obj->cell == 1)
                        $obj->set('gridSetAactive', 'active');
                    else
                        $obj->set('gridSetBactive', 'active');
            }


        switch (@$_GET['s']) {
            case 1:
                $obj->set('sSetAactive', 'active');
                break;
            case 2:
                $obj->set('sSetBactive', 'active');
                break;
            default: $obj->set('sSetCactive', 'active');
        }


        switch (@$_GET['f']) {
            case 1:
                $obj->set('fSetAactive', 'active');
                break;
            case 2:
                $obj->set('fSetBactive', 'active');
                break;
            //default: $obj->set('fSetAactive', 'active');
        }
    } else if ($rout == 'END') {

        if (!empty($_GET['lego_s'])) {
            $_SESSION['editor'][SkinName]['s'] = intval($_GET['lego_s']);
            header('Location: ' . $PHPShopNav->objNav['truepath']);
        }

        // Фильтр
        if (!empty($_SESSION['editor'][SkinName]['s'])) {
            $s = intval($_SESSION['editor'][SkinName]['s']);
        } else
            $s = $_SESSION['editor'][SkinName]['s'] = 1;

        if (PHPShopParser::checkFile('filter/filter_' . $s . '.tpl')) {
            PHPShopParser::set('filter', PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/filter/filter_' . $s . '.tpl', true, false));
        }
    }
}

/**
 * Вывод подтипов в подробном описании 
 */
function template_parent($obj, $dataArray, $rout) {

    if ($rout == 'END') {

        $currency = $obj->currency;

        $true_color_array = $true_size_color_array = $color_array = array();
        $size = $color = null;

        if (is_array($obj->select_value) and count($obj->select_value) > 0) {

            foreach ($obj->select_value as $value) {

                $row = $value[3];
                if (!empty($row['parent_enabled'])) {

                    $row['price_n'] = number_format($obj->price($row, true), $obj->format, '.', ' ');
                    $row['price'] = number_format($obj->price($row), $obj->format, '.', ' ');

                    // Цена для YML ?option=ID
                    if (!empty($_GET['option'])) {
                        if ($value[1] == $_GET['option'])
                            $obj->set('productPrice', $row['price']);
                    } else
                        $obj->set('productPrice', number_format($obj->price($dataArray), $obj->format, '.', ' '));

                    $obj->set('productValutaName', $currency);
                    $obj->set('parentName', $value[0]);
                    $obj->set('parentCheckedId', $value[1]);

                    // Единица измерения
                    if (empty($row['ed_izm']))
                        $row['ed_izm'] = $obj->lang('product_on_sklad_i');

                    $size_color_array[$value[3]['id']] = array('id' => $row['id'], 'size' => $row['parent'], 'price' => $row['price'], 'color' => array($row['parent2']), 'image' => $row['pic_big'], 'price_n' => $row['price_n'], 'items' => $row['items'], 'ed_izm' => $row['ed_izm']);

                    if (!empty($value[3]['color']))
                        $color_array[$value[3]['parent2']] = $value[3]['color'];
                    else if (!empty($value[3]['parent2']))
                        $color_array[$value[3]['parent2']] = PHPShopString::getColor($value[3]['parent2']);
                }
            }


            if (is_array($size_color_array)) {
                foreach ($size_color_array as $v) {

                    if (empty($true_size_color_array[$v['size']])) {
                        $true_size_color_array[$v['size']] = $v;
                    } else {
                        $true_size_color_array[$v['size']]['color'][] = $v['color'][0];
                    }
                }
            }


            if (is_array($true_size_color_array) and count($true_size_color_array) > 0) {
                $parentSizeEnabled = true;
                foreach ($true_size_color_array as $key => $val) {

                    // Размер
                    if (empty($key)) {
                        $obj->set('parentSizeHide', 'hide hidden');
                        $obj->set('parentSizeChecked', 'checked');
                        $parentSizeEnabled = false;
                    } else {
                        $obj->set('parentSizeChecked', null);
                    }

                    $obj->set('parentSize', $key);
                    $obj->set('parentId', $val['id']);
                    $obj->set('parentPrice', $val['price']);
                    $obj->set('parentImage', $size_color_array[$val['id']]['image']);
                    
                    // Дополнительнеы склады
                    if ($obj->PHPShopSystem->isDisplayWarehouse()) {
                        $warehouse = [];

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

                        $where = [];
                        $where['enabled'] = "='1'";

                        if (defined("HostID") or defined("HostMain")) {

                            if (defined("HostID"))
                                $where['servers'] = " REGEXP 'i" . HostID . "i'";
                            elseif (defined("HostMain"))
                                $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';
                        }

                        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array('limit' => 100));
                        if (is_array($data))
                            foreach ($data as $row) {
                                if (!empty($row['description']))
                                    $warehouse[$row['id']] = $row['description'];
                                else
                                    $warehouse[$row['id']] = $row['name'];
                            }

                        if (is_array($warehouse) and count($warehouse) > 0) {
                            $items = null;

                            $itemsData = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['*'], ['id' => '=' . (int) $val['id']]);

                            if (empty($itemsData['ed_izm']))
                                $itemsData['ed_izm'] = __('шт.');

                            // Общий склад
                            if ($obj->PHPShopSystem->getSerilizeParam('admoption.sklad_sum_enabled') == 1)
                                $items = __('Общий склад') . ": " . $itemsData['items'] . " " . $itemsData['ed_izm'].PHPShopText::br();

                            foreach ($warehouse as $store_id => $store_name) {
                                if (isset($itemsData['items' . $store_id])) {
                                    $items .= $store_name . ": " . $itemsData['items' . $store_id] . " " . $itemsData['ed_izm'].PHPShopText::br();
                                }
                            }
                        } else
                            $items = $obj->PHPShopBase->SysValue['lang']['product_on_sklad'] . " " . $val['items'] . " " . $val['ed_izm'];
                    } else
                        $items = null;
                    $obj->set('parentItems',$items);

                    if ((float) $size_color_array[$val['id']]['price_n'] > 0)
                        $obj->set('parentPriceOld', $size_color_array[$val['id']]['price_n']);
                    else
                        $obj->set('parentPriceOld', '');


                    $size .= ParseTemplateReturn("product/product_odnotip_product_parent_one.tpl");

                    // Цвет
                    foreach ($val['color'] as $colors) {
                        $true_color_array[$colors][] = $val['id'];
                    }
                }
            }

            if (is_array($color_array)) {

                foreach ($color_array as $true_name => $true_colors) {
                    $obj->set('parentColor', $true_colors);
                    $obj->set('parentName', $true_name);
                    $id = null;

                    if (is_array($true_color_array[$true_name]))
                        foreach ($true_color_array[$true_name] as $ids) {
                            $id .= ' select-color-' . $ids;
                        }

                    $obj->set('parentColorId', $id);

                    // Цвет
                    if (!empty($true_colors)) {
                        $color .= ParseTemplateReturn("product/product_odnotip_product_parent_one_color.tpl");
                    }
                    // Параметр
                    else {
                        $color .= ParseTemplateReturn("product/product_odnotip_product_parent_one_value.tpl");
                    }
                }
            }

            // Отладка
            /*
              print_r($true_size_color_array);
              print_r($true_color_array);
              print_r($color_array);
             */

            if ($parentSizeEnabled)
                $obj->set('parentListSizeTitle', $obj->parent_title);

            $obj->set('parentListSize', $size, true);

            if (!empty($color))
                $obj->set('parentListColorTitle', $obj->parent_color);

            $obj->set('parentListColor', $color, true);
            $obj->set('parentSizeMessage', $obj->lang('select_size'));

            // Наличие
            if (!$obj->get('elementCartOptionHide'))
                $obj->set('elementCartOptionHide', null);

            $obj->set('ComStartCart', null);
            $obj->set('ComEndCart', null);
            //$obj->set('productParentJson',json_safe_encode($true_color_array));
            $obj->set('productParentList', ParseTemplateReturn("product/product_odnotip_product_parent.tpl"));
        }
    }
}

function template_UID($obj, $dataArray, $rout) {
    global $PHPShopNav;

    if ($rout == 'MIDDLE') {


        if (!empty($_GET['lego_p'])) {
            $_SESSION['editor'][SkinName]['p'] = intval($_GET['lego_p']);
            header('Location: ' . $PHPShopNav->objNav['truepath']);
        }

        // Форма товара
        if (!empty($_SESSION['editor'][SkinName]['p']))
            $p = intval($_SESSION['editor'][SkinName]['p']);
        else
            $p = $_SESSION['editor'][SkinName]['p'] = 1;

        if (PHPShopParser::checkFile('product/main_product_forma_full_' . $p . '.tpl')) {
            $obj->setValue('templates.main_product_forma_full', 'product/main_product_forma_full_' . $p . '.tpl');
        }

        if ($obj->get('optionsDisp') != '' and $obj->get('parentList') == '') {
            //$obj->set('ComStart','<!--');
            $obj->set('ComStartCart', '<!--');
            $obj->set('ComEndCart', '-->');
            //$obj->set('ComEnd','-->');
            $obj->set('optionsDisp', ParseTemplateReturn("product/product_option_product.tpl"));
        }
        // ‘пецпредложениЯ
        if (!empty($dataArray['spec']))
            $obj->set('specIcon', ParseTemplateReturn('product/specIcon.tpl'));
        else
            $obj->set('specIcon', '');

        // Ќовинки
        if (!empty($dataArray['newtip']))
            $obj->set('newtipIcon', ParseTemplateReturn('product/newtipIcon.tpl'));
        else
            $obj->set('newtipIcon', '');

        if (!empty($dataArray['sklad']) || ($obj->user_price_activate == 1 and empty($_SESSION['UsersId']))) {
            $obj->set('legoPurchaseDisabled', 'hide hidden');
        } else {
            $obj->set('legoPurchaseDisabled', '');
        }
    }
}

/**
 * Шаблон вывода характеристик виртуальные каталоги
 */
function sortсattemplatehook($value, $n, $title, $vendor) {
    global $PHPShopSeoPro, $PHPShopNav;
    $disp = null;

    if (is_array($value)) {
        foreach ($value as $p) {

            $text = $p[0];
            $checked = null;
            if (is_array($vendor)) {
                foreach ($vendor as $v) {
                    if ($v == $p[1])
                        $checked = 'active';
                }
            }
            if ($p[3] != null)
                $text .= ' (' . $p[3] . ')';

            if (!empty($p[5])) {

                if (strpos($GLOBALS['SysValue']['nav']['truepath'], '.filters/') !== false) {
                    $path = preg_replace('#^(.*)/filters/.*$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
                    $filters = preg_replace('#^.*/filters/(.*)$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
                } else
                    $path = preg_replace('#^(.*).html/.*$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);


                if ($filters == $p[5])
                    $checked = 'active';
                else
                    $checked = null;
            }


            PHPShopParser::set('podcatalogIcon', $p[4]);
            PHPShopParser::set('podcatalogName', $text);

  
            // SEO ссылка
            if (!empty($p[5])){
                PHPShopParser::set('podcatalogId', $PHPShopSeoPro->getCID());
                $disp .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/catalog/cid_category.tpl', true,['.html' => '.html/filters/' . $p[5]]);
            }else{
                PHPShopParser::set('podcatalogId', $PHPShopNav->getId());
                $disp .= PHPShopParser::file($GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . '/catalog/cid_category.tpl', true, ['.html' => '.html?v[' . $n . ']=' . $p[1]]);
            }
        }
    }

    return $disp;
}

/**
 * Шаблон вывода характеристик
 */
function sorttemplatehook($value, $n, $title, $vendor) {
    $limit = 5;
    $disp = null;
    $num = 0;

    if (empty($GLOBALS['filter_count']))
        $GLOBALS['filter_count'] = 1;

    if (is_array($value)) {
        foreach ($value as $p) {

            $text = $p[0];
            $checked = null;
            if (is_array($vendor)) {
                foreach ($vendor as $sortId => $v) {
                    if (is_array($v)) {
                        foreach ($v as $s)
                            if ($s == $p[1])
                                $checked = 'checked';
                    } else {
                        if ($n == $sortId && $p[1] == $v)
                            $checked = 'checked';
                    }
                }
            }

            if ($p[3] != null)
                $text .= ' (' . $p[3] . ')';

            if ($text[0] == '#')
                $text = '<div class="filter-color" style="background:' . $text . '; border:1px solid ' . $text . '"></div>';

            $disp .= '<div class="checkbox">
  <label>
    <input type="checkbox" value="1" name="' . $n . '-' . $p[1] . '" ' . $checked . ' data-count="' . $GLOBALS['filter_count'] . '" data-url="v[' . $n . ']=' . $p[1] . '"  data-name="' . $n . '-' . $p[1] . '">
    <span class="filter-item"  title="' . $p[0] . '">' . $text . '</span>
  </label>
</div>';
            $num++;
        }
        $GLOBALS['filter_count'] ++;
    }

    if ($num > $limit) {
        $style = "collapse";
        $chevron = 'fa fa-chevron-down';
        $help = '';
    } else {
        $style = "collapse in";
        $chevron = 'fa fa-chevron-up';
        $help = '';
    }

    return '<div class="faset-filter-block-wrapper grid-item"><h4>' . $title . '</h4><div>' . $disp . '<div class="clearfix"></div></div></div>';
}

/**
 *  Фотогалерея
 */
function template_image_gallery($obj, $array) {

    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.foto'));
    $data = $PHPShopOrm->getList(array('*'), array('parent' => '=' . $array['id']), array('order' => 'num'));

    $s = 1;
    $index = 0;
    $slides = $thumbs = $controls = '';


    // Нет данных в галерее
    if (count($data) === 0 and ! empty($array['pic_big']))
        $data[] = array('name' => $array['pic_big']);

    if (count($data) === 0) {
        $data[] = array('name' => sprintf('phpshop/templates/%s/images/no_photo.png', SkinName));
    }

    foreach ($data as $k => $v) {
        if ($v['name'] == $array['pic_big'])
            $sort_data[0] = $v;
        else
            $sort_data[$s] = $v;
        $s++;
    }

    ksort($sort_data);

    foreach ($sort_data as $k => $row) {
        $original = $row['name'];
        $small = str_replace(".", "s.", $original);
        $big = str_replace(".", "_big.", $original);

        if (empty($row['info']))
            $row['info'] = $array['name'];

        $alt = str_replace('"', '', $row['info']);

        if (!$obj->PHPShopSystem->ifSerilizeParam('admoption.image_save_source') or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $big))
            $big = $original;
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $small)) {
            $small = $big;
        }

        // Поддержка Webp
        if (method_exists($obj, 'setImage')) {
            $small = $obj->setImage($small);
            $big = $obj->setImage($big);
        }

        $slides .= sprintf('<div class="%s" data-elem="slide" data-options="thumb:%s">
                         <img src="%s" data-elem="bg" alt="%s" title="%s" class="slider-img hide">
                     </div>', $index === 0 ? 'heroSlide' : '', $small, $big, $alt, $alt);
        $thumbs .= sprintf('<div class="bigThumb" style="background-image:url(\'%s\')" data-elem="thumb" data-big-image="%s" 
                  data-options="sliderId:productSlider; index:%s; offCss:{className:bigThumb off}; onCss:{className:bigThumb on}"> </div>', $small, $big, $index);

        $controls .= sprintf('<div class="buttonThumb" data-elem="thumb" data-options="sliderId:productSlider; index:%s;"> </div>', $index);
        $index++;
    }

    if ($index <= 1)
        $thumbs = $controls = null;

    $obj->set('productSliderSlides', $slides);
    $obj->set('productSliderThumbs', $thumbs);
    $obj->set('productSliderControls', $controls);
    $obj->set('productSliderOneImage', sprintf('<img class="one-image-slider" src="%s" alt="%s" title="%s"/>', !empty($array['pic_big']) ? $array['pic_big'] : $data[0]['name'],$alt, $alt));

    return true;
}

$addHandler = array
    (
    'CID_Product' => 'template_CID_Product',
    'parent' => 'template_parent',
    'UID' => 'template_UID',
    'image_gallery' => 'template_image_gallery'
);
?>