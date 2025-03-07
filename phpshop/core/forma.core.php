<?php

/**
 * ���������� ����� ��������� � �����
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopCore
 */
class PHPShopForma extends PHPShopCore {

    private $ajax = false;

    /**
     * �����������
     */
    function __construct() {
        $this->debug = false;

        if (isset($_POST['ajax'])) {
            $this->ajax = true;
        }

        // ������ �������
        $this->action = array("post" => "content", "post" => "name", "nav" => "index");
        parent::__construct();
    }

    /**
     * ����� �� ���������, ����� ����� �����
     */
    function index() {

        // ����
        $title = __('����� �����');
        $this->title = $title . ' - ' . $this->PHPShopSystem->getValue("name");

        // ���������� ����������
        $this->set('pageTitle', $title);

        // ��������� ������� ������
        $this->navigation(null, $title);

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__);

        // ���������� ������
        $this->addToTemplate("forma/page_forma_list.tpl");

        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * ����� �������� ����� ��� ��������� $_POST[name]
     */
    function name() {
        $this->content();
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
     * ����� �������� ����� ��� ��������� $_POST[content]
     */
    function content() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if ($this->ajax) {
            $_POST['tema'] = PHPShopString::utf8_win1251($_POST['tema']);
            $_POST['name'] = PHPShopString::utf8_win1251($_POST['name']);
            $_POST['content'] = PHPShopString::utf8_win1251($_POST['content']);
            $_POST['content'] .= ' 
                
' . __('��������') . ': ' . $_SERVER['HTTP_REFERER'].'
IP: '.$_SERVER['REMOTE_ADDR'];
            $_POST['mail'] = PHPShopString::utf8_win1251($_POST['mail']);
        }

        // ������������
        if ($this->security()) {
            $this->lead();
            $this->send();

            if ($this->ajax) {
                echo json_encode([
                    'message' => PHPShopString::win_utf8($this->get('Error'))
                ]);
                exit;
            }
        } else {
            if ($this->ajax) {
                echo json_encode([
                    'message' => PHPShopString::win_utf8(__("������ �����, ��������� ������� ����� �����"))
                ]);
                exit;
            }
            $this->set('Error', __("������ �����, ��������� ������� ����� �����"));
        }

        $this->index();
    }

    /**
     * ���������� ����
     */
    function lead() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
        $insert = array('date_new' => time(), 'message_new' => $_POST['tema'], 'name_new' => $_POST['name'], 'mail_new' => $_POST['mail'], 'tel_new' => $_POST['tel'], 'content_new' => PHPShopSecurity::TotalClean($_POST['content']));
        $PHPShopOrm->insert($insert);
    }

    /**
     * ��������� ���������
     */
    function send() {

        // ���������� ���������� �������� �����
        PHPShopObj::loadClass("mail");

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (!empty($_POST['tema']) and ! empty($_POST['name']) and ! empty($_POST['content'])) {
            $subject = $_POST['tema'] . " - " . $this->PHPShopSystem->getValue('name');

            $this->set('content', $_POST['content']);
            $this->set('name', $_POST['name']);
            $this->set('tel', $_POST['tel']);
            $this->set('date', date("d-m-y H:s a"));
            $this->set('mail', $_POST['mail']);

            $PHPShopMail = new PHPShopMail($this->PHPShopSystem->getEmail(), $this->PHPShopSystem->getEmail(), $subject, null, true, true, array('replyto' => $_POST['mail']));

            $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_forma.tpl', true);
            $PHPShopMail->sendMailNow($content);

            $this->set('Error', __("�������!
�� �������� � ���� � ��������� �����."));
        } else
            $this->set('Error', __("�� ��������� ������������ ����"));
    }

}

?>