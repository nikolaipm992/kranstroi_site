<?php

/**
 * ������� ����������� ��������� ����������
 * @author PHPShop Software
 * @version 2.1
 * @package PHPShopElements
 */
class PHPShopCoreElement extends PHPShopElements {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * ����� ������������
     */
    function service() {
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.service_enabled', 1)) {

            $ip = explode(",", $this->PHPShopSystem->getSerilizeParam('admoption.service_ip'));
            if (is_array($ip) and in_array(trim($_SERVER['REMOTE_ADDR']), $ip))
                return;
            else {

                $title = $this->PHPShopSystem->getSerilizeParam('admoption.service_title');
                $message = $this->PHPShopSystem->getSerilizeParam('admoption.service_content');

                if (empty($title))
                    $title = '503 Service Temporarily Unavailable';

                if (empty($message))
                    $message = 'Website is under construction';


                PHPShopParser::set('message', $message);
                PHPShopParser::set('title', $title);
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                exit(PHPShopParser::file($_SERVER['DOCUMENT_ROOT'] . '/phpshop/lib/templates/error/service.tpl', false, true, true));
            }
        }

        // ���������� IP
        $block_ip = explode(",", $this->PHPShopSystem->getSerilizeParam('admoption.block_ip'));
        if (is_array($block_ip) and in_array(trim($_SERVER['REMOTE_ADDR']), $block_ip)) {

            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            if (file_exists('./404.html'))
                include_once('./404.html');
            exit;
        }
    }

    /**
     * ���������� �������� �������
     * @return string
     */
    function skin() {

        if (empty($_SESSION['skin'])) {

            // ����������
            if (defined("HostSkin"))
                $_SESSION['skin'] = HostSkin;
            else
                $_SESSION['skin'] = $this->PHPShopSystem->getValue('skin');
        }

        return $_SESSION['skin'];
    }

    /**
     * �������� ������������� �������,
     * ����� �� ������ ��������� ������ ��� �������������� ����� � ��������
     * @return string
     */
    function checkskin() {
        if (!@file_exists("phpshop/templates/" . $_SESSION['skin'] . "/main/index.tpl")) {
            $dir = $this->getValue('dir.templates') . chr(47);
            if (is_dir($dir)) {
                if (@$dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (@is_file($dir . $file . chr(47) . 'main/index.tpl')) {
                            $_SESSION['skin'] = $file;
                            header('Location: /?status=template_error');
                        }
                    }
                    closedir($dh);
                }
            }
            exit('Template error!');
        }
    }

    /**
     * ����������� ��������� ���������� ��� ��������
     * (���, �������, ����� ��������������, ����, �������)
     */
    function setdefault() {
        global $PHPShopBase;

        // ����������
        if (defined("HostID")) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
            $PHPShopOrm->debug = false;
            $showcaseData = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'host' => "='" . str_replace('www.', '', $_SERVER['HTTP_HOST']) . "'"), array('order' => 'id'), array('limit' => 1));
            if (is_array($showcaseData)) {

                if (!empty($showcaseData['currency'])) {
                    $_SESSION['valuta'] = $showcaseData['currency'];
                    $lang = $showcaseData['lang'];
                }

                if (!empty($showcaseData['tel']))
                    $this->PHPShopSystem->setParam("tel", $showcaseData['tel']);

                if (!empty($showcaseData['adminmail']))
                    $this->PHPShopSystem->setParam("adminmail2", $showcaseData['adminmail']);

                if (!empty($showcaseData['shop_type']))
                    $this->PHPShopSystem->setParam("shop_type", $showcaseData['shop_type']);

                if (!empty($showcaseData['company_id']))
                    $this->PHPShopSystem->setCompany($showcaseData['company_id']);

                if (!empty($showcaseData['name']))
                    $this->PHPShopSystem->setParam('name', $showcaseData['name']);

                if (!empty($showcaseData['title']))
                    $this->PHPShopSystem->setParam('title', $showcaseData['title']);

                if (!empty($showcaseData['descrip']))
                    $this->PHPShopSystem->setParam('descrip', $showcaseData['descrip']);

                if (!empty($showcaseData['logo']))
                    $this->PHPShopSystem->setParam('logo', $showcaseData['logo']);

                if (!empty($showcaseData['icon']))
                    $this->PHPShopSystem->setParam('icon', $showcaseData['icon']);

                if (!empty($showcaseData['skin']))
                    define("HostSkin", $showcaseData['skin']);

                if (!empty($showcaseData['price']))
                    define("HostPrice", $showcaseData['price']);

                define("HostAdmin", $showcaseData['admin']);

                $admoption = unserialize($showcaseData['admoption']);
                if (is_array($admoption)) {

                    if (!empty($admoption['org_tel']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_tel', $admoption['org_tel']);

                    if (isset($admoption['user_price_activate']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_price_activate', $admoption['user_price_activate']);

                    if (isset($admoption['user_mail_activate']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_mail_activate', $admoption['user_mail_activate']);

                    if (isset($admoption['user_mail_activate_pre']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_mail_activate_pre', $admoption['user_mail_activate_pre']);

                    if (isset($admoption['smtp_user']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.mail_smtp_user', $admoption['smtp_user']);

                    if (isset($admoption['smtp_password']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.mail_smtp_pass', $admoption['smtp_password']);

                    if (isset($admoption['user_status']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_status', $admoption['user_status']);

                    if (isset($admoption['metrica_id']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.metrica_id', $admoption['metrica_id']);

                    if (isset($admoption['google_id']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.google_id', $admoption['google_id']);

                    if (isset($admoption['fee']))
                        $this->PHPShopSystem->setParam('percent', (int) $admoption['fee']);

                    if (!empty($admoption['org_adres']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_adres', $admoption['org_adres']);

                    if (!empty($admoption['org_time']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_time', $admoption['org_time']);
                }
            }
        } else {
            $lang = $this->PHPShopSystem->getSerilizeParam("admoption.lang");
        }

        $this->set('streetAddress', $this->PHPShopSystem->getSerilizeParam('bank.org_adres'));

        $_SESSION['lang'] = $lang;

        // ����
        $GLOBALS['PHPShopLang'] = new PHPShopLang(array('locale' => $lang, 'path' => 'shop'));
        $this->set('charset', $GLOBALS['PHPShopLang']->charset);
        $this->set('lang', $GLOBALS['PHPShopLang']->code);

        // �������
        $tel = $this->PHPShopSystem->getValue('tel');
        $this->set('telNum', $tel);
        $this->set('telNum2', $this->PHPShopSystem->getSerilizeParam("bank.org_tel"));
        $this->set('workingTime', $this->PHPShopSystem->getSerilizeParam("bank.org_time"));

        // SMS
        if ($this->PHPShopSystem->getSerilizeParam("admoption.sms_login") != 1)
            $this->set('sms_login_enabled', 'hidden d-none');
        else {
            $this->set('sms_login_enabled', 'req');
            $this->set('sms_login_control', 'required=""');
        }

        // ������� ��� �������
        if (strstr($tel, ","))
            $tel_xs = explode(",", $tel);
        else
            $tel_xs[] = $tel;

        $this->set('telNumMobile', $tel_xs[0]);
        $this->set('rule', $this->lang('rule'));
        $this->set('name', $this->PHPShopSystem->getValue('name'));

        // Favicon
        $icon = $this->PHPShopSystem->getValue('icon');
        if (empty($icon))
            $icon = '/apple-touch-icon.png';
        $this->set('icon', $icon);

        // ���������� ����
        $this->set('vk', $this->PHPShopSystem->getSerilizeParam('bank.vk'));
        $this->set('telegram', $this->PHPShopSystem->getSerilizeParam('bank.telegram'));
        $this->set('odnoklassniki', $this->PHPShopSystem->getSerilizeParam('bank.odnoklassniki'));
        $this->set('whatsapp', $this->PHPShopSystem->getSerilizeParam('bank.whatsapp'));
        $this->set('youtube', $this->PHPShopSystem->getSerilizeParam('bank.youtube'));

        $this->set('company', $this->PHPShopSystem->getValue('company'));
        $this->set('descrip', $this->PHPShopSystem->getValue('descrip'));
        $this->set('adminMail', $this->PHPShopSystem->getValue('adminmail2'));
        $this->set('pathTemplate', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);

        $this->set('serverName', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
        $this->set('serverShop', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
        if (!empty($_SESSION['UserLogin']))
            $this->set('UserLogin', $_SESSION['UserLogin']);
        $this->set('ShopDir', $this->getValue('dir.dir'));
        $this->set('date', date("d-m-y H:i a"));
        $this->set('year', date("Y"));
        $this->set('user_ip', $_SERVER['REMOTE_ADDR']);
        $this->set('NavActive', $this->PHPShopNav->getPath());
        $v = $this->getValue('upload.version');
        $this->set('version', substr($v, 0, 1) . '.' . substr($v, 1, 1));
        $this->set('hcs', '<!--');
        $this->set('hce', '-->');

        // �������� ���� �������
        $theme = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme');
        if (!empty($theme))
            $this->set($_SESSION['skin'] . '_theme', $theme);

        $theme2 = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme2');
        if (!empty($theme2))
            $this->set($_SESSION['skin'] . '_theme2', $theme2);

        $theme3 = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme3');
        if (!empty($theme3))
            $this->set($_SESSION['skin'] . '_theme3', $theme3);

        // ��������� �������
        if (empty($_SESSION['editor'][$_SESSION['skin']])) {
            $editor = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_editor');
            if (is_array($editor))
                $_SESSION['editor'][$_SESSION['skin']] = $editor;
        }

        // �������
        $this->set('logo', $this->PHPShopSystem->getLogo());

        // DaData.ru
        if ($this->PHPShopSystem->getSerilizeParam('admoption.dadata_enabled')) {
            $dadataToken = $this->PHPShopSystem->getSerilizeParam('admoption.dadata_token');
            if (empty($dadataToken))
                $dadataToken = 'b13e0b4fd092a269e229887e265c62aba36a92e5';
            $this->set('dadataToken', $dadataToken);
        } else
            $this->set('dadataToken', null);

        // Demo �����
        if (isset($_GET['demo']))
            $PHPShopBase->setParam('template_theme.demo', 'false');

        // ������ �������
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.min')) {
            $this->set('pathTemplateMin', '/files/min.php?f=' . $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
            $this->set('pathMin', '/files/min.php?f=');
        } else {
            $this->set('pathTemplateMin', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
            $this->set('pathMin', null);
        }

        // ��� ������
        $shop_type = $this->PHPShopSystem->getParam("shop_type");

        // �������
        if (empty($shop_type)) {
            $this->set('hideShop', 'hide d-none');
            $this->set('showSite', 'hide d-none');
        }
        // �������
        else if ($shop_type == 1) {
            $this->set('hideCatalog', 'hide d-none');
            $this->set('showSite', 'hide d-none');
            $this->PHPShopSystem->setSerilizeParam('admoption.nowbuy_enabled', 0);
        }
        // ����
        else if ($shop_type == 2) {
            $this->set('hideSite', 'hide d-none');
            $this->set('showSite', 'hide d-none');
            $this->set('hideCatalog', 'hide d-none');
            $this->PHPShopSystem->setSerilizeParam('admoption.nowbuy_enabled', 0);
        }

        // ������ ID
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.yandex_id_enabled') and $this->PHPShopSystem->getSerilizeParam('admoption.yandex_id_apikey') != "") {
            $this->set('yandex_id_apikey', $this->PHPShopSystem->getSerilizeParam('admoption.yandex_id_apikey'));
            $this->set('yandex_redirect_uri', $_SERVER['SERVER_NAME'] . 'phpshop/ajax/yandexid.php');

            if (PHPShopParser::checkFile("users/yandexid.tpl"))
                $this->set('yandexid', ParseTemplateReturn('users/yandexid.tpl'));
            else
                $this->set('yandexid', ParseTemplateReturn('phpshop/lib/templates/users/yandexid.tpl', true));
        }

        // VK ID
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_id_enabled') and $this->PHPShopSystem->getSerilizeParam('admoption.vk_id_apikey') != "") {
            $this->set('vk_app', $this->PHPShopSystem->getSerilizeParam('admoption.vk_id'));
            $this->set('vk_redirect_uri', $_SERVER['SERVER_NAME'] . 'phpshop/ajax/vkid.php');

            if (PHPShopParser::checkFile("users/vkid.tpl"))
                $this->set('vkid', ParseTemplateReturn('users/vkid.tpl'));
            else
                $this->set('vkid', ParseTemplateReturn('phpshop/lib/templates/users/vkid.tpl', true));
        }
    }

    /**
     * JS ���������
     */
    function setjs() {
        $js = null;

        // ����� ��������
        $phone_mask = $this->PHPShopSystem->getSerilizeParam("admoption.user_phone_mask");
        $phone_mask_enabled = $this->PHPShopSystem->getSerilizeParam("admoption.user_phone_mask_enabled");

        if (!empty($phone_mask))
            $js .= 'var PHONE_MASK = "' . str_replace('&#43;', '+', $phone_mask) . '";';
        else
            $js .= 'var PHONE_MASK = "";';
        if (!empty($phone_mask_enabled))
            $js .= 'var PHONE_FORMAT = false;';
        else
            $js .= 'var PHONE_FORMAT = true;';

        // ������������ ��������� �������
        $ajax_scroll = $this->PHPShopSystem->getSerilizeParam("admoption.ajax_scroll");

        if (empty($ajax_scroll))
            $js .= 'var AJAX_SCROLL = true;';
        else
            $js .= 'var AJAX_SCROLL = false;';

        // ����������� �������
        if ($this->PHPShopSystem->ifSerilizeParam("admoption.filter_cache_enabled"))
            $js .= 'var FILTER_CACHE = true;';
        else
            $js .= 'var FILTER_CACHE = false;';

        if ($this->PHPShopSystem->ifSerilizeParam("admoption.filter_products_count"))
            $js .= 'var FILTER_COUNT = true;';
        else
            $js .= 'var FILTER_COUNT = false;';

        // �������� �� COOKIE
        if (defined('isMobil')) {
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_cookie_mobile_enabled"))
                $js .= 'var COOKIE_AGREEMENT = false;';
            else
                $js .= 'var COOKIE_AGREEMENT = true;';
        }
        else {
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_cookie_enabled"))
                $js .= 'var COOKIE_AGREEMENT = false;';
            else
                $js .= 'var COOKIE_AGREEMENT = true;';
        }

        if (!empty($js)) {
            $this->set('editor', '
        <script>' . $js . '</script>', true);
        }
    }

    /**
     * ����� ������� �������
     * @return string
     */
    function pageCss() {
        $this->set('pathTemplate', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
        return $this->getValue('dir.templates') . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('css.default');
    }

}

/**
 * ������� ����� ����������� ������������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopUserElement extends PHPShopElements {

    /**
     * �����������
     */
    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        parent::__construct();

        // ���� ���� �������� from, ����� ��������� ����������� �������� � ������� �� ��� ������������ ����� �����������, �����������.
        if (!empty($_REQUEST['from']) and $_REQUEST['from'] AND empty($_REQUEST['fromSave']))
            $this->set('fromSave', $_SERVER['HTTP_REFERER']);
        elseif (!empty($_REQUEST['fromSave']))
            $this->set('fromSave', $_REQUEST['fromSave']);

        // ������
        $this->setAction(array('post' => array('user_enter', 'user_register'), 'get' => 'logout'));
    }

    /**
     * ����������� ������
     * @param string $str ������
     * @return string ������������ ������
     */
    function encode($str) {
        return base64_encode(trim($str));
    }

    /**
     * ����� ������ ������������
     */
    function logout() {
        unset($_SESSION['UsersId']);
        unset($_SESSION['UsersStatus']);
        unset($_SESSION['UsersLogin']);
        unset($_SESSION['UsersName']);
        unset($_SESSION['UsersMail']);
        unset($_SESSION['UsersStatus']);
        unset($_SESSION['UsersStatusPice']);
        unset($_SESSION['UsersBan']);
        unset($_COOKIE['UserLogin']);
        unset($_COOKIE['UserPassword']);

        setcookie("UserLogin", '', time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
        setcookie("UserPassword", '', time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

        $url_user = str_replace("?logout=true", "", $_SERVER['REQUEST_URI']);
        header("Location: " . $url_user);
    }

    /**
     * ������ �� ������� � ���-��� � �� ������
     */
    function wishlist() {
        if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
            $this->set('wishlistCount', $_SESSION['wishlistCount']);
            $dis = $this->parseTemplate('users/wishlist/wishlist_top_enter.tpl');
        } else {

            if (!empty($_SESSION['wishlist']) and is_array($_SESSION['wishlist']))
                $wishlistCount = count($_SESSION['wishlist']);
            else
                $wishlistCount = 0;

            $this->set('wishlistCount', $wishlistCount);

            $dis = $this->parseTemplate('users/wishlist/wishlist_top_enter.tpl');
        }
        return $dis;
    }

    public function authorize($user) {
        $PHPShopOrm = new PHPShopOrm($this->objBase);

        // ��������� ������� ������� ��� � ������ �� ����������.
        $wishlist = unserialize($user['wishlist']);
        if (!is_array($wishlist))
            $wishlist = array();
        if (!empty($_SESSION['wishlist']) and is_array($_SESSION['wishlist']))
            foreach ($_SESSION['wishlist'] as $key => $value) {
                $wishlist[$key] = 1;
            }
        $_SESSION['wishlistCount'] = count($wishlist);

        // ������� ������� �� ������, �� ������� � ��
        unset($_SESSION['wishlist']);

        $wishlist = serialize($wishlist);
        $PHPShopOrm->update(array('wishlist' => $wishlist), array('id' => '=' . $user['id']), false);

        // ID ������������
        $_SESSION['UsersId'] = $user['id'];

        // ����� ������������
        $_SESSION['UsersLogin'] = $user['login'];

        // ��� ������������
        $_SESSION['UsersName'] = $user['name'];

        // ������� ������������
        $_SESSION['UsersTel'] = $user['tel'];

        // ������ ������������
        $_SESSION['UsersStatus'] = $user['status'];

        // Bot
        $_SESSION['UsersBot'] = $user['bot'];

        // ���������� ��������
        $_SESSION['UsersBan'] = $user['dialog_ban'];

        // E-mail ������������ ��� ������
        if (PHPShopSecurity::true_email($user['login']))
            $_SESSION['UsersMail'] = $user['login'];
        else
            $_SESSION['UsersMail'] = $user['mail'];

        // ���� �����
        $this->log();
    }

    /**
     * �������� �����������
     * @return bool
     */
    function autorization() {
        if (PHPShopSecurity::true_login($_POST['login']) and PHPShopSecurity::true_passw($_POST['password'])) {
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;

            $where = array('login' => '="' . trim($_POST['login']) . '"', 'password' => '="' . $this->encode($_POST['password']) . '"', 'enabled' => "='1'");

            // ����������
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_servers_control"))
                $where['servers'] = '=' . intval(HostID);

            $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1));
            if (is_array($data) AND PHPShopSecurity::true_num($data['id'])) {

                $GLOBALS['SysValue']['other']['user_link'] = 'href="/users/"';
                $GLOBALS['SysValue']['other']['user_active'] = 'active';

                $this->authorize($data);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $data);

                // �������� ���� ���������
                if (!empty($_GET['key']))
                    header('Location: /users/');

                return true;
            } else
                $this->set("shortAuthError", __("�������� ����� ��� ������"));
        } else
            $this->set("shortAuthError", __("�������� ����� ��� ������"));
    }

    /**
     * ������ ���� ����������� ������������
     */
    function log() {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->update(array('datas_new' => time()), array('id' => '=' . $_SESSION['UsersId']));
    }

    /**
     * ����� ����� ����������� ������������ �� �������� ���������� ������
     */
    function user_register() {
        // ����������� ������ ������� �������� ��� ����������� ����������� �� �������� ���������� ������
        if (!class_exists('PHPShopUsers'))
            PHPShopObj::importCore('users');
        if (class_exists('PHPShopUsers')) {
            $PHPShopUsers = new PHPShopUsers();
            $PHPShopUsers->action_add_user();
        }
    }

    /**
     * ����� ����� ������������
     */
    function user_enter() {
        if ($this->autorization()) {

            // ���������� ������������ � cookie
            if (!empty($_POST['safe_users'])) {
                setcookie("UserLogin", trim($_POST['login']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserPassword", trim($_POST['password']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            } else {
                setcookie("UserLogin", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserPassword", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserChecked", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            }

            // ��������
            if (preg_match("/LogOut/", $_SERVER['REQUEST_URI']))
                $url_user = str_replace("?LogOut", "#userPage", $_SERVER['REQUEST_URI']);
            elseif (!empty($_GET['key']))
                $url_user = $this->getValue('dir.dir') . '/users/';
            else
                $url_user = $_SERVER['REQUEST_URI'];

            header("Location: " . $url_user);
            //$this->checkRedirect();
        } else
            $this->set('usersError', $this->lang('error_login'));
    }

    /**
     * ���� ����� �����������, ����������� ���������� ��������� �� �������� � ������� ������, ��������������
     */
    function checkRedirect() {
        // ���� ����� �����������, ����������� ���������� ��������� �� �������� � ������� ������, ��������������
        if ($_REQUEST['from'] AND $_REQUEST['fromSave'])
            header("Location: " . $_REQUEST['fromSave']);
    }

    /**
     * ����� ����������� ������������
     */
    function usersDisp() {

        if (empty($_SESSION['UsersId']) && (!empty($_COOKIE['UserLogin']) && !empty($_COOKIE['UserPassword']))) {
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;

            $where = array('login' => '="' . trim($_COOKIE['UserLogin']) . '"', 'password' => '="' . $this->encode($_COOKIE['UserPassword']) . '"', 'enabled' => "='1'");

            // ����������
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_servers_control"))
                $where['servers'] = '=' . intval(HostID);

            $user = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1));
            if (is_array($user) AND PHPShopSecurity::true_num($user['id'])) {
                $this->authorize($user);
            }
        }

        if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
            $this->set('UsersLogin', $_SESSION['UsersLogin']);
            $this->set('UsersName', $_SESSION['UsersName']);
            $dis = $this->parseTemplate($this->getValue('templates.users_forma_enter'));
        } else {

            // ���� �����������, ������ �� cookie
            if (!empty($_COOKIE['UserChecked']) and PHPShopSecurity::true_num($_COOKIE['UserChecked']))
                $this->set('UserChecked', 'checked');

            if (!empty($_COOKIE['UserLogin']) and PHPShopSecurity::true_email($_COOKIE['UserLogin']))
                $this->set('UserLogin', $_COOKIE['UserLogin']);

            if (!empty($_COOKIE['UserPassword']) and PHPShopSecurity::true_passw($_COOKIE['UserPassword']))
                $this->set('UserPassword', $_COOKIE['UserPassword']);

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__);

            $dis = $this->parseTemplate($this->getValue('templates.users_forma'));
        }
        return $dis;
    }

}

/**
 * ������� �������� �������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopPageCatalogElement extends PHPShopElements {

    /**
     * @var bool ��������� �� ��������� ��������
     */
    var $check_page = true;
    var $debug = false;

    /**
     * @var int ���������� ��� ������ ��������� ������� 
     */
    var $limit_last = 2;

    /**
     * �����������
     */
    function __construct() {
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['page_categories'];
        parent::__construct();
    }

    /**
     * ����� ��������� ���������
     * @return string
     */
    function pageCatal() {
        $dis = null;
        $i = 0;

        $where = array('parent_to' => '=0');

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $this->PHPShopOrm->cache = true;
        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array("limit" => 100));

        // �������� ������ � ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $data, 'START');
        if ($hook)
            return $hook;

        if (is_array($data))
            foreach ($data as $row) {

                // ���������� ����������
                $this->set('catalogId', $row['id']);
                $this->set('catalogI', $i);
                $this->set('catalogTemplates', $this->getValue('dir.templates') . chr(47) . $this->PHPShopSystem->getValue('skin') . chr(47));

                // ���� ���� ��������
                if ($this->check($row['id'])) {

                    $this->set('catalogName', $row['name']);
                    $this->set('catalogId', $row['id']);
                    $this->set('catalogPodcatalog', null);

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                    $dis .= $this->parseTemplate($this->getValue('templates.catalog_page_forma_2'));
                } else {
                    $this->set('catalogPodcatalog', $this->subcatalog($row['id']));
                    $this->set('catalogName', $row['name']);

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                    $dis .= $this->parseTemplate($this->getValue('templates.catalog_page_forma'));
                }

                $i++;
            }
        return $dis;
    }

    /**
     * �������� ������������
     * @param Int $id �� ��������
     * @return bool
     */
    function check($id) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;

        $where = array('parent_to' => "=$id");

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        $num = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1));
        if (empty($num['id']))
            return true;
    }

    /**
     * �������� ������������
     * @param Int $id �� ��������
     * @return bool
     */
    function checkPages($id) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page'));
        $PHPShopOrm->debug = $this->debug;

        $where = array('category' => "=$id");

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['category'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        $num = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1));
        if (empty($num['id']))
            return true;
    }

    /**
     * ����� ������������
     * @param Int $n �� ��������
     * @return string
     */
    function subcatalog($n) {
        $dis = null;
        $i = 0;
        $n = PHPShopSecurity::TotalClean($n, 1);

        $where = array('parent_to' => '=' . $n);

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array("limit" => 100));

        // �������� ������ � ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $data, 'START');
        if ($hook)
            return $hook;

        if (is_array($data))
            foreach ($data as $row) {

                // ���������� ����������
                $this->set('catalogId', $n);
                $this->set('catalogUid', $row['id']);
                $this->set('catalogI', $i);
                $this->set('catalogLink', 'CID_' . $row['id']);
                $this->set('catalogTemplates', $this->getValue('dir.templates') . chr(47) . $this->PHPShopSystem->getValue('skin') . chr(47));
                $this->set('catalogName', $row['name']);
                $i++;

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ���������� ������
                $dis .= $this->parseTemplate($this->getValue('templates.podcatalog_page_forma'));
            }
        return $dis;
    }

    /**
     * ����� ���� ��������� ������� � �������� �������������� ����
     * @return string
     */
    function topMenu() {
        $dis = $dis_page = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['menu'] = "='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['menu'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('id', 'name'), $where, array('order' => 'num,name'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                $dis_page = null;

                // ���������� ����������
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['id']);

                // ���� ���� ��������
                if (!$this->checkPages($row['id'])) {
                    $PHPShopOrm = new PHPShopOrm($this->getValue('base.page'));
                    $PHPShopOrm->debug = $this->debug;
                    $dataPage = $PHPShopOrm->select(array('link', 'name'), array('category' => '=' . $row['id'], 'enabled' => '="1"'), array('order' => 'num,name'), array("limit" => 100));
                    if (is_array($dataPage)) {
                        foreach ($dataPage as $rowPage) {
                            $dis_page .= PHPShopText::li($rowPage['name'], '/page/' . $rowPage['link'] . '.html', null);
                        }
                        $this->set('topMenuList', $dis_page);

                        // �������� ������
                        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                        // ���������� ������
                        $dis .= $this->parseTemplate($this->getValue('templates.page_top_menu'));
                    }
                } else
                    $dis .= str_replace('page/', 'page/CID_', $this->parseTemplate($this->getValue('templates.top_menu')));
            }

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $dis, 'END');
        if ($hook)
            return $hook;

        return $dis;
    }

    /**
     * ����� ��������� ������� �� �������
     * @return string
     */
    function getLastPages() {
        $dis = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        $where = array('enabled' => "='1'", 'preview' => '!=""');

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['preview'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->select(array('link', 'name', 'icon', 'datas', 'preview'), $where, array('order' => 'datas DESC'), array("limit" => $this->limit_last));

        // �������� �� ��������� ������
        if ($this->limit_last > 1)
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

/**
 * ������� ��������� �����
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopTextElement extends PHPShopElements {

    var $debug = false;

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['menu'];
        $this->template_debug = true;
        parent::__construct();
    }

    /**
     * ����� ���������� ����� � ����� �����
     * @return string
     */
    function leftMenu() {
        $dis = null;

        $where['flag'] = "='1'";
        $where['element'] = "='0'";

        // ���������
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['element'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ��������
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // �������� �������
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // �����
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {
                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
                PHPShopParser::set('productName', $PHPShopProduct->getParam('name'));
            }
            // ��������� ����������
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array("limit" => 100));
        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // �������� � ���������
                    if (!empty($row['dop_cat'])) {

                        if (empty($true_cid)) {
                            continue;
                        } elseif (!empty($true_cid) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                            continue;
                        }
                    }

                    // ���������� ����������
                    $this->set('leftMenuName', $row['name']);
                    $this->set('leftMenuContent', Parser($row['content']));

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row);

                    // ���������� ������
                    $dis .= $this->parseTemplate($this->getValue('templates.left_menu'));
                } else {
                    $dirs = explode(",", $row['dir']);
                    foreach ($dirs as $dir)
                        if (@strpos($_SERVER['REQUEST_URI'], $dir) or $_SERVER['REQUEST_URI'] == $dir) {
                            $this->set('leftMenuName', $row['name']);
                            $this->set('leftMenuContent', Parser($row['content']));

                            // �������� ������
                            $this->setHook(__CLASS__, __FUNCTION__, $row);

                            // ���������� ������
                            $dis .= $this->parseTemplate($this->getValue('templates.left_menu'));
                        }
                }
            }
        return $dis;
    }

    /**
     * ����� ���������� ����� � ������ �����
     * @return string
     */
    function rightMenu() {
        $dis = null;

        $where['flag'] = "='1'";
        $where['element'] = "='1'";

        // ���������
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['element'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ��������
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // �������� �������
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // �����
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {

                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
                PHPShopParser::set('productName', $PHPShopProduct->getParam('name'));
            }
            // ��������� ����������
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array("limit" => 100));
        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // �������� � ���������
                    if (!empty($row['dop_cat'])) {

                        if (empty($true_cid)) {
                            continue;
                        } elseif (!empty($true_cid) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                            continue;
                        }
                    }

                    // ���������� ����������
                    $this->set('leftMenuName', $row['name']);
                    $this->set('leftMenuContent', Parser($row['content']));

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row);

                    $dis .= $this->parseTemplate($this->getValue('templates.right_menu'));
                } else {
                    $dirs = explode(",", $row['dir']);
                    foreach ($dirs as $dir)
                        if (@strpos($_SERVER['REQUEST_URI'], $dir) or $_SERVER['REQUEST_URI'] == $dir) {
                            $this->set('leftMenuName', $row['name']);
                            $this->set('leftMenuContent', Parser($row['content']));

                            // �������� ������
                            $this->setHook(__CLASS__, __FUNCTION__, $row);

                            // ���������� ������
                            $dis .= $this->parseTemplate($this->getValue('templates.right_menu'));
                        }
                }
            }
        return $dis;
    }

    /**
     * ����� �������� �������������� ����
     * @return string
     */
    function topMenu() {
        $dis = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['category'] = "=1000";
        $where['enabled'] = "='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $objBase = $GLOBALS['SysValue']['base']['page'];
        $PHPShopOrm = new PHPShopOrm($objBase);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('name', 'link'), $where, array('order' => 'num'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                // ���������� ����������
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['link']);

                // �������� ��������
                if ($row['link'] == $this->PHPShopNav->getName(true))
                    $this->set('topMenuActive', 'active');
                else
                    $this->set('topMenuActive', '');

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ���������� ������
                $dis .= $this->parseTemplate($this->getValue('templates.top_menu'));
            }

        return $dis;
    }

    /**
     * ����� ������� �������������� ����
     * @return string
     */
    function bottomMenu() {
        $dis = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['enabled'] = "='1'";
        $where['footer'] = "='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $objBase = $GLOBALS['SysValue']['base']['page'];
        $PHPShopOrm = new PHPShopOrm($objBase);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('name', 'link'), $where, array('order' => 'num'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                // ���������� ����������
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['link']);

                // �������� ��������
                if ($row['link'] == $this->PHPShopNav->getName(true))
                    $this->set('topMenuActive', 'active');
                else
                    $this->set('topMenuActive', '');

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ���������� ������
                if (PHPShopParser::checkFile($this->getValue('templates.bottom_menu')))
                    $dis .= $this->parseTemplate($this->getValue('templates.bottom_menu'));
                else
                    $dis .= $this->parseTemplate($this->getValue('templates.top_menu'));
            }

        return $dis;
    }

}

