<?php

/**
 * ���������� �����-������
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopShopCore
 */
class PHPShopPrice extends PHPShopShopCore {

    /**
     * ���� ���� �������
     * @var string  
     */
    var $color_product = '#ffffff';
    var $debug = false;
    var $memory = true;
    var $category;
    var $limit = 2000;

    function __construct() {

        // ������ �������
        $this->action = array("nav" => array("CAT"));

        parent::__construct();

        $this->title = $this->lang('price_title') . ' - ' . $this->PHPShopSystem->getValue("title");

        // ��������� ������� ������
        $this->navigation(false, __('�����-����'));

        $this->checkXLS();
    }

    /**
     * ����� ������
     */
    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // ��� ������
        if ($this->PHPShopSystem->getParam("shop_type") > 0)
            return $this->setError404();

        // ����� ��������� ��� ������
        $this->category_select();

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.price_page_list'));
    }

    /**
     * ����������� ������������ ������ ������������
     * @param int $cat �� ��������
     * @param string $parent_name ������� ���� ���������
     * @return bool
     */
    function subcategory($cat, $parent_name = false) {

        if (!empty($this->ParentArray[$cat]) and is_array($this->ParentArray[$cat])) {
            foreach ($this->ParentArray[$cat] as $val) {

                $name = $this->PHPShopCategoryArray->getParam($val . '.name');
                $sup = $this->subcategory($val, $parent_name . ' / ' . $name);

                if ($cat == $this->category)
                    $this->set('priceCatName', $name);

                if (empty($sup) and $this->PHPShopCategoryArray->getParam($val . '.skin_enabled') != 1) {

                    // ���������� �������� ��������
                    if ($this->category == $val)
                        $sel = 'selected';
                    else
                        $sel = false;

                    $this->value[] = array($parent_name . ' / ' . $name, $val, $sel);

                    // ������ ��� ������ ���� �������
                    $this->category_array[$cat] = $parent_name . " / " . $name;
                }
            }
            return true;
        }
        else {
            //���������� �������� ��������
            if ($this->category == $cat)
                $sel = 'selected';
            else
                $sel = false;


            if (!$this->errorMultibase($cat) and $this->PHPShopCategoryArray->getParam($cat . '.skin_enabled') != 1)
                $this->value[] = array($parent_name, $cat, $sel);

            if ($cat == $this->category)
                $this->set('priceCatName', $parent_name);

            // ������ ��� ������ ���� �������
            $this->category_array[$cat] = $parent_name;

            return true;
        }
    }

    /**
     * ����� ��������� ��� ������
     */
    function category_select() {

        // ���������� ������ ���� ������� ��� ������� ����
        if ($_SESSION['max_item'] < 1000)
            $this->value[] = array($this->lang('search_all_cat'), 'ALL', false);

        $this->PHPShopCategoryArray = new PHPShopCategoryArray(array('skin_enabled' => "!='1'"));
        $this->ParentArray = $this->PHPShopCategoryArray->getKey('parent_to.id', true);
        if (is_array($this->ParentArray[0])) {
            foreach ($this->ParentArray[0] as $val) {
                if ($this->PHPShopCategoryArray->getParam($val . '.skin_enabled') != 1 and ! $this->errorMultibase($val)) {
                    $name = $this->PHPShopCategoryArray->getParam($val . '.name');
                    $this->subcategory($val, $name);
                }
            }
        }

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->ParentArray);
        $this->set('searchPageCategory', PHPShopText::select('catId', $this->value, '400', $float = "left"));
    }

    /**
     * ������ ����� �������
     * @return string
     */
    function tr() {
        $Arg = func_get_args();
        $colspan = null;

        if (empty($Arg[0])) {
            $style = 'class="bgprice"';
            $colspan = 3;
        } else
            $style = 'bgcolor="' . $Arg[0] . '"';

        $tr = '<tr ' . $style . '>';
        foreach ($Arg as $key => $val) {
            if ($key > 0) {
                if ($key == 2)
                    $width = 70;
                elseif ($key == 3)
                    $width = 20;
                else
                    $width = false;
                $tr .= $this->td($val, $width, $colspan);
            }
        }
        $tr .= '</tr>';
        return $tr;
    }

    /**
     * ������ ����� �������
     * @return string
     */
    function td($string, $width, $colspan = false) {
        return '<td width="' . $width . '" colspan="' . $colspan . '">' . $string . '</td>';
    }

    /**
     * ������� ��� ��� ������
     * @param array $row ������ ������
     * @return string
     */
    function seourl($row) {

        // �������� ������, ��������� � ������ ������� ������ ��� ����������� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $row);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        return '/shop/UID_' . $row['id'] . '.html';
    }

    /**
     * ����� ������� �� ���������
     */
    function product($category) {
        $dis = null;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $category, 'START'))
            return true;

        // 404
        if (!PHPShopSecurity::true_num($category))
            return $this->setError404();

        // �������������� ���������
        if (is_numeric($category))
            $str = " (category=$category or dop_cat LIKE '%#$category#%') and ";
        else
            $str = "";

        // ������� ������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->sql = "select * from " . $PHPShopOrm->objBase . " where " . $str . " enabled='1' and parent_enabled='0' ORDER BY num LIMIT " . $this->limit;
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select();

        if (!empty($this->category_name))
            $dis = $this->tr(false, $this->category_name);

        if ($this->PHPShopSystem->getSerilizeParam('admoption.user_price_activate') == 1 and empty($_SESSION['UsersId']))
            $user_price_activate = true;


        // ��������� � ������ ������ � ��������
        if (is_array($data))
            foreach ($data as $row) {
                $name = PHPShopText::a($this->seourl($row), $row['name']);
                if (empty($row['sklad']) and empty($user_price_activate))
                    $cart = PHPShopText::a('javascript:AddToCart(' . $row['id'] . ')', PHPShopText::img('images/shop/basket_put.gif', false, 'absMiddle'), $this->lang('product_sale'));
                else
                    $cart = PHPShopText::a('../users/notice.html?productId=' . $row['id'], PHPShopText::img('images/shop/date.gif', false, 'absMiddle'), $this->lang('product_notice'));

                if (empty($user_price_activate))
                    $price = $this->getPrice($row) . ' ' . $this->currency();
                else
                    $price = null;


                $dis .= $this->tr('#ffffff', $name, $price, $cart);
            }

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $data, 'END');
        if ($hook)
            $dis = $hook;

        $this->add(PHPShopText::table($dis, 3, 1, 'left', '98%', '#D2D2D2'), true);
    }

    function getPrice($row) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $row);
        if ($hook)
            return $hook;

        return parent::price($row, false, true);
    }

    /**
     * ����� ������ ������� ��� ������ ���������
     */
    function CAT() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        $this->category = $GLOBALS['SysValue']['nav']['page'];

        // ����� ��������
        $this->category_select();


        // ���� ������� ����� ������� ���
        if ($this->category == 'ALL' or $GLOBALS['SysValue']['nav']['id'] == 'ALL') {

            foreach ($this->category_array as $key => $val) {
                $dis = $this->tr(false, $val);
                $this->add(PHPShopText::table($dis, 3, 1, 'center', '98%', '#D2D2D2', 0, __CLASS__ . '_' . __FUNCTION__), true);
                $this->product($key);
            }
        } else {

            $this->product($this->category);
        }

        $this->set('PageCategory', $this->category);


        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->category_array, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.price_page_list'));
    }

    function checkXLS() {
        if (!is_file('UserFiles/Files/price.xls')) {
            $this->set('onlinePrice', 'hide d-none');
        } else
            $this->set('onlinePrice', 'price-page-list');
    }

}

?>