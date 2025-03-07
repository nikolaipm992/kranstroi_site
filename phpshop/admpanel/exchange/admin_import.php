<?php

use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLS;

$TitlePage = __("������ ������");

// �������� �����
$key_name = array(
    'id' => 'Id',
    'name' => '������������',
    'uid' => '�������',
    'price' => '���� 1',
    'price2' => '���� 2',
    'price3' => '���� 3',
    'price4' => '���� 4',
    'price5' => '���� 5',
    'price_n' => '������ ����',
    'sklad' => '��� �����',
    'newtip' => '�������',
    'spec' => '���������������',
    'items' => '�����',
    'weight' => '���',
    'num' => '���������',
    'enabled' => '�����',
    'content' => '��������� ��������',
    'description' => '������� ��������',
    'pic_small' => '��������� �����������',
    'pic_big' => '������� �����������',
    'yml' => '������.������',
    'icon' => '������',
    'parent_to' => '��������',
    'category' => '�������',
    'title' => '���������',
    'login' => '�����',
    'tel' => '�������',
    'cumulative_discount' => '������������� ������',
    'seller' => '������ �������� � 1�',
    'fio' => '�.�.�',
    'city' => '�����',
    'street' => '�����',
    'odnotip' => '������������� ������',
    'page' => '��������',
    'parent' => '����������� ������',
    'dop_cat' => '�������������� ��������',
    'ed_izm' => '������� ���������',
    'baseinputvaluta' => '������',
    'vendor_array' => '��������������',
    'p_enabled' => '������� � ������.������',
    'parent_enabled' => '������',
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
    "prod_seo_name" => 'SEO ������',
    'num_row' => '������� � �����',
    'num_cow' => '������� �� ��������',
    'count' => '�������� �������',
    'cat_seo_name' => 'SEO ������ ��������',
    'sum' => '�����',
    'servers' => '�������',
    'items1' => '����� 2',
    'items2' => '����� 3',
    'items3' => '����� 4',
    'items4' => '����� 5',
    'vendor' => '@��������������',
    'data_adres' => '�����',
    'color' => '��� �����',
    'parent2' => '����',
    'rate' => '�������',
    'productday' => '����� ���',
    'hit' => '���',
    'sendmail' => '�������� �� ��������',
    'statusi' => '������ ������',
    'country' => '������',
    'state' => '�������',
    'index' => '������',
    'house' => '���',
    'porch' => '�������',
    'door_phone' => '�������',
    'flat' => '��������',
    'delivtime' => '����� ��������',
    'org_name' => '�����������',
    'org_inn' => '���',
    'org_kpp' => '���',
    'org_yur_adres' => '����������� �����',
    'dop_info' => '����������� �����������',
    'tracking' => '��� ������������',
    'path' => '���� ��������',
    'length' => '�����',
    'width' => '������',
    'height' => '������',
    'moysklad_product_id' => '�������� Id',
    'bonus' => '�����',
    'price_purch' => '���������� ����',
    'files' => '�����',
    'external_code' => '������� ���',
    'barcode' => '��������',
    'rate_count' => '������',
    'productservices_products' => '������'
);

//if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
//unset($key_name);
// ���� ����
$key_stop = array('password', 'wishlist', 'sort', 'yml_bid_array', 'status', 'datas', 'price_search', 'vid', 'name_rambler', 'servers', 'skin', 'skin_enabled', 'secure_groups', 'icon_description', 'title_enabled', 'title_shablon', 'descrip_shablon', 'descrip_enabled', 'productsgroup_check', 'productsgroup_product', 'keywords_enabled', 'keywords_shablon', 'sort_cache', 'sort_cache_created_at', 'parent_title', 'menu', 'order_by', 'order_to', 'org_ras', 'org_bank', 'org_kor', 'org_bik', 'org_city', 'admin', 'org_fakt_adres');

if (empty($subpath[2]))
    $subpath[2] = null;

switch ($subpath[2]) {
    case 'catalog':
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $key_base = array('id');
        break;
    case 'user':
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $key_base = array('id', 'login');
        array_push($key_stop, 'tel_code', 'adres', 'inn', 'kpp', 'company', 'mail', 'token', 'token_time');
        break;
    case 'order':
        PHPShopObj::loadClass('order');
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $key_base = array('id', 'uid');
        array_push($key_stop, 'orders', 'user');
        $key_name['uid'] = __('� ������');
        $TitlePage .= ' ' . __('�������');
        break;
    default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $key_base = array('id', 'uid');
        break;
}

// �������� ����������� �� ������ 
function downloadFile($url, $path) {

    $newfname = $path;
    $url = iconv("windows-1251", "utf-8//IGNORE", $url);

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $file = @fopen($url, 'rb', false, stream_context_create($arrContextOptions));
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

// �������� �����������
function checkImage($img, $id, $uniq) {
    global $PHPShopSystem;

    // ������� � ��������
    $path_parts = pathinfo($img);
    $path_parts['basename'] = PHPShopFile::toLatin($path_parts['basename']);

    // ����� ��������
    $path = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // ��� ��� �������� � �����������
    $img_check = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    // ���������� � webp
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save') and $path_parts['extension'] != 'webp') {
        $img_check = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], '.webp', $img_check);
    }

    // ����� ���
    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    // �������� ������������� ����������� � �����������
    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $PHPShopOrmImg->debug = false;
    $check = $PHPShopOrmImg->select(array('id'), array('name' => '="' . $img_check . '"', 'parent' => '=' . intval($id)), false, array('limit' => 1))['id'];

    // �������� ���
    if (!is_array($check)) {

        // �������� ����� �����
        if (empty($uniq) and file_exists($_SERVER['DOCUMENT_ROOT'] . $img_check)) {

            // ����
            $rand = '_' . substr(abs(crc32($img)), 0, 5);
            $path_parts['basename'] = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], [$rand . ".png", $rand . ".jpg", $rand . ".jpeg", $rand . ".gif", $rand . ".PNG", $rand . ".JPG", $rand . ".JPEG", $rand . ".GIF", $rand . ".WEBP"], $path_parts['basename']);
        }
    }

    // ����� ���
    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    return ['img' => $img, 'check' => $check];
}

// ��������� ���������
function setCategory() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $row = $PHPShopOrm->getOne(array('id'), array('name' => '="�������� CSV ' . PHPShopDate::get() . '"'));

    if (empty($row['id'])) {
        $result = $PHPShopOrm->insert(array('name_new' => '�������� CSV ' . PHPShopDate::get(), 'skin_enabled_new' => 1));
        return $result;
    } else
        return $row['id'];
}

// ��������� ������������� ����� ��������
function sort_encode_general($sort, $category) {

    $return = [];
    $delim = $_POST['export_sortdelim'];
    $sortsdelim = $_POST['export_sortsdelim'];
    $debug = false;
    if (!empty($sort)) {

        if (strstr($sort, $delim)) {
            $sort_array = explode($delim, $sort);
        } else
            $sort_array[] = $sort;

        if (is_array($sort_array))
            foreach ($sort_array as $sort_list) {

                if (strstr($sort_list, $sortsdelim)) {

                    $sort_list_array = explode($sortsdelim, $sort_list, 2);
                    $sort_name = PHPShopSecurity::TotalClean($sort_list_array[0]);
                    $sort_value = PHPShopSecurity::TotalClean($sort_list_array[1]);

                    if (!empty($sort_name) and ! empty($sort_value))
                        $return += (new sortCheck($sort_name, $sort_value, $category, $debug))->result();
                }
            }
    }

    return $return;
}

