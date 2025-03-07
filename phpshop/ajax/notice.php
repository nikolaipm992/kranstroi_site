<?php

session_start();

$_classPath = "../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "inc/elements.inc.php");
include_once($_classPath . "class/mail.class.php");
include_once($_classPath . "core/users.core.php");
include_once($_classPath . "core/users.core/notice_add.php");
PHPShopObj::loadClass(['base', 'system', 'security', 'valuta', 'lang', 'security', 'product', 'parser', 'user']);

// ����������� � ��
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
// �������, ������ ��� ���������� � ������ ������ �������.
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopNav = new PHPShopNav();
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopLang = new PHPShopLang(['locale' => $_SESSION['lang'], 'path' => 'shop']);
if ($PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {
    $PHPShopRecaptchaElement->recaptcha = true;
}
$ajaxNotice = new AjaxNotice();

try {
    if (isset($_REQUEST['loadForm'])) {
        $_RESULT = $ajaxNotice->getProductData((int) $_REQUEST['productId']);
        $_RESULT['success'] = true;
    } else {
        $ajaxNotice->write($_REQUEST['mail']);
        $_RESULT = [
            'message' => PHPShopString::win_utf8(__("�������! �� �������� ��� ��� ��������� ������ � �������.")),
            'success' => true
        ];
    }
} catch (\Exception $exception) {
    $_RESULT = [
        'message' => PHPShopString::win_utf8($exception->getMessage()),
        'success' => false
    ];
}

echo json_encode($_RESULT);

class AjaxNotice {

    public function write($email) {
        if ($this->security()) {
            $_POST['name_new'] = PHPShopString::utf8_win1251(strip_tags($_POST['name_new']));
            $_POST['name_new'] = PHPShopSecurity::TotalClean($_POST['name_new'], 4);
            $_POST['message'] = PHPShopString::utf8_win1251(strip_tags($_POST['message']));
            $_POST['tel_new'] = PHPShopString::utf8_win1251(strip_tags($_POST['tel_new']));
            $PHPShopUsers = new PHPShopUsers();
            $PHPShopUsers->add_user_from_order($email);

            notice_add($PHPShopUsers);
            $this->lead();
        } else {
            throw new Exception(__("������ �����, ��������� ������� ����� �����"));
        }
    }

    private function security() {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security(['url' => false, 'captcha' => true, 'referer' => true]);
    }

    /**
     * ���������� ����
     */
    private function lead() {
        
        $product = $this->getProductData((int) $_REQUEST['productId']);
        
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
        $content = "http://" . $_SERVER['SERVER_NAME'] . "/shop/UID_" . $product['id'] . ".html\n".PHPShopString::utf8_win1251($_POST['message']);
        $insert = array('date_new' => time(), 'message_new' => __('�����������').' '.PHPShopString::utf8_win1251($product['title']), 'name_new' => $_POST['name_new'], 'mail_new' => $_POST['mail'], 'tel_new' => '', 'content_new' => __('����������� � ����������� ������').' '.PHPShopSecurity::TotalClean($content));
        $PHPShopOrm->insert($insert);
    }

    public function getProductData($productId) {
        $product = new PHPShopProduct($productId);

        return [
            'id' => $productId,
            'link' => sprintf('/shop/UID_%s.html', $productId),
            'title' => PHPShopString::win_utf8($product->getName()),
            'image' => sprintf('<a href="'.$GLOBALS['SysValue']['dir']['dir'].'/shop/UID_%s.html" title="%s">
                                   <img class="one-image-slider" src="%s" alt="%s" title="%s"/>
                                </a>', $productId, PHPShopString::win_utf8($product->getName()), $product->getImage(), PHPShopString::win_utf8($product->getName()), PHPShopString::win_utf8($product->getName()))
        ];
    }

}

?>