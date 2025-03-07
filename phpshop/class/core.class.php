<?php

/**
 * ������������ ����� ����
 * ������� ������������� ��������� � ����� phpshop/core/
 * @author PHPShop Software
 * @version 1.11
 * @package PHPShopClass
 */
class PHPShopCore {

    /**
     * ��� ��
     * @var string 
     */
    var $objBase;

    /**
     * ���� ��� ���������
     * @var string
     */
    var $objPath;

    /**
     * ����� �������
     * @var bool 
     */
    var $debug = false;
    var $template_debug = true;

    /**
     * ����� SQL ������
     * @var bool 
     */
    var $mysql_error = false;

    /**
     * ��������� ������ �������
     * @var string 
     */
    var $Disp, $ListInfoItems;

    /**
     * ������ ��������� POST, GET ��������
     * @var array 
     */
    var $action = array("nav" => "index");

    /**
     * ������� ����� ������� (action_|a_), ������������ ��� ������� ���������� ������� � �������
     * @var string 
     * 
     */
    var $action_prefix = null;

    /**
     * ��������
     * @var string
     */
    var $title, $description, $keywords, $lastmodified;

    /**
     * ������ � ��������� �� �����
     * @var string 
     */
    var $navigation_link, $navigation_array = null;

    /**
     * ������ ������
     * @var string 
     */
    var $template = 'templates.shop';

    /**
     * ������� ������� ���������
     * @var string  
     */
    var $navigationBase = 'base.categories';
    var $arrayPath;

    /**
     * ����� ��������� ��� �������������� ����� ������
     * @var int 
     */
    var $nav_len = 3;
    var $cache = false;

    /**
     * ������� ��������� ���������� ������� 
     * @var bool 
     */
    var $garbage_enabled = false;

    /**
     * ���������� ������ �������� ������� ������
     * @var bool
     */
    var $empty_index_action = true;
    var $memory = true;
    var $max_item = 300;

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopSystem, $PHPShopNav, $PHPShopModules;

        if ($this->objBase) {
            $this->PHPShopOrm = new PHPShopOrm($this->objBase);
            $this->PHPShopOrm->debug = $this->debug;
            $this->PHPShopOrm->cache = $this->cache;
        }
        $this->SysValue = &$GLOBALS['SysValue'];

        $this->PHPShopSystem = $PHPShopSystem;
        $this->num_row = $this->PHPShopSystem->getParam('num_row');
        $this->PHPShopNav = $PHPShopNav;
        $this->PHPShopModules = &$PHPShopModules;
        $this->page = $this->PHPShopNav->getId();

        if (strlen($this->page) == 0)
            $this->page = 1;

        // ���������� ����������
        $this->set('pageProduct', $this->SysValue['license']['product_name']);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ��������� ��������� �� �������
     * @param string $paramName ��� ����������
     * @param string $paramValue �������� ����������
     * @return bool
     */
    function ifValue($paramName, $paramValue = false) {
        if (empty($paramValue))
            $paramValue = 1;
        if ($this->objRow[$paramName] == $paramValue)
            return true;
    }

    /**
     * ������ ��������� ������� ������
     * @param int $id �� �������
     * @return array
     */
    function getNavigationPath($id) {
        $PHPShopOrm = new PHPShopOrm($this->getValue($this->navigationBase));
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $this->cache;

        if (!empty($id)) {
            $PHPShopOrm->comment = "���������";
            $v = $PHPShopOrm->select(array('name,id,parent_to'), array('id' => '=' . $id), false, array('limit' => 1));
            if (is_array($v)) {
                $this->navigation_array[] = array('id' => $v['id'], 'name' => $v['name'], 'parent_to' => $v['parent_to']);
                if (!empty($v['parent_to']))
                    $this->getNavigationPath($v['parent_to']);
            }
        }
    }

