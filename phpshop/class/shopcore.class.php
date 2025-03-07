<?php

/**
 * ������������ ����� ���� ������ �������
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopClass
 */
class PHPShopShopCore extends PHPShopCore {

    /**
     * ����-�������� ��� ���������� ���� ������
     * @var string
     */
    var $no_photo = 'images/shop/no_photo.gif';

    /**
     * �������
     * @var bool
     */
    var $debug = false;

    /**
     * �����������, ������������� [true]
     * @var bool
     */
    var $cache = true;

    /**
     * �������������� ��������� ����
     * @var array
     */
    var $cache_format = array('content', 'yml_bid_array');

    /**
     * ��������� ����� � ����� �������
     * @var bool
     */
    var $grid = true;

    /**
     * ����� ������ ������� �� 1 ��������, ������������� 100-300
     * @var int
     */
    var $max_item = 100;

    /**
     * ������ ��������� ���������� ������� � �������. ��� �������������� ������� � ����� ������� ��������� ������ [false]
     * @var bool
     */
    var $memory = true;
    var $multi_cat = array();

    /**
     * ��� ������� ������ ������� [default | li | div]
     * @var string  
     */
    var $cell_type = 'default';

    /**
     * ����� �������� ������
     * @var string 
     */
    var $cell_type_class = 'product-block';

    /**
     * ������������ � ����������� ����
     */
    var $price_min = 0;
    var $price_max = 0;
    // ���������� ������
    var $previewSorts;
    var $sortCategories;
    var $warehouse;

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopValutaArray, $PHPShopPromotions;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['products'];

        // ������ �����
        $this->Valuta = $PHPShopValutaArray->getArray();

        PHPShopObj::loadClass('product');
        parent::__construct();

        // ������ ������
        $this->dengi = $this->PHPShopSystem->getParam('dengi');
        $this->currency = $this->currency();

        // ����������
        $this->PHPShopPromotions = $PHPShopPromotions;

        // ���������
        $this->parent_price_enabled = $this->PHPShopSystem->getSerilizeParam('admoption.parent_price_enabled');
        $this->user_price_activate = $this->PHPShopSystem->getSerilizeParam('admoption.user_price_activate');
        $this->sklad_status = $this->PHPShopSystem->getSerilizeParam('admoption.sklad_status');
        $this->format = intval($this->PHPShopSystem->getSerilizeParam("admoption.price_znak"));
        $this->warehouse_sum = $this->PHPShopSystem->getSerilizeParam('admoption.sklad_sum_enabled');
        $this->webp = $this->PHPShopSystem->getSerilizeParam('admoption.image_webp');