// ��������� ������������� ���������� ��������
function sort_encode($sort, $category) {
    global $PHPShopBase;

    $return = null;
    $delim = $_POST['export_sortdelim'];
    $sortsdelim = $_POST['export_sortsdelim'];
    $debug = false;
    if (!empty($sort)) {

        if (strstr($sort, $delim)) {
            $sort_array = explode($delim, $sort);
        } else
            $sort_array[] = $sort;

        if (is_array($sort_array))
            foreach ($sort_array as $sort_list) {

                if (strstr($sort_list, $sortsdelim)) {

                    $sort_list_array = explode($sortsdelim, $sort_list, 2);
                    $sort_name = PHPShopSecurity::TotalClean($sort_list_array[0]);
                    $sort_value = PHPShopSecurity::TotalClean($sort_list_array[1]);

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
                                if ($parent = $PHPShopOrm->insert(array('name_new' => $sort_name, 'category_new' => $cat_set), '_new', __FUNCTION__, __LINE__)) {

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
                }
            }
    }

    return $return;
}

// ��������� ������ CSV
function csv_update($data) {
    global $PHPShopOrm, $PHPShopBase, $csv_load_option, $key_name, $csv_load_count, $subpath, $PHPShopSystem, $csv_load, $csv_load_totale, $img_load;

    // ��������� UTF-8
    if ($_POST['export_code'] == 'utf' and is_array($data)) {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {

            $key_name_utf = $key_name;
            unset($key_name);

            foreach ($key_name_utf as $k => $v)
                $key_name[$k] = PHPShopString::win_utf8($v, true);
        } else {
            foreach ($data as $k => $v)
                $data[$k] = PHPShopString::utf8_win1251($v);
        }
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');

    // AI
    if ($_POST['export_ai'] == 1) {
        PHPShopObj::loadClass('yandexcloud');
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/parsedown/Parsedown.php';
        $YandexGPT = new YandexGPT();
    }

    // ����� ������
    if ($_POST['export_imgsearch'] == 1) {
        PHPShopObj::loadClass('yandexcloud');
        $YandexSearch = new YandexSearch();
        $yandexsearch_image_num = (int) $PHPShopSystem->getSerilizeParam('ai.yandexsearch_image_num');
    }

    if (is_array($data)) {

        $key_name_true = array_flip($key_name);

        // ����� �����
        if (empty($csv_load_option)) {
            $select = false;

            // ������������� �����
            if (is_array($_POST['select_action'])) {

                if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
                    foreach ($_POST['select_action'] as $k => $v)
                        $_POST['select_action'][$k] = PHPShopString::utf8_win1251($v, true);
                }


                foreach ($_POST['select_action'] as $k => $name) {

                    // �������������
                    if (!empty($_POST['smart'])) {
                        $_POST['select_action'][$k] = PHPShopString::utf8_win1251($name, true);
                    }

                    if (!empty($name))
                        $select = true;

                    if (substr($name, 0, 1) == '@')
                        $_POST['select_action'][$k] = '@' . $data[$k];
                }
            }

            if ($select)
                $csv_load_option = $_POST['select_action'];
            else
                $csv_load_option = $data;
        }
        // ��������
        else {
            // ����������� �����
            foreach ($csv_load_option as $k => $cols_name) {

                // base64
                if (substr($data[$k], 0, 7) == 'base64-') {

                    // ������������
                    if ($subpath[2] == 'user') {
                        $array = array();
                        $array['main'] = 0;
                        $array['list'][] = json_decode(base64_decode(substr($data[$k], 7, strlen($data[$k]) - 7)), true);
                        array_walk_recursive($array, 'array2iconv');

                        $data[$k] = serialize($array);
                    }
                }

                // ���� �������������
                if (!empty($key_name_true[$cols_name])) {

                    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {

                        if ($_POST['export_code'] == 'ansi')
                            $row[$key_name_true[$cols_name]] = PHPShopString::win_utf8($data[$k], true);
                        else
                            $row[$key_name_true[$cols_name]] = $data[$k];
                    } else
                        $row[$key_name_true[$cols_name]] = $data[$k];
                }
                // ���� �������������� � ��������
                elseif (substr($cols_name, 0, 1) == '@') {
                    $row[$cols_name] = $data[$k];
                    $sort_name = substr($cols_name, 1, (strlen($cols_name) - 1));

                    // ��������� ��������
                    if (strstr($data[$k], $_POST['export_sortsdelim'])) {
                        $sort_array = explode($_POST['export_sortsdelim'], $data[$k]);
                    } else
                        $sort_array[] = $data[$k];

                    if (is_array($sort_array)) {
                        foreach ($sort_array as $v)
                            $row['vendor_array'] .= $sort_name . $_POST['export_sortsdelim'] . $v . $_POST['export_sortdelim'];
                    }

                    unset($row[$cols_name]);
                    unset($sort_array);
                }
                // ���������
                else
                    $row[strtolower($cols_name)] = $data[$k];
            }

            // ������� ������������
            if (!empty($row['data_adres'])) {

                $row['enabled'] = 1;

                $tel['main'] = 0;
                $tel['list'][0]['tel_new'] = $row['data_adres'];
                $row['data_adres'] = serialize($tel);
            }

            // ������ ����� �������� �������
            if (isset($row['pic_big']) and isset($YandexSearch)) {

                if ($YandexSearch->init())
                    $result = $YandexSearch->search_img($row['name']);

                if (is_array($result)) {
                    $row['pic_big'] = null;
                    $i = 0;
                    foreach ($result as $images) {

                        if ($i < $yandexsearch_image_num)
                            $row['pic_big'] .= $images['url'] . ',';
                        else
                            continue;

                        $i++;
                    }
                }
            }

            // ������ ����� �������� ���������
            if (isset($row['icon']) and isset($YandexSearch)) {

                if ($YandexSearch->init())
                    $result = $YandexSearch->search_img($row['name']);

                if (is_array($result)) {
                    $row['icon'] = null;
                    $i = 0;
                    foreach ($result as $images) {

                        if ($i < 1)
                            $row['icon'] = $images['url'];
                        else
                            continue;

                        $i++;
                    }
                }
            }

            // AI ��������
            if (isset($row['content']) and isset($YandexGPT)) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_content_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_content_role');

                if (!empty($row['content']))
                    $message = $row['content'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message) and $YandexGPT->init()) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 500);
                    $text = $YandexGPT->html($result['result']['alternatives'][0]['message']['text']);
                    $row['content'] = PHPShopString::utf8_win1251($text);
                }
            }

            // AI ������� ��������
            if (isset($row['description']) and isset($YandexGPT)) {

                $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_description_role');

                if (!empty($row['description']))
                    $message = $row['description'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message) and $YandexGPT->init()) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 200);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['description'] = PHPShopString::utf8_win1251($text);
                }
            }

            // AI Meta Title
            if (isset($row['title']) and class_exists('YandexGPT')) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_title_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_title_role');

                if (!empty($row['title']))
                    $message = $row['title'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message)) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 100);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['title'] = PHPShopString::utf8_win1251($text);
                    $row['title_enabled'] = 1;
                    
                }
            }

            // AI Meta Description
            if (isset($row['descrip']) and class_exists('YandexGPT')) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_description_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_descrip_role');

                if (!empty($row['descrip']))
                    $message = $row['descrip'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message)) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 100);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['descrip'] = PHPShopString::utf8_win1251($text);
                    $row['descrip_enabled'] =  1;
                }
            }


            // �����
            if (!empty($row['files'])) {

                if (strstr($row['files'], ",")) {
                    $files_array = explode(",", $row['files']);
                } else
                    $files_array[] = $row['files'];

                if (is_array($files_array)) {
                    foreach ($files_array as $file) {
                        $name = pathinfo($file);
                        $files[] = ['name' => $name['basename'], 'path' => $file];
                    }

                    $row['files'] = serialize($files);
                }
            }

            // ���� ��������
            if (isset($row['path'])) {

                $search = $row['path'];
                $category = new PHPShopCategory(0);
                $category->getChildrenCategories(100, ['id', 'parent_to', 'name'], false, $search);

                while (count($category->search) != $category->found) {
                    $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                    $PHPShopOrmCat->debug = false;
                    $category->search_id = $PHPShopOrmCat->insert(array('name_new' => $category->search[$category->found], 'parent_to_new' => $category->search_id));
                    $category->found++;
                }

                $row['category'] = $category->search_id;
            }

            // ��������� ����� �������
            if (isset($row['parent']) and $row['parent'] == '')
                unset($row['parent']);

            // ��������������
            if (!empty($row['vendor_array'])) {

                // �� ������� ���������
                if (empty($row['category'])) {

                    // ����� ��������� �� ��
                    if (!empty($row['id'])) {
                        $row['category'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['category'], ['id' => '=' . (string) $row['id']])['category'];
                    }

                    // ����� ��������� �� �������
                    if (empty($row['category'])) {
                        $row['category'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['category'], ['uid' => '="' . $row['uid'] . '"'])['category'];
                    }

                    // ��������� ���������
                    if (empty($row['category'])) {
                        $row['category'] = setCategory();
                    }
                }

                $row['vendor'] = null;

                // ��������� ������������� ����� ��������
                if ($PHPShopSystem->getSerilizeParam("admoption.update_sort_type") == 1) {
                    $vendor_array = sort_encode_general($row['vendor_array'], $row['category']);
                }
                // ��������� ������������� ���������� ��������
                else {
                    $vendor_array = sort_encode($row['vendor_array'], $row['category']);
                }

                if (is_array($vendor_array)) {
                    $row['vendor_array'] = serialize($vendor_array);
                    foreach ($vendor_array as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $p) {
                                $row['vendor'] .= "i" . $k . "-" . $p . "i";
                            }
                        } else
                            $row['vendor'] .= "i" . $k . "-" . $v . "i";
                    }
                } else
                    $row['vendor_array'] = null;
            }

            // ������ ���� � �������������
            if (!strstr($row['pic_big'], '/UserFiles/Image/') and ! strstr($row['pic_big'], 'http'))
                $_POST['export_imgpath'] = true;
            else
                $_POST['export_imgpath'] = false;


            if (!empty($_POST['export_imgpath'])) {
                if (!empty($row['pic_small']))
                    $row['pic_small'] = '/UserFiles/Image/' . $row['pic_small'];
            }

            // ����������� ��� �����������
            if (empty($_POST['export_imgdelim'])) {
                $imgdelim = [' ', ',', ';', '#'];
                foreach ($imgdelim as $delim) {
                    if (strstr($row['pic_big'], $delim)) {
                        $_POST['export_imgdelim'] = $delim;
                    }
                }
            }

            // �������������� �����������
            if (!empty($_POST['export_imgdelim']) and strstr($row['pic_big'], $_POST['export_imgdelim'])) {
                $data_img = explode($_POST['export_imgdelim'], $row['pic_big']);
            } elseif (!empty($row['pic_big']))
                $data_img[] = $row['pic_big'];

            // �������� ������������ �������
            if (empty($subpath[2]) and ! empty($_POST['export_uniq']) and ! empty($row['uid'])) {
                $uniq = $PHPShopBase->getNumRows('products', "where uid = '" . $row['uid'] . "'");
            } else
                $uniq = 0;

            // ���������� �����������
            if ($_POST['export_imgload'] == 0) {
                unset($data_img);
                $row['pic_big'] = null;
            }

            if (!empty($data_img) and is_array($data_img)) {

                // ������� ��������� ������
                unset($row['pic_big']);

                // ���� ��������� ��������� ����
                if (isset($_POST['export_imgproc']) and $_POST['export_imgload'] == 1)
                    unset($row['pic_small']);

                // ��������� ID ������ �� �������� ��� ����������
                if ($_POST['export_action'] == 'update' and empty($row['id']) and ! empty($row['uid'])) {
                    $PHPShopOrmProd = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $data_prod = $PHPShopOrmProd->getOne(array('id'), array('uid' => '="' . $row['uid'] . '"'));
                    $row['id'] = $data_prod['id'];
                }

                // ������� ����������� ��� �������� ������������
                if ($_POST['export_action'] == 'insert' and ! empty($uniq)) {
                    unset($data_img);
                }

                // ������ �����������
                if ($_POST['export_imgfunc'] == 1) {
                    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $PHPShopOrmImg->delete(['parent' => '=' . intval($row['id'])]);
                }

                // �������� ����������� � �������
                if ($_POST['export_imgfunc'] == 2) {
                    fotoDelete(['parent' => '=' . intval($row['id'])]);
                }

                foreach ($data_img as $k => $img) {
                    if (!empty($img)) {

                        // ������ ���� � ������������
                        if (!empty($_POST['export_imgpath']))
                            $img = '/UserFiles/Image/' . $img;



                        // �������� ����������
                        $checkImage = checkImage($img, $row['id'], $row['parent_enabled']);
                        $img_save = $checkImage['img'];

                        // ������� �����
                        if (empty($checkImage['check'])) {

                            // �������� ����������� �� ������
                            if ($_POST['export_imgload'] == 1 and strstr($img, 'http')) {

                                // ���� ��������
                                if (downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $img_save))
                                    $img_load++;
                                else
                                    continue;

                                // ����� ���
                                $img = $img_save;
                                $path_parts = pathinfo($img);

                                // ���������� � webp
                                if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save') and $path_parts['extension'] != 'webp') {

                                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $thumb->setFormat('WEBP');
                                    $name_webp = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], '.webp', $img);

                                    $thumb->save($_SERVER['DOCUMENT_ROOT'] . $name_webp);
                                    @unlink($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $img = $name_webp;
                                }
                            }


                            // ������ � �����������
                            $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                            $PHPShopOrmImg->insert(array('parent_new' => intval($row['id']), 'name_new' => $img, 'num_new' => $k));

                            $file = $_SERVER['DOCUMENT_ROOT'] . $img;
                            $name = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".webp", ".WEBP"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif", "s.webp", "s.webp"), $file);

                            if (!file_exists($name) and file_exists($file)) {

                                // ��������� �������� 
                                if (!empty($_POST['export_imgproc'])) {
                                    $thumb = new PHPThumb($file);
                                    $thumb->setOptions(array('jpegQuality' => $width_kratko));
                                    $thumb->resize($img_tw, $img_th);
                                    $thumb->save($name);
                                } else
                                    copy($file, $name);
                            }

                            // ������� �����������
                            if ($k == 0 and ! empty($file)) {

                                $row['pic_big'] = $img;

                                // ������� ������
                                if ($_POST['export_imgload'] == 2) {
                                    $row['pic_small'] = $img;
                                } else if ($_POST['export_imgload'] == 1 and isset($_POST['export_imgproc'])) {
                                    $row['pic_small'] = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".webp", ".WEBP"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif", "s.webp", "s.webp"), $img);
                                }
                            }
                        } else
                            continue;
                    }
                }
            }
            // ������ ���� � �������������
            else if (isset($_POST['export_imgpath']) and ! empty($row['pic_big']))
                $row['pic_big'] = '/UserFiles/Image/' . $row['pic_big'];

            // �������� ������
            if ($_POST['export_action'] == 'insert') {


                $PHPShopOrm->debug = false;
                $PHPShopOrm->mysql_error = false;

                // ���������� �� ������
                if (isset($row['items'])) {
                    switch ($GLOBALS['admoption_sklad_status']) {

                        case(3):
                            if ($row['items'] < 1) {
                                $row['sklad'] = 1;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['sklad'] = 0;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        case(2):
                            if ($row['items'] < 1) {
                                $row['enabled'] = 0;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['enabled'] = 1;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        default:
                            break;
                    }
                }

                // ���� ��������
                $row['datas'] = time();

                // �������� SEO ����� ��������
                if ($subpath[2] == 'catalog' and ! empty($row['name'])) {
                    $uniq_cat_data = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['name' => '="' . $row['name'] . '"']);

                    // ���� ����������� �������
                    if (!empty($uniq_cat_data['name'])) {
                        $parent_cat_data = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $uniq_cat_data['parent_to'] . '"']);
                        $row['cat_seo_name'] = PHPShopString::toLatin($row['name']);
                        $row['cat_seo_name'] = PHPShopString::toLatin($parent_cat_data['name']) . '-' . PHPShopString::toLatin($row['name']);
                    } else
                        $row['cat_seo_name'] = PHPShopString::toLatin($row['name']);
                }

                // �������� ������� �����
                if (empty($row['name']))
                    $uniq = true;

                if (empty($uniq)) {

                    if (isset($row['price'])) {
                        $row['price'] = str_replace(',', '.', $row['price']);
                    }
                    if (isset($row['price_n'])) {
                        $row['price_n'] = str_replace(',', '.', $row['price_n']);
                    }
                    if (isset($row['price2'])) {
                        $row['price2'] = str_replace(',', '.', $row['price2']);
                    }
                    if (isset($row['price3'])) {
                        $row['price3'] = str_replace(',', '.', $row['price3']);
                    }
                    if (isset($row['price4'])) {
                        $row['price4'] = str_replace(',', '.', $row['price4']);
                    }
                    if (isset($row['price5'])) {
                        $row['price5'] = str_replace(',', '.', $row['price5']);
                    }

                    // ID ��������
                    $row['import_id'] = $_SESSION['import_id'];

                    $insertID = $PHPShopOrm->insert($row, '');
                    if (is_numeric($insertID)) {

                        $PHPShopOrm->clean();

                        // ��������� ID � ����������� ������ ������
                        if ($PHPShopOrmImg)
                            $PHPShopOrmImg->update(array('parent_new' => $insertID), array('parent' => '=0'));

                        // �������
                        $csv_load_count++;
                        $csv_load_totale++;

                        // �����
                        $GLOBALS['csv_load'][] = $row;
                    }
                }
            }
            // ���������� ������
            else {

                // ������������� ����
                if (!empty($_POST['export_key'])) {
                    $where = array($_POST['export_key'] => '="' . $row[$_POST['export_key']] . '"');
                    unset($row[$_POST['export_key']]);
                } else {

                    // ���������� �� ID
                    if (!empty($row['id'])) {
                        $where = array('id' => '="' . intval($row['id']) . '"');
                        unset($row['id']);
                    }

                    // ���������� �� ��������
                    elseif (!empty($row['uid'])) {
                        $where = array('uid' => '="' . $row['uid'] . '"');
                        unset($row['uid']);
                    }

                    // ���������� �� ������
                    elseif (!empty($row['login'])) {
                        $where = array('login' => '="' . $row['login'] . '"');
                        unset($row['login']);
                    }

                    // ������
                    else {
                        unset($row);
                        return false;
                    }
                }

                // ���������� �� ������
                if (isset($row['items'])) {
                    switch ($GLOBALS['admoption_sklad_status']) {

                        case(3):
                            if ($row['items'] < 1) {
                                $row['sklad'] = 1;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['sklad'] = 0;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        case(2):
                            if ($row['items'] < 1) {
                                $row['enabled'] = 0;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['enabled'] = 1;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        default:
                            break;
                    }
                }

                // ���� ����������
                $row['datas'] = time();

                // ID ��������
                $row['import_id'] = $_SESSION['import_id'];

                if (!empty($where)) {
                    $PHPShopOrm->debug = false;
                    if ($PHPShopOrm->update($row, $where, '') === true) {

                        // ��������� ID � ����������� ������ �� ��������
                        if (!empty($where['uid']) and is_array($data_img) and $PHPShopOrmImg) {

                            $PHPShopOrmProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                            $data_product = $PHPShopOrmProduct->select(array('id'), array('uid' => $where['uid']), false, array('limit' => 1));
                            $PHPShopOrmImg->update(array('parent_new' => $data_product['id']), array('parent' => '=0'));
                        }

                        // �������
                        $count = $PHPShopOrm->get_affected_rows();

                        $csv_load_count += $count;
                        $csv_load_totale++;

                        // �����
                        if (!empty($count))
                            $GLOBALS['csv_load'][] = $row;
                    }
                }
            }
        }
    }
}

