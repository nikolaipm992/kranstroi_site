<?php

/**
 * ���������� ��������������� ��������� �� ������� ��������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCore
 */
class PHPShopIndex extends PHPShopCore {


    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['page'];
        $this->debug = false;
        $this->template = 'templates.index';
        parent::__construct();
    }

    function index() {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;
        
        if($this->PHPShopNav->objNav['truepath'] != '/')
            return header('Location: /404/');
        
        $where['category'] = "=2000";
        $where['enabled'] = "='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif(defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������� ������
        $row = parent::getFullInfoItem(array('id,name,content'), $where);

        // ���������� ����������
        $this->set('mainContent', Parser($row['content'], $this->getValue('templates.index')));
        $this->set('mainContentTitle', Parser($row['name'], $this->getValue('templates.index')));
        $this->PHPShopNav->objNav['id'] = $row['id'];

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }
}

?>