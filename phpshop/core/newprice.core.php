<?php

/**
 * ���������� ��������� � ������ ����������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopShopCore
 */
class PHPShopNewprice extends PHPShopShopCore {

    var $debug = false;
    var $cache = true;
    var $cache_format = array('content', 'yml_bid_array');

    /**
     * ����� �������
     * @var int 
     */
    var $cell;

    /**
     * �����������
     */
    function __construct() {

        parent::__construct();
        $this->PHPShopOrm->cache_format = $this->cache_format;

        // ��������� ������� ������
        $this->navigation(null, __('����������'));
    }

    function promotions() {
        global $PHPShopPromotions;

        $categories = $products = $where = null;
        if (is_array($PHPShopPromotions->promotionslist)) {
            
            foreach ($PHPShopPromotions->promotionslist as $pro) {

                // �������� ���������� ����������
                $date_act = $PHPShopPromotions->promotion_check_activity($pro['active_check'], $pro['active_date_ot'], $pro['active_date_do']);

                // ��������� ������ ������������
                $user_act = $PHPShopPromotions->promotion_check_userstatus(unserialize($pro['statuses']));

                if ($date_act == 1 && $user_act and $pro['sum_order_check'] == 0) {
                    $categories .= $pro['categories'];
                    $products .= $pro['products'];
                }
            }

            if (!empty($categories)) {
                $category = array_diff(explode(',', $categories), ['']);
                $where .= 'category IN (' . implode(",", $category) . ')';
            }

            if (!empty($products))
                $where .= ' or id IN (' . $products . ')';

            if (!empty($where))
                return ' and (' . $where . ' or price_n!="")';
        }
    }

    /**
     * ����� ������ �������
     */
    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // ���� ��� ���������
        $this->objPath = './newprice_';

        // ������
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', $this->lang('newprice'));

        // ���������� �����
        if (empty($this->cell))
            $this->cell = $this->calculateCell("newprice", $this->PHPShopSystem->getValue('num_vitrina'));

        $promotions = $this->promotions();
        if (empty($promotions))
            $where['price_n'] = "!=''";

        // ������ ����������
        $order = $this->query_filter("price_n!=''" . $promotions);


        // ���-�� ������� �� ��������
        // ���� 0 ������ �� ������� 6 ����� ���-�� ������� * ���-�� �������.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;


        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'" . $promotions;

        // ����������
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'] .= ' ' . $queryMultibase;

        // ������� ������
        if (is_array($order)) {
            $this->dataArray = parent::getListInfoItem(array('*'), $where, $order, __CLASS__, __FUNCTION__);
        } else {
            // ������� ������
            $this->PHPShopOrm->sql = $order;
            $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
            $this->dataArray = $this->PHPShopOrm->select();
            $this->PHPShopOrm->clean();
        }

        // ���������
        if (is_array($this->dataArray))
            $this->setPaginator(count($this->dataArray));

        // ��������� � ������ ������ � ��������
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // ���������
        $this->title = $this->lang('newprice') . " - " . $this->PHPShopSystem->getParam('title');

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.product_page_spec_list'));
    }

    /**
     * ��������� SQL ������� �� �������� ��������� � ���������
     * @return mixed
     */
    function query_filter($where = false, $v = false) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where);
        if (!empty($hook))
            return $hook;

        return parent::query_filter($where);
    }

}

?>