/**
 * ������� ����� ��������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopSkinElement extends PHPShopElements {

    function __construct() {
        parent::__construct();

        // ������
        $this->setAction(array('post' => 'skin', 'get' => 'skin'));
    }

    /**
     * ����� �� ���������, ����� ����� ������ �������
     * @return string
     */
    function index() {
        if ($this->PHPShopSystem->getSerilizeParam("admoption.user_skin") == 1) {
            $dir = $this->getValue('dir.templates') . chr(47);
            if (is_dir($dir)) {
                if (@$dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (@file_exists($dir . '/' . $file . "/main/index.tpl")) {
                            if ($_SESSION['skin'] == $file)
                                $sel = "selected";
                            else
                                $sel = "";

                            if ($file != "." and $file != ".." and $file != "index.html") {


                                $value[] = array($file, $file, $sel);
                            }
                        }
                    }
                    closedir($dh);
                }
            }


            // ���������� ����������
            $forma = PHPShopText::div(PHPShopText::form(PHPShopText::select('skin', $value, '100%', $float = "none", $caption = false, $onchange = "ChangeSkin()"), 'SkinForm', 'get'), 'left', 'padding:10px');
            $this->set('leftMenuContent', $forma);
            $this->set('leftMenuName', __("������� ������"));

            // ���������� ������
            $dis = $this->parseTemplate($this->getValue('templates.right_menu'));
        }
        return $dis;
    }

    /**
     * ����� ����� �������
     */
    function skin() {
        if ($this->PHPShopSystem->getValue('num_vitrina')) {
            if (@file_exists("phpshop/templates/" . $_REQUEST['skin'] . "/main/index.tpl")) {
                $skin = $_REQUEST['skin'];
                if (PHPShopSecurity::true_skin($skin)) {
                    unset($_SESSION['Memory']);
                    unset($_SESSION['gridChange']);
                    $_SESSION['skin'] = $skin;

                    // ������ �������
                    if ($this->PHPShopSystem->ifSerilizeParam('admoption.min')) {
                        $this->set('pathTemplateMin', '/files/min.php?f=' . $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
                        $this->set('pathMin', '/files/min.php?f=');
                    } else {
                        $this->set('pathTemplateMin', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
                        $this->set('pathMin', null);
                    }
                }
            }
        }
    }

}

/**
 * ������� ��������� ������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopElements
 */
class PHPShopGbookElement extends PHPShopElements {

    /**
     * @var bool  ���������� ������ �� �������
     */
    var $disp_only_index = true;

    /**
     * @var Int ���-�� �������
     */
    var $limit = 10;

    /**
     * �����������
     */
    function __construct() {

        // �������
        $this->debug = false;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['gbook'];
        parent::__construct();
    }

    /**
     * ����� ��������� �������
     * @return string
     */
    function index() {
        global $PHPShopModules;
        $dis = null;

        // ���������� ������ �� ������� ��������
        if ($this->disp_only_index) {
            if ($this->PHPShopNav->index())
                $view = true;
            else
                $view = false;
        } else
            $view = true;

        if ($view) {

            $where['flag'] = "='1'";

            // ����������
            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array("limit" => $this->limit));
            if (is_array($data))
                foreach ($data as $row) {

                    // ������ �� ������
                    if (!empty($row['mail']))
                        $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']), $row['name']);
                    else
                        $d_mail = PHPShopText::b($row['name']);

                    // ���������� ���������
                    $this->set('gbookData', PHPShopDate::dataV($row['datas'], false, true));
                    $this->set('gbookName', $row['name']);
                    $this->set('gbookTema', $row['tema']);
                    $this->set('gbookMail', $d_mail);
                    $this->set('gbookOtsiv', $row['otsiv']);
                    $this->set('gbookOtvet', $row['otvet']);
                    $this->set('gbookId', $row['id']);

                    // �������� ������
                    $PHPShopModules->setHookHandler(__CLASS__, __FUNCTION__, $this, $row);

                    // ���������� ������
                    $dis .= $this->parseTemplate($this->getValue('templates.gbook_main_mini'));
                }

            return $dis;
        }
    }

}