    /**
     * ��������� ������� ������
     * @param int $id ������� �� ��������
     * @param string $name ��� �������
     * @param array $title ������ �������� [url,name]
     */
    function navigation($id, $name, $title = false) {
        global $SysValue;

        $dis = null;
        // ������� ����������� ���������
        $elementTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('templates.breadcrumbs_splitter');
        $lastElemTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('templates.breadcrumbs_splitter_last');

        if ((bool) PHPShopParser::check($this->getValue('templates.breadcrumbs_splitter'), 'breadcrumbElemTitle') === false) {
            $elementTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_splitter.tpl';
        }
        $lastTemplatePath = $this->getValue('templates.breadcrumbs_splitter_last');
        if (empty($lastTemplatePath) || PHPShopParser::checkFile($lastElemTemplate, true) === false) {
            $lastElemTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_last.tpl';
        }
        $home = ParseTemplateReturn($this->getValue('templates.breadcrumbs_home'), false, $this->template_debug);

        if (empty($home))
            $home = PHPShopText::a('/', __('�������'));

        // ����������� ���������� ������� ���������
        $this->getNavigationPath($id);

        if (is_array($this->navigation_array))
            $arrayPath = array_reverse($this->navigation_array);

        $currentIndex = 2;
        $i = 0;
        if (!empty($arrayPath) and is_array($arrayPath)) {
            foreach ($arrayPath as $v) {
                // ��������� thisCat, ����� � ������ ��������� �� ������ �������� ��������� � ������� shop.
                if ($this->PHPShopNav->getPath() == "shop")
                    $this->set('thisCat' . $i++, $v['id']);

                if ($name != $v['name']) {
                    $this->set('breadcrumbElemLink', '/' . $this->PHPShopNav->getPath() . '/CID_' . $v['id'] . '.html');
                    $this->set('breadcrumbElemTitle', $v['name']);
                    $this->set('breadcrumbElemIndex', $currentIndex++);
                    $dis .= ParseTemplateReturn($elementTemplate, true, $this->template_debug);
                }
            }
        }

        // ��������� thisCat, ����� � ������ ��������� �� ������ �������� ��������� � ������� shop.
        if (!empty($this->PHPShopNav) and $this->PHPShopNav->getPath() == "shop")
            $this->set('thisCat' . $i++, $this->PHPShopNav->getId());

        // ������ ������ ��������
        if (empty($dis) and is_array($title)) {
            $this->set('breadcrumbElemLink', $title['url']);
            $this->set('breadcrumbElemTitle', $title['name']);
            $this->set('breadcrumbElemIndex', $currentIndex++);

            $home .= ParseTemplateReturn($elementTemplate, true, $this->template_debug);
        }

        $this->set('breadcrumbElemTitle', $name);

        if (empty($name))
            $lastElemTemplate = null;

        $dis = $home . $dis . ParseTemplateReturn($lastElemTemplate, true, $this->template_debug);

        $this->set('breadCrumbs', $dis);

        // ��������� ��� javascript � shop.tpl
        $this->set('pageNameId', $id);
    }

    /**
     * ��������� ���� ��������� ���������
     */
    function header() {
        if ($this->getValue("cache.last_modified") == "true" and empty($_SESSION['cart'])) {

            // ��������� ������� ������� ����������� ���������� 200
            //header("HTTP/1.1 200");
            //header("Status: 200");
            @header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            @header("Pragma: no-cache");

            if (!empty($this->lastmodified)) {
                $updateDate = @gmdate("D, d M Y H:i:s", $this->lastmodified);
            } else {
                $updateDate = gmdate("D, d M Y H:i:s", (date("U") - 21600));
            }

            @header("Last-Modified: " . $updateDate . " GMT");
        }

        // HSTS
        if (empty($this->PHPShopSystem) or ! $this->PHPShopSystem instanceof PHPShopSystem) {
            $this->PHPShopSystem = new PHPShopSystem();
        }
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.hsts', 1))
            @header("Strict-Transport-Security:max-age=63072000");

        @header("X-Powered-By: PHPShop");
    }

    /**
     * ��������� ���������� ���������
     */
    function meta() {

        if (!empty($this->PHPShopSystem)) {

            if (!empty($this->title))
                $this->set('pageTitl', strip_tags($this->title));
            else
                $this->set('pageTitl', $this->PHPShopSystem->getValue("title"));

            if (!empty($this->description))
                $this->set('pageDesc', str_replace('"', "", strip_tags($this->description)));
            else
                $this->set('pageDesc', $this->PHPShopSystem->getValue("descrip"));

            if (!empty($this->keywords))
                $this->set('pageKeyw', str_replace('"', "", strip_tags($this->keywords)));
            else
                $this->set('pageKeyw', $this->PHPShopSystem->getValue("keywords"));
        }


        // ��������� ������� ������ ���� �� ���������
        if ($this->get('breadCrumbs') == '') {
            if (strstr($this->title, '-'))
                $title = explode("-", $this->title);
            else
                $title[0] = $this->title;
            $this->navigation(false, $title[0]);
        }
    }

