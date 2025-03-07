<?php

/**
 * ���������� ������ �������
 * @author PHPShop Software 
 * @version 1.8
 * @package PHPShopShopCore
 */
class PHPShopSearch extends PHPShopShopCore {

    const YANDEX_SEARCH_API_URL = 'https://catalogapi.site.yandex.net/v1.0';
    const YANDEX_SPELLER_API_URL = 'https://speller.yandex.net/services/spellservice.json/checkText';

    /**
     * ����� �������
     * @var int 
     */
    var $cell = 3;
    var $line = false;
    var $debug = false;
    var $cache = false;
    var $grid = false;
    var $empty_index_action = false;
    var $isYandexSearch = false;
    var $isYandexSpeller = false;
    var $yandexSearchAPI;
    var $yandexSearchId;
    var $dataArray;
    var $num_page;
    var $num_row = 9;

    function __construct() {

        // ������ �������
        $this->action = array("post" => "words", "get" => "words", "nav" => "index");
        parent::__construct();

        if ($this->PHPShopSystem->getSerilizeParam('admoption.search_row')) {
            $this->cell = $this->PHPShopSystem->getSerilizeParam('admoption.search_row');
            $this->num_row = $this->PHPShopSystem->getSerilizeParam('admoption.search_num');
        }

        $this->yandexSearchAPI = $this->PHPShopSystem->getSerilizeParam('admoption.yandex_search_apikey');
        $this->yandexSearchId = (int) $this->PHPShopSystem->getSerilizeParam('admoption.yandex_search_id');
        if (!empty($this->yandexSearchAPI) && !empty($this->yandexSearchId)) {
            $this->isYandexSearch = (bool) $this->PHPShopSystem->getSerilizeParam('admoption.yandex_search_enabled');
        }

        $this->isYandexSpeller = (bool) $this->PHPShopSystem->getSerilizeParam('admoption.yandex_speller_enabled');
        $this->isYandexSearchCloud = (bool) $this->PHPShopSystem->getSerilizeParam('ai.yandexsearch_site_enabled');
        $this->isYandexSearchToken = (bool) $this->PHPShopSystem->getSerilizeParam('ai.yandexsearch_token');

        if (empty($this->isYandexSearchToken))
            $this->isYandexSearchCloud = false;
        else
            PHPShopObj::loadClass('yandexcloud');

        $this->title = __('�����') . " - " . $this->PHPShopSystem->getValue("name");
    }

    /**
     * ����� �� ���������, ����� ����� ������
     */
    function index() {

        $this->category_select();

        $this->set('searchSetA', 'checked');
        $this->set('searchSetC', 'checked');

        if ($this->isYandexSearch) {
            $this->set('hideSearchType', 'hidden');
        }

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__);

        if (isset($_REQUEST['ajax']))
            exit();