/**
 * ������� ��������� �������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopElements
 */
class PHPShopNewsElement extends PHPShopElements {

    /**
     * @var bool ���������� ������� ������ �� �������
     */
    var $disp_only_index = true;

    /**
     * @var int  ���-�� ��������
     */
    var $limit = 3;

    /**
     * �����������
     */
    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['news'];
        parent::__construct();
    }

    /**
     * ����� ��������� ��������
     * @return string
     */
    function index() {
        $dis = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // ���������� ������ �� ������� ��������
        if ($this->disp_only_index) {
            if ($this->PHPShopNav->index())
                $view = true;
            else
                $view = false;
        } else
            $view = true;

        $where['datau'] = '<' . time();

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        if (!empty($view)) {

            $result = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'datau DESC'), array("limit" => $this->limit));

            // �������� �� ��������� ������
            if ($this->limit > 1)
                $data = $result;
            else
                $data[] = $result;

            if (is_array($data))
                foreach ($data as $row) {

                    // ���������� ����������
                    $this->set('newsId', $row['id']);
                    $this->set('newsZag', $row['zag']);
                    $this->set('newsData', $row['datas']);
                    $this->set('newsKratko', $row['kratko']);
                    $this->set('newsIcon', $this->setImage($row['icon']));

                    // �������� ������
                    $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');
                    if ($hook)
                        $dis .= $hook;
                    else
                        $dis .= $this->parseTemplate($this->getValue('templates.news_main_mini'));
                }
            return $dis;
        }
    }

}

