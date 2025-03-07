<?php
/**
 * ���������� ������� �� ��������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopUnsubscribe extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {

        // �������
        $this->debug = false;

        $this->title = "����� �� ��������";

        $this->description = "����� �� ��������";

        // ������ �������
        $this->action = array('nav' => 'index');
        parent::__construct();
    }

    /**
     * �������
     */
    function index() {
        if (isset($_REQUEST['id']) && isset($_REQUEST['hash'])) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $user = $PHPShopOrm->select(array('id', 'mail', 'password'), array('id=' => intval($_REQUEST['id'])));

            if ($user['id']) {
                $hash = md5($user['mail'] . $user['password']);

                if ($hash === $_REQUEST['hash']) {
                    $PHPShopOrm->update(array('sendmail_new' => 0), array('id=' => $user['id']));
                    $this->set('content', PHPShopText::alert(__("�� ������� ���������� �� ��������� ��������"), 'success'));
                }
                else
                    $this->set('content', PHPShopText::alert(__("������������ �� ������")));

            }
            else
                $this->set('content', PHPShopText::alert(__("������������ �� ������")));

            // ���������� ������
            $this->parseTemplate($this->getValue('templates.unsubscribe_message'), true);
        }
        else
            $this->setError404();
    }

}

?>