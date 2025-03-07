<?php

/**
 * ���������� ���� �������
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopCore
 */
class PHPShopPhoto extends PHPShopCore {

    /**
     * @var Int  ���-�� ���� � �����
     */
    var $ilim = 4;
    var $empty_index_action = true;

    /**
     * �����������
     */
    function __construct() {

        // ���-�� ���� �� ��������
        $num_row = 30;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['photo'];

        // �������
        $this->debug = false;

        // ������ �������
        $this->action = array("nav" => "CID");

        // ������ ��� ��������� ������� ������
        $this->navigationArray = 'CatalogPhoto';

        // �� ��� ������� ������
        $this->navigationBase = 'base.photo_categories';
        parent::__construct();

        $this->page = $GLOBALS['SysValue']['nav']['page'];
        if (strlen($this->page) == 0)
            $this->page = 1;

        $this->num_row = $num_row;
    }

    /**
     * ����� �� ���������, ��������
     */
    function index() {

        // ����
        $this->category_name = __('�����������');
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("title");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");
        $this->ListCategory();
    }

    /**
     * ����� ������� ���������� ��� ������� ���������� ��������� CID
     */
    function CID() {

        // ID ���������
        $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
        $this->PHPShopPhotoCategory = new PHPShopPhotoCategory($this->category);
        $this->category_name = $this->PHPShopPhotoCategory->getName();
        if (empty($this->category_name))
            $this->category_name = __('�����������');

        if (empty($this->category)) {
            return $this->setError404();
        }

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(array('id,name'), array('parent_to' => "=" . (int) $this->category, 'enabled' => "='1'"), false, array('limit' => 1));

        // ���� ����
        if (empty($row['id'])) {

            $this->ListPhoto();
        }
        // ���� ��������
        else {

            $this->ListCategory();
        }
    }

    /**
     * ����� ������ ����
     */
    function ListPhoto() {
        $disp = '
                <link href="phpshop/lib/templates/photo/highslide/highslide.css" rel="stylesheet">
                <script src="phpshop/lib/templates/photo/highslide/highslide-p.js"></script>
                <script>
                    hs.registerOverlay({html: \'<div class="closebutton" onclick="return hs.close(this)" title="�������"></div>\', position: \'top right\', fade: 2});
                    hs.graphicsDir = \'phpshop/lib/templates/photo/highslide/graphics/\';
                    hs.wrapperClassName = \'borderless\';
                </script>
                ';

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'START');

        // ���� ��� ���������
        $this->objPath = '/photo/CID_' . $this->category . '_';

        // ������� ������
        $this->dataArray = parent::getListInfoItem(array('*'), array('category' => '=' . $this->category, 'enabled' => "='1'"), array('order' => 'num'));
        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {

                $name_s = str_replace(".", "s.", $row['name']);
                $this->set('photoIcon', $name_s);
                $this->set('photoInfo', $row['info']);
                $this->set('photoImg', $row['name']);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                if (PHPShopParser::checkFile('photo/photo_element_forma.tpl'))
                    $disp .= ParseTemplateReturn('photo/photo_element_forma.tpl');
                else
                    $disp .= ParseTemplateReturn('phpshop/lib/templates/photo/photo_element_forma.tpl', true);
            }
        // ���� ���� �������� ��������
        if (empty($this->LoadItems['CatalogPhoto'][$this->category]))
            $content = $this->PHPShopPhotoCategory->getContent();
        elseif (!empty($this->LoadItems['CatalogPhoto'][$this->category]['content_enabled']))
            $content = $this->PHPShopPhotoCategory->getContent();



        $this->set('pageContent', $content . $disp);
        $this->set('pageTitle', $this->category_name);

        // ���������
        $this->setPaginator();

        // ����
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("title");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");

        // ��������� ������� ������
        $this->navigation($row['parent_to'], $this->category_name);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * ����� ������ ��������� ����
     */
    function ListCategory() {

        // ������� ������
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;

        $dataArray = $PHPShopOrm->select(array('name', 'id'), array('parent_to' => '=' . (int) $this->category), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataArray))
            foreach ($dataArray as $row) {
                $dis .= PHPShopText::li($row['name'], "/photo/CID_" . $row['id'] . ".html");
            }

        // ���� ���� �������� ��������
        if (!empty($this->LoadItems['CatalogPhoto'][$this->category]['content_enabled']))
            $disp .= $this->PHPShopPhotoCategory->getContent();

        $disp .= PHPShopText::ul($dis);

        $this->set('isPage', true);
        $this->set('pageContent', Parser($disp));
        $this->set('pageTitle', $this->category_name);

        // ����
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $this->category_name . ", " . $this->PHPShopSystem->getValue("descrip");

        // ��������� ������� ������
        $this->navigation($this->category, $this->category_name);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray);

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

}
?>