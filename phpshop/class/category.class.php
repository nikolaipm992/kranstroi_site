<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
    require_once(dirname(__FILE__) . "/array.class.php");
}

/**
 * ��������� �������
 * ���������� ������ � �����������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopObj
 */
class PHPShopCategory extends PHPShopObj {

    private $categories = [];
    private $cachedCategories = [];
    public $search_id;
    public $found;
    public $search;

    /**
     * �����������
     * @param int $objID �� ���������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['categories'];
        $this->cache = false;
        $this->debug = false;
        parent::__construct('id');
    }

    /**
     * ������ ����� ���������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ������ �������� ���������
     * @return string
     */
    function getContent() {
        if (empty($this->cache))
            return parent::getParam("content");
        else {
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $data = $PHPShopOrm->select(array('content'), array('id' => '=' . intval($this->objID)), false, array('limit' => 1));
            return $data['content'];
        }
    }

    /**
     * �������� �� �������������
     * @return bool
     */
    function init() {
        $id = parent::getParam("id");
        if (!empty($id))
            return true;
    }

    /**
     * ������� ������������
     * @param int $depth ������� ������
     * @param array $select ������ ������ �������
     * @param bool dop_cat ��������� �������������� ��������
     * @param string  $search ������ ���� �������� �������/����������
     * @param bool $revers �������� ����� ��������� �����
     * @return array
     */
    public function getChildrenCategories($depth = 2, $select = array("id", "name", "parent_to", "skin_enabled", "parent_title", "icon", "dop_cat", "vid", "num_row", "tile"), $dop_cat = true, $search = false, $revers = false) {

        if (!empty($search)) {
            $this->search = explode("/", $search);
            $this->found = 0;
            $this->search_id = 0;
        }

        if (!empty($revers)) {
            $this->parent = $this->getParam('parent_to');
            $this->search_str[] = $this->getParam('name');
        }

        return $this->recursive($this->objID, $depth, $select, $dop_cat, $revers);
    }

    private function recursive($categoryId, $depth, $select, $dop_cat, $revers) {

        if ($revers) {
            $key = 'id';
            $categoryId = $this->parent;
        } else
            $key = 'parent_to';

        if (!empty($dop_cat))
            $where = array($key . '=' => $categoryId . " or dop_cat LIKE '%#" . (int) $categoryId . "#%'");
        else
            $where = array($key . '=' => $categoryId);


        $PHPShopCategoryArray = new PHPShopCategoryArray($where, $select);
        $PHPShopCategoryArray->order = array('order' => 'num, name');
        $PHPShopCategoryArray->setArray();

        $childrens = $PHPShopCategoryArray->getArray();

        if ($this->getLevel($categoryId, $this->objID) >= $depth) {
            return $this->categories;
        }

        if (!is_array($childrens) || count($childrens) === 0) {
            return $this->categories;
        }

        foreach ($childrens as $category) {
            // ����� �������� ��
            if (is_array($this->search)) {
                if ($category['name'] == $this->search[$this->found] and $category['parent_to'] == $this->search_id) {
                    $this->search_id = $category['id'];
                    $this->found++;
                }
            }

            // ��������� ������ ���� ���������
            if ($revers) {
                $this->parent = $category['parent_to'];
                $this->search_str[] = $category['name'];
                $this->categories[$category['parent_to']] = $category;
                $this->recursive($category['parent_to'], $depth, $select, $dop_cat, $revers);
            } else {
                $this->categories[$category['id']] = $category;
                $this->recursive($category['id'], $depth, $select, $dop_cat, $revers);
            }
        }

        return $this->categories;
    }

    public function getLevel($categoryId, $rootId = 0, $level = 1)
    {
        if(count($this->cachedCategories) === 0) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $this->cachedCategories = array_column($orm->getList(['id', 'parent_to'], false, false, ['limit' => 100000000]), 'parent_to', 'id');
        }

        if((int) $categoryId === (int) $rootId) {
            return 1;
        }

        $level++;

        if((int) $this->cachedCategories[$categoryId] === (int) $rootId) {
            return $level;
        }

        // ������ �� ������������ ���� ������ ������������ �������
        if(!isset($this->cachedCategories[$categoryId])) {
            return 1;
        }

        return $this->getLevel((int) $this->cachedCategories[$categoryId], $rootId, $level);
    }
}

/**
 * ��������
 * ���������� ������ � ���������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopObj
 */
class PHPShopPages extends PHPShopObj {

    /**
     * �����������
     * @param int $objID �� ��������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['table_name11'];
        parent::__construct();
    }

    /**
     * ������ ����� ��������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ������ ����������
     * @return string
     */
    function getContent() {
        return parent::getParam("content");
    }

}

/**
 * ��������� �������
 * ���������� ������ � �����������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopObj
 */
class PHPShopPageCategory extends PHPShopObj {

    /**
     * �����������
     * @param int $objID �� ���������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['page_categories'];
        $this->cache = true;
        $this->debug = false;
        parent::__construct('id');
    }

    /**
     * ������ ����� ���������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ������ �������� ���������
     * @return string
     */
    function getContent() {
        return parent::getParam("content");
    }

    /**
     * �������� �� �������������
     * @return bool
     */
    function init() {
        $id = parent::getParam("id");
        if (!empty($id))
            return true;
    }

}

/**
 * ������ ��������� �������
 * ���������� ������ � �����������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopArray
 */
class PHPShopCategoryArray extends PHPShopArray {

    /**
     * �����������
     * @param string $sql SQL ������� �������
     */
    function __construct($sql = false, $select = ["id", "name", "parent_to", "skin_enabled", "parent_title", "icon", "dop_cat", "vid", "num_row", "tile","color"]) {

        // ����������
        if (defined("HostID"))
            $sql['servers'] = " REGEXP 'i" . HostID . "i'";

        // ��������������� ������
        //$this->objArray = new SplFixedArray(count($data)+1);

        $this->objSQL = $sql;
        $this->cache = false;
        $this->debug = false;
        $this->ignor = false;
        $this->order = ['order' => 'num,name'];
        $this->objBase = $GLOBALS['SysValue']['base']['categories'];
        parent::__construct(...$select);
    }

}

/**
 * ��������� �����������
 * ���������� ������ � ���������� ����������� 
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopObj
 */
class PHPShopPhotoCategory extends PHPShopObj {

    /**
     * �����������
     * @param int $objID �� ���������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['photo_categories'];
        $this->cache = true;
        $this->debug = false;
        parent::__construct('id');
    }

    /**
     * ������ ����� ���������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ������ �������� ���������
     * @return string
     */
    function getContent() {
        return parent::getParam("content");
    }

    /**
     * �������� �� �������������
     * @return bool
     */
    function init() {
        $id = parent::getParam("id");
        if (!empty($id))
            return true;
    }

}

/**
 * ������ ��������� �����������
 * ���������� ������ � ����������� �����������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopArray
 */
class PHPShopPhotoCategoryArray extends PHPShopArray {

    /**
     * �����������
     * @param string $sql SQL ������� �������
     */
    function __construct($sql = false) {
        $this->objSQL = $sql;
        $this->cache = false;
        $this->order = array('order' => 'num');
        $this->objBase = $GLOBALS['SysValue']['base']['photo_categories'];
        parent::__construct("id", "name", "parent_to", "link");
    }

}

?>