<?php

/**
 * ���������� ��������������� �������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopShopCore
 */
class PHPShopSpec extends PHPShopShopCore {

    var $debug = false;
    var $cache = false;
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

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // ���� ��� ���������
        $this->objPath = './spec_';

        // ������
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', $this->lang('specprod'));

        // ���������� 
        if (empty($this->cell))
            $this->cell = $this->calculateCell("spec", $this->PHPShopSystem->getValue('num_row_adm'));

        // ������ ����������
        $order = $this->query_filter("spec='1'");

        // ���-�� ������� �� ��������
        // ���� 0 ������ �� ������� ���-�� ������� * 2 ������.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;

        $where['spec'] = "='1'";
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
        if (is_array($this->dataArray))
            $this->setPaginator(count($this->dataArray));

        // ��������� � ������ ������ � ��������
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // ���������
        $this->title = $this->lang('specprod') . " - " . $this->PHPShopSystem->getParam('title');

        // ��������� ������� ������
        $this->navigation(null, __('���������������'));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.product_page_spec_list'));
    }

    /**
     * ��������� SQL ������� �� �������� ��������� � ���������
     * @return mixed
     */
    function query_filter($where = false,$v = false) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where);
        if (!empty($hook))
            return $hook;

        return parent::query_filter($where);
    }

}

?>