/**
 * ������� ������ ����������� � �������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopElements
 */
class PHPShopSliderElement extends PHPShopElements {

    /**
     * @var bool ���������� ������� ������ �� �������
     */
    var $disp_only_index = true;
    var $template_debug = false;
    var $debug = false;

    /**
     * @var int  ���-�� �����������
     */
    var $limit = 25;

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['slider'];
        parent::__construct();
    }

    /**
     * ����� ����������� � �������
     * @return string
     */
    function index() {
        $dis = null;

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, false, 'START');

        // ���������� ������ �� ������� ��������
        $view = true;
        if ($this->disp_only_index && $this->PHPShopNav->index() === false) {
            $view = false;
        }

        // ���������
        $isMobile = PHPShopString::is_mobile();

        $where = [
            'enabled' => '="1"',
        ];

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        if (!empty($view)) {
            $result = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num, id DESC'), array("limit" => $this->limit));

            // �������� �� ��������� ������
            if ($this->limit > 1)
                $data = $result;
            else
                $data[] = $result;

            if (is_array($data))
                foreach ($data as $row) {

                    // ���������� ����������
                    $this->set('image', $this->setImage($row['image']));
                    $this->set('alt', $row['alt']);
                    $this->set('link', $row['link']);
                    $this->set('sliderID', $row['id']);
                    $this->set('sliderName', $row['name']);
                    $this->set('sliderLinkName', $row['link_text']);
                    $this->set('sliderColor', (int) $row['color']);

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                    // ���������� ������
                    $dis .= $this->parseTemplate("/slider/slider_oneImg.tpl");
                }
            if ($dis) {
                $this->set('imageSliderContent', $dis);
                return $this->parseTemplate("/slider/slider_main.tpl");
            }
            return false;
        }
    }

}

