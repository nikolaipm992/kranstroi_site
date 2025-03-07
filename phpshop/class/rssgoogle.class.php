<?php

/**
 * ���������� YML ��� Google Merchant
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 */
class PHPShopRssGoogle {

    var $xml = null;

    /**
     * ����� �������������
     * @var bool 
     */
    var $vendor = false;

    /**
     * ����� ����������
     * @var bool 
     */
    var $param = false;

    /**
     * ������ �������
     * @var array 
     */
    var $brand_array = array();

    /**
     * ������ ����������
     * @var array 
     */
    var $param_array = array();

    /**
     * ������ �������� ���/��� ��������������
     * @var array 
     */
    var $vendor_name = array('vendor' => '�����');

    /**
     * ������ ������� �������
     * @var bool 
     */
    var $memory = true;
    var $ssl = 'http://';

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopModules, $PHPShopSystem;

        $this->PHPShopSystem = $PHPShopSystem;
        $PHPShopValuta = new PHPShopValutaArray();
        $this->PHPShopValuta = $PHPShopValuta->getArray();

        // ������
        $this->PHPShopModules = &$PHPShopModules;

        // ����������
        $this->PHPShopPromotions = new PHPShopPromotions();

        // ������� ��������
        $this->percent = $this->PHPShopSystem->getValue('percent');

        // ������ �� ���������
        $this->defvaluta = $this->PHPShopSystem->getValue('dengi');
        $this->defvalutaiso = $this->PHPShopValuta[$this->defvaluta]['iso'];
        $this->defvalutacode = $this->PHPShopValuta[$this->defvaluta]['code'];

        // ���-�� ������ ����� ������� � ����
        $this->format = $this->PHPShopSystem->getSerilizeParam('admoption.price_znak');

        // SSL
        if (isset($_GET['ssl']))
            $this->ssl = 'https://';
        else if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';

