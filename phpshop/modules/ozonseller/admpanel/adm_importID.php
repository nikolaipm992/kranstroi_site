<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';
$TitlePage = __('����� �� Ozon');
PHPShopObj::loadClass("product");
PHPShopObj::loadClass('category');

// ���������� ������ ���������
function treegenerator($array, $i, $curent) {
    global $tree_array, $CategoryArray;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k);
            $disabled = null;

            if ($CategoryArray[$k]['category_ozonseller'] == $curent)
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// ����
$OzonSeller = new OzonSeller();

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $OzonSeller, $PHPShopModules;

    $PHPShopGUI->field_col = 4;

    $product_info = $OzonSeller->getProductAttribures($_GET['id'])['result'][0];

    if (!empty($product_info['id']))
        $PHPShopGUI->action_button['��������� �����'] = array(
            'name' => '��������� �����',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('�����') . ': ' . PHPShopString::utf8_win1251($product_info['name']), false, array('��������� �����'));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // ��������� Ozon
    $PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_type"));

    // ��������� ��
    $category = $PHPShopOrmCat->getOne(['name'], ['id' => '=' . $_GET['type_id']])['name'];


    $PHPShopCategoryArray = new PHPShopCategoryArray(false, ["id", "name", "parent_to", "category_ozonseller"]);
    $GLOBALS['CategoryArray'] = $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('������� �������') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }

    $GLOBALS['tree_array'] = &$tree_array;


    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $_GET['type_id']);

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            if ($CategoryArray[$k]['category_ozonseller'] == $_GET['type_id'])
                $selected = 'selected';
            else
                $selected = null;

            $tree_select .= '<option value="' . $k . '"  ' . $disabled . ' ' . $selected . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';


    // ��������
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = PHPShopString::utf8_win1251($OzonSeller->getProductDescription($product_info['id'])['result']['description']);

    if (is_array($product_info['images']) and count($product_info['images']) > 0) {
        $icon = null;
        foreach ($product_info['images'] as $img)
            $icon .= $PHPShopGUI->setIcon($img, "images[]", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));
    } else
        $icon = $PHPShopGUI->setIcon('./images/no_photo.gif', "pic", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));


    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setTextarea('name_new', PHPShopString::utf8_win1251($product_info['name'])));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->setInputText(null, 'uid_new', PHPShopString::utf8_win1251($product_info['offer_id'])));
    $Tab1 .= $PHPShopGUI->setField("�����������", $icon);
    $Tab1 .= $PHPShopGUI->setField("OZON ID", $PHPShopGUI->setText($product_info['id']));
    $Tab1 .= $PHPShopGUI->setField("SKU OZON", $PHPShopGUI->setInputText(null, 'sku_ozon_new', $_GET['sku'], 150));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->setInputText(null, 'barcode_ozon_new', $product_info['barcode']));
    $Tab1 .= $PHPShopGUI->setField("��������� � Ozon", $PHPShopGUI->setText($category,"left", false, false) . $PHPShopGUI->setInput("hidden", "category_ozonseller", $_GET['type_id']).$PHPShopGUI->setInput("hidden", "description_category_id", $product_info['description_category_id']));

    // ����� ��������
    $Tab1 .= $PHPShopGUI->setField("����������", $tree_select);

    // ����� ������
    $Tab1 .= $PHPShopGUI->setField('����� ������', $PHPShopGUI->setCheckbox('enabled_new', 1, '����� � ��������', 1) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, '���������������', 0) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, '�������', 0));
    $Tab1 .= $PHPShopGUI->setField('����������', $PHPShopGUI->setInputText('&#8470;', 'num_new', 0, 150));

    // ����
    $product_price = $OzonSeller->getProductPrices($product_info['id'])['items'][0]['price'];

    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInputText(null, 'price_new', (float) $product_price['price'], 150));
    $Tab1 .= $PHPShopGUI->setField("������ ����", $PHPShopGUI->setInputText(null, 'price_n_new', (float) $product_price['old_price'], 150));
    $Tab1 .= $PHPShopGUI->setField("���������� ����", $PHPShopGUI->setInputText(null, 'price_purch_new', (float) 0, 150));

    $baseinputvaluta = $PHPShopSystem->getDefaultOrderValutaId();

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $baseinputvaluta,false);
        }

    // ������
    $Tab1 .= $PHPShopGUI->setField('������', $valuta_area);

    $Tab_size = $PHPShopGUI->setField("���", $PHPShopGUI->setInputText(null, 'weight_new', $product_info['weight'], 100, __('�&nbsp;&nbsp;&nbsp;&nbsp;')));
    $Tab_size .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(null, 'height_new', $product_info['height'] / 100, 100, __('c�')));
    $Tab_size .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(null, 'width_new', $product_info['width'] / 100, 100, __('c�')));
    $Tab_size .= $PHPShopGUI->setField("�����", $PHPShopGUI->setInputText(null, 'length_new', $product_info['depth'] / 100, 100, __('c�')));

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('��������', $Tab_size);
    $Tab2 = $PHPShopGUI->setCollapse("��������", $oFCKeditor->AddGUI());

    // �������������� � Ozon
    $sort_ozon_data = $OzonSeller->getTreeAttribute(["description_category_id" => $product_info['description_category_id'],"type_id"=>$_GET['type_id']]);
    if (is_array($sort_ozon_data['result'])) {
        foreach ($sort_ozon_data['result'] as $sort_ozon_value) {
            $attribute[$sort_ozon_value['id']] = PHPShopString::utf8_win1251($sort_ozon_value['name'],true);
        }
    }
    

    $Tab_sort = null;
    if (is_array($product_info['attributes']))
        foreach ($product_info['attributes'] as $attributes) {
            if (!empty($attribute[$attributes['id']]) and ! empty($attributes['values'][0]['dictionary_value_id'])) {
                unset($value_new);
                $value_new[] = [__('������ �� �������'), 0];
                $value_new[] = array(PHPShopString::utf8_win1251($attributes['values'][0]['value']), PHPShopString::utf8_win1251($attributes['values'][0]['value']), PHPShopString::utf8_win1251($attributes['values'][0]['value']));

                $Tab_sort .= $PHPShopGUI->setField($attribute[$attributes['id']], $PHPShopGUI->setSelect('vendor_array[' . $attributes['id'] . ']', $value_new, '100%', false, false, false, false, 1, false, false, 'selectpicker'));
            }
        }

    $Tab2 .= $PHPShopGUI->setCollapse("��������������", $Tab_sort);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1 . $Tab2, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.catalog.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// �������� ����������� �� ������ 
