<?php
/**
 * ���������� ������������ html ������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopDoc extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        $this->empty_index_action=true;
        parent::__construct();
    }

    /**
     * ������� ����������� �����
     * @global array $SysValue ���������
     * @param string $pages ��� ����� ��� ����������
     * @return string
     */
    function OpenHTML($pages) {
        $dir="pageHTML/";
        $pages=$pages.".html";
        $handle=opendir($dir);
        while ($file = readdir($handle)) {
            if($file==$pages) {
                $urlfile=fopen ("$dir$file","r");
                $text=fread($urlfile,1000000);
                $text=Parser($text,$this->parser);
                return $text;
            }
        }
        return false;
    }
    /**
     * ����� �� ���������
     */
    function index() {

        // ������ ����
        $dis=$this->OpenHTML($this->SysValue['nav']['name']);

        // ����
        $meta = $this->getMeta($dis);

        $this->title=$meta['title'].' - '.$this->PHPShopSystem->getValue("name");
        $this->description = $meta['description'];
        $this->keywords = $meta['keywords'];

        // ���������� ���������
        $this->set('pageContent',$dis);
        $this->set('pageTitle',$this->meta[$this->SysValue['nav']['name']]);


        // ���������� ������
        $this->parseTemplate($this->getValue('templates.page_page_list'));

    }

    function getMeta($content) {

        // Title
        $patern="/<h1>(.*)<\/h1>/i";
        preg_match($patern,$content,$matches);
        $title = $matches[1];

        // Description
        $patern="/<desc>(.*)<\/desc>/i";
        preg_match($patern,$content,$matches);
        $description = $matches[1];

        // Keywords
        $patern="/<key>(.*)<\/key>/i";
        preg_match($patern,$content,$matches);
        $keywords = $matches[1];

        return array('title'=>$title,'description'=>$description,'keywords'=>$keywords);
    }

}

?>
