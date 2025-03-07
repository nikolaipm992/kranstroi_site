<?php

/**
 * ���������� �������� ��������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopTest
 */
class PHPShopCatalog extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * ����� �� ���������
     */
    function index() {
        global $PHPShopShopCatalogElement;

        // ����
        $this->title = __('�������').' - ' . $this->PHPShopSystem->getValue("name");
        $this->description = __('�������').' ' . $this->PHPShopSystem->getValue("name");
        
        // ���������� ���������
        $this->set('catalogList', $this->catalog());
        $this->set('pageTitle', __('�������'));
        $this->set('catalogName', __('�������'));
        
        // ���������� ������
        $this->parseTemplate($this->getValue('templates.catalog_info_forma'));
    }

    /**
     * ������� ��������� � ��������
     * @return string
     */
    function catalog() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $dis = null;

        // �� �������� ������� ��������
        $where['skin_enabled'] = "!='1' and parent_to=0";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $categories = $PHPShopOrm->getList(['*'],$where,['order' => 'num,name']);
        if (is_array($categories))
            foreach ($categories as $category) {

                $this->set('catalogId', $category['id']);
                $this->set('catalogTitle', $category['name']);
                $this->set('catalogName', $category['name']);
                $this->set('catalogIcon', $this->setImage($category['icon']));
                $this->set('catalogDescription', $category['content']);

                $dis .= ParseTemplateReturn("catalog/catalog_list_forma.tpl");

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $category, 'END');
            }

        return $dis;
    }
    
    /**
     * ��������� webp
     * @param string $image ��� �����
     * @return string
     */
    function setImage($image) {
        global $_classPath;

        if (!empty($image)) {

            // �������������� webp -> jpg ��� iOS < 14
            if (PHPShopSecurity::getExt($image) == 'webp') {
                if (defined('isMobil') and defined('isIOS')) {

                    if (!class_exists('PHPThumb'))
                        include_once($_classPath . 'lib/thumb/phpthumb.php');

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                        $thumb->setFormat('STRING');
                        $image = 'data:image/jpg;base64, ' . base64_encode($thumb->getImageAsString('webp'));
                    }
                }
            }
            // �������������� � webp
            elseif ($this->webp) {

                if (!class_exists('PHPThumb'))
                    include_once($_classPath . 'lib/thumb/phpthumb.php');

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                    $thumb->setFormat('WEBP');
                    $image = 'data:image/webp;base64, ' . base64_encode($thumb->getImageAsString(PHPShopSecurity::getExt($image)));
                }
            }
        }

        return $image;
    }


}
?>