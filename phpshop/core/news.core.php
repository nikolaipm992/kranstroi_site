<?php

/**
 * ���������� ��������
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopCore
 */
class PHPShopNews extends PHPShopCore {

    /**
     * ����� �������
     * @var bool
     */
    var $debug = false;
    var $empty_index_action = true;
    var $odnootip_cell_center = 2;
    var $odnootip_cell_block = 1;

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['news'];

        // ���� ��� ���������
        $this->objPath = "/news/news_";

        // ������ �������
        $this->action = array("nav" => array("index", "ID"));
        parent::__construct();

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['news'];
    }

    /**
     * ���������� ������
     * @param array $row ������ ������
     */
    function odnotip($row) {
        global $PHPShopProductIconElements;

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
        if (is_array($odnotip))
            foreach ($odnotip as $value) {
                if (!empty($value))
                    $odnotipList.=' id=' . trim($value) . ' OR';
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
     * ����� �� ���������
     */
    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $where['datau'] = '<'.time();

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $this->dataArray = parent::getListInfoItem(array('*'), $where, array('order' => 'datau DESC'));

        // 404
        if (!isset($this->dataArray))
            return $this->setError404();

        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {

                // ���������� ����������
                $this->set('newsId', $row['id']);
                $this->set('newsData', $row['datas']);
                $this->set('newsZag', $row['zag']);
                $this->set('newsKratko', $row['kratko']);
                $this->set('newsIcon', $row['icon']);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ���������� ������
                $this->addToTemplate($this->getValue('templates.main_news_forma'));
            }

        // ���������
        $this->setPaginator();

        // ����
        $this->title = __("�������") . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = __('�������') . '  ' . $this->PHPShopSystem->getValue("name");
        $this->keywords = __('�������') . ', ' . $this->PHPShopSystem->getValue("name");

        $page = $this->PHPShopNav->getId();
        if ($page > 1) {
            $this->description.= ' ����� ' . $page;
            $this->title.=' - �������� ' . $page;
        }

        // ��������� ������� ������
        $this->navigation(false, __("�������"));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // ���������� ������
        return $this->parseTemplate($this->getValue('templates.news_page_list'));
    }

    /**
     * ����� ���������� �������� �� ���� �� ���������
     */
    function timestamp() {

        if (PHPShopSecurity::true_num($_GET['timestamp'])) {
            $year = date("Y", $_GET['timestamp']);
            $month = date("m", $_GET['timestamp']);
            $day = date("d", $_GET['timestamp']);
            $timestampstart = intval($_GET['timestamp']);
            $timestampend = mktime(23, 59, 59, $month, $day, $year);

            // ������� ������
            $this->PHPShopOrm->sql = 'select * from ' . $this->objBase . ' where datau>=' . $timestampstart . ' AND datau<=' . $timestampend . ' order by datau desc';
            $this->dataArray = $this->PHPShopOrm->select();

            // 404
            if (!isset($this->dataArray))
                return $this->setError404();

            if (is_array($this->dataArray))
                foreach ($this->dataArray as $row) {

                    // ���������� ����������
                    $this->set('newsId', $row['id']);
                    $this->set('newsData', $row['datas']);
                    $this->set('newsZag', $row['zag']);
                    $this->set('newsKratko', $row['kratko']);

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');


                    // ���������� ������
                    $this->addToTemplate($this->getValue('templates.main_news_forma'));
                }

            // ����
            $this->title = "������� - " . $this->PHPShopSystem->getValue("name");

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

            // ���������� ������
            $this->parseTemplate($this->getValue('templates.news_page_list'));
        } else {
            $this->setError404();
        }
    }

    /**
     * ����� ������� ��������� ���������� ��� ������� ���������� ��������� ID
     * @return string
     */
    function ID() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // ������������
        if (!PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            return $this->setError404();

        $where['id'] = '='.$this->PHPShopNav->getId();
        $where['datau'] = '<'.time();

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $row = parent::getFullInfoItem(array('*'), $where);

        // 404
        if (!isset($row))
            return $this->setError404();

        // ���������� ������
        $this->odnotip($row);

        // ���������� ���������
        $this->set('newsData', $row['datas']);
        $this->set('newsZag', $row['zag']);

        if(empty($row['podrob'])){
            $row['podrob'] = $row['kratko'];
            $row['kratko']=null;
        }
        
        $this->set('newsKratko', $row['kratko']);
        $this->set('newsPodrob', Parser($row['podrob']));
        $this->set('newsIcon', $row['icon']);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

        // ���������� ������
        $this->addToTemplate($this->getValue('templates.main_news_forma_full'));

        // ����
        $this->title = strip_tags($row['zag']) . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = strip_tags($row['kratko']);
        $this->lastmodified = PHPShopDate::GetUnixTime($row['datas']);

        // ��������� keywords
        include('./phpshop/lib/autokeyword/class.autokeyword.php');
        $this->keywords = callAutokeyword($row['kratko']);

        // ��������� ������� ������
        $this->navigation(false, null, array('name' => __('�������'), 'url' => '/news/'));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.news_page_full'));
    }
}

?>