<?php

/**
 * ���������� �������� �����
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopCore
 */
class PHPShopGbook extends PHPShopCore {
    
    var $empty_index_action = true;

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['gbook'];

        // ���� ��� ���������
        $this->objPath = "/gbook/gbook_";

        // �������
        $this->debug = false;

        // ������ �������
        $this->action = array("post" => "send_gb", "nav" => "ID", "get" => "add_forma");
        parent::__construct();

        // ���-�� ������� �� ��������
        if (!$this->num_row)
            $this->num_row = 10;

        // ��������� ������� ������
        $this->navigation(false, __('������'));
    }

    /**
     * ����� �� ���������, ����� �������
     */
    function index() {

        // ��������� � ������ ������
        if (!empty($_GET['write']))
            $this->set('Error', __("��������� ������� ���������. ����� ����� �������� ������ ����� �������� �����������"));
        
        $where['flag']="='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $this->dataArray = parent::getListInfoItem(array('*'), $where, array('order' => 'datas DESC'));

        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {

                // ������ �� ������
                if (!empty($row['mail']))
                    $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']), $row['name']);
                else
                    $d_mail = PHPShopText::b($row['name']);

                // ���������� ���������
                $this->set('gbookData', PHPShopDate::dataV($row['datas'], false));
                $this->set('gbookName', $row['name']);
                $this->set('gbookTema', $row['tema']);
                $this->set('gbookMail', $d_mail);
                $this->set('gbookOtsiv', $row['otsiv']);
                $this->set('gbookOtvet', $row['otvet']);
                $this->set('gbookId', $row['id']);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ���������� ������
                $this->addToTemplate($this->getValue('templates.main_gbook_forma'));
            }

        // ���������
        $this->setPaginator();

        // ����
        $this->title = __('������') . ' - ' . $this->PHPShopSystem->getValue("name");
        $this->description = __('������') . '  ' . $this->PHPShopSystem->getValue("name");
        $this->keywords = __('������') . ', ' . $this->PHPShopSystem->getValue("name");

        $page = $this->PHPShopNav->getId();
        if ($page > 1) {
            $this->description .= ' ����� ' . $page;
            $this->title .= ' - �������� ' . $page;
        }

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.gbook_page_list'));

        // ������ �� ����� �����
        $this->add($this->attachLink());
    }

    /**
     * ����� ������� ��������� ���������� ��� ������� ���������� ��������� ID
     * @return string
     */
    function ID() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        // ������������
        if (!PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            return $this->setError404();
        
        $where['id'] = '='.$this->PHPShopNav->getId();

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['id'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $row = parent::getFullInfoItem(array('*'), $where);

        // 404
        if (!isset($row))
            return $this->setError404();

        // ������ �� ������
        if (!empty($row['mail']))
            $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']));
        else
            $d_mail = PHPShopText::b($row['name']);

        // ���������� ����������
        $this->set('gbookData', PHPShopDate::dataV($row['datas']));
        $this->set('gbookName', $row['name']);
        $this->set('gbookTema', $row['tema']);
        $this->set('gbookMail', $d_mail);
        $this->set('gbookOtsiv', $row['otsiv']);
        $this->set('gbookOtvet', $row['otvet']);
        $this->set('gbookId', $row['id']);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

        // ���������� ������
        $this->addToTemplate($this->getValue('templates.main_gbook_forma'));

        // ����
        $this->title = $row['tema'] . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = strip_tags($row['otsiv']);
        $this->lastmodified = PHPShopDate::GetUnixTime($row['datas']);

        // ��������� keywords
        include('./phpshop/lib/autokeyword/class.autokeyword.php');
        $this->keywords = callAutokeyword($row['otsiv']);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.gbook_page_list'));
    }

    /**
     * ������ �� ����� �����
     * @return string
     */
    function attachLink() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        return PHPShopText::div(PHPShopText::a('/gbook/?add_forma=true', __('�������� �����')), 'center', 'padding:20px', 'gbook_add');
    }

    /**
     * ����� �����
     */
    function add_forma() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->parseTemplate($this->getValue('templates.gbook_forma_otsiv'));
    }

    /**
     * �������� �����
     * @param array $option ��������� �������� [url|captcha|referer]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security($option);
    }

    /**
     * ����� ������ ������ ��� ��������� $_POST[send_gb]
     */
    function send_gb() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        if ($this->security()) {
            $this->write();
            header("Location: ../gbook/?write=ok");
        } else {
            $this->set('Error', __("������ �����, ��������� ������� ����� �����"));

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $_POST, 'END');

            $this->parseTemplate($this->getValue('templates.gbook_forma_otsiv'));
        }
    }

    /**
     * ������ ������ � ����
     */
    function write() {

        // ���������� ���������� �������� �����
        PHPShopObj::loadClass("mail");

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (isset($_POST['send_gb'])) {
            if (!PHPShopSecurity::true_email($_POST['mail_new'])) {//�������� �����
                $_POST['mail_new'] = null;
            }
            if (PHPShopSecurity::true_param($_POST['name_new'], $_POST['otsiv_new'], $_POST['tema_new'])) {
                $name_new = PHPShopSecurity::TotalClean($_POST['name_new'], 2);
                $otsiv_new = PHPShopSecurity::TotalClean($_POST['otsiv_new'], 2);
                $tema_new = PHPShopSecurity::TotalClean($_POST['tema_new'], 2);
                $mail_new = PHPShopSecurity::TotalClean($_POST['mail_new'], 3);
                $date = date("U");
                $ip = $_SERVER['REMOTE_ADDR'];

                // ������ � ����
                $this->PHPShopOrm->insert(array('datas' => $date, 'name' => $name_new, 'mail' => $mail_new, 'tema' => $tema_new, 'otsiv' => $otsiv_new), $prefix = '');

                $subject = __("����������� � ����� ������");

                // ���������� ��� ������� ���������
                $this->set('gbook_name', $name_new);
                $this->set('gbook_mail', $mail_new);
                $this->set('gbook_title', $tema_new);
                $this->set('gbook_content', $otsiv_new);
                $this->set('gbook_ip', $ip);

                // ������ ��������� ��������������
                $message = ParseTemplateReturn('phpshop/lib/templates/gbook/mail.tpl', true);

                new PHPShopMail($this->PHPShopSystem->getEmail(), $this->PHPShopSystem->getEmail(), $subject, $message, false, false);
            }
        }
    }

}

?>