// ���������� ���� ���������
function createCategoryPath($category_array, $id, $path = null) {

    if (isset($category_array[$id])) {
        $path .= '/' . $category_array[$id][0];

        if (isset($category_array[$category_array[$id][1]])) {
            $path .= '/' . $category_array[$category_array[$id][1]][0];

            $path .= createCategoryPath($category_array, $category_array[$category_array[$id][1]][0], $path);
            return $path;
        }

        return $path;
    }
}

// ������� ����������
function actionSave() {
    global $PHPShopGUI, $PHPShopSystem, $key_name, $key_name, $result_message, $csv_load_count, $subpath, $csv_load, $csv_load_totale, $img_load;

    // ������� ���������
    if ($_POST['exchanges'] != 'new') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);

        // �������� ��� ���������
        if (!empty($_POST['exchanges_new'])) {
            $PHPShopOrm->update(array('name_new' => $_POST['exchanges_new']), array('id' => '=' . intval($_POST['exchanges'])));
        }

        // ��������� ��� Cron
        if (!empty($_POST['exchanges_cron'])) {
            $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['exchanges'])), false, array("limit" => 1));
            if (is_array($data)) {
                unset($_POST);
                $_POST = unserialize($data['option']);
                $exchanges_name = $data['name'];
                unset($_POST['exchanges_new']);
                unset($_POST['smart']);
            }
        }
    }

    // ������� ���������
    if (!empty($_POST['exchanges_remove']) and is_array($_POST['exchanges_remove'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        foreach ($_POST['exchanges_remove'] as $v)
            $data = $PHPShopOrm->delete(array('id' => '=' . intval($v)));
    }

    // ������ �� ������ ��������
    if (!empty($_POST['subpath']))
        $subpath[2] = $_POST['subpath'];

    switch ($subpath[2]) {
        case 'catalog':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            break;
        case 'user':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            break;
        case 'order':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            break;
        default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            break;
    }

    $delim = $_POST['export_delim'];

    // ��������� �������� ������
    $GLOBALS['admoption_sklad_status'] = $PHPShopSystem->getSerilizeParam('admoption.sklad_status');

    // ������ ��������
    $memory[$_GET['path']]['export_sortdelim'] = @$_POST['export_sortdelim'];
    $memory[$_GET['path']]['export_sortsdelim'] = @$_POST['export_sortsdelim'];
    $memory[$_GET['path']]['export_imgdelim'] = @$_POST['export_imgdelim'];
    $memory[$_GET['path']]['export_imgpath'] = @$_POST['export_imgpath'];
    $memory[$_GET['path']]['export_uniq'] = @$_POST['export_uniq'];
    $memory[$_GET['path']]['export_action'] = @$_POST['export_action'];
    $memory[$_GET['path']]['export_delim'] = @$_POST['export_delim'];
    $memory[$_GET['path']]['export_imgproc'] = @$_POST['export_imgproc'];
    $memory[$_GET['path']]['export_imgload'] = @$_POST['export_imgload'];
    $memory[$_GET['path']]['export_imgsearch'] = @$_POST['export_search'];
    $memory[$_GET['path']]['export_ai'] = @$_POST['export_ai'];

    // �������� csv �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], ['csv', 'xml', 'yml', 'xlsx', 'xls'])) {
            if (@move_uploaded_file($_FILES['file']['tmp_name'], "csv/" . PHPShopString::toLatin($_FILES['file']['name']) . '.' . $_FILES['file']['ext'])) {
                $csv_file_name = PHPShopString::toLatin($_FILES['file']['name']) . '.' . $_FILES['file']['ext'];
                $csv_file = "csv/" . $csv_file_name;
                $_POST['lfile'] = $GLOBALS['dir']['dir'] . "/phpshop/admpanel/csv/" . $csv_file_name;
            } else
                $result_message = $PHPShopGUI->setAlert('������ ���������� ����� <strong>' . $csv_file_name . '</strong> � phpshop/admpanel/csv', 'danger');
        }
    }

    // ������ csv �� URL
    elseif (!empty($_POST['furl'])) {

        // Google
        $path = parse_url($_POST['furl']);
        if ($path['host'] == 'docs.google.com') {
            $a_path = explode("/", $path['path']);
            if (is_array($a_path)) {
                $id = $a_path[3];

                if ($id == 'e') {
                    $id = $a_path[4];
                    $csv_file = $_POST['furl'];
                } else
                    $csv_file = 'https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&' . $path['fragment'];

                $csv_file_name = 'Google ������ ' . $_POST['exchanges_new'] . $exchanges_name;
                $_POST['export_code'] = 'utf';
                $delim = ',';
            }
        }
        // Url
        else {
            $csv_file = $_POST['furl'];
            $path_parts = pathinfo($csv_file);
            $csv_file_name = $path_parts['basename'];
            $url = true;
        }
    }

    // ������ csv �� ��������� ���������
    elseif (!empty($_POST['lfile'])) {
        $csv_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['lfile'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }
    // �������������
    elseif (!empty($_POST['csv_file'])) {
        $csv_file = $_POST['csv_file'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }

    // ��������� csv
    if (!empty($csv_file)) {

        PHPShopObj::loadClass('file');

        // ID ��������
        $_SESSION['import_id'] = md5($csv_file . date("m.d.y"));

        // ��������������� ����������
        if ($_POST['export_extension'] == 'auto') {

            $_POST['export_extension'] = PHPShopSecurity::getExt($csv_file);

            if (!in_array($_POST['export_extension'], ['csv', 'xls', 'xlsx'])) {

                $find_extension = file($csv_file);

                if (strpos($find_extension['1'], 'yml_catalog') or strpos($find_extension['2'], 'yml_catalog'))
                    $_POST['export_extension'] = 'yml';
                else if (strpos($find_extension['1'], 'google') or strpos($find_extension['2'], 'channel'))
                    $_POST['export_extension'] = 'rss';
                else if (strpos($find_extension['1'], '����������������������') or strpos($find_extension['1'], PHPShopString::win_utf8('����������������������')))
                    $_POST['export_extension'] = 'cml';
            }
        } elseif (empty($_POST['export_extension'])) {
            $_POST['export_extension'] = 'csv';
        }

        // ��������������� ���������
        if ($_POST['export_code'] == 'auto') {

            if (in_array($_POST['export_extension'], ['csv', 'xls', 'xlsx'])) {

                if (!$find_extension)
                    $find_extension = file($csv_file);

                if (stripos($find_extension['0'], '�������') or stripos($find_extension['0'], '�����') or stripos($find_extension['0'], '���� 1') or stripos($find_extension['0'], '������������'))
                    $_POST['export_code'] = 'ansi';
                elseif (stripos($find_extension['0'], PHPShopString::win_utf8('�������')) or stripos($find_extension['0'], PHPShopString::win_utf8('�����')) or stripos($find_extension['0'], PHPShopString::win_utf8('���� 1')) or stripos($find_extension['0'], PHPShopString::win_utf8('������������')))
                    $_POST['export_code'] = 'utf';
            }
        }

        if ($find_extension)
            unset($find_extension);

        // YML
        if ($_POST['export_extension'] == 'yml') {

            if ($xml = simplexml_load_file($csv_file)) {
                $_POST['export_code'] = 'ansi';

                // ������
                if (empty($subpath[2])) {

                    $yml_array[0] = ["�������", "������������", "������� �����������", "��������� ��������", "�����", "���� 1", "���", "ISO", "�������", "���� ��������", "��������������", "��������", "������", "����������� ������", "����", "������ ����", "�����", "������", "������", "������� ���"];

                    // ��������
                    foreach ($xml->shop[0]->categories[0]->category as $item) {
                        $category_array[(string) $item->attributes()->id] = [PHPShopString::utf8_win1251((string) $item[0]), (string) $item->attributes()->parentId];
                    }

                    // ������
                    foreach ($xml->shop[0]->offers[0]->offer as $item) {

                        $warehouse = 0;
                        $parent2 = $parent = '';

                        // ���� ��������
                        $category_path = createCategoryPath($category_array, (string) $item->categoryId[0]);
                        $category_path = substr($category_path, 1, strlen($category_path) - 1);
                        $category_path_array = explode("/", $category_path);
                        $category_path = implode("/", array_reverse($category_path_array));


                        // �����
                        if (isset($item->count[0]))
                            $warehouse = (int) $item->count[0];

                        // �����
                        if (isset($item->amount[0]))
                            $warehouse = (int) $item->count[0];

                        // �����
                        if ((string) $item->attributes()->available == "true" and empty($warehouse))
                            $warehouse = 1;

                        // ��������
                        if (is_array((array) $item->picture)) {
                            $images = implode(",", (array) $item->picture);
                        } else
                            $images = (string) $item->picture;

                        // ������ ����
                        if (isset($item->oldprice[0]))
                            $oldprice = (string) $item->oldprice[0];

                        // ��������
                        if (isset($item->dimensions[0])) {
                            $dimensions = explode("/", (string) $item->dimensions[0]);
                            $length = $dimensions[0];
                            $width = $dimensions[1];
                            $height = $dimensions[2];
                        }

                        // ��������������
                        $sort = null;
                        $i = 0;

                        if (is_array((array) $item->param)) {
                            while ($i < (count((array) $item->param) - 1)) {

                                $sort_name = PHPShopString::utf8_win1251((string) $item->param[$i]->attributes()->name);
                                $sort_value = PHPShopString::utf8_win1251((string) $item->param[$i]);
                                $i++;

                                $sort .= $sort_name . '/' . $sort_value . '#';
                            }
                        } else
                            $sort = (string) $item->param[0];

                        // �����
                        if (isset($item->vendor[0]))
                            $sort .= '�����/' . (string) $item->vendor[0];

                        // ��������
                        if (!empty((string) $item->barcode[0]))
                            $barcode = (string) $item->barcode[0];
                        else
                            $barcode = null;

                        // �������
                        if (!empty((string) $item->vendorCode[0])) {
                            $uid = (string) $item->vendorCode[0];
                            
                            // ������� ���
                            $external_code = (string) $item->attributes()->id;
                        } else {
                            $uid = (string) $item->attributes()->id;
                            $external_code = null;
                        }

                        // �������
                        if (!empty((string) $item->attributes()->group_id)) {

                            $parent_enabled = 1;
                            $sort = null;

                            if (!empty((string) $item->param[0]))
                                $parent = PHPShopString::utf8_win1251((string) $item->param[0]);

                            if (!empty((string) $item->param[1]))
                                $parent2 = PHPShopString::utf8_win1251((string) $item->param[1]);

                            // ������� �����
                            if (!is_array($yml_array[(string) $item->attributes()->group_id])) {

                                // ��������
                                $name = ucfirst(trim(str_replace([$parent, $parent2], ['', ''], PHPShopString::utf8_win1251((string) $item->name[0]))));

                                $yml_array[(string) $item->attributes()->group_id] = [(string) $item->attributes()->group_id, $name, $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0])), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (string) $item->categoryId[0], $category_path, $sort, $barcode, 0, (string) $item->attributes()->id, '', $oldprice, $length, $width, $height];
                            } else {

                                // ������ ��������
                                $yml_array[(string) $item->attributes()->group_id][13] .= ',' . (string) $item->attributes()->id;

                                // ��������
                                $yml_array[(string) $item->attributes()->group_id][3] .= ',' . $images;

                                // ����������� ����
                                if ($yml_array[(string) $item->attributes()->group_id][6] > (string) $item->price[0])
                                    $yml_array[(string) $item->attributes()->group_id][6] = (string) $item->price[0];
                            }
                        }
                        else {
                            $parent_enabled = 0;
                            $parent = $parent2 = '';
                        }






                        $yml_array[$uid] = [$uid, PHPShopString::utf8_win1251((string) $item->name[0]), $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0])), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (string) $item->categoryId[0], $category_path, $sort, $barcode, $parent_enabled, $parent, $parent2, $oldprice, $length, $width, $height, $external_code];
                    }

                    if (empty($GLOBALS['exchanges_cron']))
                        $csv_file = './csv/product.yml.csv';
                    else
                        $csv_file = '../../../admpanel/csv/product.yml.csv';
                }
                // ���������
                else if ($subpath[2] == 'catalog') {
                    $yml_array[] = ['Id', '������������', '��������'];
                    foreach ($xml->shop[0]->categories[0]->category as $item) {
                        $yml_array[] = [(string) $item->attributes()->id, PHPShopString::utf8_win1251((string) $item[0]), (string) $item->attributes()->parentId];
                    }

                    if (empty($GLOBALS['exchanges_cron']))
                        $csv_file = './csv/category.yml.csv';
                    else
                        $csv_file = '../../../admpanel/csv/category.yml.csv';
                }

                // ��������� ����
                PHPShopFile::writeCsv($csv_file, $yml_array);
            }
        }

        // RSS
        else if ($_POST['export_extension'] == 'rss') {

            $_POST['export_code'] = 'ansi';
            $feed = str_replace(['g:'], [''], file_get_contents($csv_file));
            $xml = simplexml_load_string($feed);

            // ������
            $yml_array[] = ["�������", "������������", "������� �����������", "��������� ��������", "�����", "���� 1", "ISO"];

            foreach ($xml->channel[0]->item as $item) {

                // �����
                if ((string) $item->availability == "in stock")
                    $warehouse = 1;
                else
                    $warehouse = 0;

                // ��������
                if (is_array((array) $item->image_link))
                    $images = implode(",", (array) $item->image_link);
                else
                    $images = (string) $item->image_link;

                // ����
                $price = explode(" ", (string) $item->price[0]);

                $yml_array[] = [(string) $item->id[0], PHPShopString::utf8_win1251((string) $item->title[0]), $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0])), $warehouse, $price[0], $price[1], (int) $item->categoryId[0]];
            }


            if (empty($GLOBALS['exchanges_cron']))
                $csv_file = './csv/product.rss.csv';
            else
                $csv_file = '../../../admpanel/csv/product.rss.csv';

            // ��������� ����
            PHPShopFile::writeCsv($csv_file, $yml_array);
        }

        // XLSX
        else if ($_POST['export_extension'] == 'xlsx') {

            require_once '../lib/simplexlsx/SimpleXLSX.php';
            $_POST['export_code'] = 'utf';

            if ($xlsx = SimpleXLSX::parse($csv_file)) {

                if (empty($GLOBALS['exchanges_cron']))
                    $csv_file = './csv/product.xlsx.csv';
                else
                    $csv_file = '../../../admpanel/csv/product.xlsx.csv';

                // ��������� ����
                PHPShopFile::writeCsv($csv_file, $xlsx->rows());
            } else {
                echo SimpleXLSX::parseError();
            }
        }

        // XLS
        else if ($_POST['export_extension'] == 'xls') {

            require_once '../lib/simplexlsx/SimpleXLS.php';
            $_POST['export_code'] = 'utf';

            if ($xls = SimpleXLS::parse($csv_file)) {

                if (empty($GLOBALS['exchanges_cron']))
                    $csv_file = './csv/product.xls.csv';
                else
                    $csv_file = '../../../admpanel/csv/product.xls.csv';

                // ��������� ����
                PHPShopFile::writeCsv($csv_file, $xls->rows());
            } else {
                echo SimpleXLS::parseError();
            }
        }
        // �������� CSV ���� �������� ��� �������������
        else if ($_POST['export_extension'] == 'csv' and ! empty($url) and ! empty($_POST['smart'])) {

            if (!empty($_POST['furl'])) {
                $csv_file = './csv/' . $path_parts['basename'];
                @file_put_contents($csv_file, @file_get_contents($_POST['furl']));
            }
        }

        // �������������
        if (!empty($_POST['smart'])) {

            $limit = intval($_POST['line_limit']);

            if (empty($_POST['end']))
                $_POST['end'] = intval($_POST['line_limit']);

            $end = $_POST['end'];

            if (isset($_POST['total']) and $_POST['end'] > $_POST['total'])
                $end = $_POST['total'];

            if (empty($_POST['start']))
                $_POST['start'] = 0;

            // ������ ��������
            if (empty($_POST['total'])) {

                // ����� � �����
                $total = 0;
                $handle = fopen($csv_file, "r");
                while ($data = fgetcsv($handle, 0, $delim)) {
                    $total++;
                }

                $bar = 0;
                $end = 0;
                $csv_load_count = 0;
                $bar_class = "active";

                if ($_POST['export_action'] == 'insert')
                    $do = '�������';
                else
                    $do = '��������';

                $total_min = round($total / $_POST['line_limit'] * $_POST['time_limit']);

                $result_message = $PHPShopGUI->setAlert('<div id="bot_result">' . __('����') . ' <strong>' . $csv_file_name . '</strong> ' . __('��������. ���������� ') . $end . __(' �� ') . $total . __(' �����. ' . $do) . ' <b id="total-update">' . intval($csv_load_count) . '</b> ' . __('�������.') . '</div>
<div class="progress bot-progress">
  <div class="progress-bar progress-bar-striped  progress-bar-success ' . $bar_class . '" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: ' . $bar . '%"> ' . $bar . '% 
  </div>
</div>', 'success load-result', false, false, false);
                $result_message .= $PHPShopGUI->setAlert('<b>����������, �� ���������� ���� �� ������ �������� �������</b><br>
�� ������ ���������� ������ � ������� ��������� �����, �������� ���� � ����� ������� (������� <kbd>CTRL</kbd> � �������� �� ������).', 'info load-info', true, false, false);
                $result_message .= $PHPShopGUI->setInput("hidden", "csv_file", $csv_file);
                $result_message .= $PHPShopGUI->setInput("hidden", "total", $total);
                $result_message .= $PHPShopGUI->setInput("hidden", "stop", 0);
            } else {

                $result = PHPShopFile::readCsvGenerators($csv_file, 'csv_update', $delim, array($_POST['start'], $_POST['end']));
                if ($result) {

                    $total = $_POST['total'];

                    $bar = round($_POST['line_limit'] * 100 / $total);

                    // �����
                    if ($end > $total) {
                        $end = $total;
                        $bar = 100;
                        $bar_class = null;
                    } else {
                        $bar_class = "active";
                    }

                    if ($_POST['export_action'] == 'insert')
                        $lang_do = __('�������');
                    else
                        $lang_do = __('��������');

                    if ($csv_load_count < 0)
                        $csv_load_count = 0;

                    $total_min = round(($total - $csv_load_count) / $_POST['line_limit'] * $_POST['time_limit']);
                    $action = true;
                    $json_message = __('����') . ' <strong>' . $csv_file_name . '</strong> ' . __('��������. ���������� ') . $end . __(' �� ') . $total . __(' �����. ') . $lang_do . ' <b id="total-update">' . intval($csv_load_count) . '</b> ' . __('�������.');

                    // ���� ��������
                    if ($_POST['line_limit'] >= 10) {
                        $result_csv = './csv/result_' . date("d_m_y_His") . '.csv';
                        PHPShopFile::writeCsv($result_csv, $GLOBALS['csv_load']);
                    }

                    // ������ ��� �������
                    $csv_load_totale = $_POST['start'] . '-' . $_POST['end'];
                } else
                    $result_message = $PHPShopGUI->setAlert(__('��� ���� �� ������ �����') . ' ' . $csv_file, 'danger', false);
            }
        }
        else {

            $result = PHPShopFile::readCsv($csv_file, 'csv_update', $delim);

            if ($result) {

                if (empty($csv_load_count))
                    $result_message = $PHPShopGUI->setAlert(__('����') . ' <strong>' . $csv_file_name . '</strong> ' . __('��������. ���������� ' . $csv_load_totale . ' �����. ��������') . ' <strong>' . intval($csv_load_count) . '</strong> ' . __('�������') . '.', 'warning', false);
                else {

                    // ���� ��������
                    $result_csv = 'result_' . date("d_m_y_His") . '.csv';
                    if (empty($GLOBALS['exchanges_cron']))
                        PHPShopFile::writeCsv('./csv/' . $result_csv, $csv_load);
                    else
                        PHPShopFile::writeCsv('../../../admpanel/csv/' . $result_csv, $csv_load);

                    if ($_POST['export_action'] == 'insert') {
                        $lang_do = '�������';
                        $lang_do2 = '���������';
                    } else {
                        $lang_do = '��������';
                        $lang_do2 = '�����������';
                    }

                    $result_message = $PHPShopGUI->setAlert(__('����') . ' <strong>' . $csv_file_name . '</strong> ' . __('��������. ���������� ' . $csv_load_totale . ' �����. ' . $lang_do) . ' <strong>' . intval($csv_load_count) . '</strong> ' . __('�������') . '. ' . __('����� �� ' . $lang_do2 . ' �������� ') . ' <a href="./csv/' . $result_csv . '" target="_blank">CSV</a>.', 'success', false);
                }
            } else {
                $result = 0;
                $result_message = $PHPShopGUI->setAlert(__('��� ���� �� ������ �����') . ' ' . $csv_file, 'danger', false);
            }
        }
    }

    // ���������� ���������
    if ($_POST['exchanges'] == 'new' and ! empty($_POST['exchanges_new'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $PHPShopOrm->insert(array('name_new' => $_POST['exchanges_new'], 'option_new' => serialize($_POST), 'type_new' => 'import'));
    }

    if (!empty($_POST['smart']) and ( empty($_POST['total']) or $_POST['line_limit'] < 10))
        $log_off = true;

    // ������ ��������
    if (empty($log_off)) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
        $_POST['exchanges'] = $_GET['exchanges'];
        $PHPShopOrm->insert(array('date_new' => time(), 'file_new' => $csv_file, 'status_new' => $result, 'info_new' => serialize([$csv_load_totale, $lang_do, (int) $csv_load_count, $result_csv, (int) $img_load]), 'option_new' => serialize($_POST), 'import_id_new' => $_SESSION['import_id']));
    }

    // �������������
    if (!empty($_POST['ajax'])) {

        if ($total > $end) {

            $bar = round($_POST['end'] * 100 / $total);

            return array("success" => $action, "bar" => $bar, "count" => $csv_load_count, "result" => PHPShopString::win_utf8($json_message), 'limit' => $limit, 'img_load' => (int) $img_load, 'action' => PHPShopString::win_utf8(mb_strtolower($lang_do, $GLOBALS['PHPShopBase']->codBase)));
        } else {

            // ������ ��������
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
            $_POST['exchanges'] = $_GET['exchanges'];
            $PHPShopOrm->insert(array('date_new' => time(), 'file_new' => $csv_file, 'status_new' => $result, 'info_new' => serialize([$total, $lang_do, (int) ($total - 1), $result_csv, (int) $_POST['img_load']]), 'option_new' => serialize($_POST), 'import_id_new' => $_SESSION['import_id']));


            return array("success" => 'done', "count" => $csv_load_count, "result" => PHPShopString::win_utf8($json_message), 'limit' => $limit, 'action' => PHPShopString::win_utf8(mb_strtolower($lang_do, $GLOBALS['PHPShopBase']->codBase)));
        }
    }
}

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopSystem, $TitlePage, $PHPShopOrm, $key_name, $subpath, $key_base, $key_stop, $result_message;


    // ������� ���������
    if (!empty($_GET['exchanges'])) {

        $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($_GET['exchanges'])), false, array("limit" => 1));

        if (is_array($data_exchanges)) {
            $_POST = unserialize($data_exchanges['option']);
            $exchanges_name = ": " . $data_exchanges['name'];
        }
    }

    if (!empty($_POST['export_action'])) {


        $memory[$_GET['path']]['export_sortdelim'] = @$_POST['export_sortdelim'];
        $memory[$_GET['path']]['export_sortsdelim'] = @$_POST['export_sortsdelim'];
        $memory[$_GET['path']]['export_imgdelim'] = @$_POST['export_imgdelim'];
        $memory[$_GET['path']]['export_imgpath'] = @$_POST['export_imgpath'];
        $memory[$_GET['path']]['export_imgload'] = @$_POST['export_imgload'];
        $memory[$_GET['path']]['export_uniq'] = @$_POST['export_uniq'];
        $memory[$_GET['path']]['export_action'] = @$_POST['export_action'];
        $memory[$_GET['path']]['export_delim'] = @$_POST['export_delim'];
        $memory[$_GET['path']]['export_imgproc'] = @$_POST['export_imgproc'];
        $memory[$_GET['path']]['export_code'] = @$_POST['export_code'];
        $memory[$_GET['path']]['smart'] = @$_POST['smart'];
        $memory[$_GET['path']]['export_key'] = @$_POST['export_key'];
        $memory[$_GET['path']]['export_imgfunc'] = @$_POST['export_imgfunc'];
        $memory[$_GET['path']]['export_extension'] = @$_POST['export_extension'];
        $memory[$_GET['path']]['export_imgsearch'] = @$_POST['export_imgsearch'];
        $memory[$_GET['path']]['export_ai'] = @$_POST['export_ai'];


        $export_sortdelim = @$memory[$_GET['path']]['export_sortdelim'];
        $export_sortsdelim = @$memory[$_GET['path']]['export_sortsdelim'];
        $export_imgvalue = @$memory[$_GET['path']]['export_imgdelim'];
        $export_code = $memory[$_GET['path']]['export_code'];
        $export_extension = $memory[$_GET['path']]['export_extension'];
        $export_key = $memory[$_GET['path']]['export_key'];
        $export_imgfunc = @$memory[$_GET['path']]['export_imgfunc'];
        $export_imgload = $memory[$_GET['path']]['export_imgload'];
    }
    // ��������� �� ���������
    else {
        $memory[$_GET['path']]['export_imgload'] = 1;
        $memory[$_GET['path']]['export_imgproc'] = 1;
        $memory[$_GET['path']]['export_imgsearch'] = 0;
        $memory[$_GET['path']]['export_ai'] = 0;
        $export_imgload = 1;

        $_POST['line_limit'] = 1;

        if ($_GET['path'] == 'exchange.import')
            $_POST['smart'] = 1;

        if ($subpath[2] == 'catalog')
            $memory[$_GET['path']]['export_action'] = 'insert';

        /*
          if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
          $export_code = 'utf';
          else
          $export_code = 'ansi'; */

        $export_code = 'auto';
    }

    $PHPShopGUI->action_button['������'] = array(
        'name' => __('���������'),
        'action' => 'saveID',
        'class' => 'btn btn-primary btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-save'
    );

    $list = null;
    $PHPShopOrm->clean();
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));
    $select_value[] = array('�� �������', false, false);

    // ������ ����
    if (!is_array($data)) {
        $PHPShopOrm->insert(array('name_new' => '�������� �����'));
        $PHPShopOrm->clean();
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));
        $PHPShopOrm->delete(array('name' => '="�������� �����"'));

        if (empty($subpath[2]))
            $memory[$_GET['path']]['export_action'] = 'insert';
    }

    if (is_array($data)) {

        // ���� ��������
        if (empty($subpath[2])) {
            $data['path'] = null;
        }

        $key_value[] = array('Id ��� �������', 0);

        foreach ($data as $key => $val) {

            if (!empty($key_name[$key]))
                $name = $key_name[$key];
            else
                $name = $key;

            if (@in_array($key, $key_base)) {
                if ($key == 'id')
                    $kbd_class = 'enabled';
                else
                    $kbd_class = null;

                $list .= '<div class="pull-left" style="width:190px;min-height: 19px;"><kbd class="' . $kbd_class . '">' . __(ucfirst($name)) . '</kbd></div>';
                $help = 'data-subtext="<span class=\'glyphicon glyphicon-flag text-success\'></span>"';
            }
            elseif (!in_array($key, $key_stop)) {
                $list .= '<div class="pull-left" style="width:190px;min-height: 19px;">' . __(ucfirst($name)) . '</div>';
                $help = null;
            }

            if (!in_array($key, $key_stop)) {
                $select_value[] = array(ucfirst($name), __(ucfirst($name)), false, $help);

                // ���� ���������
                if ($key != 'id' and $key != 'uid' and $key != 'vendor' and $key != 'vendor_array') {
                    $key_value[] = array(ucfirst($name), $key, $export_key);
                }
            }
        }
    } else
        $list = '<span class="text-warning hidden-xs">' . __('������������ ������ ��� �������� ����� �����. �������� ���� ������ � ������ ������� � ������ ������ ��� ������ ������') . '.</span>';


    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js');
    $PHPShopGUI->_CODE = $result_message;

    // ������
    if (empty($subpath[2])) {
        $class = $yml = $class_ai = false;
        $TitlePage .= ' ' . __('�������');
        $data['path'] = null;
    }

    // ��������
    elseif ($subpath[2] == 'catalog') {
        $class = 'hide';
        $yml = $class_ai = false;
        $TitlePage .= ' ' . __('���������');
    }

    // ������������
    elseif ($subpath[2] == 'user') {
        $class = $yml = $class_ai = 'hide';
        $TitlePage .= ' ' . __('�������������');
    }

    // ������������
    elseif ($subpath[2] == 'order') {
        $class = $yml = $class_ai = 'hide';
    }

    $PHPShopGUI->setActionPanel($TitlePage . $exchanges_name, false, array('������'));

    $delim_value[] = array('����� � �������', ';', @$memory[$_GET['path']]['export_delim']);
    $delim_value[] = array('�������', ',', @$memory[$_GET['path']]['export_delim']);

    $action_value[] = array('����������', 'update', @$memory[$_GET['path']]['export_action']);
    $action_value[] = array('��������', 'insert', @$memory[$_GET['path']]['export_action']);

    $delim_sortvalue[] = array('#', '#', $export_sortdelim);
    $delim_sortvalue[] = array('@', '@', $export_sortdelim);
    $delim_sortvalue[] = array('$', '$', $export_sortdelim);
    $delim_sortvalue[] = array(__('�������'), '-', $export_sortdelim);

    $delim_sort[] = array('/', '/', $export_sortsdelim);
    $delim_sort[] = array('|', '|', $export_sortsdelim);
    $delim_sort[] = array('-', '-', $export_sortsdelim);
    $delim_sort[] = array('&', '&', $export_sortsdelim);
    $delim_sort[] = array(';', ';', $export_sortsdelim);
    $delim_sort[] = array(',', ',', $export_sortsdelim);

    $delim_imgvalue[] = array(__('��������������'), 0, $export_imgvalue);
    $delim_imgvalue[] = array(__('�������'), ',', $export_imgvalue);
    $delim_imgvalue[] = array(__('����� � �������'), ';', $export_imgvalue);
    $delim_imgvalue[] = array('#', '#', $export_imgvalue);
    $delim_imgvalue[] = array(__('������'), ' ', $export_imgvalue);

    $code_value[] = array('��������������', 'auto', $export_code);
    $code_value[] = array('ANSI', 'ansi', $export_code);
    $code_value[] = array('UTF-8', 'utf', $export_code);

    $code_extension[] = array(__('��������������'), 'auto', $export_extension);
    $code_extension[] = array('Excel (CSV)', 'csv', $export_extension);
    $code_extension[] = array('������ (YML)', 'yml', $export_extension);
    $code_extension[] = array('Google (RSS)', 'rss', $export_extension);
    $code_extension[] = array('Excel (XLSX)', 'xlsx', $export_extension);
    $code_extension[] = array('Excel (XLS)', 'xls', $export_extension);

    $imgfunc_value[] = array(__('�������� ���� � ������������'), 0, $export_imgfunc);
    $imgfunc_value[] = array(__('�������� ���� � ����, ��� �������� � �������'), 1, $export_imgfunc);
    $imgfunc_value[] = array(__('�������� � ������� ���� �� �������'), 2, $export_imgfunc);

    $imgload_value[] = array(__('������������'), 0, $export_imgload);
    $imgload_value[] = array(__('��������� �� ������� ������'), 1, $export_imgload);
    $imgload_value[] = array(__('��������� ������ � ����'), 2, $export_imgload);

    // AI
    if (empty($PHPShopSystem->ifSerilizeParam('admoption.yandexcloud_enabled'))) {
        $yandexcloud = $PHPShopGUI->setField('�������� �������� � ������� AI', $PHPShopGUI->setCheckbox('export_ai', 1, null, @$memory[$_GET['path']]['export_ai'], $PHPShopGUI->disabled_yandexcloud), 1, '�������� � ��������� �������� � ������� AI. ��������� �������� Yandex Cloud.', $class_ai) .
                $PHPShopGUI->setField('����� ����������� � ������', $PHPShopGUI->setCheckbox('export_imgsearch', 1, null, @$memory[$_GET['path']]['export_imgsearch'], $PHPShopGUI->disabled_yandexcloud), 1, '����� ����������� � ������� �� ����� ������. ��������� �������� Yandex Cloud.', $class_ai);
    }

    // �������� 1
    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setFile($_POST['lfile']), 1, '�������������� ����� csv, xls, xlsx, yml, xml') .
            $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('export_action', $action_value, 150, true)) .
            $PHPShopGUI->setField('CSV-�����������', $PHPShopGUI->setSelect('export_delim', $delim_value, 150, true)) .
            $PHPShopGUI->setField('����������� ��� �������������', $PHPShopGUI->setSelect('export_sortdelim', $delim_sortvalue, 150), false, '��� ������� Excel', $class) .
            $PHPShopGUI->setField('����������� �������� �������������', $PHPShopGUI->setSelect('export_sortsdelim', $delim_sort, 150), false, '��� ������� Excel', $class) .
            $yandexcloud .
            $PHPShopGUI->setField('��������� �����������', $PHPShopGUI->setCheckbox('export_imgproc', 1, null, @$memory[$_GET['path']]['export_imgproc']), 1, '�������� ����������� ��� ������ � ���������', $class) .
            $PHPShopGUI->setField('�������� �����������', $PHPShopGUI->setSelect('export_imgload', $imgload_value, 250), 1, '��������� ����������� ��� ������������ ������', $class) .
            $PHPShopGUI->setField('�������� ��� �����������', $PHPShopGUI->setSelect('export_imgfunc', $imgfunc_value, 250), 1, '�������� �� ����� ��� ��������� �����������', $class) .
            $PHPShopGUI->setField('����������� ��� �����������', $PHPShopGUI->setSelect('export_imgdelim', $delim_imgvalue, 150), 1, '�������������� ����������� ��� ������� Excel', $class) .
            $PHPShopGUI->setField('��������� ������', $PHPShopGUI->setSelect('export_code', $code_value, 150)) .
            $PHPShopGUI->setField('��� �����', $PHPShopGUI->setSelect('export_extension', $code_extension, 150), 1, null, $yml) .
            $PHPShopGUI->setField('���� ����������', $PHPShopGUI->setSelect('export_key', $key_value, 150, true, false, true), 1, '��������� ����� ���������� ����� �������� � ����� ������', $class) .
            $PHPShopGUI->setField('�������� ������������', $PHPShopGUI->setCheckbox('export_uniq', 1, null, @$memory[$_GET['path']]['export_uniq']), 1, '��������� ������������ ������ ��� ��������', $class);

    // ������
    if (is_array($_POST['select_action'])) {
        foreach ($_POST['select_action'] as $x => $p)
            if (is_array($select_value)) {
                $select_value_pre = [];
                foreach ($select_value as $k => $v) {

                    if ($v[0] == $p or ( strstr($v[0], '@') and strstr($p, '@')))
                        $v[2] = 'selected';
                    else
                        $v[2] = null;

                    $select_value_pre[] = [$v[0], $v[1], $v[2], $v[3]];
                }
                ${'select_value' . ($x + 1)} = $select_value_pre;
            }
    }else {
        $n = 1;
        while ($n < 21) {
            ${'select_value' . ($n)} = $select_value;
            $n++;
        }
    }

    // �������� 2
    $Tab2 = $PHPShopGUI->setField(array('������� A', '������� B'), array($PHPShopGUI->setSelect('select_action[]', $select_value1, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value2, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� C', '������� D'), array($PHPShopGUI->setSelect('select_action[]', $select_value3, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value4, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� E', '������� F'), array($PHPShopGUI->setSelect('select_action[]', $select_value5, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value6, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� G', '������� H'), array($PHPShopGUI->setSelect('select_action[]', $select_value7, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value8, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� I', '������� J'), array($PHPShopGUI->setSelect('select_action[]', $select_value9, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value10, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� K', '������� L'), array($PHPShopGUI->setSelect('select_action[]', $select_value11, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value12, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� M', '������� N'), array($PHPShopGUI->setSelect('select_action[]', $select_value13, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value14, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� O', '������� P'), array($PHPShopGUI->setSelect('select_action[]', $select_value15, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value16, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� Q', '������� R'), array($PHPShopGUI->setSelect('select_action[]', $select_value17, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value18, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('������� S', '������� T'), array($PHPShopGUI->setSelect('select_action[]', $select_value19, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value20, 150, true, false, true)), array(array(3, 2), array(2, 2)));

    // �������� 3
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $data = $PHPShopOrm->select(array('*'), array('type' => '="import"'), array('order' => 'id DESC'), array("limit" => "1000"));
    $exchanges_value[] = array(__('������� ����� ���������'), 'new');
    if (is_array($data)) {
        foreach ($data as $row) {
            $exchanges_value[] = array($row['name'], $row['id'], $_REQUEST['exchanges']);
            $exchanges_remove_value[] = array($row['name'], $row['id']);
        }
    } else
        $exchanges_remove_value = null;

    $Tab3 = $PHPShopGUI->setField('������� ���������', $PHPShopGUI->setSelect('exchanges', $exchanges_value, 300, false));
    $Tab3 .= $PHPShopGUI->setField('��������� ���������', $PHPShopGUI->setInputArg(array('type' => 'text', 'placeholder' => '��� ���������', 'size' => '300', 'name' => 'exchanges_new', 'class' => 'vendor_add')));

    if (is_array($exchanges_remove_value))
        $Tab3 .= $PHPShopGUI->setField('������� ���������', $PHPShopGUI->setSelect('exchanges_remove[]', $exchanges_remove_value, 300, false, false, false, false, 1, true));

    // �������� 4
    if (empty($_POST['time_limit']))
        $_POST['time_limit'] = 10;

    if (empty($_POST['line_limit']))
        $_POST['line_limit'] = 50;

    if (empty($_POST['smart']))
        $_POST['smart'] = null;

    $Tab4 = $PHPShopGUI->setField('����� �����', $PHPShopGUI->setInputText(null, 'line_limit', $_POST['line_limit'], 150), 1, '������� �� �������� ��������');
    //$Tab4 .= $PHPShopGUI->setField('��������� ��������', $PHPShopGUI->setInputText(null, 'time_limit', $_POST['time_limit'], 150, __('������')), 1, '������� �� �������� ��������');
    //$Tab4 .= $PHPShopGUI->setInput("hidden", "line_limit", $_POST['line_limit']);
    $Tab4 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setCheckbox('smart', 1, __('����� �������� ��� ���������� ������� ����������� �� ��������'), @$_POST['smart'], false, false));

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);
    $Tab2 = $PHPShopGUI->setCollapse('���������', $PHPShopGUI->setHelp(__('���� �� ���������� ����, ������� ������� � ���� "����" &rarr; "������� ����", � �� ��������') . ' <a name="import-col-name" href="#">' . __('������� ��������� ��������') . '</a> - ' . __('������������� ����� ������ <b>�� �����</b>. ���� ��� ��������� ����� �� ������ ���������� �������, �������� <b>C������������ �����</b>.') . '<div style="margin-top:10px" id="import-col-name" class="none panel panel-default"><div class="panel-body">' . $list . '</div></div>', false, false)) .
            $PHPShopGUI->setCollapse('������������� �����', $Tab2);

    $Tab3 = $PHPShopGUI->setCollapse('����������� ���������', $Tab3);
    $Tab4 = $PHPShopGUI->setCollapse('�������������', $Tab4);

    $Tab5 = $PHPShopGUI->loadLib('tab_log', $data, 'exchange/');
    if (!empty($Tab5))
        $Tab5_status = false;
    else
        $Tab5_status = true;

    $PHPShopGUI->tab_return = true;
    $PHPShopGUI->setTab(array('���������', $Tab1, true), array('������������� �����', $Tab2, true), array('����������� ���������', $Tab3, true), array('�������������', $Tab4, true), array('������� ��������', $Tab5, true, $Tab5_status));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", true, "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.exchange.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.exchange.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $help = '<p class="text-muted data-row">' . __('��� ������� ������ ����� �������') . ' <a href="?path=exchange.export"><span class="glyphicon glyphicon-share-alt"></span>' . __('������ �����') . '</a>' . __(', ������ ������ ��� ����. ����� �������� ��� �������� ������ ����������, �� ������� ���������, � �������� ����') . ' <em> ' . __('"������ ������"') . '</em></p>';

    $sidebarleft[] = array('title' => '��� ������', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './exchange/'));
    $sidebarleft[] = array('title' => '���������', 'content' => $help, 'class' => 'hidden-xs');

    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // �����
    $PHPShopGUI->Compile(2);

    return true;
}

// ��������� �������������
class sortCheck {

    var $debug = false;

    function __construct($name, $value, $category, $debug = false) {

        $this->debug = $debug;

        $this->debug('���� �������������� "' . $name . '" = "' . $value . '" � �������� � ID=' . $category);

        // �������� ����� �������������� 
        $check_name = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['name' => '="' . $name . '"']);
        if ($check_name) {

            $this->debug('���� �������������� "' . $name . '" c ID=' . $check_name['id'] . ' � CATEGORY=' . $check_name['category']);

            // �������� �������� ��������������
            $check_value = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->getOne(['*'], ['name' => '="' . $value . '"', 'category' => '="' . $check_name['id'] . '"']);
            if ($check_value) {
                $this->debug('���� �������� �������������� "' . $name . '" = "' . $value . '" c ID=' . $check_value['id']);

                // �������� ��������� ������ ��������������
                $check_category = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $category . '"']);
                $sort = unserialize($check_category['sort']);

                if (is_array($sort) and in_array($check_name['id'], $sort)) {
                    $this->debug('���� ����� �������������� "' . $name . '" = "' . $value . '" c ID=' . $check_value['id'] . ' � �������� ' . $check_category['name'] . '" � ID=' . $category);
                } else {
                    $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['id' => '=' . $check_name['category']]);
                    $this->debug('��� ����� �������������� "' . $sort_categories['name'] . '" c ID=' . $check_name['category'] . ' � �������� ' . $check_category['name'] . '" � ID=' . $category);

                    // ���������� � ��������� ������ ��������������
                    $sort[] = $check_name['id'];
                    (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->update(['sort_new' => serialize($sort)], ['id' => '=' . $category]);
                    $this->debug('����� ������������� "' . $sort_categories['name'] . '" c ID=' . $check_name['category'] . ' �������� � ������� "' . $check_category['name'] . '" � ID=' . $category);

                    $result[$check_name['id']][] = $check_value['id'];
                }
                $result[$check_name['id']][] = $check_value['id'];
            } else {
                $this->debug('��� �������� �������������� "' . $name . '" = "' . $value . '"');

                // �������� ������ �������� ��������������
                $new_value_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->insert(['name_new' => $value, 'category_new' => $check_name['id'], 'sort_seo_name_new' => str_replace("_", "-", PHPShopString::toLatin($value))]);

                $this->debug('�������� ������ �������� �������������� "' . $name . '" = "' . $value . '" c ID=' . $new_value_id);
                $result[$check_name['id']][] = $new_value_id;
            }
        } else {

            $this->debug('��� �������������� "' . $name . '"');

            // �������� ��������� ������ ��������������
            $check_category = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $category . '"']);
            $sort = unserialize($check_category['sort']);

            // � �������� ���� ��������������
            if (is_array($sort)) {

                // �������� �������� ��������������
                foreach ($sort as $val) {
                    $check_value = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['id' => '=' . $val]);
                    if (!empty($check_value['category'])) {
                        $sort_categories = $check_value['category'];
                        continue;
                    }
                }

                $this->debug('������ ����� ������������� c ID=' . $sort_categories);
            }
            // � �������� ��� ������ �������������
            else {

                // �������� ����� ������
                $sort_categories_name = __('����� ������');
                $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['name' => '="' . $sort_categories_name . '"'])['id'];

                // �������� ������ ������ �������������
                if (empty($sort_categories)) {

                    $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->insert(['name_new' => $sort_categories_name, 'category_new' => 0]);
                    $this->debug('�������� ������ ����� ������������� "' . $sort_categories_name . '" c ID=' . $sort_categories . ' ');
                }
            }

            // �������� ����� �������������� 
            if (!empty($sort_categories)) {
                $new_name_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->insert(['name_new' => $name, 'category_new' => $sort_categories]);
                $this->debug('�������� ����� �������������� "' . $name . '" c ID=' . $new_name_id . ' � ������ ������������� ID=' . $sort_categories);

                // �������� ������ �������� ��������������
                $new_value_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->insert(['name_new' => $value, 'category_new' => $new_name_id, 'sort_seo_name_new' => str_replace("_", "-", PHPShopString::toLatin($value))]);
                $this->debug('�������� ������ �������� �������������� "' . $name . '" = "' . $value . '" c ID=' . $new_value_id);

                // ���������� � ��������� ��������������
                $sort[] = $new_name_id;
                (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->update(['sort_new' => serialize($sort)], ['id' => '=' . $category]);
                $this->debug('�������������� "' . $name . '" c ID=' . $new_name_id . ' �������� � ������� "' . $check_category['name'] . '" � ID=' . $category);

                $result[$new_name_id][] = $new_value_id;
            }
        }

        $this->result = $result;
    }

    // �������
    function debug($str) {
        if ($this->debug)
            echo $str . PHP_EOL . '<br>';
    }

    // ���������
    function result() {
        return $this->result;
    }

}