    /**
     * �������� �������
     */
    function loadAction() {
        $this->setAction();
        $this->Compile();
    }

    /**
     * ������ ������ ������
     * @param array $select ����� ������� �� ��� �������
     * @param array $where ��������� ������� �������
     * @param array $order ��������� ���������� ������ ��� ������
     * @return array
     */
    function getListInfoItem($select = false, $where = false, $order = false, $class_name = false, $function_name = false, $sql = false) {
        $this->ListInfoItems = null;
        $this->where = $where;

        // ��������� ������ ��������
        if (!PHPShopSecurity::true_num($this->page))
            return $this->setError404();

        if (empty($this->page)) {
            $num_ot = 0;
            $num_do = $this->num_row;
        } else {
            $num_ot = $this->num_row * ($this->page - 1);
            $num_do = $this->num_row;
        }

        $option = array('limit' => intval($num_ot) . ',' . intval($num_do));

        $this->set('productFound', $this->getValue('lang.found_of_products'));
        $this->set('productNumOnPage', $this->getValue('lang.row_on_page'));
        $this->set('productPage', $this->getValue('lang.page_now'));

        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;

        if (!empty($sql)) {
            $this->PHPShopOrm->sql = 'select *, (price_n - price) as discount from ' . $this->objBase . ' where ' . $sql . ' limit ' . $option['limit'];
        }

        return $this->PHPShopOrm->select($select, $where, $order, $option, $class_name, $function_name);
    }

