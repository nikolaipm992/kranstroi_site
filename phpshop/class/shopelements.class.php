<?php

/**
 * ����� �������� ��������� �������
 * ������� ������������� ��������� � ����� phpshop/inc/
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopClass
 */
class PHPShopProductElements extends PHPShopElements {

    /**
     * @var bool �����������
     */
    var $cache = false;
    var $template_debug = true;
    var $price_max = null;

    /**
     * @var array ������ ��������� ����
     */
    var $cache_format = array('content');

    /**
     * @var bool ����������� ����� ��������
     */
    var $grid = false;

    /**
     * @var int ���-�� ������ �� ��������, ���� �� ������ ��������� �����������.
     */
    var $num_row = 9;

    /**
     * @var bool ����������� ���������� ���������� ������� � �������
     * ��� �������������� ������� � ����� ������� ��������� ������ [false]
     */
    var $memory = false;

    /**
     * ��� ����������� ��������
     * @var string 
     */
    var $no_photo = 'images/shop/no_photo.gif';
    var $total = 0;
    var $product_grid;
    var $previewSorts;

    /**
     * ��� ������� ������ ������� [default | li | div]
     * @var string  
     */
    var $cell_type = 'default';

    /**
     * ����� �������� ������
     * @var string 
     */
    var $cell_type_class = 'product-element-block';
    var $warehouse;

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopPromotions;

        $this->objBase = $GLOBALS['SysValue']['base']['products'];

        // ���������� ��������� �������
        PHPShopObj::loadClass('product');
        parent::__construct();

        // ������ ������
        $this->dengi = $this->PHPShopSystem->getParam('dengi');
        $this->currency = $this->currency();

        // ����������
        $this->PHPShopPromotions = $PHPShopPromotions;

        // ���������
        $this->user_price_activate = $this->PHPShopSystem->getSerilizeParam('admoption.user_price_activate');
        $this->format = intval($this->PHPShopSystem->getSerilizeParam("admoption.price_znak"));
        $this->warehouse_sum = $this->PHPShopSystem->getSerilizeParam('admoption.sklad_sum_enabled');