// �������� �����������
function fotoDelete($where = null) {

    if (!is_array($where))
        $where = array('parent' => '=' . intval($_POST['rowID']));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 100));
    if (is_array($data)) {
        foreach ($data as $row) {
            $name = $row['name'];
            $pathinfo = pathinfo($name);
            $oldWD = getcwd();
            $dirWhereRenameeIs = $_SERVER['DOCUMENT_ROOT'] . $pathinfo['dirname'];
            $oldFilename = $pathinfo['basename'];

            @chdir($dirWhereRenameeIs);
            @unlink($oldFilename);
            $oldFilename_s = str_replace(".", "s.", $oldFilename);
            @unlink($oldFilename_s);
            $oldFilename_big = str_replace(".", "_big.", $oldFilename);
            @unlink($oldFilename_big);
            @chdir($oldWD);
        }
        $PHPShopOrm->clean();
        $result = $PHPShopOrm->delete($where);

        // �������� �������� ����������� ������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $data_main = $PHPShopOrm->getOne(array('pic_big'), array('id' => '=' . intval($row['parent'])));

        if (is_array($data_main) and $name == $data_main['pic_big']) {
            $result = $PHPShopOrm->update(array('pic_small_new' => '', 'pic_big_new' => ''), array('id' => '=' . intval($row['parent'])));
        }


        return $result;
    }
}

// ��������� �������
$PHPShopGUI->getAction();
?>