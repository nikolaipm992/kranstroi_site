<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class PHPShopFormgenerator extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {

        // ��� ��
        $this->objBase = $GLOBALS['SysValue']['base']['formgenerator']['formgenerator_forms'];
        $this->debug = false;

        // ������ �������
        $this->action = array('nav' => 'index', 'post' => 'forma_send');

        parent::__construct();
    }

    /**
     * ����� �� ���������
     */
    function index() {

        $forma_path = $GLOBALS['SysValue']['nav']['nav'];

        if (!empty($forma_path))
            $this->forma($forma_path);
        else {
            $data = $this->PHPShopOrm->select(array('path'), false, false, array('limit' => 1));
            $this->forma($data['path']);
        }
    }

    function fixtags($str) {
        return str_replace(array('<//'), array('</'), $str);
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
     * ����� �������� �� �����
     */
    function forma_send() {
        $content = null;
        $error = false;
        $i = 1;

        if (PHPShopSecurity::true_num($_POST['forma_id']) and $this->security()) {

            $mail = PHPShopSecurity::TotalClean($_POST['forma_mail'], 3);

            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $data = $PHPShopOrm->select(array('*'), array('id' => "='" . $_POST['forma_id'] . "'", 'enabled' => "='1'"), false, array('limit' => 1));

            // �������������� ���� formgenerator_
            foreach ($_POST as $k => $v)
                if (strstr($k, 'formgenerator')) {

                    // �������� ������������ �����
                    if (strstr($k, '*') and empty($v)) {
                        $error = true;
                    }

                    // ���������� ���� ��� ������ ������������� ����������
                    $formamemory['formamemory' . $i] = $v;
                    $i++;

                    $content.='
' . str_replace('formgenerator_', '', $k) . ': ' . $v . '';
                }


            // ������ �� ������
            $product_id = intval($_POST['product_id']);
            if (!empty($product_id)) {

                $PHPShopProduct = new PHPShopProduct($product_id);
                $product_name = $PHPShopProduct->getName();
                if (!empty($product_name)) {
                    $content.= '
�����: ' . $product_name . '
ID: ' . $product_id . '
�������: ' . $PHPShopProduct->getParam('uid') . '
������: http://' . $_SERVER['SERVER_NAME'] . '/shop/UID_' . $product_id . '.html
                    ';
                }
            }


            // ���� ��� ���� ���������
            if (empty($error)) {

                PHPShopObj::loadClass("mail");
                $zag = $data['name'] . " - " . $this->PHPShopSystem->getValue("name");
                $content = '������� �������,

' . $data['name'] . '
---------------- 
E-mail: ' . $mail . '
' . $content . '
��������: ' . $_SERVER['HTTP_REFERER'] . '
';

                // ��������� ������������
                if (!empty($data['user_mail_copy']) and PHPShopSecurity::true_email($mail)) {
                    new PHPShopMail($mail, $this->PHPShopSystem->getEmail(), $zag, $content, false, false, array('replyto' => $this->PHPShopSystem->getEmail()));
                }


                // ���� ������ ����� �����������
                if (empty($mail))
                    $mail = $this->PHPShopSystem->getValue("adminmail2");

                // ���������� ����� �������� �����
                if (!class_exists('PHPShopMailFile'))
                    include_once($GLOBALS['SysValue']['class']['formgeneratormail']);

                // ��������� ��������������
                if (empty($data['mail']))
                    $data['mail'] = $this->PHPShopSystem->getEmail();
                if (!empty($_FILES['forma_file']['tmp_name']))
                    new PHPShopMailFile($data['mail'], $mail, $zag, $content, $_FILES['forma_file']['name'], $_FILES['forma_file']['tmp_name']);
                else {
                    new PHPShopMail($data['mail'], $this->PHPShopSystem->getEmail(), $zag, $content, false, false, array('replyto' => $mail));
                }

                // ����
                $this->title = "��������� ���������� - " . $this->PHPShopSystem->getValue("name");

                // ���������� ����������
                $this->set('pageContent', $data['success_message']);
                $this->set('pageTitle', $data['name']);

                // ���������� ������
                $this->parseTemplate($this->getValue('templates.page_page_list'));
            } else {

                if (is_array($formamemory))
                    foreach ($formamemory as $pole => $value)
                        $this->set($pole, $value);

                // �� ��������� ������������ ����
                $this->set('formamail', $mail);
                $this->set('isPage',true);

                if (!empty($data['dir']))
                    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=error_message");
                else
                    $this->forma($data['path'], $data['error_message']);
            }
        }
        else {
            $this->title = "������ - " . $this->PHPShopSystem->getValue("name");
            $this->set('pageContent', '������ ���������� �����.');
            $this->set('pageTitle', '������');

            // ���������� ������
            $this->parseTemplate($this->getValue('templates.page_page_list'));
        }
    }

    /**
     *  ����� �����
     */
    function forma($path, $error = false) {
        $i = 1;

        $path = PHPShopSecurity::TotalClean($path, 4);
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->select(array('*'), array('path' => "='" . $path . "'", 'enabled' => "='1'"), false, array('limit' => 1));

        if (is_array($data)) {

            // ������� ������ �����
            if (empty($error))
                while ($i < 10) {
                    $this->set('formamemory' . $i, '');
                    $i++;
                }


            if (!empty($_GET['error']))
                $error = $data['error_message'];
            
            $this->set('isPage',true);

            if(!empty($error))
                $error=PHPShopText::alert($error);

            
            $forma_content = $error.'<form method="post" enctype="multipart/form-data" name="formgenerator" id="formgenerator" action="/formgenerator/' . $path . '/">
            ' . Parser($this->fixtags($data['content']));

            // �������� ������
            if (!empty($data['captcha']))
                $forma_content.='<div class="form-group">'.Parser('@captcha@').'</div>';

            $forma_content.='<p id="formgenerator_buttons">
            <input type="hidden" name="forma_id" value="' . $data['id'] . '">
            <input type="hidden" name="product_id" value="' . $_REQUEST['product_id'] . '">
            <input class="btn btn-default" type="reset" value="��������">
            <input class="btn btn-primary" type="submit" name="forma_send" value="���������">
             </p>
</form>';

            // ���������� ����������
            $this->set('pageContent', $forma_content);
            $this->set('pageTitle', $data['name']);

            // ����
            $this->title = $data['name'] . " - " . $this->PHPShopSystem->getValue("name");

            // ���������� ������
            $this->parseTemplate($this->getValue('templates.page_page_list'));
        }
        else
            $this->setError404();
    }

}

?>