function downloadFile($url, $path) {
    $newfname = $path;

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $file = fopen($url, 'rb', false, stream_context_create($arrContextOptions));
    if ($file) {
        $newf = fopen($newfname, 'wb');
        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
        return true;
    }
}

/**
 *  �������� �������������
 */
function ozon_sort($sort_name, $sort_id, $sort_value, $category) {
    global $PHPShopBase;

    $return = null;
    $debug = false;

    // �������� �� ������ ������������� � ��������
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = $debug;
    $result_1 = $PHPShopOrm->query('select sort,name from ' . $GLOBALS['SysValue']['base']['categories'] . ' where id="' . $category . '"  limit 1', __FUNCTION__, __LINE__);
    $row_1 = mysqli_fetch_array($result_1);
    $cat_sort = unserialize($row_1['sort']);
    $cat_name = $row_1['name'];

    // ����������� � ����
    if (is_array($cat_sort))
        $where_in = ' and a.id IN (' . @implode(",", $cat_sort) . ') ';
    else
        $where_in = null;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = $debug;

    $result_2 = $PHPShopOrm->query('select a.id as parent, b.id from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.name="' . $sort_name . '" and b.name="' . $sort_value . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
    $row_2 = mysqli_fetch_array($result_2);

    // ������������ �  ����
    if (!empty($where_in) and isset($row_2['id'])) {
        $return[$row_2['parent']][] = $row_2['id'];
    }
    // ����������� � ����
    else {

        // �������� ��������������
        if (!empty($where_in))
            $sort_name_present = $PHPShopBase->getNumRows('sort_categories', 'as a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1');

        // ������� ����� ��������������
        if (empty($sort_name_present) and ! empty($category)) {

            // ����
            if (!empty($cat_sort[0])) {
                $PHPShopOrm = new PHPShopOrm();
                $PHPShopOrm->debug = $debug;

                $result_3 = $PHPShopOrm->query('select category from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' where id="' . intval($cat_sort[0]) . '"  limit 1', __FUNCTION__, __LINE__);
                $row_3 = mysqli_fetch_array($result_3);
                $cat_set = $row_3['category'];
            }
            // ���, ������� ����� �����
            elseif (!empty($cat_name)) {

                // �������� ������ �������������
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                $PHPShopOrm->debug = $debug;
                $cat_set = $PHPShopOrm->insert(array('name_new' => __('��� ��������') . ' ' . $cat_name, 'category_new' => 0), '_new', __FUNCTION__, __LINE__);
            }

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $PHPShopOrm->debug = $debug;

            if (!empty($sort_name) and ! empty($cat_set))
                if ($parent = $PHPShopOrm->insert(array('name_new' => $sort_name, 'category_new' => $cat_set, 'attribute_ozonseller_new' => $sort_id), '_new')) {

                    // ������� ����� �������� ��������������
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                    $PHPShopOrm->debug = $debug;
                    $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent, 'sort_seo_name_new' => PHPShopString::toLatin($sort_value)), '_new', __FUNCTION__, __LINE__);

                    $return[$parent][] = $slave;
                    $cat_sort[] = $parent;

                    // ��������� ����� �������� �������
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                    $PHPShopOrm->debug = $debug;
                    $PHPShopOrm->update(array('sort_new' => serialize($cat_sort)), array('id' => '=' . $category), '_new', __FUNCTION__, __LINE__);
                }
        }
        // ���������� �������� 
        elseif (!empty($sort_value)) {

            // �������� �� ������������ ��������������
            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $debug;
            $result = $PHPShopOrm->query('select a.id  from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
            if ($row = mysqli_fetch_array($result)) {
                $parent = $row['id'];
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                $PHPShopOrm->debug = $debug;
                $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent), '_new', __FUNCTION__, __LINE__);

                $return[$parent][] = $slave;
            }
        }
    }

    return $return;
}

