<?php

/**
 * ��������� �� �������� ����������� ������������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 */
function message_register_success($obj) {

    $obj->set('user_mail', $_POST['mail_new']);
    $obj->set('user_name', $_POST['name_new']);
    $obj->set('user_login', $_POST['login_new']);
    $obj->set('user_password', $_POST['password_new']);

    // ����� ��� ��������� � �����������
    $admin_mail = $obj->PHPShopSystem->getParam('adminmail2');


    // ��������� e-mail ������������
    $title = $obj->lang('activation_title') . " " . $_POST['name_new'];

    // �������� e-mail ������������
    $PHPShopMail = new PHPShopMail($_POST['mail_new'], $admin_mail, $title, $content, true, true);
    // ���������� e-mail ������������
    $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_user_register_success.tpl', true);
    $PHPShopMail->sendMailNow($content);
}

?>