/**
 * ������� ������
 * @author PHPShop Software
 * @version 2.4
 * @package PHPShopElements
 */
class PHPShopBannerElement extends PHPShopElements {

    var $popup;
    var $horizontal;
    var $vertical;
    var $menu;

    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['banner'];
        parent::__construct();
        $this->index();
    }

    function template($row) {

        // ������
        $size_value = array('modal-sm', '', 'modal-lg');

        // ���������� ����������
        $this->set('banerTitle', $row['name']);
        $this->set('banerContent', $row['content']);
        $this->set('banerDescription', $row['description']);
        $this->set('banerImage', $this->setImage($row['image']));
        $this->set('banerLink', $row['link']);
        $this->set('banerColor', (int) $row['color']);
        $this->set('popupSize', $size_value[$row['size']]);
        $this->set('popupId', $row['id']);

        switch ($row['type']) {

            // ������ � �������
            case 0:

                $this->vertical = $this->parseTemplate($this->getValue('templates.baner_list_forma'));
                break;

            // PopUp
            case 1:

                // ������ ����� �� ����
                if ($row['display'] == 1) {

                    if (empty($_COOKIE['popup' . $row['id'] . '_close'])) {
                        if (PHPShopParser::checkFile($this->getValue('templates.banner_window_forma'))) {
                            $this->popup = $this->parseTemplate($this->getValue('templates.banner_window_forma'));
                        } else {
                            $this->popup = $this->parseTemplate('phpshop/lib/templates/banner/banner_window_forma.tpl', true);
                        }
                    }
                } else {

                    if (PHPShopParser::checkFile($this->getValue('templates.banner_window_forma'))) {
                        $this->popup = $this->parseTemplate($this->getValue('templates.banner_window_forma'));
                    } else {
                        $this->popup = $this->parseTemplate('phpshop/lib/templates/banner/banner_window_forma.tpl', true);
                    }
                }
                break;

            // ������ ��� ������
            case 2:

                $this->horizontal = $this->parseTemplate($this->getValue('templates.banner_horizontal_forma'));
                break;

            // ������ � ����
            case 3:

                $this->menu = $this->parseTemplate($this->getValue('templates.banner_menu_forma'));
                break;
        }
    }

    /**
     * ������� ������
     * @return string
     */
    function index() {

        $where['flag'] = "='1'";

        // ���������
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

// ��������
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // �������� �������
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // �����
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {

                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
            }
            // ��������� ����������
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $this->PHPShopOrm->debug = false;
        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'RAND()'), array("limit" => 100));

        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // �������� � ���������
                    if (!empty($true_cid) and ! empty($row['dop_cat']) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                        continue;
                    } elseif (!empty($row['dop_cat']) and empty($true_cid)) {
                        continue;
                    }

                    // ������
                    $this->template($row);
                } else {

                    $dirs = explode(",", $row['dir']);
                    if (!is_array($dirs))
                        $dirs[] = $row['dir'];

                    // ���������
                    foreach ($dirs as $dir) {
                        if (!empty($dir))
                            if ($this->PHPShopNav->objNav['truepath'] == trim($dir) or ! empty($true_cid)) {

                                if (!empty($true_cid) and empty($row['dop_cat']))
                                    continue;

                                // ������
                                $this->template($row);
                            }
                    }


                    if (!empty($row['dop_cat']) and empty($true_cid))
                        continue;
                }
            }
    }

    /**
     * ����� popup
     * @return string
     */
    function getPopup() {
        echo $this->popup;
    }

    /**
     * ����� ��������������� �������
     * @return string
     */
    function banersDispHorizontal() {
        return $this->horizontal;
    }

    /**
     * ����� ������� � ����
     * @return string
     */
    function banersDispMenu() {
        return $this->menu;
    }

    /**
     * ����� ������� � �������
     * @return string
     */
    function banersDisp() {
        return $this->vertical;
    }

}

