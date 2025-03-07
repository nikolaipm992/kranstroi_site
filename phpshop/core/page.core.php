<?php

/**
 * ���������� �������
 * @author PHPShop Software
 * @version 1.9
 * @package PHPShopCore
 */
class PHPShopPage extends PHPShopCore {

    /**
     * ������� ��� ��������� ������� ������
     * @var string
     */
    var $navigationBase = 'base.page_categories';

    /**
     * ����� �������
     * @var bool
     */
    var $debug = false;
    var $odnootip_cell_center = 2;
    var $odnootip_cell_block = 1;

    /**
     * ���-�� ��������� ������
     * @var int 
     */
    var $limit = 3;
    var $odnotip_setka_num;

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['page'];

        // ������ �������
        $this->action = array("nav" => "CID");
        $this->empty_index_action = true;
        $this->cid_cat_with_foto_template = 'catalog/cid_page_category.tpl';

        // ���� ������ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $this->seourlpro_enabled = true;
        }

        parent::__construct();
    }

    function getAll() {

        // ����
        $title = __('����');
        $this->title = $title . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $title . ", " . $this->PHPShopSystem->getValue("name");

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('parent_to' => "=0"), array('order' => 'num,id desc'), array('limit' => 300));
        $dis = null;
        if (is_array($data)) {
            foreach ($data as $row) {

                if (empty($row['page_cat_seo_name']) or empty($this->seourlpro_enabled))
                    $url = 'CID_' . $row['id'];
                else
                    $url = $row['page_cat_seo_name'];

                // ���������� ����������
                $this->set('pageLink', $url);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // ���������� ������
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }
        }
        else 
            return $this->setError404();

        // ��������� ������� ������
        $this->navigation(0, $title);

        $this->set('pageContent', $dis);
        $this->set('pageTitle', $title);
        $this->set('pageIcon', $data['icon']);

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * ����� �� ���������, ����� ������ �� ��������
     * @return string
     */
    function index($link = false) {

        $hook_start = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook_start)
            return true;

        // ������������
        if (empty($link))
            $link = PHPShopSecurity::TotalClean($this->PHPShopNav->getName(true), 2);

        if (empty($link) and $this->PHPShopNav->objNav['truepath'] == '/page/')
            return $this->getAll();

        // �������� ������ ��� �������������
        if (isset($_SESSION['UsersId'])) {
            $sort = " and ((secure !='1') OR (secure ='1' AND secure_groups='') OR (secure ='1' AND secure_groups REGEXP 'i" . $_SESSION['UsersStatus'] . "-1i')) ";
        } else {
            $sort = " and (secure !='1') ";
        }

        // ����������
        if (defined("HostID")) {
            $sort .= " and servers REGEXP 'i" . HostID . "i'";
        } elseif (defined("HostMain"))
            $sort .= " and (servers = '' or servers REGEXP 'i1000i')";

        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->query("select * from " . $this->objBase . " where link='$link' and enabled='1' $sort limit 1");
        $row = mysqli_fetch_array($result);

        // ���������� �������� �� �����
        if ($row['category'] == 2000)
            return $this->setError404();
        elseif (empty($row['id']) or $link != $row['link'])
            return $this->setError404();

        $this->category = $row['category'];
        $this->PHPShopCategory = new PHPShopPageCategory($this->category);
        $this->category_name = $this->PHPShopCategory->getName();

        // ���������� ������
        $this->odnotip($row);

        // ���������� ����������
        $this->set('isPage', true);
        $this->set('pageContent', Parser(stripslashes($row['content'])));
        $this->set('pageTitle', $row['name']);
        $this->set('catalogCategory', $this->category_name);
        $this->set('catalogId', $this->category);
        $this->set('pageMainIcon', $row['icon']);
        $this->set('pageMainPreview', Parser(stripslashes($row['preview'])));
        $this->PHPShopNav->objNav['id'] = $row['id'];

        // �������� ���� �������
        $this->set('NavActive', $row['link']);

        // ����
        if (empty($row['title']))
            $title = $row['name'] . " - " . $this->PHPShopSystem->getValue("name");
        else
            $title = $row['title'];

        // OpenGraph
        $this->set('ogTitle', $row['name']);
        if (!empty($row['icon']))
            $this->set('ogImage', $row['icon']);
        $this->set('ogDescription', $row['description']);

        $this->title = $title;
        $this->description = $row['description'];
        $this->keywords = $row['keywords'];
        $this->lastmodified = $row['datas'];

        // ��������� ������� ������
        if($row['category'] == 1000 or empty($row['category'])) 
            $this->navigation($row['category'], $row['name']);
        else $this->navigation($row['category'], $row['name'], ['url' => '/page/', 'name' => __('����')]);

        // ��������� ������
        $this->set('pageLast', $this->getLast($link));

        // �������� ������
        $hook_end = $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
        if ($hook_end)
            return true;

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * ����� ������� ��������� ���������� ��� ������� ���������� ��������� CID
     */
    function CID() {

        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        // ID ���������
        $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
        $this->PHPShopCategory = new PHPShopPageCategory($this->category);
        $this->category_name = $this->PHPShopCategory->getName();

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(array('id,name'), array('parent_to' => "=" . $this->category), false, array('limit' => 1));

        // ���� ��������
        if (empty($row['id'])) {

            $this->ListPage();
        }
        // ���� ��������
        else {

            $this->ListCategory();
        }
    }

    /**
     * ����� ������ �������
     * @return string
     */
    function ListPage() {
        $dis = null;
        $lastmodified = 0;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // 404
        if (empty($this->category_name))
            return $this->setError404();

        $where = array('category' => '=' . $this->category, 'enabled' => "='1'");

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $dataArray = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array('limit' => 100));
        if (is_array($dataArray)) {

            foreach ($dataArray as $row) {

                // ������������ ���� ���������
                if ($row['datas'] > $lastmodified)
                    $lastmodified = $row['datas'];

                // ���������� ����������
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // ���������� ������
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }
        }

        $this->set('catContent', $this->PHPShopCategory->getContent());
        $this->set('catIcon', $this->PHPShopCategory->getParam('icon'));

        $this->set('pageContent', $dis);
        $this->set('pageTitle', $this->category_name);
        $this->set('pageIcon', $row['icon']);
        $this->set('pagePreview', Parser(stripslashes($row['preview'])));

        // ������ ������������ ���������
        $cat = $this->PHPShopCategory->getValue('parent_to');
        if (!empty($cat)) {
            $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
            $PHPShopOrm->cache = true;
            $PHPShopOrm->debug = $this->debug;
            $parent_category_row = $PHPShopOrm->select(array('id,name'), array('id' => '=' . $cat), false, array('limit' => 1), __FUNCTION__);
        } else {
            $parent_category_row['name'] = $this->lang('catalog');
        }
        $this->set('catalogCategory', $parent_category_row['name']);
        $this->set('catalogId', $cat);

        // ����
        if ($this->PHPShopCategory->getValue('title') != "")
            $this->title = $this->PHPShopCategory->getValue('title');
        else
            $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");

        if ($this->PHPShopCategory->getValue('description') != "")
            $this->description = $this->PHPShopCategory->getValue('description');

        if ($this->PHPShopCategory->getValue('keywords') != "")
            $this->description = $this->PHPShopCategory->getValue('keywords');

        $this->lastmodified = $lastmodified;

        // ��������� ������� ������
        $this->navigation($cat, $this->category_name, ['url' => '/page/', 'name' => __('����')]);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * ����� ������ ���������
     */
    function ListCategory() {
        $dis = null;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // 404
        if (empty($this->category_name))
            return $this->setError404();

        $where = array('parent_to' => '=' . $this->category);

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $dataArray = $PHPShopOrm->select(array('name', 'id'), $where, array('order' => 'num,id desc'), array('limit' => 100));
        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                if (empty($row['page_cat_seo_name']) or empty($this->seourlpro_enabled))
                    $url = '/page/CID_' . $row['id'] . '.html';
                else
                    $url = '/page/' . $row['page_cat_seo_name'] . '.html';

                // ���������� ����������
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // ���������� ������
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }


        // �������� ��������
        $this->set('catContent', $this->PHPShopCategory->getContent());
        $this->set('catIcon', $this->PHPShopCategory->getParam('icon'));
        $this->set('catName', $this->category_name);
        $this->set('catName', $this->category_name);


        $this->set('pageContent', $dis);
        $this->set('pageTitle', $this->category_name);

        // ����
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");

        // ��������� ������� ������
        $this->navigation($this->PHPShopCategory->getParam('parent_to'), $this->category_name, ['url' => '/page/', 'name' => __('����')]);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * ���������� ������
     * @param array $row ������ ������
     */
    function odnotip($row) {
        global $PHPShopProductIconElements;

        //$this->odnotip_setka_num = 2;
        $this->line = false;
        $this->template_odnotip = 'main_spec_forma_icon';

        // �������� ������ � ������ �������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'START');
        if ($hook)
            return true;

        $disp = null;
        $odnotipList = null;
        if (!empty($row['odnotip'])) {
            if (strpos($row['odnotip'], ','))
                $odnotip = explode(",", $row['odnotip']);
            elseif (is_numeric(trim($row['odnotip'])))
                $odnotip[] = trim($row['odnotip']);
        }

        // ������ ��� �������
        if (!empty($odnotip) and is_array($odnotip))
            foreach ($odnotip as $value) {
                if (!empty($value))
                    $odnotipList .= ' id=' . trim($value) . ' OR';
            }

        $odnotipList = substr($odnotipList, 0, strlen($odnotipList) - 2);

        // ����� �������� �������� �� ������
        if ($this->PHPShopSystem->getSerilizeParam('admoption.sklad_status') == 2)
            $chek_items = ' and items>0';
        else
            $chek_items = null;

        if (!empty($odnotipList)) {

            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $this->debug;
            $result = $PHPShopOrm->query("select * from " . $this->getValue('base.products') . " where (" . $odnotipList . ") " . $chek_items . " and  enabled='1' and parent_enabled='0' and sklad!='1' order by num");
            while ($product_row = mysqli_fetch_assoc($result))
                $data[] = $product_row;

            // ����� �������
            if (!empty($data) and is_array($data))
                $disp = $PHPShopProductIconElements->seamply_forma($data, $this->odnotip_setka_num, $this->template_odnotip, $this->line);
        }


        if (!empty($disp)) {
            // ������� � ����������� �����
            if (PHPShopParser::check($this->getValue('templates.main_product_odnotip_list'), 'productOdnotipList')) {
                $this->set('productOdnotipList', $disp);
                $this->set('productOdnotip', __('������������� ������'));
            } else {
                // ������� � ������ �������
                $this->set('specMainTitle', __('������������� ������'));
                $this->set('specMainIcon', $disp);
            }

            // �������� ������ � �������� �������
            $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

            $odnotipDisp = ParseTemplateReturn($this->getValue('templates.main_product_odnotip_list'));
            $this->set('odnotipDisp', $odnotipDisp);
        }
        // ������� ��������� �������
        else {
            $this->set('specMainIcon', $PHPShopProductIconElements->specMainIcon(true, $this->category));
        }

        // �������� ������ � ����� �������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }

    /**
     * ����� ��������� �������
     * @return string
     */
    function getLast($link) {
        $dis = null;


        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;


        $where = array('enabled' => "='1'", 'preview' => '!=""', 'link' => '!="' . $link . '"');

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['preview'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->select(array('link', 'name', 'icon', 'datas', 'preview'), $where, array('order' => 'datas DESC'), array("limit" => $this->limit));

        // �������� �� ��������� ������
        if ($this->limit > 1)
            $data = $result;
        else
            $data[] = $result;

        if (is_array($data))
            foreach ($data as $row) {

                // ���������� ����������
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // ���������� ������
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }

        return $dis;
    }

}

?>