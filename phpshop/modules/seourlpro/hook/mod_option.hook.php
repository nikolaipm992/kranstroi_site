<?php

// ��������� ������
class PHPShopSeourlOption extends PHPShopArray {
    
    function __construct() {
        $this->objType=3;
        $this->checkKey=true;
        
        // ������ ��������
        $this->memory = __CLASS__;
        
        $this->objBase=$GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'];
        parent::__construct('paginator','cat_content_enabled', 'seo_brands_enabled', 'seo_news_enabled', 'seo_page_enabled');
    }
}

?>