/**
 * ������� ���� �������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopPhotoElement extends PHPShopElements {

    /**
     * �����������
     */
    function __construct() {

        // �������
        $this->debug = false;

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['photo_categories'];
        parent::__construct();
    }

    /**
     * ����� ���� �� ����������
     * @return string
     */
    function getPhotos() {
        $dis = null;
        $url = addslashes(substr($this->SysValue['nav']['url'], 1));
        if (empty($url))
            $url = '/';

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", "page" => " LIKE '%$url'"), array('order' => 'num'), array("limit" => 1000));

        if (is_array($data))
            foreach ($data as $row) {
                $this->set('photoTitle', $row['name']);
                $this->set('photoLink', $row['id']);
                $this->set('photoContent', $this->ListPhoto($row['id'], $row['count']));


                if (PHPShopParser::checkFile('photo/photo_list_forma.tpl'))
                    $dis .= ParseTemplateReturn('photo/photo_list_forma.tpl');
                else
                    $dis .= $this->parseTemplate('./phpshop/lib/templates/photo/photo_list_forma.tpl', true);
            }
        return $dis;
    }

    /**
     * ����� ����
     * @param int $cat �� ��������� ����
     * @param int $num ���-�� ���� ��� ������
     * @return string
     */
    function ListPhoto($cat, $num) {
        $dis = null;

        // ������� ������
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('category' => '=' . intval($cat), 'enabled' => "='1'"), array('order' => 'num'), array('limit' => $num));
        if ($num == 1)
            $dataArray[] = $data;
        else
            $dataArray = $data;

        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                $name_s = str_replace(".", "s.", $row['name']);
                $this->set('photoIcon', $name_s);
                $this->set('photoInfo', $row['info']);
                $this->set('photoImg', $row['name']);

                if (PHPShopParser::checkFile('photo/photo_list_forma.tpl'))
                    $dis .= ParseTemplateReturn('photo/photo_element_preview.tpl');
                else
                    $dis .= $this->parseTemplate('./phpshop/lib/templates/photo/photo_element_preview.tpl', true);
            }
        return $dis;
    }

}

