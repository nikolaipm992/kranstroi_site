<?php

class PHPShopHit extends PHPShopShopCore {

    var $debug = false;
    var $cache = true;
    var $cache_format = array('content', 'yml_bid_array');
    var $cell;

    /**
     * �����������
     */
    function __construct() {

        parent::__construct();
        $this->PHPShopOrm->cache_format = $this->cache_format;
    }

    /**
     * ����� ������ �������
     */
    function index() {

        // ���� ��� ���������
        $this->objPath = './hit_';

        // ������
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', '����');

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_hit_system');
        $options = $PHPShopOrm->select(array('*'));

        (int) $options['hit_page'] > 0 ? $this->cell = (int) $options['hit_page'] : $this->cell = 4;

        // ������ ����������
        $order = $this->query_filter("hit='1'");

        // ���-�� ������� �� ��������
        // ���� 0 ������ �� ������� ���-�� ������� * 2 ������.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;

        $where['hit'] = "='1'";
        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";

        // ����������
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'].= ' ' . $queryMultibase;

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
        if (is_array($order))
            $this->setPaginator(count($this->dataArray));

        // ��������� � ������ ������ � ��������
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // ���������
        $this->title = '����' . " - " . $this->PHPShopSystem->getParam('title');

        // ��������� ������� ������
        $this->navigation(null, __('����'));

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.product_page_spec_list'));
    }
    
    function query_filter($where = false) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where);
        if (!empty($hook))
            return $hook;

        return parent::query_filter($where);
    }

}

?>