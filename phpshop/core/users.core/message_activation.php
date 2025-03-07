<?php

/**
 * ��������� ����������� ������������
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 */
function message_activation($obj) {

    $obj->set('user_key', $obj->user_status);
    $obj->set('user_mail', PHPShopSecurity::TotalClean($_POST['mail_new'], 3));
    $obj->set('user_name', PHPShopSecurity::TotalClean($_POST['name_new'], 4));
    $obj->set('user_login', $_POST['login_new']);
    $obj->set('user_password', $_POST['password_new']);

    // ����� ��� ��������� � �����������
    $admin_mail = $obj->PHPShopSystem->getParam('adminmail2');

    // ��������� e-mail ������������
    $title = $obj->lang('activation_title') . " " . PHPShopSecurity::TotalClean($_POST['name_new']);

    if ($obj->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate')) {


        // �������� e-mail ������������
        $PHPShopMail = new PHPShopMail($_POST['mail_new'], $admin_mail, $title, '', true, true);
        // ���������� e-mail ������������
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_user_activation.tpl', true);
        $PHPShopMail->sendMailNow($content);

        $obj->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_activation.tpl', true));
    } elseif ($obj->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate_pre')) {
        
        // �������� e-mail ������������, ��� �� ������ ������� ������ ��������� �� ���������������� �������.
        $PHPShopMail = new PHPShopMail($_POST['mail_new'], $admin_mail, $title, '', true, true);
        // ���������� e-mail ������������
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_user_activation_by_admin.tpl', true);
        $PHPShopMail->sendMailNow($content);

        // ��������� e-mail ��������������
        $title = $obj->lang('activation_admin_title') . " " . $_POST['name_new'];

        // �������� e-mail ��������������
        $PHPShopMail = new PHPShopMail($admin_mail, $admin_mail, $title, '', true, true);
        
        // ���������� e-mail  ��������������
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_activation.tpl', true);
        $PHPShopMail->sendMailNow($content);
        
        $obj->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_admin_activation.tpl', true), true);
    }


    $obj->set('formaTitle', $obj->lang('user_register_title'));
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

?>