        // HTML ����� �������
        $this->setHtmlOption(__CLASS__);
    }

    /**
     * ��������� ������� ������������
     */
    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ��������� SQL ������� ��� �������
     * @param string $where �������� ������
     * @return mixed
     */
    function query_filter($where = false, $v = false) {

        if (!empty($where))
            $where .= ' and ';

        $sort = null;

        $this->set('productRriceOT', 0);
        $this->set('productRriceDO', 0);

        $v = @$_GET['v'];
        $s = intval(@$_GET['s']);
        $f = intval(@$_GET['f']);

        if ($this->PHPShopNav->isPageAll())
            $p = PHPShopSecurity::TotalClean($p, 1);

        // ���������� �� ���������������
        if (is_array($v)) {

            $sort .= " and (";
            foreach ($v as $key => $val) {
                if (PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($val)) {
                    $hash = $key . "-" . $val;
                    $sort .= " vendor REGEXP 'i" . $hash . "i' or";
                }
            }
            $sort = substr($sort, 0, strlen($sort) - 2);
            $sort .= ") ";
        }


        // ��������
        $percent = $this->PHPShopSystem->getValue('percent');

        // ����� ������
        if (!empty($_GET['gridChange']))
            switch ($_GET['gridChange']) {
                case 1:
                    $this->set('gridSetAactive', 'active');
                    break;
                case 2:
                    $this->set('gridSetBactive', 'active');
                    break;
                default: $this->set('gridSetBactive', 'active');
            }

        // ���������� �������������� �������������
        switch ($f) {
            case(1): $order_direction = "";
                $this->set('productSortNext', 2);
                $this->set('productSortImg', 1);
                $this->set('productSortT', 1);
                $this->set('fSetBactive', 'active');
                break;
            case(2): $order_direction = " desc";
                $this->set('productSortNext', 1);
                $this->set('productSortImg', 2);
                $this->set('productSortT', 2);
                $this->set('fSetAactive', 'active');
                break;
            default: $order_direction = "";
                $this->set('productSortNext', 2);
                $this->set('productSortImg', 1);
                $this->set('productSortT', 1);
                $this->set('fSetBactive', 'active');
                break;
        }
        switch ($s) {
            case(1): $order = array('order' => 'name' . $order_direction);
                $this->set('productSortA', 'sortActiv');
                $this->set('sSetBactive', 'active');
                break;
            case(2): $order = array('order' => 'price' . $order_direction);
                $this->set('productSortB', 'sortActiv');
                $this->set('sSetAactive', 'active');
                break;
            default:
                $order = array('order' => 'num' . $order_direction);
                $this->set('productSortC', 'sortActiv');
                $this->set('sSetCactive', 'active');
                break;
        }

        // ����������� ������ ������� ���������� � ������
        foreach ($order as $key => $val)
            $string = $key . ' by ' . $val;

        // ��� ��������
        if ($this->PHPShopNav->isPageAll()) {
            $sql = "select * from " . $this->getValue('base.products') . " where (" . $where . " enabled='1' and parent_enabled='0') " . $sort . " " . $string . ' limit ' . $this->max_item;
        }

        // ����� �� ����
        elseif (isset($_POST['priceSearch']) or ! empty($sort)) {

            if (!empty($_POST['priceOT']) or ! empty($_POST['priceDO'])) {
                $priceOT = intval($_POST['priceOT']);
                $priceDO = intval($_POST['priceDO']);

                $this->set('productRriceOT', $priceOT);
                $this->set('productRriceDO', $priceDO);

                // �������������
                if ($priceDO == 0)
                    $priceDO = 1000000000;

                if (empty($priceOT))
                    $priceOT = 0;

                // ���� � ������ ��������� ������
                $priceOT /= $this->currency('kurs');
                $priceDO /= $this->currency('kurs');

                // ������� ������ �� ����
                $price_sort = "and price >= " . ($priceOT / (100 + $percent) * 100) . " AND price <= " . ($priceDO / (100 + $percent) * 100);
            }

            $sql = "select * from " . $this->getValue('base.products') . " where " . $where . " enabled='1' and parent_enabled='0' " . $price_sort . " " . $sort . $string . ' limit 0,' . $this->max_item;
        }
        else {
            // ���������� ������ ��������� ���������� ����������
            return $order;
        }

        // ���������� SQL ������� ������
        return $sql;
    }

    /**
     * ������
     * @param string $name ��� ���� � ������� ����� ��� ������
     * @return string
     */
    function currency($name = 'code') {

        if (isset($_SESSION['valuta']))
            $currency = $_SESSION['valuta'];
        else
            $currency = $this->dengi;

        $row = $this->select(array('*'), array('id' => '=' . intval($currency)), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.currency'), 'cache' => 'true'));

        if ($name == 'code' and ( $row['iso'] == 'RUR' or $row['iso'] == "RUB"))
            return 'p';

        return $row[$name];
    }

    /**
     * ������� �� ��
     * @param array $select ������ ������� �������
     * @param array $where ������ ������� �������
     * @param array $order ������ ������� �������
     * @param array $option ������ ������� �������
     * @param string $function_name ��� ������� ��� �������
     * @param array $from ������ �����
     * @param array $mysql_error ���������� ������
     * @return array
     */
    function select($select, $where, $order = false, $option = array('limit' => 1), $function_name = false, $from = false, $mysql_error = true) {

        if (is_array($from)) {
            $base = @$from['base'];
            $cache = @$from['cache'];
            if (!empty($from['cache_format']))
                $cache_format = $from['cache_format'];
        } else {
            $base = $this->objBase;
            $cache = $this->cache;
            $cache_format = $this->cache_format;
        }

        $PHPShopOrm = new PHPShopOrm($base);
        $PHPShopOrm->objBase = $base;
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $cache;
        $PHPShopOrm->mysql_error = $mysql_error;

        if (!empty($cache_format))
            $PHPShopOrm->cache_format = $cache_format;

        $result = $PHPShopOrm->select($select, $where, $order, $option, __CLASS__, $function_name);

        return $result;
    }

    /**
     * ��������� ������
     * @param array $row ������ ������ ������
     * @param bool $newprice ������ ����
     * @param bool $promo �������� ����������
     * @return float
     */
    function price($row, $newprice = false, $promo = true) {

        // �������� ������, ��������� � ������ ������� ������ ��� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, $newprice);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        // ���� ���� ����� ����
        if (empty($newprice)) {
            $price = $row['price'];
        } else {
            $price = $row['price_n'];
            $row['price2'] = $row['price3'] = $row['price4'] = $row['price5'] = null;
        }

        // ����������
        if ($promo) {
            $promotions = $this->PHPShopPromotions->getPrice($row);
            if (is_array($promotions)) {

                if (empty($newprice))
                    $price = $promotions['price'];
                else
                    $price = $promotions['price_n'];
            }
        }

        return PHPShopProductFunction::GetPriceValuta($row['id'], array($price, $row['price2'], $row['price3'], $row['price4'], $row['price5']), $row['baseinputvaluta']);
    }

    /**
     * ��������� ����������
     * @param int $count ���������� ������� �� ��������
     * @param string $sql SQL ������ � ���� ������ ��� ������� ������� (���������� AND � OR � ����� �������, ������� �� WHERE)
     */
    function setPaginator($count = null, $sql = null) {

        // �������� ������ � ������ �������
        if ($this->setHook(__CLASS__, __FUNCTION__, array('count' => $count, 'sql' => $sql), 'START'))
            return true;

        // ���������� ���������
        $dir = $this->getValue('dir.dir');
        if ($this->PHPShopNav->objNav['path'] != 'shop')
            $dir = null;

        // ��������� ������� �������� ��������� � ����� �������
        // ���� �����������, �� ���������� ������� �� lib
        $type = $this->memory_get(__CLASS__ . '.' . __FUNCTION__);
        if (!$type) {
            if (!PHPShopParser::checkFile("paginator/paginator_one_link.tpl")) {
                $type = "lib";
            } else {
                $type = "templates";
            }

            $this->memory_set(__CLASS__ . '.' . __FUNCTION__, $type);
        }

        if ($type == "lib") {
            $template_location = "./phpshop/lib/templates/";
            $template_location_bool = true;
        } else
            $template_location = $template_location_bool = null;

        // ���-�� ������
        $this->count = $count;
        $SQL = null;

        // ������� �� ���������� WHERE
        $nWhere = 1;
        if (is_array($this->where)) {
            foreach ($this->where as $pole => $value) {
                $SQL .= $pole . $value;
                if ($nWhere < count($this->where))
                    $SQL .= $this->PHPShopOrm->Option['where'];
                $nWhere++;
            }
        } else
            $SQL = $sql;

        // ��������� ����������� ��������� /filters/
        if (strpos($GLOBALS['SysValue']['nav']['truepath'], '/filters/') !== false) {
            $filters = '/filters/' . preg_replace('#^.*/filters/(.*)$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
            $this->set("page_filters", $filters);
        } else
            $filters = null;

        $sort = $filters . '?';

        // �������
        if (!empty($_GET['v']) and is_array($_GET['v']))
            foreach ($_GET['v'] as $key => $val) {

                if (is_array($val)) {

                    foreach ($val as $v)
                        $sort .= 'v[' . $key . '][]=' . $v . '&';
                } else if (is_numeric($key) and is_numeric($val))
                    $sort .= 'v[' . $key . ']=' . $val . '&';
            }

        // ����������
        if (!empty($_GET['s']) and is_numeric($_GET['s']))
            $sort .= 's=' . $_GET['s'] . '&';
        if (!empty($_GET['f']) and is_numeric($_GET['f']))
            $sort .= 'f=' . $_GET['f'] . '&';

        // ������
        if (!empty($_GET['w']) and is_numeric($_GET['w']))
            $sort .= 'w=' . $_GET['w'] . '&';

        $sort = substr($sort, 0, strlen($sort) - 1);

        // ����� �������
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $result = $this->PHPShopOrm->query("select COUNT('id') as count, (price_n - price) as discount from " . $this->objBase . ' where ' . $SQL);

        if ($result)
            $row = mysqli_fetch_array($result);
        $this->num_page = $row['count'];

        $i = 1;
        $navigat = $nav = null;

        // ���-�� ������� � ���������
        $num = @ceil($this->num_page / $this->num_row);
        $this->max_page = $num;

        // 404 ������ ��� ��������� ���������
        if (((int) $this->page > 1) && $this->page > $this->num_page) {
            return $this->setError404();
        }

        if ($num > 1) {
            if ($this->page >= $num) {
                $p_to = $i - 1;
                $p_do = $this->page - 1;
            } else {
                $p_to = $this->page + 1;
                $p_do = 1;
            }

            $this->set("paginPageCount", $num);

            while ($i <= $num) {
                if ($i > 1) {
                    $p_start = $this->num_row * ($i - 1) + 1;
                    $p_end = $p_start + $this->num_row - 1;
                } else {
                    $p_start = $i;
                    $p_end = $this->num_row;
                }

                $this->set("paginPageRangeStart", $p_start);
                $this->set("paginPageRangeEnd", $p_end);
                $this->set("paginPageNumber", $i);
                if ($i != $this->page) {
                    if ($i == 1) {
                        $this->set("paginLink", $dir . substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
                        $this->set("catalogFirstPage", $dir . substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html');
                        $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $this->set("paginLink", $dir . $this->objPath . $i . '.html' . $sort);
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                        } else if ($i - ($this->page + $this->nav_len) < 3 and ( ($this->page - $this->nav_len) - $i) < 3) {
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_more.tpl", $template_location_bool);
                        }
                    }
                } else
                    $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_selected.tpl", $template_location_bool);

                $i++;
            }

            $this->set("pageNow", $this->getValue('lang.page_now'));
            $this->set("navBack", $this->lang('nav_back'));
            $this->set("navNext", $this->lang('nav_forw'));
            $this->set("navigation", $navigat);


            // ������� ����� ������ �������� CID_X_1.html
            if ($p_do == 1)
                $this->set("previousLink", $dir . substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("previousLink", $dir . $this->objPath . ($p_do) . '.html' . $sort);


            // ������� ����� ������ �������� CID_X_0.html
            if ($p_to == 0)
                $this->set("nextLink", $dir . substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("nextLink", $dir . $this->objPath . ($p_to) . '.html' . $sort);

            // ��������� ���������� �������������
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('productPageNav', $nav);
        }

        // �������� ������ � ����� �������
        $this->setHook(__CLASS__, __FUNCTION__, $nav, 'END');
    }

    /**
     * �������� ���� �������� ������ Multibase
     * @return string 
     */
    function queryMultibase() {
        global $queryMultibase;

        // ����������
        if (defined("HostID") or defined("HostMain")) {

            // ������
            if (!empty($queryMultibase))
                return $queryMultibase;

            $multi_cat = array();
            $multi_dop_cat = null;

            // �� �������� ������� ��������
            $where['skin_enabled '] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1000), __CLASS__, __FUNCTION__);
            if (is_array($data)) {
                foreach ($data as $row) {
                    $multi_cat[] = $row['id'];
                    $multi_dop_cat .= " or dop_cat REGEXP '#" . $row['id'] . "#'";
                }
            }

            $queryMultibase = $multi_select = ' and ( category IN (' . @implode(',', $multi_cat) . ')' . $multi_dop_cat . ')';

            return $multi_select;
        }
    }

    /**
     * �������� ����� �������� ������ Multibase
     * @param int $category ID ��������
     * @param string $dop_cat #ID# �������������� ���������
     * @return boolean 
     */
    function errorMultibase($category, $dop_cat = null) {

        if (defined("HostID") or defined("HostMain")) {

            if (empty($this->multi_cat)) {

                // ���������� ��������
                if (strstr($dop_cat, "#")) {

                    $dop_cat_array = explode("#", $dop_cat);

                    if (is_array($dop_cat_array))
                        foreach ($dop_cat_array as $v)
                            if (!empty($v))
                                $dop_cat_array_true[] = intval($v);

                    if (is_array($dop_cat_array_true))
                        $where['id'] = ' IN ("' . $category . '", "' . @implode('","', $dop_cat_array_true) . '")';
                }

                // �� �������� ������� ��������
                $where['skin_enabled'] = "!='1'";

                if (defined("HostID"))
                    $where['servers'] = " REGEXP 'i" . HostID . "i'";
                elseif (defined("HostMain"))
                    $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                $PHPShopOrm->debug = $this->debug;
                $PHPShopOrm->cache = false;
                $data = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 10000));

                if (is_array($data)) {
                    foreach ($data as $row) {
                        $this->multi_cat[] = $row['id'];
                    }
                }
            }

            if (in_array($category, $this->multi_cat)) {
                return false;
            }

            if (is_array($dop_cat_array_true)) {

                // ���������� ��������
                if (count($dop_cat_array_true) > 0) {

                    // ���������
                    $this->category = $dop_cat_array_true[0];
                    $this->PHPShopCategory = new PHPShopCategory($this->category);
                    $this->category_name = $this->PHPShopCategory->getName();

                    if (count($this->multi_cat) == 0)
                        return true;
                } else if (!in_array($category, $this->multi_cat))
                    return true;
            } else
                return true;
        }
    }

    /**
     * �������� �������������� �������
     */
    function getStore() {

        if (is_array($this->warehouse)) {
            return;
        }

        $this->warehouse = [];

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

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
                    $this->warehouse[$row['id']] = $row['description'];
                else
                    $this->warehouse[$row['id']] = $row['name'];
            }
    }

    /**
     * �������� �������������� ������ ������ �� ������
     * @param array $row ������ ������ �� ������
     */
    function checkStore($row = array()) {

        // ������
        $this->set('productValutaName', $this->currency);

        // ������� ���������
        if (empty($row['ed_izm']))
            $row['ed_izm'] = $this->lang('product_on_sklad_i');
        $this->set('productEdIzm', $row['ed_izm']);

        // ����������
        $promotions = $this->PHPShopPromotions->getPrice($row);
        if (is_array($promotions)) {
            $priceColumn = $this->PHPShopSystem->getPriceColumn();
            $row[$priceColumn] = $promotions['price'];
            $row['price_n'] = $promotions['price_n'];
            $row['promo_label'] = $promotions['label'];
        }

        // ���������� ��������� ������
        if ($this->PHPShopSystem->isDisplayWarehouse()) {

            // �������� �������������� �������
            $this->getStore($row);

            // ����� �����
            $this->set('productWarehouse', $row['items']);

            // �������������� ������
            if (is_array($this->warehouse) and count($this->warehouse) > 0) {
                $this->set('productSklad', '');

                // ����� �����
                if ($this->warehouse_sum == 1)
                    $this->set('productSklad', PHPShopText::div(__('����� �����') . ": " . $row['items'] . " " . $row['ed_izm']), true);

                foreach ($this->warehouse as $store_id => $store_name) {
                    if (isset($row['items' . $store_id])) {
                        $this->set('productSklad', PHPShopText::div($store_name . ": " . $row['items' . $store_id] . " " . $row['ed_izm']), true);
                    }
                }
            } else
                $this->set('productSklad', $this->lang('product_on_sklad') . " " . $row['items'] . " " . $row['ed_izm']);
        } else
            $this->set('productSklad', '');

        // ����
        $price = $this->price($row, false, false);

        // ������ ����������� � ������������ ����
        if ($price > $this->price_max)
            $this->price_max = $price;

        if (empty($this->price_min))
            $this->price_min = $price;

        if ($price < $this->price_min)
            $this->price_min = $price;

        // ������
        $bonus = $price * $this->PHPShopSystem->getSerilizeParam('admoption.bonus') / 100;
        if (!empty($bonus))
            $this->set('productBonus', $bonus);

        // ��������������
        $this->set('productSchemaPrice', $price);
        $price = number_format($price, $this->format, '.', ' ');

        // ���� ����� �� ������
        if (empty($row['sklad'])) {
            $this->set('Notice', '');
            $this->set('ComStartCart', '');
            $this->set('ComEndCart', '');
            $this->set('ComStartNotice', PHPShopText::comment('<'));
            $this->set('ComEndNotice', PHPShopText::comment('>'));
            $this->set('elementCartHide', null);
            $this->set('elementNoticeHide', 'hide hidden d-none');
            $this->set('productOutStock', null);
            $this->set('productPriceRub', null);
        }

        // ����� ��� �����
        else {
            $this->set('productPriceRub', $this->lang('sklad_mesage'));
            $this->set('productOutStock', $this->lang('sklad_mesage'));
            $this->set('ComStartNotice', '');
            $this->set('ComEndNotice', '');
            $this->set('elementCartHide', 'hide hidden d-none');
            $this->set('ComStartCart', PHPShopText::comment('<'));
            $this->set('ComEndCart', PHPShopText::comment('>'));
            $this->set('productNotice', $this->lang('product_notice'));
            $this->set('elementNoticeHide', null);
            $this->set('elementCartOptionHide', 'hide hidden d-none');
            $this->set('productSklad', '');
            $this->set('productPriceOld', '');
            $this->set('productLabelDiscount', '');
        }

        // ���� ��� ����� ����
        if (empty($row['price_n'])) {

            $this->set('productPrice', $price);
            $this->set('productLabelDiscount', $this->lang('specprod'));
            $this->set('productPriceOld', null);
        }

        // ���� ���� ����� ����
        else {
            $productPrice = $price;
            $productPriceNew = $this->price($row, true, false);
            $this->set('productPrice', $productPrice);
            $this->set('productPriceOld', PHPShopText::strike($productPriceNew . " " . $this->currency, $this->format));

            $priceColumn = $this->PHPShopSystem->getPriceColumn();
            if (empty($row[$priceColumn])) {
                $priceColumn = 'price';
            }

            // ����� % ������
            $this->set('productLabelDiscount', '-' . ceil(($row['price_n'] - $row[$priceColumn]) * 100 / $row['price_n']) . '%');
        }

        // �������� �� ������� ���� 
        if (empty($row['price'])) {
            $this->set('ComStartCart', PHPShopText::comment('<'));
            $this->set('ComEndCart', PHPShopText::comment('>'));


            $this->set('elementCartHide', 'hide hidden d-none');

            $this->set('productPrice', null);
            $this->set('productPriceRub', null);
            $this->set('productValutaName', null);
            $this->set('productPriceOld', null);
        }

        // �������� �������
        if (!empty($row['parent'])) {
            $this->set('parentLangFrom', __('��'));
            $this->set('elementCartHide', 'hide hidden d-none');
            $this->set('ComStartCart', PHPShopText::comment('<'));
            $this->set('ComEndCart', PHPShopText::comment('>'));
            $this->set('productSale', $this->lang('product_select'));

            if (empty($row['sklad']))
                $this->set('elementCartOptionHide', null);
        }
        else {
            $this->set('elementCartOptionHide', 'hide hidden d-none');
            $this->set('parentLangFrom', null);
            $this->set('productSale', $this->lang('product_sale'));
        }

        // ���� ���� ���������� ������ ����� �����������
        if ($this->user_price_activate == 1 and empty($_SESSION['UsersId'])) {
            $this->set('ComStartCart', PHPShopText::comment('<'));
            $this->set('ComEndCart', PHPShopText::comment('>'));
            $this->set('productPrice', null);
            $this->set('productPriceRub', null);
            $this->set('productValutaName', null);
            $this->set('elementCartOptionHide', 'hide hidden d-none');
            $this->set('elementCartHide', 'hide hidden d-none');
            $this->set('parentLangFrom', null);
            $this->set('productPriceOld', null);
        }

        // ���������� ������
        if (!empty($row['promo_label'])) {
            $this->set('promoLabel', $row['promo_label']);
            $this->set('promotionsIcon', ParseTemplateReturn('product/promoIcon.tpl'));
        } else
            $this->set('promotionsIcon', '');

        // �������� ������, ��������� � ������ ������� ������ ��� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $row);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }
    }

    /**
     * ����� ����� � ��������
     * @return string
     */
    function setCell($d1, $d2 = null, $d3 = null, $d4 = null, $d5 = null, $d6 = null, $d7 = null) {

        // ���������� ����������� �����
        if ($this->grid)
            $this->grid_style = 'class="setka"';
        else
            $this->grid_style = '';

        $this->separator = null;

        $Arg = func_get_args();
        $item = 1;

        foreach ($Arg as $key => $value)
            if ($key < $this->cell)
                $args[] = $value;

        switch ($this->cell_type) {

            // ������
            case 'li':
                if (is_array($args))
                    foreach ($args as $key => $val) {
                        $tr .= '<li class="' . $this->cell_type_class . '">' . $val . '</li>';
                        $item++;
                    }
                break;

            // �����
            case 'div':
                if (is_array($args))
                    foreach ($args as $key => $val) {
                        $tr .= '<div class="' . $this->cell_type_class . '">' . $val . '</div>';
                        $item++;
                    }
                break;

            // Bootstrap
            case 'bootstrap':
                $tr = '<div class="row">';
                if (is_array($args))
                    foreach ($args as $key => $val) {
                        $tr .= $val;
                        $item++;
                    }
                $tr .= '</div>';
                break;

            // Flex
            case 'flex':
                $tr = null;
                if (is_array($args))
                    foreach ($args as $key => $val) {
                        $tr .= $val;
                        $item++;
                    }
                break;
        }


        return $tr;
    }

    /**
     * ������ ���-�� �������� ������ � ������ ���������� ��������� ������������� ����� ������
     * @param Int $category �� ������� ���������
     * @param Int $num_row  ���-�� ������� � ��������� �� ���������
     */
    function calculateCell($category, $num_row) {

        if (!empty($_REQUEST['gridChange'])) {
            if ($_REQUEST['gridChange'] == 2 AND $num_row > 1) {
                $_SESSION['gridChange'][$category] = $num_row;
                $this->set("gridChange2", "btn-primary");
                return $num_row;
            } elseif ($_REQUEST['gridChange'] == 2) {
                $_SESSION['gridChange'][$category] = 2;
                $this->set("gridChange2", "btn-primary");
                return 2;
            } else {

                $_SESSION['gridChange'][$category] = 1;
                $this->set("gridChange", "btn-primary");
                return 1;
            }
        } elseif (isset($_SESSION['gridChange'][$category])) {
            if ($_SESSION['gridChange'][$category] > 1)
                $this->set("gridChange2", "btn-primary");
            else
                $this->set("gridChange", "btn-primary");
            return $_SESSION['gridChange'][$category];
        }
        if ($num_row > 1)
            $this->set("gridChange2", "btn-primary");
        else
            $this->set("gridChange", "btn-primary");
        return $num_row;
    }

    /**
     * ����� �������� �������
     * @param array $row ������ ��������
     */
    function parent($row) {

    // �������� ������ � ������ �������
    if($this->setHook(__CLASS__, __FUNCTION__, $row, 'START'))
        return true;

    $this->select_value = array();
    $row['parent'] = PHPShopSecurity::CleanOut($row['parent']);

    if (!empty($row['parent'])) {
        $parent = explode(",", $row['parent']);

        // ������� ���������� � ������� �������� ������
        $this->set('ComStartCart', '<!--');
        $this->set('ComEndCart', '-->');

        // �������� ������ �������
        if (is_array($parent))
            foreach ($parent as $value) {
                if (PHPShopProductFunction::true_parent($value))
                    $Product[$value] = $this->select(array('*'), array('uid' => '="' . $value . '"', 'enabled' => "='1'", 'sklad' => "!='1'"), false, false, __FUNCTION__);
                else
                    $Product[intval($value)] = $this->select(array('*'), array('id' => '=' . intval($value), 'enabled' => "='1'", 'sklad' => "!='1'"), false, false, __FUNCTION__);
            }

        // ���� �������� ������
        if (!empty($row['price']) and empty($row['priceSklad']) and ( !empty($row['items']) or ( empty($row['items']) and $this->sklad_status == 1))) {
            $this->select_value[] = array($row['name'] . " -  (" . $this->price($row) . "
                    " . $this->currency . ')', $row['id'], false);
        } else {
            $this->set('ComStartNotice', PHPShopText::comment('<'));
            $this->set('ComEndNotice', PHPShopText::comment('>'));
        }

        // ���������� ������ �������
        if (is_array($Product))
            foreach ($Product as $p) {
                if (!empty($p)) {

                    // ���� ����� �� ������
                    if (empty($p['priceSklad']) and ( !empty($p['items']) or ( empty($p['items']) and $this->sklad_status == 1))) {
                        $price = $this->price($p);
                        $this->select_value[] = array($p['name'] . ' -  (' . $price . ' ' . $this->currency . ')', $p['id'], false);
                    }
                }
            }

        if (count($this->select_value) > 0) {
            $this->set('parentList', PHPShopText::select('parentId', $this->select_value, "; max-width:300px;"));
            $this->set('productParentList', ParseTemplateReturn("product/product_odnotip_product_parent.tpl"));
        }

        $this->set('productPrice', '');
        $this->set('productPriceRub', '');
        $this->set('productValutaName', '');

        // �������� ������ � ����� �������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }
}

/**
 * ��������� ����� �������
 * @param array $dataArray ������ ������
 * @param int $cell ������ [1-5]
 * @return string
 */
function product_grid($dataArray, $cell = 2, $template = false) {
    global $_classPath;

    if (empty($cell))
        $cell = 2;
    $this->cell = $cell;
    $this->setka_footer = true;

    $table = null;
    $j = 1;
    $item = 1;
    $lastmodified = 0;

    // �����������
    $this->set('productSale', $this->lang('product_sale'));
    $this->set('productSaleReady', $this->lang('productSaleReady'));
    $this->set('productInfo', $this->lang('product_info'));
    $this->set('productPriceMoney', $this->dengi);
    $this->set('catalog', $this->lang('catalog'));
    if ($this->PHPShopNav->getPage() > 0)
        $this->set('productPageThis', $this->PHPShopNav->getPage());
    else
        $this->set('productPageThis', 1);

    $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = null;
    if (is_array($dataArray)) {
        $total = count($dataArray);

        // �������� ����������� �����
        if ($total < $cell)
            $this->grid = false;

        foreach ($dataArray as $row) {

            // ����� ������
            $this->checkStore($row);

            // ��������
            $this->set('productName', $row['name']);
            $this->set('productNameClean', str_replace(['"', "'"], '', strip_tags($row['name'])));

            // �������
            $this->set('productArt', $row['uid']);

            // ������� ��������
            $this->set('productDes', Parser($row['description']));

            // ���
            $this->set('productWeight', $row['weight']);

            // ������������ ���� ���������
            if ($row['datas'] > $lastmodified)
                $lastmodified = $row['datas'];

            // ��������� webp � iOS
            $row['pic_small'] = $this->setImage($row['pic_small']);

            // ��������� ��������
            $this->set('productImg', $row['pic_small']);

            // ������ ��������, ��������
            if (empty($row['pic_small']))
                $this->set('productImg', $this->no_photo);

            // ������� ��������
            $this->set('productImgBigFoto', $row['pic_big']);

            // �� ������
            $this->set('productUid', $row['id']);

            $this->set('previewSorts', $this->getPreviewSorts($dataArray, $row));

            $this->set('productLink', $row['link']);

            // ����������� ������� ������ ������� ������ ������ �� ������� �������������
            $this->doLoadFunction(__CLASS__, 'comment_rate', array("row" => $row, "type" => "CID"), 'shop');

            // ����� ������
            //$this->option_select($row);
            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $row);

            if (empty($template))
                $template = $this->getValue('templates.main_product_forma_' . $this->cell);

            // ���������� ������ ������ ������
            $dis = ParseTemplateReturn($template);


            // ������� ��������� ����������� � �����
            if ($item == $total)
                $this->setka_footer = false;

            $cell_name = 'd' . $j;
            $$cell_name = $dis;

            if ($j == $this->cell) {
                $table .= $this->setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = null;
                $j = 0;
            } elseif ($item == $total) {
                $table .= $this->setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
            }

            $j++;
            $item++;
        }
    }

    $this->lastmodified = $lastmodified;
    return $table;
}

/**
 * ��������� webp
 * @param string $image ��� �����
 * @return string
 */
function setImage($image) {
    global $_classPath;

    if (!empty($image)) {

        // �������������� webp -> jpg ��� iOS < 14
        if (PHPShopSecurity::getExt($image) == 'webp') {
            if (defined('isMobil') and defined('isIOS')) {

                if (!class_exists('PHPThumb'))
                    include_once($_classPath . 'lib/thumb/phpthumb.php');

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                    $thumb->setFormat('STRING');
                    $image = 'data:image/jpg;base64, ' . base64_encode($thumb->getImageAsString('webp'));
                }
            }
        }
        // �������������� � webp
        elseif ($this->webp) {

            if (!class_exists('PHPThumb'))
                include_once($_classPath . 'lib/thumb/phpthumb.php');

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                $thumb->setFormat('WEBP');
                $image = 'data:image/webp;base64, ' . base64_encode($thumb->getImageAsString(PHPShopSecurity::getExt($image)));
            }
        }
    }

    return $image;
}