/**
 * ����� �������� ������
 */
function actionSave() {
    global $PHPShopSystem, $OzonSeller;

    // ����� �������� ��������� ����
    $PHPShopCategoryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $category_ozonseller = $_POST['category_ozonseller'];
    $category = $_POST['category_new'];
    $data_categories = $PHPShopCategoryOrm->getOne(['id'], ['category_ozonseller' => '=' . $category_ozonseller]);

    // ��� �������� ��������� ����
    if (empty($data_categories['id'])) {
        $PHPShopCategoryOrm->update(['category_ozonseller_new' => $category_ozonseller], ['id' => '=' . $category]);
    }

    // �������������� � Ozon
    $sort_ozon_data = $OzonSeller->getTreeAttribute(["description_category_id" => $_POST["description_category_id"],"type_id"=>$category_ozonseller]);
    if (is_array($sort_ozon_data['result'])) {
        foreach ($sort_ozon_data['result'] as $sort_ozon_value) {
            $attribute[$sort_ozon_value['id']] = PHPShopString::utf8_win1251($sort_ozon_value['name']);
        }
    }

    $vendor_array = [];
    if (is_array($_POST['vendor_array']))
        foreach ($_POST['vendor_array'] as $sort_id => $sort_value) {
            $ozon_sort = ozon_sort($attribute[$sort_id], $sort_id, $sort_value, $category);

            if (is_array($ozon_sort))
                $vendor_array += $ozon_sort;
        }

    if (is_array($vendor_array)) {
        $_POST['vendor_array_new'] = serialize($vendor_array);
        foreach ($vendor_array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $p) {
                    $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                }
            } else
                $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
        }
    }

    // ����� ������������
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];

    // ���� �����������
    $_POST['datas_new'] = time();
    $_POST['export_ozon_new'] = 1;
    $_POST['export_ozon_id_new'] = $_POST['rowID'];

    // ������������� ������ ��������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST);

    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
    $img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
    $img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
    $image_save_source = $PHPShopSystem->getSerilizeParam('admoption.image_save_source');

    // ����� ��������
    $path = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
    if (!empty($path))
        $path = $path . '/';

    if (is_array($_POST['images'])) {
        foreach ($_POST['images'] as $k => $img) {
            if (!empty($img)) {

                $path_parts = pathinfo($img);

                // ���� ��������
                if (downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename']))
                    $img_load++;
                else
                    continue;

                // ����� ���
                $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

                // ������ � �����������
                $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                $PHPShopOrmImg->insert(array('parent_new' => intval($action), 'name_new' => $img, 'num_new' => $k));

                $file = $_SERVER['DOCUMENT_ROOT'] . $img;
                $name = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $file);

                if (!file_exists($name) and file_exists($file)) {

                    // ��������� �������� 
                    $thumb = new PHPThumb($file);
                    $thumb->setOptions(array('jpegQuality' => $width_kratko));
                    $thumb->resize($img_tw, $img_th);
                    $thumb->save($name);

                    // �������� �����������
                    if (!empty($image_save_source)) {
                        $name_big = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("_big.png", "_big.jpg", "_big.jpeg", "_big.gif", "_big.png", "_big.jpg", "_big.jpeg", "_big.gif"), $file);
                        @copy($file, $name_big);
                    }

                    // �������� �����������
                    $thumb = new PHPThumb($file);
                    $thumb->setOptions(array('jpegQuality' => $width_kratko));
                    $thumb->resize($img_w, $img_h);
                    $thumb->save($file);
                }

                // ������� �����������
                if ($k == 0 and ! empty($file)) {

                    $update['pic_big_new'] = $img;

                    // ������� ������
                    $update['pic_small_new'] = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $img);
                }
            }
        }

        $PHPShopOrm->update($update, ['id' => '=' . intval($action)]);
    }

    header('Location: ?path=product&return=catalog&id=' . $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>