    function setPaginator($count = null, $sql = null) {

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
        $SQL = null;

        // ������� �� ���������� WHERE
        $nWhere = 1;
        if (is_array($this->where)) {
            $SQL .= ' where ';
            foreach ($this->where as $pole => $value) {
                $SQL .= $pole . $value;
                if ($nWhere < count($this->where))
                    $SQL .= $this->PHPShopOrm->Option['where'];
                $nWhere++;
            }
        }

        // ����� �������
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $result = $this->PHPShopOrm->query("select COUNT('id') as count from " . $this->objBase . $SQL);
        $row = mysqli_fetch_array($result);
        $this->num_page = $row['count'];

        $i = 1;

        // ���-�� ������� � ���������
        $num = ceil($this->num_page / $this->num_row);
        $this->max_page = $num;

        // 404 ������ ��� ��������� ���������
        if ($this->page > $this->num_page) {
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
                        $this->set("paginLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
                        $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $this->set("paginLink", $this->objPath . $i . '.html' . $sort);
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
                $this->set("previousLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("previousLink", $this->objPath . ($p_do) . '.html' . $sort);


            // ������� ����� ������ �������� CID_X_0.html
            if ($p_to == 0)
                $this->set("nextLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("nextLink", $this->objPath . ($p_to) . '.html' . $sort);

            // ��������� ���������� �������������
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('productPageNav', $nav);

            // �������� ������ � ����� �������
            $this->setHook(__CLASS__, __FUNCTION__, $nav, 'END');
        }
    }

    /**
     * ������ ���������� ��������
     * @param array $select ����� ������� �� ��� �������
     * @param array $where ��������� ������� �������
     * @param array $order ��������� ���������� ������ ��� ������
     * @return array
     */
    function getFullInfoItem($select, $where, $class_name = false, $function_name = false) {
        $result = $this->PHPShopOrm->select($select, $where, false, array('limit' => '1'), $class_name, $function_name);
        return $result;
    }

    /**
     * ���������� ������ � ����� �������
     * @param string $template ������ ��� ��������
     * @param bool $mod ������ � ������
     * @param array ����� ������ � �������
     */
    function addToTemplate($template, $mod = false, $replace = null) {
        if ($mod)
            $template_file = $template;
        else
            $template_file = $this->getValue('dir.templates') . chr(47) . $_SESSION['skin'] . chr(47) . $template;
        if (is_file($template_file)) {
            $dis = ParseTemplateReturn($template, $mod, $this->template_debug);

            // ������ � �������
            if (is_array($replace)) {
                foreach ($replace as $key => $val)
                    $dis = str_replace($key, $val, $dis);
            }

            $this->ListInfoItems .= $dis;

            $this->set('pageContent', $this->ListInfoItems);
        } else
            $this->setError("addToTemplate", $template_file);
    }

    /**
     * ���������� ������
     * @param string $content ����������
     * @param bool $list [1] - ���������� � ������ ������, [0] - ���������� � ����� ���������� ������
     */
    function add($content, $list = false) {
        if ($list)
            $this->ListInfoItems .= $content;
        else
            $this->Disp .= $content;
    }

    /**
     * ������� ������� � ���������� � ����� ���������� ������
     * @param string $template ��� �������
     * @param bool $mod ������ � ������
     * @param array $replace ����� ������ � �������
     */
    function parseTemplate($template, $mod = false, $replace = null) {
        $this->set('productPageDis', $this->ListInfoItems);
        $dis = ParseTemplateReturn($template, $mod, $this->template_debug);

        // ������ � �������
        if (is_array($replace)) {
            foreach ($replace as $key => $val)
                $dis = str_replace($key, $val, $dis);
        }

        $this->Disp = $dis;
    }

    /**
     * ��������� �� ������
     * @param string $name ��� �������
     * @param string $action ���������
     */
    function setError($name, $action) {
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
            <div class="alert alert-danger alert-dismissible" id="debug-message" role="alert" style="margin:10px">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong><span class="glyphicon glyphicon-alert"></span> ' . $name . '()</strong> ' . $action . '
</div>';
    }

    /**
     * ���������� ��������
     */
    function Compile() {

        // ���������� ������
        $this->set('DispShop', $this->Disp, false);

        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // ����
        $this->meta();

        // ���� �����������
        $this->header();

        // ������ ����� �����������
        writeLangFile();

        /**
         * �������� ������ 
         * ���� ������ �� ���������� ������ ����������, �� ����� ����������� ����� � �������� str_replace. 
         * ����� $obj->Disp ��� $obj->get('DispShop');
         */
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'END');
        if ($hook) {
            return $hook;
        } else {
            // ����� � ������
            ParseTemplate($this->getValue($this->template));
        }

        // ������� ��������� ���������� �������� [off]
        $this->garbage();
    }

    /**
     * �������� ���������� ������������� ��� ��������
     * @param mixid $name ��� [������]
     * @param mixed $value ��������
     * @param bool $flag [1] - ��������, [0] - ����������
     */
    function set($var, $value, $flag = false) {

        if (!is_array($var))
            $name_array[] = $var;
        else
            $name_array = $var;

        foreach ($name_array as $name) {

            if ($flag and ! empty($this->SysValue['other'][$name]))
                $this->SysValue['other'][$name] .= $value;
            else
                $this->SysValue['other'][$name] = $value;
        }
    }

    /**
     * ������ ���������� �������������
     * @param string $name
     * @return string
     */
    function get($name) {
        if (isset($this->SysValue['other'][$name]))
            return $this->SysValue['other'][$name];
    }

    /**
     * ������ ��������� ����������
     * @param string $param ������.��� ����������
     * @return mixed
     */
    function getValue($param) {
        $param = explode(".", $param);

        if (count($param) > 2 and ! empty($this->SysValue[$param[0]][$param[1]][$param[2]]))
            return $this->SysValue[$param[0]][$param[1]][$param[2]];

        if (!empty($this->SysValue[$param[0]][$param[1]]))
            return $this->SysValue[$param[0]][$param[1]];
    }

    /**
     * ��������� ��������� ����������
     * @param string $param ������.��� ����������
     * @param mixed $value �������� ���������
     */
    function setValue($param, $value) {
        $param = explode(".", $param);
        if ($param[0] == "var")
            $param[0] = "other";
        $this->SysValue[$param[0]][$param[1]] = $value;
    }

    /**
     * ���������� ������ ��������� ���������� POST � GET
     */
    function setAction() {

        if (is_array($this->action)) {
            foreach ($this->action as $k => $v) {

                switch ($k) {

                    // ����� POST
                    case("post"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_POST[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if (!empty($_POST[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }
                        break;

                    // ����� GET
                    case("get"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_GET[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if (!empty($_GET[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;

                    // ����� NAME
                    case("name"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if ($this->PHPShopNav->getName() == $function and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if ($this->PHPShopNav->getName() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;


                    // ����� NAV
                    case("nav"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function) {
                                if ($this->PHPShopNav->getNav() == $function and $this->isAction($function)) {
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                                    $call_user_func = true;
                                }
                            }
                            if (empty($call_user_func)) {
                                if ($this->isAction('index')) {

                                    // ������ �� ����� ������� /page/page/page/****
                                    if ($this->PHPShopNav->getNav() and ! $this->empty_index_action)
                                        $this->setError404();
                                    else
                                        call_user_func(array(&$this, $this->action_prefix . 'index'));
                                } else
                                    $this->setError($this->action_prefix . "index", "����� �� ����������");
                            }
                        } else {
                            // ���� ���� �����
                            if (@$this->PHPShopNav and @ $this->PHPShopNav->getNav() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                            elseif ($this->isAction('index')) {

                                // ������ �� ����� ������� /page/page/page/****
                                if (@$this->PHPShopNav->getNav() and ! $this->empty_index_action)
                                    $this->setError404();
                                else
                                    call_user_func(array(&$this, $this->action_prefix . 'index'));
                            }
                            elseif (!empty($this->PHPShopNav))
                                $this->setError($this->action_prefix . "phpshop" . $this->PHPShopNav->getPath() . "->index", "����� �� ����������");
                        }

                        break;
                }
            }
        } else
            $this->setError("action", "������ ��������� �������");
    }

    /**
     * �������� ������
     * @param string $method_name ��� ������
     * @return bool
     */
    function isAction($method_name) {
        if (method_exists($this, $this->action_prefix . $method_name))
            return true;
    }

    /**
     * �������� ������
     * @param string $method_name  ��� ������
     */
    function waitAction($method_name) {
        if (!empty($_REQUEST[$method_name]) and $this->isAction($method_name))
            call_user_func(array(&$this, $this->action_prefix . $method_name));
    }

    /**
     * ��������� ������ 404
     */
    function setError404() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        // ����
        $this->title = __("������") . " 404  - " . $this->PHPShopSystem->getValue("name");

        // ��������� ������
        @header("HTTP/1.0 404 Not Found");
        @header("Status: 404 Not Found");

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.error_page_forma'));
    }

    /**
     * ����������� ������� �� ������ ����
     * @param string $class_name ��� ������
     * @param string $function_name ��� �������
     * @param array $function_row ������ ������������� ������ �� �������
     * @param string $path ��� �������
     * @return mixed
     */
    function doLoadFunction($class_name, $function_name, $function_row = false, $path = false) {

        if (empty($path))
            $path = $GLOBALS['SysValue']['nav']['path'];

        $function_path = './phpshop/core/' . $path . '.core/' . $function_name . '.php';
        if (is_file($function_path)) {
            include_once($function_path);
            if (function_exists($function_name)) {
                return call_user_func_array($function_name, array(&$this, $function_row));
            }
        }
    }

    /**
     * ����� ��������� ��������� �� ����� [config.ini]
     * @param string $str ���� ��������� �������
     * @return string
     */
    function lang($str) {
        if ($this->SysValue['lang'][$str])
            return $this->SysValue['lang'][$str];
        else
            return $str;
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
        $this->memory_clean();
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
     * ������ ������ �� �������
     * @param bool $clean_now �������������� ������
     */
    function memory_clean($clean_now = false) {
        if (!empty($_SESSION['Memory'])) {
            if (!empty($clean_now))
                unset($_SESSION['Memory'][__CLASS__]);
            elseif (@$_SESSION['Memory'][__CLASS__]['time'] < (time() - 60 * 10))
                unset($_SESSION['Memory'][__CLASS__]);
        }
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
        if (!empty($this->PHPShopModules))
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
        else
            return false;
    }

    /**
     * ���������� HTML ���������� �������
     * @param string $class_name ��� ������
     */
    function setHtmlOption($class_name) {
        if (!empty($GLOBALS['SysValue']['html'][strtolower($class_name)]))
            $this->cell_type = $GLOBALS['SysValue']['html'][strtolower($class_name)];
    }

    /**
     * ���������
     * @param string $title ���������
     * @param string $content ����������
     * @return string
     */
    function message($title, $content) {
        $message = PHPShopText::b(PHPShopText::notice($title, false, '14px')) . PHPShopText::br();
        $message .= PHPShopText::message($content, false, '12px', 'black');
        return $message;
    }

    /**
     * ������� ��������� ����������
     */
    function garbage() {
        if ($this->garbage_enabled) {
            timer('start', 'Garbage');
            unset($this->SysValue['other']);
            timer('end', 'Garbage');
        }
    }

}

?>