public function getPreviewSorts($products, $currentProduct) {

    if (is_null($this->sortCategories)) {
        $sortCategoryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $this->sortCategories = $sortCategoryOrm->getList(['id', 'name'], ['show_preview' => '="1"'], ['order' => 'num, name']);
    }

    if (count($this->sortCategories) === 0) {
        return null;
    }

    // ����������� ��� ����� �������� ����� ������ �� ������ ��������
    if (is_null($this->previewSorts)) {
        $sortValueIds = array();
        foreach ($products as $product) {
            $vendorArray = unserialize($product['vendor_array']);
            if (is_array($vendorArray)) {
                foreach (array_values($vendorArray) as $sortValues) {
                    foreach ($sortValues as $sortValue) {
                        $sortValueIds[] = (int) $sortValue;
                    }
                }
            }
        }

        if (count($sortValueIds) === 0) {
            return null;
        }

        $sortOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
        $sorts = $sortOrm->getList(array('id', 'name'), array('`id` IN ' => sprintf('(%s)', implode(',', $sortValueIds))));

        foreach ($sorts as $sort) {
            $this->previewSorts[$sort['id']] = $sort['name'];
        }
    }

    $vendorArray = unserialize($currentProduct['vendor_array']);
    $html = '';
    foreach ($this->sortCategories as $sortCategory) {
        if (isset($vendorArray[$sortCategory['id']])) {
            $titles = array();
            foreach ($vendorArray[$sortCategory['id']] as $value) {
                if (isset($this->previewSorts[(int) $value])) {
                    $titles[(int) $value] = $this->previewSorts[(int) $value];
                }
            }
            $this->set('previewSortTitle', $sortCategory['name']);
            $this->set('previewSortValue', implode(', ', $titles));
            $html .= ParseTemplateReturn("product/preview_sort_one.tpl");
        }
    }

    $this->set('previewSorts', $html);

    return ParseTemplateReturn("product/preview_sorts.tpl");
}

}
