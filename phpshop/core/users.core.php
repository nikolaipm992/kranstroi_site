<?php

// ����������
PHPShopObj::loadClass('user');
PHPShopObj::loadClass('mail');
PHPShopObj::loadClass('order');
PHPShopObj::loadClass('delivery');

/**
 * ���������� �������� ������������
 * @author PHPShop Software
 * @version 2.3
 * @package PHPShopCore
 */
class PHPShopUsers extends PHPShopCore {

    var $activation = false;
    var $debug = false;
    var $no_captcha = false;

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['shopusers'];

        // ������ �������
        $this->action = array('get' => array('productId', 'noticeId'), 'post' => array('add_notice', 'update_password', 'add_user', 'update_user', 'passw_send'),
            'name' => array('register', 'order', 'wishlist', 'useractivate', 'sendpassword', 'notice', 'message', 'newsletter', 'sms'), 'nav' => 'index');

        // ������� ��� ������� �������
        $this->action_prefix = 'action_';

        // ������� ����� ������������ ������������
        $this->PHPShopUserElement = new PHPShopUserElement();

        // �����������
        $this->locale = array();

        parent::__construct();

        // ������ ���������
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.user_captcha_enabled'))
            $this->no_captcha = true;

        // �������� �� ������������� ���������
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate') or $this->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate_pre'))
            $this->activation = true;

        // ��������� ������� ������
        $this->title = __('������ �������');
    }

    /**
     * ����� �� ���������
     */
    function action_index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // �������� ����������� �����������
        if ($this->true_user()) {

            // ����� �������������� ������������ ������
            $this->user_info();
        } else {
            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ����������� �� SMS
     */
    function action_sms() {

        // �������� ������ SMS
        if ($this->PHPShopSystem->getSerilizeParam("admoption.sms_login") != 1) {
            $this->setError404();
            return true;
        }

        if (PHPShopSecurity::true_tel($_POST['tel'])) {

            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = false;

            if (!empty($_POST['token'])) {
                if (!empty($this->true_sms($_POST['tel'], $_POST['token']))) {
                    header('Location: /users/');
                    return true;
                } else
                    $this->set('user_sms_error', PHPShopText::alert(__('������ ������������ ���� � SMS')));
            }

            $until = time() + 180;
            $data = $PHPShopOrm->getOne(array('*'), array('tel' => '="' . $_POST['tel'] . '"'));

            if (is_array($data)) {
                if (!empty($data['tel'])) {

                    // ������� � ���� ����������� SMS 5 ��������
                    if ($data['token_time'] < $until) {
                        $token_new = substr(rand(10000, 100000), 0, 5);
                        $PHPShopOrm->update(array('token_new' => $token_new, 'token_time_new' => $until), array('id' => '=' . $data['id']));
                        $phone = trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $data['tel']));

                        // �������� �� ������ 7 ��� 8
                        $first_d = substr($phone, 0, 1);
                        if ($first_d != 8 and $first_d != 7)
                            $phone = '7' . $phone;

                        include_once $this->getValue('file.sms');
                        $msg = __('����������� ��� ��� ����������� ' . $token_new);
                        $send = SendSMS($msg, $phone);
                    }

                    $this->set('userTel', $data['tel']);

                    // ������ ����� SMS
                    if (PHPShopParser::checkFile("users/sms.tpl"))
                        $this->set('formaContent', ParseTemplateReturn('users/sms.tpl'));
                    else
                        $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/sms.tpl', true));

                    $this->setHook(__CLASS__, __FUNCTION__);
                }
            } else {

                $this->set('user_sms_error', PHPShopText::alert(__('������ ������ ��������')));

                // ������ ����� ��������
                if (PHPShopParser::checkFile("users/tel.tpl"))
                    $this->set('formaContent', ParseTemplateReturn('users/tel.tpl'));
                else
                    $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/tel.tpl', true));
            }
        }
        else {

            // ������ ����� ��������
            if (PHPShopParser::checkFile("users/tel.tpl"))
                $this->set('formaContent', ParseTemplateReturn('users/tel.tpl'));
            else
                $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/tel.tpl', true));

            $this->set('usersError', null);
        }


        $this->set('formaTitle', __('����������� �� ��������'));
        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * �������� ���������� SMS
     * @param string $sms
     */
    function true_sms($tel, $sms) {
        global $PHPShopUserElement;
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->getOne(array('*'), array('tel' => '="' . $tel . '"', 'token' => '=' . intval($sms)), array('order' => 'id'));
        if (is_array($data)) {
            $_POST['login'] = $data['login'];
            $_POST['password'] = base64_decode($data['password']);
            return $PHPShopUserElement->autorization();
        }
    }

    /**
     * ����� ������ ������ �����������
     */
    function action_add_notice() {
        if ($this->true_user()) {
            if (PHPShopSecurity::true_num($_POST['productId']))
                $this->notice_add();
            else
                $this->action_notice();
        } else {
            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ������ ���� ���������
     */
    function action_message() {
        if ($this->true_user()) {
            $this->user_message();
        } else {
            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ������ ���������
     * ������� �������� � ��������� ���� users.core/user_message.php
     */
    function user_message() {

        $this->title .= ' - ' . __('����� � �����������');

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__);
    }

    /**
     * ����� ������ ���� �����������
     */
    function action_notice() {
        if ($this->true_user()) {
            $this->notice_list();
        } else {
            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ������ �����������
     */
    function notice_list() {

        $this->title .= ' - ' . __('�����������');

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__);
    }

    /**
     * ������ ������ �����������
     */
    function notice_add() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__);
    }

    /**
     * ����� �������� �����������
     */
    function action_noticeId() {
        if ($this->true_user()) {
            if (PHPShopSecurity::true_num($_GET['noticeId'])) {
                $PHPShopOrm = new PHPShopOrm($this->getValue('base.notice'));
                $PHPShopOrm->debug = $this->debug;
                $PHPShopOrm->delete(array('user_id' => '=' . $this->UsersId, 'id' => '=' . $_GET['noticeId']));
                $this->action_notice();
            } else
                $this->setError404();
        }
        else {
            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ����� �����������
     */
    function action_productId() {

        $this->title .= ' - ' . __('���������');

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        if (PHPShopSecurity::true_num($_GET['productId'])) {
            $PHPShopProduct = new PHPShopProduct($_GET['productId']);
            if (PHPShopSecurity::true_num($PHPShopProduct->getParam('id'))) {
                $this->set('productId', $_GET['productId']);
                $this->set('pic_small', $PHPShopProduct->getParam('pic_small'));
                $this->set('pic_big', $PHPShopProduct->getParam('pic_big'));
                $this->set('productName', $PHPShopProduct->getParam('name'));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $PHPShopProduct, 'MIDDLE');

                if ($this->true_user())
                    $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/notice.tpl', true));
                else
                    $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/notice_no_auth.tpl', true));
                $this->set('formaTitle', __('��������� ��� ��������� ������ � �������'));

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $PHPShopProduct, 'END');

                $this->ParseTemplate($this->getValue('templates.users_page_list'));
            } else
                $this->setError404();
        } else
            $this->setError404();
    }

    /**
     * ����� ��������� ������ �� �������
     * ������� �������� � ��������� ���� users.core/order_info.php
     * @return mixed
     */
    function action_order_info() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $tip = 1);
    }

    /**
     * ����� ������� ������� ������������
     * ������� �������� � ��������� ���� users.core/order_list.php
     * @return mixed
     */
    function order_list() {

        $this->title .= ' - ' . __('������');

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $tip = 1);
    }

    /**
     * ���������� ������ �� �����
     * @param string $files ��� �����
     * @return string 
     */
    function link_encode($files) {
        $time = time();
        $str = array(
            "files" => $files,
            "time" => ($time + $this->getValue('my.digital_time') * 86400)
        );
        $str = serialize($str);
        $code = base64_encode($str);
        $code2 = str_replace($this->getValue('my.digital_pass1'), "!", $code);
        $code2 = str_replace($this->getValue('my.digital_pass2'), "$", $code2);

        return $code2;
    }

    /**
     * ������ ������ ���������
     */
    function clean_old_activation() {
        $nowData = time() - 432000;
        $this->PHPShopOrm->delete(array('datas' => '<' . $nowData, 'enabled' => "='0'"));
        $this->PHPShopOrm->clean();
    }

    /**
     * �������� ����� ���������
     */
    function true_key($passw) {
        return preg_match("/^[a-zA-Z0-9_]{4,35}$/", $passw);
    }

    /**
     * ����� ��������� �� �����
     */
    function action_useractivate() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        if ($this->true_key($_GET['key'])) {

            // ������ ������ ���������
            $this->clean_old_activation();

            $data = $this->PHPShopOrm->select(array('login'), array('status' => "='" . $_GET['key'] . "'"), false, array('limit' => 1));
            if (!empty($data['login'])) {

                $this->set('date', date("d-m-y H:i a"));
                $this->set('user_ip', $_SERVER['REMOTE_ADDR']);
                $this->set('user_name', $data['login']);
                $this->set('user_login', $data['login']);

                if (!$this->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate_pre')) {

                    $this->PHPShopOrm->clean();

                    $this->PHPShopOrm->update(array('enabled_new' => '1', 'status_new' => $this->PHPShopSystem->getSerilizeParam('admoption.user_status')), array('status' => "='" . $_GET['key'] . "'"));

                    $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_activation_done.tpl', true));
                } else {

                    $this->PHPShopOrm->clean();

                    $this->PHPShopOrm->update(array('status_new' => $this->PHPShopSystem->getSerilizeParam('admoption.user_status')), array('status' => "='" . $_GET['key'] . "'"));

                    // ��������� e-mail ��������������
                    $title = $this->lang('activation_admin_title') . " " . $_POST['name_new'];

                    // ���������� e-mail ��������������
                    $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_activation.tpl', true);

                    // �������� e-mail ��������������
                    $PHPShopMail = new PHPShopMail($this->PHPShopSystem->getValue('adminmail2'), $this->PHPShopSystem->getValue('adminmail2'), $title, '', true, true, array('replyto' => $data['login']));

                    // ���������� e-mail  ��������������
                    $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_activation.tpl', true);
                    $PHPShopMail->sendMailNow($content);

                    $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_admin_activation.tpl', true), true);
                }
            } else {
                $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_activation_error.tpl', true));
            }

            $this->set('formaTitle', $this->lang('user_register_title'));

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $data, 'END');

            $this->ParseTemplate($this->getValue('templates.users_page_list'));
        } else
            $this->action_register();
    }

    /**
     * ����� ������ �������
     */
    function action_order() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        if ($this->true_user()) {

            // ����� ������ �������
            $this->order_list();

            // �������� ������ ���������� �������� ������
            $this->waitAction('order_info');

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

            $this->ParseTemplate($this->getValue('templates.users_page_list'));
        } else {

            // ��������� �� ������������ �����������
            $this->set('usersError', __('��������� ��������� ������������'));

            // ����� ����������� ������ ������������
            $this->action_register();
        }
    }

    /**
     * ����� ����������/����������  ������ ������������.
     */
    function update_user_adres() {
        if (PHPShopSecurity::true_email($_POST['mail'])) {
            $data = $this->PHPShopOrm->select(array('data_adres'), array('mail' => "='" . $_POST['mail'] . "'"), false, array('limit' => 1));
            if ($data['data_adres'])
                $data_adres = unserialize($data['data_adres']);

            // ��������� ������ ������ ������
            if (!empty($_POST['country_new']))
                $newAdres['country_new'] = PHPShopSecurity::TotalClean($_POST['country_new']);
            if (!empty($_POST['state_new']))
                $newAdres['state_new'] = PHPShopSecurity::TotalClean($_POST['state_new']);
            if (!empty($_POST['city_new']))
                $newAdres['city_new'] = PHPShopSecurity::TotalClean($_POST['city_new']);
            if (!empty($_POST['index_new']))
                $newAdres['index_new'] = PHPShopSecurity::TotalClean($_POST['index_new']);
            if (!empty($_POST['fio_new']))
                $newAdres['fio_new'] = PHPShopSecurity::TotalClean($_POST['fio_new']);
            if (!empty($_POST['tel_new']))
                $newAdres['tel_new'] = PHPShopSecurity::TotalClean($_POST['tel_new']);
            if (!empty($_POST['street_new']))
                $newAdres['street_new'] = PHPShopSecurity::TotalClean($_POST['street_new']);
            if (!empty($_POST['house_new']))
                $newAdres['house_new'] = PHPShopSecurity::TotalClean($_POST['house_new']);
            if (!empty($_POST['porch_new']))
                $newAdres['porch_new'] = PHPShopSecurity::TotalClean($_POST['porch_new']);
            if (!empty($_POST['door_phone_new']))
                $newAdres['door_phone_new'] = PHPShopSecurity::TotalClean($_POST['door_phone_new']);
            if (!empty($_POST['flat_new']))
                $newAdres['flat_new'] = PHPShopSecurity::TotalClean($_POST['flat_new']);
            if (!empty($_POST['delivtime_new']))
                $newAdres['delivtime_new'] = PHPShopSecurity::TotalClean($_POST['delivtime_new']);

            if (!empty($_POST['org_name_new']))
                $newAdres['org_name_new'] = PHPShopSecurity::TotalClean($_POST['org_name_new']);
            if (!empty($_POST['org_inn_new']))
                $newAdres['org_inn_new'] = PHPShopSecurity::TotalClean($_POST['org_inn_new']);
            if (!empty($_POST['org_kpp_new']))
                $newAdres['org_kpp_new'] = PHPShopSecurity::TotalClean($_POST['org_kpp_new']);
            if (!empty($_POST['org_yur_adres_new']))
                $newAdres['org_yur_adres_new'] = PHPShopSecurity::TotalClean($_POST['org_yur_adres_new']);
            if (!empty($_POST['org_fakt_adres_new']))
                $newAdres['org_fakt_adres_new'] = PHPShopSecurity::TotalClean($_POST['org_fakt_adres_new']);
            if (!empty($_POST['org_ras_new']))
                $newAdres['org_ras_new'] = PHPShopSecurity::TotalClean($_POST['org_ras_new']);
            if (!empty($_POST['org_bank_new']))
                $newAdres['org_bank_new'] = PHPShopSecurity::TotalClean($_POST['org_bank_new']);
            if (!empty($_POST['org_kor_new']))
                $newAdres['org_kor_new'] = PHPShopSecurity::TotalClean($_POST['org_kor_new']);
            if (!empty($_POST['org_bik_new']))
                $newAdres['org_bik_new'] = PHPShopSecurity::TotalClean($_POST['org_bik_new']);
            if (!empty($_POST['org_city_new']))
                $newAdres['org_city_new'] = PHPShopSecurity::TotalClean($_POST['org_city_new']);

            if (is_array($newAdres) AND count($newAdres)) {
                // ���� ������� �� ������������� ������, ��������� ���
                if (isset($_POST['adres_id']) AND is_numeric($_POST['adres_id'])) {
                    $id = intval($_POST['adres_id']);
                    if (is_array($newAdres) and is_array($data_adres['list'][$id]))
                        $data_adres['list'][$id] = array_merge($data_adres['list'][$id], $newAdres);
                } else {
                    // ���� ����� ����� ��������� ��� � ������
                    $data_adres['list'][] = $newAdres;
                    // �������� �� ������������ ������
                    end($data_adres['list']);         // move the internal pointer to the end of the array
                    $id = key($data_adres['list']);
                }

                if ((!empty($_POST['adres_this_default']) AND $_POST['adres_this_default']) OR ! isset($data_adres['main']) OR ! isset($data_adres['list'][$data_adres['main']])) {
                    $data_adres['main'] = $id;
                }

                $data_adres = serialize($data_adres);

                $this->PHPShopOrm->clean();
                $this->PHPShopOrm->update(array(
                    'data_adres_new' => $data_adres), array('mail' => "='" . $_POST['mail'] . "'"));
            }
            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $_POST);

            return $newAdres;
        }
    }

    /**
     * ����� ���������� ������������ ������
     */
    function action_update_user() {

        if (PHPShopSecurity::true_num($_SESSION['UsersId'])) {


            // ��������� ��������� e-mail
//            if (!PHPShopSecurity::true_email($_POST['mail_new']))
//                $this->error[] = $this->lang('error_mail');
            //if (strlen($_POST['name_new']) < 3)
            //  $this->error[] = $this->lang('error_name');

            if (!is_array($this->error)) {

                if (!empty($_POST['sendmail_new']))
                    $update['sendmail_new'] = 0;
                else
                    $update['sendmail_new'] = 1;

                if (PHPShopSecurity::true_email($_POST['login_new']))
                    $update['login_new'] = PHPShopSecurity::TotalClean($_POST['login_new']);

                if (PHPShopSecurity::true_tel($_POST['tel_new']))
                    $update['tel_new'] = PHPShopSecurity::TotalClean($_POST['tel_new']);

                if (!empty($_POST['name_new'])) {
                    $_SESSION['UsersName'] = PHPShopSecurity::TotalClean($_POST['name_new']);
                    $update['name_new'] = $_SESSION['UsersName'];
                }

                if (!empty($_POST['password_new']))
                    $update['password_new'] = $this->encode($_POST['password_new']);

                $this->PHPShopOrm->update($update, array('id' => '=' . $_SESSION['UsersId']));

                $this->error[] = $this->lang('done');

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $_POST);
            }
        }

        // ������ ������ ����� ������
        $this->error();

        // ����� ������������ ������ ������������
        $this->user_info();
    }

    /**
     * ����� ����� �������������� ������
     */
    function action_sendpassword() {
        $this->set('formaTitle', __('�������������� ������'));

        // ������ ����� �������������� ������
        if (PHPShopParser::checkFile("users/register.tpl"))
            $this->set('formaContent', ParseTemplateReturn('users/sendpassword.tpl'));
        else
            $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/sendpassword.tpl', true));

        $this->setHook(__CLASS__, __FUNCTION__);
        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * ����� ����������� ������ �� �����
     */
    function action_passw_send() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        if (PHPShopSecurity::true_email($_POST['login']) and strpos($_SERVER["HTTP_REFERER"], $_SERVER['SERVER_NAME'])) {
            $this->PHPShopOrm->clean();
            $data = $this->PHPShopOrm->select(array('*'), array('login' => '="' . $_POST['login'] . '"'), false, array('limit' => 1));
            if (is_array($data)) {

                $this->set('date', date("d-m-y H:i a"));
                $this->set('user_ip', $_SERVER['REMOTE_ADDR']);
                $this->set('user_login', $data['login']);
                $this->set('user_name', $data['name']);
                $this->set('user_mail', $data['login']);
                $this->set('user_password', $this->decode($data['password']));

                // ��������� e-mail ������������
                $title = $this->PHPShopSystem->getName() . " - " . __('�������������� ������ ������������') . " " . $_POST['login'];
                $title = __('�������������� ������ ������������') . " " . $_POST['login'];

                // �������� e-mail ������������
                $PHPShopMail = new PHPShopMail($data['login'], $this->PHPShopSystem->getEmail(), $title, '', true, true);

                // ���������� e-mail ������������
                $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_sendpassword.tpl', true);
                $PHPShopMail->sendMailNow($content);

                // ��������� �� �������� ����������� ������
                $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_sendpassword.tpl', true));
            } else {
                // ��������� �� �������� ����������� ������
                $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_sendpassword_error.tpl', true));
            }
        }

        $this->set('formaTitle', __('������ �������'));
        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * ����� ���������� ������ ������������
     */
    function action_update_password() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        //�������� ���������� ����� ������
        if (PHPShopSecurity::true_num($_SESSION['UsersId'])) {

            if ($_POST['password_new'] != $_POST['password_new2'])
                $this->error[] = $this->lang('error_password');

            /*
              if (!PHPShopSecurity::true_email($_POST['login_new']))
              $this->error[] = $this->lang('error_login');
              else
              $update['mail_new'] = PHPShopSecurity::TotalClean($_POST['login_new'], 3); */

            if (!empty($_POST['sendmail_new']))
                $update['sendmail_new'] = 0;
            else
                $update['sendmail_new'] = 1;


            if (!PHPShopSecurity::true_passw($_POST['password_new']))
                $this->error[] = $this->lang('error_password_hack');

            //$update['login_new'] = PHPShopSecurity::TotalClean($_POST['login_new'], 3);
            $update['password_new'] = $this->encode($_POST['password_new']);

            if (count($this->error) == 0) {
                $this->PHPShopOrm->update($update, array('id' => '=' . $_SESSION['UsersId']));
                $this->error[] = $this->lang('done');
            }
        }

        // ������ ������ ����� ������
        $this->error();

        // ����� ������������ ������ ������������
        $this->user_info();
    }

    /**
     * ��������� � ������ � ����� ������ �������������
     */
    function error() {
        $user_error = null;
        if (is_array($this->error))
            foreach ($this->error as $val)
                $user_error .= PHPShopText::div($val);

        $this->set('user_error', $user_error);
    }

    /**
     * ����� ������� ��������
     */
    function action_wishlist() {
        global $PHPShopSystem;

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $dis = null;

        $this->set('formaTitle', __('���������� ������'));

        if ($this->true_user()) {
            $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
            $wishlist = unserialize($PHPShopUser->objRow['wishlist']);
        } else {
            // ���� �� �����������, ���� �� ������
            $wishlist = &$_SESSION['wishlist'];
        }

        if (is_array($wishlist)) {
            // �������� �� ��������
            if ($_REQUEST['delete']) {
                unset($wishlist[$_REQUEST['delete']]);
                // ��������� ���-�� ��� ������ � ����
                $_SESSION['wishlistCount'] = count($wishlist);
                if ($this->true_user())
                    $this->PHPShopOrm->update(array('wishlist' => serialize($wishlist)), array('id' => '=' . $_SESSION['UsersId']), false, false);
                header("Location: ./wishlist.html");
                die();
            }

            foreach ($wishlist as $key => $value) {

                // ������ �� ������
                $objProduct = new PHPShopProduct($key);

                if ($objProduct->getParam("enabled") == 1) {


                    if ($objProduct->getParam("sklad") == 1 or ( $this->PHPShopSystem->getSerilizeParam('admoption.user_price_activate') == 1 and empty($_SESSION['UsersId'])))
                        $this->set('prodDisabled', 'disabled');
                    else
                        $this->set('prodDisabled', '');

                    $this->set('prodId', $key);
                    $this->set('prodName', $objProduct->getParam("name"));

                    if (empty($objProduct->getParam("pic_small") == ""))
                        $this->set('prodPic', $objProduct->getParam("pic_big"));
                    else
                        $this->set('prodPic', $objProduct->getParam("pic_small"));

                    // �������� �������
                    if ($value > 1) {

                        // ������ �� ��������
                        $objProductParent = new PHPShopProduct($value);
                        $this->set('prodUid', $value);

                        if ($this->get('prodPic') == "")
                            $this->set('prodPic', $objProductParent->getParam("pic_small"));

                        $this->set('wishlistCartHide', null);
                    }
                    elseif ($objProduct->getParam("parent") != "") {
                        $this->set('wishlistCartHide', 'hide d-none');
                    } else {
                        $this->set('prodUid', $key);
                        $this->set('wishlistCartHide', null);
                    }

                    // ����
                    $price = PHPShopProductFunction::GetPriceValuta($objProduct->objRow['id'], array($objProduct->objRow['price'], $objProduct->objRow['price2'], $objProduct->objRow['price3'], $objProduct->objRow['price4'], $objProduct->objRow['price5']), $objProduct->objRow['baseinputvaluta']);
                    $this->set('prodPrice', number_format($price, $this->format, '.', ' '));

                    // ���� ���� ���������� ������ ����� �����������
                    if ($this->PHPShopSystem->getSerilizeParam('admoption.user_price_activate') == 1 and empty($_SESSION['UsersId'])) {
                        $this->set('wishlistCartHide', 'hide d-none');
                        $this->set('prodPrice', null);
                    }

                    $dis .= ParseTemplateReturn('users/wishlist/wishlist_list_one.tpl');
                }
            }
        }
        if ($dis) {
            $this->set('wishlistList', $dis);
            $this->set('formaContent', ParseTemplateReturn('users/wishlist/wishlist_list_main.tpl'));
        } else {
            $this->set('formaContent', ParseTemplateReturn('users/wishlist/wishlist_list_empty.tpl'));
        }
        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * ������������ ������ ������������
     */
    function user_info() {
        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $this->PHPShopUser = new PHPShopUser($_SESSION['UsersId']);

        // ������������ ������ ������������
        $this->set('user_status', $this->PHPShopUser->getStatusName());

        if ($this->get('user_status') == "")
            $this->set('user_status', __('�������������� ������������'));

        //������������ ������
        $discount = 0 + max($this->PHPShopUser->getDiscount(), $this->PHPShopUser->getParam('cumulative_discount'));

        $this->set('user_login', $this->PHPShopUser->getParam('login'));
        $this->set('user_password', $this->decode($this->PHPShopUser->getParam('password')));
        $this->set('user_name', $this->PHPShopUser->getParam('name'));
        $this->set('user_mail', $this->PHPShopUser->getParam('mail'));
        $this->set('user_company', $this->PHPShopUser->getParam('company'));
        $this->set('user_inn', $this->PHPShopUser->getParam('inn'));
        $this->set('user_tel', $this->PHPShopUser->getParam('tel'));
        $this->set('user_tel_code', $this->PHPShopUser->getParam('tel_code'));
        $this->set('user_adres', $this->PHPShopUser->getParam('adres'));
        $this->set('user_kpp', $this->PHPShopUser->getParam('kpp'));
        $this->set('user_cumulative_discount', $discount);

        if ($this->PHPShopSystem->getSerilizeParam('admoption.bonus') > 0)
            $this->set('user_bonus', $this->PHPShopUser->getBonus());

        if ($this->PHPShopUser->getParam('sendmail') == 0)
            $this->set('user_sendmail_checked', 'checked');

        // ����� ������� �������� ������������
        $this->set('formaTitle', $this->lang('user_info_title'));

        // ������ ����� �������
        if (PHPShopParser::checkFile("users/users_page_info.tpl"))
            $this->set('formaContent', ParseTemplateReturn('users/users_page_info.tpl'));
        else
            $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/info.tpl', true));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $this->PHPShopUser, 'END');

        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * �������� ����������� �����������
     * @return bool
     */
    function true_user() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        if (!empty($_SESSION['UsersId']))
            $UsersId = $_SESSION['UsersId'];
        else
            $UsersId = null;

        if (PHPShopSecurity::true_num($UsersId)) {
            $this->UsersId = $UsersId;
            $this->UsersStatus = $_SESSION['UsersStatus'];
            $this->UserName = $_SESSION['UsersName'];
            return true;
        }
    }

    /**
     * ����������� ������
     * @param string $str ������
     * @return string
     */
    function encode($str) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $str);
        if ($hook)
            return $hook;

        return base64_encode($str);
    }

    /**
     * ������������� ������
     * @param string $str ������
     * @return string
     */
    function decode($str) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $str);
        if ($hook)
            return $hook;

        return base64_decode($str);
    }

    /**
     * �������� �����
     * @param array $option ��������� �������� [url/captcha]
     * @return boolean
     */
    function secirity($option = array('url' => false, 'captcha' => true)) {
        global $PHPShopRecaptchaElement;


        // �������� ��������� ������
        if (!empty($option['url'])) {
            preg_match_all('/http:?/', $_POST[$option['url']], $url, PREG_SET_ORDER);
            if (count($url) > 0)
                return false;
        }

        // �������� ������
        if ($option['captcha'] === true) {

            // Recaptcha
            if ($PHPShopRecaptchaElement->true()) {
                $result = $PHPShopRecaptchaElement->check();
                return $result;
            }

            // ������� ������
            elseif (!empty($_SESSION['text']) and strtoupper($_POST['key']) == strtoupper($_SESSION['text'])) {
                return true;
            } else
                return false;
        }

        return true;
    }

    /**
     * �������� ������ ������������
     * @return Bool
     */
    function add_user_check() {

        // �������� �� �������� ��������
        if (!$this->secirity() and $this->no_captcha == false) {
            $this->error[] = $this->lang('error_key');
            return false;
        }

        // ����� � ���� �����
        $_POST['mail_new'] = $_POST['login_new'];

        // �������� ������������ ������ � ��� ����������
        if (PHPShopSecurity::true_email($_POST['login_new'])) {

            $where = array('login' => "='" . $_POST['login_new'] . "'");

            // ����������
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_servers_control"))
                $where['servers'] = '=' . intval(HostID);

            $data = $this->PHPShopOrm->select(array('id'), $where, false, array('limit' => 1));
            if (!empty($data['id']))
                $this->error[] = $this->lang('error_id');
        } else {
            $this->error[] = $this->lang('error_login');
        }

        // �������� �������� ������� 1 � 2
        if ($_POST['password_new'] != $_POST['password_new2'])
            $this->error[] = $this->lang('error_password');

        // �������� ���������� ������
        if (!PHPShopSecurity::true_passw($_POST['password_new']))
            $this->error[] = $this->lang('error_password_hack');

        // �������� ���������� �����
        if (strlen($_POST['name_new']) < 3)
            $this->error[] = $this->lang('error_name');

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $_POST);

        if (!is_array($this->error))
            return true;
    }

    /**
     * ������ ������ ������������ � ��
     * @return Int �� ������ ������������ � ��
     */
    function add($content = false, $list = false) {

        // �������� �� ������������� ���������
        if (!$this->activation) {
            $user_mail_activate = 1;
            $this->user_status = $this->PHPShopSystem->getSerilizeParam('admoption.user_status');
        } else {
            $user_mail_activate = 0;
            $this->user_status = md5(time());
        }

        //��������
        if ($_POST['subscribe_new'] == 'on') {
            $subscribe = 1;
        }

        // ������ ������ ������ ������������
        $insert = array(
            'login_new' => PHPShopSecurity::TotalClean($_POST['login_new'], 3),
            'password_new' => $this->encode($_POST['password_new']),
            'datas_new' => time(),
            'mail_new' => PHPShopSecurity::TotalClean($_POST['mail_new'], 3),
            'name_new' => PHPShopSecurity::TotalClean($_POST['name_new']),
            'company_new' => PHPShopSecurity::TotalClean($_POST['company_new']),
            'inn_new' => PHPShopSecurity::TotalClean($_POST['inn_new']),
            'tel_new' => PHPShopSecurity::TotalClean($_POST['tel_new']),
            'adres_new' => PHPShopSecurity::TotalClean($_POST['adres_new']),
            'enabled_new' => $user_mail_activate,
            'status_new' => $this->user_status,
            'kpp_new' => PHPShopSecurity::TotalClean($_POST['kpp_new']),
            'subscribe_new' => $subscribe,
            'tel_code_new' => PHPShopSecurity::TotalClean($_POST['tel_code_new']),
            'bot_new' => md5($_POST['login_new'] . time())
        );

        if (defined('HostID'))
            $insert['servers_new'] = HostID;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $insert);
        if (is_array($hook))
            $insert = $hook;

        // ������ � ��
        $result = $this->PHPShopOrm->insert($insert);

        // ���������� �� ������ ������������
        return $result;
    }

    /**
     * ����� �������� ������������� ������������ �� email. ���� ����������, ���������� ��
     */
    function user_check_by_email($login) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.shopusers'));
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->Option['where'] = " or ";
        if (PHPShopSecurity::true_email($login)) {
            $data = $PHPShopOrm->select(array('id', 'password'), array('mail' => '="' . trim($login) . '"', 'login' => '="' . trim($login) . '"'), array('order' => 'id desc'), array('limit' => 1));
            if (is_array($data) AND PHPShopSecurity::true_num($data['id'])) {

                return $data['id'];
            }
        }
        return false;
    }

    /**
     * ����� ��������� ������ ������������
     */
    function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    /**
     * ����� ���������� ������ ������������ �� �������� ���������� ������
     */
    function add_user_from_order($login, $name = false, $tel = false) {

        // ���������� ��������� � ������
        $this->activation = false;

        // ��������� ������ �����������
        $this->no_captcha = true;

        // ����� � ���� �����
        $_POST['mail_new'] = $_POST['login_new'] = $login;

        if (!empty($tel))
            $_POST['tel_new'] = $tel;

        if (!empty($name))
            $_POST['name_new'] = $name;

        $_POST['password_new'] = $_POST['password_new2'] = $this->generatePassword();

        $this->UsersId = $this->user_check_by_email($login);
        // ���� ������������ ������������, ���������� ���
        if (!$this->UsersId) {
            $this->action_add_user();
            setcookie("UserLogin", trim(trim($login)), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserPassword", base64_decode($_POST['password_new']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
        }

        if ($this->UsersId)
            return $this->UsersId;
        else
            return false;
    }

    /**
     * ����� ���������� ������ ������������ � ����� �������� �� ��������
     */
    function action_newsletter() {

        // ���������� ��������� � ������
//        $this->activation = true;

        $_SESSION['text'] = $_POST['key'] = "fromOrder";
        // ����� � ���� �����
        $login = $_REQUEST['newsletter_email'];
        $_POST['mail_new'] = $_POST['login_new'] = $login;
        $_POST['password_new'] = $_POST['password_new2'] = $this->generatePassword();
        $_POST['name_new'] = "�������� ��������";

        $this->UsersId = $this->user_check_by_email($login);
        // ���� ������������ ������������, ���������� ���
        if ($this->UsersId) {
            // ������������ ��� �������� � ��������, �������� �� ����
            if (PHPShopParser::checkFile("users/newsletter/newsletter_user_exist.tpl"))
                $this->Disp = ParseTemplateReturn('users/newsletter/newsletter_user_exist.tpl');
            else
                $this->Disp = ParseTemplateReturn('phpshop/lib/templates/users/newsletter/newsletter_user_exist.tpl', true);

            return true;
        }

        if (!$this->UsersId) {
            $this->action_add_user();
        }

        if (count($this->error)) {
            // ������� ��������� �� ������ ���������� � ��������
            if (PHPShopParser::checkFile("users/newsletter/newsletter_add_error.tpl"))
                $this->Disp = ParseTemplateReturn('users/newsletter/newsletter_add_error.tpl');
            else
                $this->Disp = ParseTemplateReturn('phpshop/lib/templates/users/newsletter/newsletter_add_error.tpl', true);

            return true;
        }

        if ($this->UsersId) {
            if (!$this->activation) {
                // ������� ��������� �� �������� ���������� � ��������
                if (PHPShopParser::checkFile("users/newsletter/newsletter_add_success.tpl"))
                    $this->Disp = ParseTemplateReturn('users/newsletter/newsletter_add_success.tpl');
                else
                    $this->Disp = ParseTemplateReturn('phpshop/lib/templates/users/newsletter/newsletter_add_success.tpl', true);
            }
            else {
                // ������� ��������� �� �������� ���������� � �������� + ��� ����� ������������ email
                if (PHPShopParser::checkFile("users/newsletter/newsletter_add_success_need_activation.tpl"))
                    $this->Disp = ParseTemplateReturn('users/newsletter/newsletter_add_success_need_activation.tpl');
                else
                    $this->Disp = ParseTemplateReturn('phpshop/lib/templates/users/newsletter/newsletter_add_success_need_activation.tpl', true);
            }
        }
    }

    /**
     * ����� ���������� ������ ������������
     */
    function action_add_user() {

        // ���� �������� �������� �� ������������ �����
        if ($this->add_user_check()) {

            // ���������� ������ � ��
            $this->UsersId = $this->add();

            // �������� �� ������������� ���������
            if (!$this->activation) {

                // ���������� ������������
                $_POST['login'] = $_POST['login_new'];
                $_POST['password'] = $_POST['password_new'];
                $this->PHPShopUserElement->autorization();


                // ��������� �� �������� �����������
                $this->message_register_success();
                // ����� ������������ ������
                $this->PHPShopUserElement->checkRedirect();
                //���������� �� ������������.
//                $this->user_info();
                $this->redirectToUserInfo();
            } else {

                // ��������� �� ��������� ��������
                $this->message_activation();
            }
        } else {

            // ������ ������
            $this->error();

            // ����� �����������
            $this->action_register();
        }
    }

    /**
     * ��������� �� �������� �����������
     * ������� �������� � ��������� ���� users.core/message_register_success.php
     * @return mixed
     */
    function message_register_success() {
        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, false, 'users');
    }

    /**
     * �������� � �� ������������.
     */
    function redirectToUserInfo() {
        if ($this->PHPShopNav->getPath() != "done" AND $this->PHPShopNav->getName() != "newsletter" AND ! $this->stop_redirect)
            header("Location: " . $GLOBALS['SysValue']['dir']['dir'] . "/users/");
    }

    /**
     * ��������� �����������
     * ������� �������� � ��������� ���� users.core/message_activation.php
     * @return mixed
     */
    function message_activation() {

// �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, false, 'users');
    }

    /**
     * ����� ����� ����������� ������ ������������
     * ��������� ���������� ����� �������������� � action_add_user()
     */
    function action_register() {

        // �������� ����������� �����������
        if ($this->true_user()) {
            // ����� �������������� ������������ ������
            $this->user_info();
            return;
        }

        $this->set('formaTitle', $this->lang('user_register_title'));

        // ������ �����������
        if (PHPShopParser::checkFile("users/register.tpl"))
            $this->set('formaContent', ParseTemplateReturn('users/register.tpl'));
        else
            $this->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/register.tpl', true));

        $this->setHook(__CLASS__, __FUNCTION__);

        $this->ParseTemplate($this->getValue('templates.users_page_list'));
    }

    /**
     * ������������ ������ ������
     * @return string
     */
    function tr() {
        $Arg = func_get_args();

        $tr = '<tr>';

        foreach ($Arg as $key => $val)
            if ($val != '-')
                $col[$key] = 1;
            else
                $col[$key] = 2;

        foreach ($Arg as $key => $val) {
            if ($val != '-')
                $tr .= PHPShopText::td($val, false, @$col[$key + 1], $id = 'allspecwhite');
        }

        $tr .= '</tr>';
        return $tr;
    }

    /**
     * ������������ ������ ������. ���������.
     * @return string
     */
    function caption() {
        $Arg = func_get_args();
        $tr = '<thead><tr id="allspec">';
        foreach ($Arg as $val) {
            $tr .= PHPShopText::td(PHPShopText::b($val), false, false);
        }
        $tr .= '</tr></thead>';
        return $tr;
    }

}

?>