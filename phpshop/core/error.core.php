<?php

/**
 * ���������� 404 ������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopError extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->title = '404';

        // �������� ������
        $hook=$this->setHook(__CLASS__, __FUNCTION__);
        if($hook)
            return $hook;

        header("HTTP/1.0 404 Not Found");
        header("Status: 404 Not Found");

        $this->parseTemplate($this->getValue('templates.error_page_forma'));
    }

}

?>