        // ���������� ������
        $this->parseTemplate($this->getValue('templates.search_page_list'));
    }

    /**
     * ��������� SQL ������� �� �������� ��������� � ���������
     * ������� �������� � ��������� ���� query_filter.php
     * @return mixed
     */
    function query_filter($where = false, $v = false) {

        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        return $this->doLoadFunction(__CLASS__, __FUNCTION__);
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
                if (empty($sup) and $this->PHPShopCategoryArray->getParam($val . '.skin_enabled') != 1) {

                    // ���������� �������� ��������
                    if ($_REQUEST['cat'] == $val)
                        $sel = 'selected';
                    else
                        $sel = false;

                    $this->value[] = array($parent_name . ' / ' . $name, $val, $sel);
                }
            }
            return true;
        }
        else {
            //���������� �������� ��������
            if (!empty($_REQUEST['cat']) and $_REQUEST['cat'] == $cat)
                $sel = 'selected';
            else
                $sel = false;

            if (!$this->errorMultibase($cat) and $this->PHPShopCategoryArray->getParam($cat . '.skin_enabled') != 1)
                $this->value[] = array($parent_name, $cat, $sel);

            return true;
        }
    }

    /**
     * ����� ��������� ��� ������
     */
    function category_select() {

        $this->value[] = array(__('��� �������'), 0, false);
        $this->PHPShopCategoryArray = new PHPShopCategoryArray();
        $this->ParentArray = $this->PHPShopCategoryArray->getKey('parent_to.id', true);
        if (is_array($this->ParentArray[0])) {
            foreach ($this->ParentArray[0] as $val) {
                if ($this->PHPShopCategoryArray->getParam($val . '.skin_enabled') != 1 and ! $this->errorMultibase($val)) {
                    $name = $this->PHPShopCategoryArray->getParam($val . '.name');
                    $this->subcategory($val, $name);
                }
            }
        }

        $disp = PHPShopText::select('cat', $this->value, '400', $float = "none", false, "proSearch(this.value)", false, 1, 'cat');
        $this->set('searchPageCategory', $disp);


        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->value);
    }

    /**
     *  ����� ��������
     */
    function sort_select() {
        if (PHPShopSecurity::true_param(@$_REQUEST['v'], @$_REQUEST['cat']))
            if (is_array($_REQUEST['v'])) {
                PHPShopObj::loadClass('sort');
                if (PHPShopSecurity::true_num($_REQUEST['cat'])) {
                    $PHPShopSort = new PHPShopSort($_REQUEST['cat']);
                    $this->set('searchPageSort', $PHPShopSort->display());

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $PHPShopSort);
                }
            }
    }

    /**
     *  ����� �� ������ ���������
     */
    function words_category() {

        $template = 'search/search_ajax_catalog_forma.tpl';
        if (PHPShopParser::checkFile($template)) {
            $PHPShopOrm = new PHPShopOrm($this->getValue('base.categories'));
            $PHPShopOrm->debug = $this->debug;


            $data = $PHPShopOrm->select(array('id', 'name'), array('name' => " REGEXP '" . explode(" ", $_REQUEST['words'])[0] . "'"), array('order' => 'name'), array('limit' => 5));

            return $this->product_grid($data, $this->cell, $template, $this->line);
        }
    }

    /**
     *  ����� �� ������ ���������
     */
    function words_page() {

        if (PHPShopParser::checkFile($template)) {
            $PHPShopOrm = new PHPShopOrm($this->getValue('base.page'));
            $PHPShopOrm->debug = $this->debug;


            $data = $PHPShopOrm->select(array('link', 'name'), array('name' => " REGEXP '" . explode(" ", $_REQUEST['words'])[0] . "' or content REGEXP '" . explode(" ", $_REQUEST['words'])[0] . "'", "enabled" => "!='0'", 'category' => '!=2000'), array('order' => 'name'), array('limit' => 5));

            return $data;
        }
    }

    /**
     * Yandex Speller
     */
    function speller($words) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::YANDEX_SPELLER_API_URL . '?text=' . PHPShopString::win_utf8($words, true));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), 1);
        curl_close($ch);

        if (is_array($data)) {
            if (!empty($data[0]['s'][0]))
                $words = PHPShopString::utf8_win1251($data[0]['s'][0], true);
        }

        return $words;
    }

    /**
     * ����� ������ �� �������
     */
    function words() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_REQUEST, 'START'))
            return true;

        // ������
        $this->set('productValutaName', $this->currency());

        // ��������� ������
        $this->category_select();

        // ������� ������
        $this->sort_select();

        // ������� ����� � Ajax �������
        if (isset($_REQUEST['ajax']))
            $_REQUEST['words'] = urldecode($_REQUEST['words']);

        // ������ ������
        $_REQUEST['words'] = PHPShopSecurity::true_search($_REQUEST['words'], true);

        // Yandex Speller
        if ($this->isYandexSpeller)
            $_REQUEST['words'] = $this->speller($_REQUEST['words']);

        if (!empty($_REQUEST['words'])) {

            // Ajax Search
            if (isset($_REQUEST['ajax'])) {
                $this->cell = 1;
                $this->num_row = 5;
                $template = 'search/search_ajax_product_forma.tpl';

                if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
                    $seourlpro = true;
                }
            } else
                $template = false;

            $order = $this->query_filter();

            if (!empty($order)) {
                // ������� ������
                $this->PHPShopOrm->sql = $order;
                $this->PHPShopOrm->debug = $this->debug;
                $this->PHPShopOrm->mysql_error = false;
                $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;

                $dataArray = $this->PHPShopOrm->select();
            }

            if (is_array($dataArray) and is_array($this->dataArray))
                $this->dataArray += $dataArray;
            elseif (is_array($dataArray))
                $this->dataArray = $dataArray;

            $this->PHPShopOrm->clean();

            // ����� �� ���������
            $grid_category = $this->words_category();

            // ����� �� ���������
            $words_page = $this->words_page();

            if (!empty($this->dataArray) or ! empty($grid_category) or ! empty($words_page)) {

                // ���������
                if (!$this->get('hideSite') and is_array($this->dataArray))
                    $this->setPaginator(count($this->dataArray), $order);

                // ��������� � ������ ������ � ��������
                $grid = $this->product_grid($this->dataArray, $this->cell, $template, $this->line);

                // Ajax Search
                if (isset($_REQUEST['ajax'])) {

                    // ����� �� ���������
                    $grid_page = $this->product_grid($words_page, $this->cell, 'search/search_ajax_page_forma.tpl', $this->line);

                    $grid = $grid_category . $grid . $grid_page;

                    // ��������� ������ SeoUrlPro
                    if (!empty($seourlpro))
                        $grid = $GLOBALS['PHPShopSeoPro']->AjaxCompile($grid);

                    header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);
                    exit(PHPShopParser::replacedir($this->separator . $grid));
                }


                if (!$this->get('hideSite'))
                    $this->add($grid, true);

                // ����� �����
                if ($this->get('hideSite')) {

                    // ����� �� ���������
                    $grid_page = $this->product_grid($words_page, $this->cell, 'search/search_page_forma.tpl', $this->line);

                    $this->add($grid_page, true);
                }
            } else {
                if (isset($_REQUEST['ajax']))
                    exit('false');
                $this->add(PHPShopText::h3(__('������ �� �������')), true);
            }

            if (!empty($_REQUEST['cat']))
                $cat = $_REQUEST['cat'];
            else
                $cat = null;

            // ������ � ������
            $this->write($this->get('searchString'), (int) $this->num_page, (int) $cat);

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');
        }

        if (isset($_REQUEST['ajax']))
            exit('false');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.search_page_list'));
    }

    /**
     * ������ � ������ ������
     */
    function write($name, $num, $cat) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.search_jurnal'));
        $PHPShopOrm->debug = $this->debug;

        // �������� ������
        $arg = func_get_args();
        $this->PHPShopModules->setHookHandler(__CLASS__, __FUNCTION__, $this, $arg);

        $PHPShopOrm->insert([
            'name_new' => PHPShopSecurity::TotalClean($name),
            'num_new' => intval($num),
            'datas_new' => time(),
            'cat_new' => intval($cat),
            'dir_new' => $_SERVER['HTTP_REFERER'],
            'ip_new' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    /**
     * ��������� ����������
     */
    function setPaginator($count = null, $sql = null) {

        $this->search_order['cat'] = (int) $_REQUEST['cat'];

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

        if (is_array($this->search_order)) {
            $SQL = " where (" . $this->search_order['string'] . " " . $this->search_order['sort'] . "
                 " . $this->search_order['prewords'] . " " . $this->search_order['sortV'] . ") and enabled='1' and parent_enabled='0' ";
        } else
            $SQL = null;


        // ����� �������
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
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
                    if ($i == 1) {
                        $this->set("paginLink", "?words=" . $this->search_order['words'] . "&pole=" . $this->search_order['pole'] . "&p=" . $i . "&cat=" . $this->search_order['cat']);
                        $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $this->set("paginLink", "?words=" . $this->search_order['words'] . "&pole=" . $this->search_order['pole'] . "&p=" . $i . "&cat=" . $this->search_order['cat']);
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                        } else if ($i - ($this->page + $this->nav_len) < 3 and ( ($this->page - $this->nav_len) - $i) < 3) {
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_more.tpl", $template_location_bool);
                        }
                    }
                } else
                    $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_selected.tpl", $template_location_bool);

                $i++;
            }


            $nav = $this->getValue('lang.page_now') . ': ';

            $this->set("previousLink", "?words=" . $this->search_order['words'] . "&pole=" . $this->search_order['pole'] . "&p=" . $p_do . "&cat=" . $this->search_order['cat']);

            $this->set("nextLink", "?words=" . $this->search_order['words'] . "&pole=" . $this->search_order['pole'] . "&p=" . $p_to . "&cat=" . $this->search_order['cat']);

            $nav .= $navigat;


            $this->set("pageNow", $this->getValue('lang.page_now'));
            $this->set("navBack", $this->lang('nav_back'));
            $this->set("navNext", $this->lang('nav_forw'));
            $this->set("navigation", $navigat);

            // ��������� ���������� �������������
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('searchPageNav', $nav);

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $nav);
        }
    }

    /**
     * ����� ����� � ��������
     * @return string
     */
    function setCell($d1, $d2 = null, $d3 = null, $d4 = null, $d5 = null, $d6 = null, $d7 = null) {

        // �������� ������, ��������� � ������ ������� ������ ��� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $Arg = func_get_args();
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $Arg);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        return parent::setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
    }

}

?>