/**
 * ������� �������� �������� Captcha
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopRecaptchaElement extends PHPShopElements {

    // ����� ����� Recaptcha 
    public $secret = '6LdhAiYUAAAAAGzO0wlENkavrN49gFhHiHqH9vkv';
    public $public = '6LdhAiYUAAAAAO1uc9b8KfotAyfoInSrWuygbQKC';
    protected $api = 'https://www.google.com/recaptcha/api/siteverify';
    // ����� ����� Hcaptcha         
    public $hsecret = '0xba1b193f433F4656778a3C7a96326CA412769E3D';
    public $hpublic = '6756c855-3f50-4360-a799-4f7b4855c927';
    protected $hapi = 'https://hcaptcha.com/siteverify';

    public function __construct() {

        parent::__construct();

        // Recaptcha
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {

            $public = $this->PHPShopSystem->getSerilizeParam('admoption.recaptcha_pkey');
            if (!empty($public))
                $this->public = $public;

            $secret = $this->PHPShopSystem->getSerilizeParam('admoption.recaptcha_skey');

            if (!empty($secret))
                $this->secret = $secret;

            if (isset($_POST['g-recaptcha-response']))
                $this->check = $_POST['g-recaptcha-response'];
        }
        // Hcaptcha
        elseif ($this->PHPShopSystem->ifSerilizeParam('admoption.hcaptcha_enabled')) {

            $public = $this->PHPShopSystem->getSerilizeParam('admoption.hcaptcha_pkey');
            if (!empty($public))
                $this->public = $public;
            else
                $this->public = $this->hpublic;

            $secret = $this->PHPShopSystem->getSerilizeParam('admoption.hcaptcha_skey');

            if (!empty($secret))
                $this->secret = $secret;
            else
                $this->secret = $this->hsecret;

            if (isset($_POST['h-captcha-response']))
                $this->check = $_POST['h-captcha-response'];
            $this->api = $this->hapi;
        }
    }

    /**
     * �������� ������������ ���������� ������
     * @return boolean
     */
    public function check() {
        if (!empty($this->check)) {
            $res = $this->request();

            if (!empty($res['success']))
                return true;
        }
    }

    /**
     * �������� �����
     * @param array $option ��������� �������� [url|captcha|referer]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {

        // �������� ��������� ������
        if (!empty($option['url'])) {
            preg_match_all('/http:?/', $_POST[$option['url']], $url, PREG_SET_ORDER);
            if (count($url) > 0)
                return false;
        }

        // �������� Referer
        if (!empty($option['referer'])) {
            if (!strpos($_SERVER["HTTP_REFERER"], $_SERVER['SERVER_NAME']))
                return false;
        }

        // �������� ������
        if ($option['captcha'] === true) {

            // ������ ��������
            if (!$this->PHPShopSystem->ifSerilizeParam('admoption.user_captcha_enabled')) {

                // Recaptcha
                if ($this->true()) {
                    $result = $this->check();
                    return $result;
                }

                // ������� ������
                elseif (!empty($_SESSION['text']) and strtoupper($_POST['key']) == strtoupper($_SESSION['text'])) {
                    return true;
                } else
                    return false;
            } else
                return true;
        }

        return true;
    }

    /**
     * �������� ������ �� �������
     * @return array
     */
    protected function request() {

        $rout = "?secret=" . $this->secret . "&response=" . $this->check;

        // ��������� �����
        if ($_SERVER["SERVER_ADDR"] == "127.0.0.1" and getenv("COMSPEC")) {
            $responsecontent = file_get_contents($this->api . $rout);
        } else {
            $data_string = $rout;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->api . $rout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ));

            $output = curl_exec($ch);
            curl_close($ch);

            $response = explode("\r\n\r\n", $output);
            $responsecontent = $response[1];
        }

        return json_decode($responsecontent, true);
    }

    /**
     * ����� �������� �������� captcha
     * @param string $name �� ������
     * @param string $size ������ ������ [normal|compact]   
     * @return string
     */
    public function captcha($name = 'default', $size = 'normal') {

        // ������ ��������
        if (!$this->PHPShopSystem->ifSerilizeParam('admoption.user_captcha_enabled')) {

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {
                $dis = '<div id="recaptcha_' . $name . '" data-size="' . $size . '" data-key="' . $this->public . '"></div>';
                $this->recaptcha = true;
            } else if ($this->PHPShopSystem->ifSerilizeParam('admoption.hcaptcha_enabled')) {
                $dis = '<div id="hcaptcha_' . $name . '" data-size="' . $size . '" data-key="' . $this->public . '"></div>';
                $this->recaptcha = true;
            } else {
                $dis = '<img src="phpshop/lib/captcha/captcha.php" align="left" style="margin-right:10px"> <input type="text" name="key" class="form-control" placeholder="' . __('��� � ��������') . '..." style="width:100px" required="">';
                $this->recaptcha = false;
            }
        } else
            $dis = null;

        return $dis;
    }

    /**
     * ������������ ��������
     * @return boolen
     */
    public function true(){
    return $this->recaptcha;






























    }

}

?>