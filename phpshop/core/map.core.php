<?php

/**
 * ���������� ����� �����
 * @author PHPShop Software
 * @version 1.6
 * @package PHPShopCore
 */
class PHPShopMap extends PHPShopCore {
    
    var $empty_index_action = false;

    /**
     * �����������
     */
    function __construct() {

        $this->debug = false;
        $this->memory = true;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();
    }

    /**
     * ��� ������
     * @param int $val �� ��������
     * @param string $dir �������� ���������� [category|category_page]
     * @return string 
     */
    function seourl($val, $dir, $name = false) {

        // �������� ������, ��������� � ������ ������� ������ ��� ����������� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $hook = $this->setHook(__CLASS__, __FUNCTION__, array('val' => $val, 'dir' => $dir, 'name' => $name));
            if ($hook) {
                return $hook;
            }
            else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        switch ($dir) {
            case 'category':
                $link = '/shop/CID_' . $val . '.html';
                break;

            case 'category_page':
                $link = '/page/CID_' . $val . '.html';
                break;
        }

        return $link;
    }

    /**
     * ������������ �������
     * @param int $cat ID ���������
     * @return string
     */
    function subcategory($cat) {
        $dis = null;
        if (!empty($this->ParentArray[$cat]) and is_array($this->ParentArray[$cat])) {
            foreach ($this->ParentArray[$cat] as $val) {
                $sup = $this->subcategory($val);
                $name = $this->PHPShopCategoryArray->getParam($val . '.name');
                $vid = $this->PHPShopCategoryArray->getParam($val . '.skin_enabled');
                if (empty($vid)) {
                    if (empty($sup)) {

                        $dis.=PHPShopText::li($name, $this->seourl($val, 'category', $name));
                    } else {
                        $dis.=PHPShopText::li(PHPShopText::b($name));
                        $dis.=$sup;
                    }
                }
            }
            return PHPShopText::ul($dis);
        }
    }

    /**
     * ��������� �������
     */
    function category() {
        
        $where['skin_enabled'] = "!='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $this->PHPShopCategoryArray = new PHPShopCategoryArray($where);
        $this->ParentArray = $this->PHPShopCategoryArray->getKey('parent_to.id', true);
        $dis = $sup = null;
        if (is_array($this->ParentArray[0])) {
            foreach ($this->ParentArray[0] as $val) {
                $vid = $this->PHPShopCategoryArray->getParam($val . '.skin_enabled');
                if (empty($vid)) {
                    $name = $this->PHPShopCategoryArray->getParam($val . '.name');
                    $sup = $this->subcategory($val);
                    if (!empty($sup))
                        $dis.=PHPShopText::p(PHPShopText::b($name) . $sup);
                    else
                        $dis.=PHPShopText::p(PHPShopText::a($this->seourl($val, 'category', $name), PHPShopText::b($name), $name));
                }
            }
        }
        $this->add($dis, true);
    }

    /**
     * ������������ �������
     * @param int $cat ID ���������
     * @return string
     */
    function subcategory_page($cat) {
        $dis = null;
        if (!empty($this->ParentPageArray[$cat]) and is_array($this->ParentPageArray[$cat])) {
            foreach ($this->ParentPageArray[$cat] as $val) {
                $vid = $this->PHPShopCategoryArray->getParam($val . '.vid');
                if (empty($vid)) {
                    $sup = $this->subcategory_page($val);
                    $name = $this->PHPShopPageCategoryArray->getParam($val . '.name');
                    if (empty($sup)) {
                        $dis.=PHPShopText::li($name, $this->seourl($val, 'category_page', $name));
                    } else {
                        $dis.=PHPShopText::li(PHPShopText::b($name));
                        $dis.=$sup;
                    }
                }
            }
            return PHPShopText::ul($dis);
        }
    }

    /**
     * ��������� �������
     */
    function category_page() {
        PHPShopObj::loadClass('page');
        $this->PHPShopPageCategoryArray = new PHPShopPageCategoryArray();
        $this->ParentPageArray = $this->PHPShopPageCategoryArray->getKey('parent_to.id', true);
        $dis = null;
        if (is_array($this->ParentPageArray[0])) {
            foreach ($this->ParentPageArray[0] as $val) {
                $sup = $this->subcategory_page($val);
                $name = $this->PHPShopPageCategoryArray->getParam($val . '.name');
                if (empty($sup)) {
                    $dis.=PHPShopText::p(PHPShopText::a($this->seourl($val, 'category_page', $name), PHPShopText::b($name), $name));
                } else {
                    $dis.=PHPShopText::b($name);
                    $dis.=$sup;
                }
            }
        }
        $this->add(PHPShopText::ul($dis), true);
    }

    /**
     * �����
     */
    function special() {
        $special = PHPShopText::ul(PHPShopText::li(__('�������'), '/newtip/') . PHPShopText::li(__('���������������'), '/spec/') .
                        PHPShopText::li(__('����������'), '/newprice/'));
        $this->add(PHPShopText::b(__('�����')) . $special, true);
    }

    /**
     * �������
     */
    function news() {
        $this->add(PHPShopText::b(PHPShopText::a('/news/', __('�������'))), true);
    }

    /**
     * ���-�� �������
     * @return int
     */
    function product() {
        $data = $this->PHPShopOrm->select(array('COUNT(id) as total'), array('enabled' => '="1"'));
        if (is_array($data))
            $total = $data['total'];
        else
            $total = 0;

        return $total;
    }

    /**
     * �����
     */
    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))        
            return true;

        // ��������� �������
        $this->category();

        // ��������
        $this->category_page();

        // �����
        if (!$this->get('hideSite'))
        $this->special();

        // �������
        $this->news();

        // ����
        $this->title = __("����� �����") . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = __("����� �����") . " " . $this->PHPShopSystem->getValue("name");
        $this->keywords = __("����� �����") . ", " . $this->PHPShopSystem->getValue("name");

        $this->set('catalFound', $this->lang('found_of_catalogs'));
        $this->set('catalNum', $this->PHPShopCategoryArray->getNum());
        $this->set('producFound', $this->lang('found_of_products'));
        $this->set('productNum', $this->product());

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.map_page_list'));
    }

}

?>