        $this->setHook(__CLASS__, __FUNCTION__);
    }

    /**
     * ���������� ��������� ������� ���������� �������
     * @param string $class_name ��� ������
     * @param string $function_name ��� ������
     * @param mixed $data ������ ��� ���������
     * @param string $rout ������� ������ � ������� [END | START | MIDDLE], �� ��������� END
     * @return bool
     */
    function setHook($class_name, $function_name, $data = false, $rout = false) {
        if ($this->PHPShopModules)
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
    }

    /**
     * ������ � ������
     * @param string $param ��� ��������� [catalog.param]
     * @param mixed $value ��������
     */
    function memory_set($param, $value) {
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]] = $value;
            $_SESSION['Memory'][__CLASS__]['time'] = time();
        }
    }

    /**
     * ������� �� ������
     * @param string $param ��� ��������� [catalog.param]
     * @param bool $check �������� � �����
     * @return
     */
    function memory_get($param, $check = false) {
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            if (isset($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]])) {
                if (!empty($check)) {
                    if (!empty($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]]))
                        return true;
                } else
                    return $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]];
            }
            elseif (!empty($check))
                return true;
        } else
            return true;
    }

    /**
     * �������� ���� �������� ������ Multibase
     * @return string
     */
    function queryMultibase() {

        // ����������
        if (defined("HostID") or defined("HostMain")) {

            // �� �������� ������� ��������
            $where['skin_enabled '] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $PHPShopOrm->debug = $this->debug;
            $this->categories = array_column($PHPShopOrm->getList(['id'], $where, false, ['limit' => 1000], __CLASS__, __FUNCTION__), 'id');

            if (count($this->categories) > 0) {
                $dop_cats = '';
                foreach ($this->categories as $category) {
                    $dop_cats .= ' OR dop_cat LIKE \'%#' . $category . '#%\' ';
                }
                $categories_str = implode("','", $this->categories);

                return " (category IN ('$categories_str') " . $dop_cats . " ) and ";
            }
        }
    }

    /**
     * ������ �� �������. ��������������.
     * @return array ������ �������
     */
    function product() {
        $Products = array();

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

        if (isset($_GET['getall']))
            $where = null;
        else {
            if (isset($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system'])) {
                $where = "google_merchant='1' and";
            } else {
                $where = null;
            }
        }

        // ����������
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where .= ' ' . $queryMultibase;

        // IDs
        if (is_array($this->where)) {
            $where = 'id  IN (' . implode(',', $this->where) . ') and ';
        }

        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where $where enabled='1' and parent_enabled='0' and price>0");
        while ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];
            $name = trim(strip_tags($row['name']));
            $category = $row['category'];
            $uid = $row['uid'];
            $price = $row['price'];
            $oldprice = $row['price_n'];

            // ����������
            $promotions = $this->PHPShopPromotions->getPrice($row);
            if (is_array($promotions)) {
                $price = $promotions['price'];
                $oldprice = $promotions['price_n'];
            }

            if ($row['p_enabled'] == 1)
                $p_enabled = "in stock";
            else
                $p_enabled = "out of stock";

            if (empty($row['description']))
                $row['description'] = $row['content'];
            $description = '<![CDATA[' . trim(strip_tags($row['description'], '<p><h3><ul><li><br>')) . ']]>';
            $content = '<![CDATA[' . $row['content'] . ']]>';
            $baseinputvaluta = $row['baseinputvaluta'];

            if ($baseinputvaluta) {
                //���� ������ ���������� �� �������
                if ($baseinputvaluta !== $this->defvaluta) {
                    $vkurs = $this->PHPShopValuta[$baseinputvaluta]['kurs'];

                    // ���� ���� ������� ��� ������ �������
                    if (empty($vkurs))
                        $vkurs = 1;

                    // �������� ���� � ������� ������
                    $price = $price / $vkurs;
                    $oldprice = $oldprice / $vkurs;
                }
            }

            $price = ($price + (($price * $this->percent) / 100));
            $price = round($price, $this->format);
            $oldprice = round($oldprice, $this->format);

            $array = array(
                "id" => $id,
                "category" => $category,
                "name" => $name,
                "picture" => $row['pic_big'],
                "price" => $price,
                "price2" => round($row['price2'], (int) $this->format),
                "price3" => round($row['price3'], (int) $this->format),
                "price4" => round($row['price4'], (int) $this->format),
                "price5" => round($row['price5'], (int) $this->format),
                "oldprice" => $oldprice,
                "weight" => $row['weight'],
                "p_enabled" => $p_enabled,
                "yml_bid_array" => unserialize($row['yml_bid_array']),
                "uid" => $uid,
                "description" => $description,
                "content" => $content,
                "prod_seo_name" => $row['prod_seo_name'],
                "fee" => $row['fee'],
                "cpa" => $row['cpa'],
                "manufacturer_warranty" => $row['manufacturer_warranty'],
                "sales_notes" => $row['sales_notes'],
                "country_of_origin" => $row['country_of_origin'],
                "adult" => $row['adult'],
                "rec" => $row['odnotip'],
                "delivery" => $row['delivery'],
                "pickup" => $row['pickup'],
                "store" => $row['store'],
                "vendor_code" => $row['vendor_code'],
                "vendor_name" => $row['vendor_name'],
                "condition" => $row['yandex_condition'],
                "barcode" => $row['barcode'],
                "price_google" => round($row['price_google'], (int) $this->format),
            );

            // �������� ����������
            if (!empty($this->vendor))
                $array['vendor_array'] = unserialize($row['vendor_array']);

            $Products[$id] = $array;
        }
        return $Products;
    }

    /**
     * ��������� 
     */
    function setHeader() {
        $this->xml .= '<?xml version="1.0"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>' . $this->PHPShopSystem->getName() . '</title>
<link>' . $this->ssl . $_SERVER['SERVER_NAME'] . '</link>
<description>' . $this->PHPShopSystem->getValue('company') . '</description>
<platform>PHPShop</platform>
<version>' . $GLOBALS['SysValue']['upload']['version'] . '</version>';
    }

    /**
     * ������� ������������
     */
    function cleanStr($string) {
        $string = html_entity_decode($string, ENT_QUOTES, 'windows-1251');
        return str_replace('&#43;', '+', $string);
    }

    /**
     * ������ 
     */
    function setProducts() {
        $vendor = null;
        $this->xml .= null;
        $product = $this->product($vendor = true);

        // ���� ������ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $seourlpro_enabled = true;
        }

        // ���������� ��������
        if (isset($_GET['from']))
            $from = '?from=xml';
        else
            $from = null;


        foreach ($product as $val) {

            $bid_str = null;
            $vendor = $param = null;

            // ����������� ���
            $url = '/shop/UID_' . $val['id'];

            // SEOURLPRO
            if (!empty($seourlpro_enabled)) {
                if (empty($val['prod_seo_name']))
                    $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($val['name'])) . '-' . $val['id'];
                else
                    $url = '/id/' . $val['prod_seo_name'] . '-' . $val['id'];
            }

            $xml = '
      <item> 
      <title><![CDATA[' . $this->cleanStr($val['name']) . ']]></title> 
      <link>' . $this->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . $url . '.html' . $from . '</link> 
      <description>' . $this->cleanStr($val['description']) . '</description>
      <g:image_link>' . $this->ssl . $_SERVER['SERVER_NAME'] . $val['picture'] . '</g:image_link> 
      <g:price>' . $val['price'] . ' ' . $this->defvalutaiso . '</g:price> 
      <g:availability>' . $val['p_enabled'] . '</g:availability>
      <g:id>' . $val['id'] . '</g:id>
      </item>';


            // �������� ������, ��������� � ������ ������� ������ ��� �����������
            if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
                $hook = $this->setHook(__CLASS__, __FUNCTION__, array('xml' => $xml, 'val' => $val));
                if ($hook) {
                    $this->xml .= $hook;
                } else {
                    $this->xml .= $xml;
                    $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
                }
            } else
                $this->xml .= $xml;
        }
    }

    /**
     * ������ 
     */
    function serFooter() {
        $this->xml .= '</channel></rss>';
    }

    /**
     * ���������� ���������, ����� ���������� 
     */
    function compile() {
        $this->setHeader();
        $this->setProducts();
        $this->serFooter();
        return PHPShopString::win_utf8($this->xml);
    }

}