        // HTML ����� �������
        $this->setHtmlOption(__CLASS__);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ������
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
     * ����� ���������
     */
    function seamply() {

        // ���������� ����� ��� ������ ������
        $cell = 2;

        // ���-�� ������� �� ��������
        $limit = 4;

        $this->dataArray = $this->select(array('*'), array('enabled' => "='1'"), array('order' => 'RAND()'), array('limit' => $limit), __FUNCTION__);

        // ��������� � ������ ������ � ��������
        $this->product_grid($this->dataArray, $cell);

        // �������� � ���������� ������� � ��������
        $this->compile();
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
            $where['skin_enabled'] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 10000), __CLASS__, __FUNCTION__);
            if (is_array($data)) {
                foreach ($data as $row) {
                    $multi_cat[] = $row['id'];
                    $multi_dop_cat .= " or dop_cat REGEXP '#" . $row['id'] . "#'";
                }
            }

            if (count($multi_cat) > 0)
                $queryMultibase = $multi_select = ' and ( category IN (' . @implode(',', $multi_cat) . ') ' . $multi_dop_cat . ')';

            return $multi_select;
        }
    }

    /**
     * ������ ���������� ������ �������
     * @param int $limit ���-�� ������� ��� ������
     * @return array
     */
    function setramdom($limit) {

        // ���� �� ��������� � ����
        if (empty($_SESSION['max_item'])) {
            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $this->debug;
            $PHPShopOrm->cache = false;
            $PHPShopOrm->sql = 'SELECT MAX(id) as max_item FROM ' . $GLOBALS['SysValue']['base']['products'];
            $data = $PHPShopOrm->select();

            if (is_array($data[0]))
                $this->max_item = $data[0]['max_item'];
            else
                $this->max_item = 0;

            // ��������� � ��� ����� ���-�� �������
            $_SESSION['max_item'] = $this->max_item;
        } else
            $this->max_item = $_SESSION['max_item'];


        $limit_start = rand(1, (int) $this->max_item / rand(1, 7));
        return ' BETWEEN ' . $limit_start . ' and ' . round($limit_start + $limit + (int) $this->max_item / 3);
    }

    /**
     * ������� �� ��
     */
    function select($select, $where, $order = false, $option = array('limit' => 1), $function_name = false, $from = false) {

        $cache_format = null;

        if (is_array($from)) {
            $base = $from['base'];
            $cache = $from['cache'];

            if (!empty($from['cache_format']))
                $cache_format = $from['cache_format'];
        }
        else {
            $base = $this->objBase;
            $cache = $this->cache;
            $cache_format = $this->cache_format;
        }

        $PHPShopOrm = new PHPShopOrm($base);
        $PHPShopOrm->objBase = $base;
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $cache;
        $PHPShopOrm->cache_format = $cache_format;
        $result = $PHPShopOrm->select($select, $where, $order, $option, __CLASS__, $function_name);

        return $result;
    }

    /**
     * ���� ������ �� ������� � �������
     * @return string
     */
    function compile() {

        if ($this->cell_type == 'default' or $this->cell_type == 'table')
            $table = '<table cellpadding="0" cellspacing="0" border="0">' . $this->product_grid . '</table>';
        else
            $table = $this->product_grid;

        $this->product_grid = null;
        return $table;
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
     * ��������� ������
     * @param array $row ������ ������ ������
     * @param bool $newpric ���������� ����
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

        // ���� ������
        $sklad_status = $this->PHPShopSystem->getSerilizeParam('admoption.sklad_status');

        // ������� ���������� � ������� �������� ������
        $this->set('ComStartCart', '<!--');
        $this->set('ComEndCart', '-->');

        // �������� ������ �������
        if (is_array($parent))
            foreach ($parent as $value) {
                if (PHPShopProductFunction::true_parent($value))
                    $Product[$value] = $this->select(array('*'), array('uid' => '="' . $value . '"', 'enabled' => "='1'", 'sklad' => "!='1'"), false, false, __FUNCTION__);
                else
                    $Product[intval($value)] = $this->select(array('*'), array('id' => '=' . intval($value), 'enabled' => "='1'"), false, false, __FUNCTION__);
            }

        // ���� �������� ������
        if (!empty($row['price']) and empty($row['priceSklad']) and ( !empty($row['items']) or ( empty($row['items']) and $sklad_status == 1))) {
            $this->select_value[] = array($row['name'] . " -  (" . $this->price($row) . "
                    " . $this->get('productValutaName') . ')', $row['id'], false);
        } else {
            $this->set('ComStartNotice', PHPShopText::comment('<'));
            $this->set('ComEndNotice', PHPShopText::comment('>'));
        }

        // ���������� ������ �������
        if (is_array($Product))
            foreach ($Product as $p) {
                if (!empty($p)) {

                    // ���� ����� �� ������
                    if (empty($p['priceSklad']) and ( !empty($p['items']) or ( empty($p['items']) and $sklad_status == 1))) {
                        $price = $this->price($p);
                        $this->select_value[] = array($p['name'] . ' -  (' . $price . ' ' . $this->get('productValutaName') . ')', $p['id'], false);
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
 * @param int $cell ������ ����� [1-5]
 * @param string $template ���� �������
 * @param bool $line ���������� ����� �����������
 * @param bool $mod ������ ��� ������
 * @return string
 */
function product_grid($dataArray, $cell, $template = false, $line = true, $mod = false) {
    global $_classPath;

    if (!empty($line))
        $this->grid = true;
    else
        $this->grid = false;

    if (empty($cell))
        $cell = 2;
    $this->cell = $cell;

    $this->setka_footer = true;

    $table = null;
    $j = 1;
    $item = 1;

    $this->set('productInfo', $this->lang('productInfo'));
    $this->set('productSale', $this->lang('productSale'));
    $this->set('productSaleReady', $this->lang('productSaleReady'));

    $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = null;
    if (is_array($dataArray)) {
        $this->total = count($dataArray);
        foreach ($dataArray as $row) {

            // ����� ������
            $this->checkStore($row);

            // ���������� ����������
            $this->set('productName', $row['name']);
            $this->set('productNameClean', str_replace(['"', "'"], '', strip_tags($row['name'])));
            $this->set('productArt', $row['uid']);
            $this->set('productDes', $row['description']);
            $this->set('productPageThis', $this->PHPShopNav->getPage());

            // ��������� webp � iOS
            $row['pic_small'] = $this->setImage($row['pic_small']);

            // ������ ��������
            if (empty($row['pic_small']))
                $this->set('productImg', $this->no_photo);
            else
                $this->set('productImg', $row['pic_small']);

            $this->set('productImgBigFoto', $row['pic_big']);
            $this->set('productPriceMoney', $this->PHPShopSystem->getValue('dengi'));

            $this->set('productUid', $row['id']);
            $this->set('catalog', $this->lang('catalog'));

            $this->set('previewSorts', $this->getPreviewSorts($dataArray, $row));

            // ����������� ������� ������ ������� ������ ������ �� ������� �������������
            $this->doLoadFunction(__CLASS__, 'comment_rate', array("row" => $row, "type" => "CID"), 'shop');


            // ������ ������ ������
            if (empty($template))
                $template = 'main_product_forma_' . $this->cell;

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $row);

            // ���������� ������ ������ ������
            $dis = ParseTemplateReturn($this->getValue('templates.' . $template), $mod, $this->template_debug);


            // ������� ��������� ����������� � �����
            if ($item == $this->total)
                $this->setka_footer = false;

            $cell_name = 'd' . $j;
            $$cell_name = $dis;

            if ($j == $this->cell) {
                $table .= $this->setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                $d1 = $d2 = $d3 = $d4 = $d5 = $d6 = $d7 = null;
                $j = 0;
            } elseif ($item == $this->total) {
                $table .= $this->setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
            }

            $j++;
            $item++;
        }
    }
    $this->product_grid = $table;
    return $table;
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

    $Arg = func_get_args();
    $item = 1;

    foreach ($Arg as $key => $value)
        if ($key < $this->cell and $this->total >= $this->cell)
            $args[] = $value;
        elseif (!empty($value))
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
