<?php

/**
 * ���������� ������� ������� �� ���������������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopShopCore
 */
class PHPShopSelection extends PHPShopShopCore {

    /**
     * �������
     * @var bool
     */
    var $debug = false;
    /*
     * �����������
     */
    var $cache = false;

    /**
     * ����� ������ �������
     * @var int
     */
    var $max_item = 250;

    /**
     * ����, �������� � �������� �������� �������� ��� ����� ��������������. True - �������������. False - ��������.
     * @var bool
     */
    var $descrFlag = false;

    /**
     * �����������
     */
    function __construct() {

        PHPShopObj::loadClass("sort");

        // ������ �������
        $this->action = array("get" => "v", 'nav' => 'index');
        parent::__construct();
        $this->PHPShopOrm->cache_format = $this->cache_format;

        // ��������� ������� ������
        $this->navigation(false, __('������'));
    }

    function index() {
        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        $this->setError404();
    }

    /**
     * ����� ����������� �������������
     */
    function checkName() {

        foreach ($_GET['v'] as $v)
            $id = intval($v);

        $PHPShopSortArray1 = new PHPShopSortArray(array('id' => '=' . $id));
        $name = $PHPShopSortArray1->getParam($id . '.name');

        $PHPShopSortArray = new PHPShopSortArray(array('name' => "='$name'", 'id' => '!=' . $id));

        if (is_array($PHPShopSortArray->getArray()))
            foreach ($PHPShopSortArray->getArray() as $val)
                $_GET['v'][$val['category']] = $val['id'];
    }

    /**
     * ����� ������ �������
     */
    function v() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        // ������
        $this->set('productValutaName', $this->currency());

        // ���������� �����
        if (empty($this->cell))
            $this->cell = $this->calculateCell("selection", $this->PHPShopSystem->getValue('num_row_adm'));

        // ����� ����������� �������������
        $this->checkName();

        // ������ ����������
        $order = $this->query_filter();

        // ������� ������
        $this->PHPShopOrm->sql = $order;
        $this->PHPShopOrm->debug = $this->debug;
        $this->PHPShopOrm->mysql_error = false;
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $this->dataArray = $this->PHPShopOrm->select();
        $this->PHPShopOrm->clean();
        // ���������
        $count = count($this->dataArray);
        if ($count)
            $this->setPaginator($count, $order);


        // ��������� � ������ ������ � ��������
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h4($this->lang('empty_product_list'));
        $this->add($grid, true);

        // ID ��������������
        foreach ($GLOBALS['SysValue']['nav']['query']['v'] as $key => $val) {
            if ($this->descrFlag)
                $v = intval($key);
            else
                $v = intval($val);
        }

        // �������� �������� �������������� �� ��������
        $PHPShopOrm = new PHPShopOrm();
        $result = $PHPShopOrm->query('SELECT a.*, b.content FROM ' . $this->getValue("base.sort") . ' AS a JOIN ' . $this->getValue("base.page") . ' AS b ON a.page = b.link where a.id = ' . $v . ' limit 1');
        $row = mysqli_fetch_array($result);
        if (is_array($row)) {
            // ��������
            $this->set('sortDes', stripslashes($row['content']));
        } else {

            // �������� �� ��������������
            $PHPShopOrm = new PHPShopOrm($this->getValue("base.sort"));
            $row = $PHPShopOrm->select(array('*'), array('id' => '=' . $v), false, array('limit' => 1));
            if (is_array($row)) {

                $this->set('sortDes', stripslashes($row['description']));
            }
        }

        // ��������
        $this->set('sortName', $row['name']);

        // ���������
        if (empty($row['title']))
            $this->title = __('�����') . " - " . $row['name'] . " - " . $this->PHPShopSystem->getParam('title');
        else
            $this->title = str_replace(['@System@', '@valueTitle@'], [$this->PHPShopSystem->getParam('title'), $row['name']],$row['title']);
        
        if (empty($row['meta_description']))
            $this->description = $row['name'] . ', ' . $this->PHPShopSystem->getParam('descrip');
        else
            $this->description = str_replace(['@System@', '@valueTitle@'], [$this->PHPShopSystem->getParam('descrip'), $row['name']],$row['meta_description']);

        $this->keywords = $row['name'];

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.product_selection_list'));
    }

    /**
     * ��������� SQL ������� �� �������� ��������� � ���������
     * ������� �������� � ��������� ���� query_filter.php
     * @return mixed
     */
    function query_filter($where = false, $v = false) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if (!empty($hook))
            return $hook;

        return $this->doLoadFunction(__CLASS__, __FUNCTION__);
    }

    /**
     * ��������� ����������
     */
    function setPaginator($count=null, $sql = null) {

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
        }

        // ���-�� ������
        $this->count = $count;

        if (is_array($this->selection_order)) {
            $SQL = " where enabled='1' and parent_enabled='0' " . $this->selection_order['sortV'];
        }
        else
            $SQL = null;


        // ����� �������
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $this->PHPShopOrm->clean();
        $result = $this->PHPShopOrm->query("select COUNT('id') as count from " . $this->objBase . $SQL);
        $row = mysqli_fetch_array($result);
        $this->num_page = $row['count'];

        $i = 1;
        $navigat = null;
        $num = ceil($this->num_page / $this->num_row);
        if (empty($_GET['p']))
            $_GET['p'] = 1;
        $this->page = $_GET['p'];

        if ($num > 1) {
            if ($this->page >= $num) {
                $p_to = $i - 1;
                $p_do = $this->page - 1;
            } else {
                $p_to = $this->page + 1;
                $p_do = 1;
            }

            while ($i <= $num) {

                if ($i > 1) {
                    $p_start = $this->num_row * ($i - 1);
                    $p_end = $p_start + $this->num_row;
                } else {
                    $p_start = $i;
                    $p_end = $this->num_row;
                }


                $this->set("paginPageRangeStart", $p_start);
                $this->set("paginPageRangeEnd", $p_end);
                $this->set("paginPageNumber", $i);


                if ($i != $this->page) {
                    $this->set("paginLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . $this->selection_order['sortQuery'] . "&p=" . $i);
                    if ($i == 1) {
                        $navigat.= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $navigat.= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                        } else if ($i - ($this->page + $this->nav_len) < 3 and (($this->page - $this->nav_len) - $i) < 3) {
                            $navigat.= parseTemplateReturn($template_location . "paginator/paginator_one_more.tpl", $template_location_bool);
                        }
                    }
                }
                else
                    $navigat.= parseTemplateReturn($template_location . "paginator/paginator_one_selected.tpl", $template_location_bool);

                $i++;
            }


            $nav = $this->getValue('lang.page_now') . ': ';

            $this->set("previousLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . $this->selection_order['sortQuery'] . "&p=" . $p_do);

            $this->set("nextLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . $this->selection_order['sortQuery'] . "&p=" . $p_to);

            $nav.=$navigat;


            $this->set("pageNow", $this->getValue('lang.page_now'));
            $this->set("navBack", $this->lang('nav_back'));
            $this->set("navNext", $this->lang('nav_forw'));
            $this->set("navigation", $navigat);

            // ��������� ���������� �������������
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('productPageNav', $nav);

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $nav);
        }
    }

}

?>