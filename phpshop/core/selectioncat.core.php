<?php
/**
 * ���������� ������� ������� �� ��������������� ��� ���������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopShopCore
 */
class PHPShopSelectioncat extends PHPShopShopCore {

    var $debug=true;
    var $cache=false;
    var $cache_format=array('content','yml_bid_array');
    var $max_item=100;

    /**
     * �����������
     */
    function __construct() {
        
        // ������ �������
        $this->action=array("get"=>"v",'nav'=>'index');
        parent::__construct();
        $this->PHPShopOrm->cache_format=$this->cache_format;
    }

    function index() {
        $this->setError404();
    }

    /**
     * ����� ������ �������
     */
    function v() {

        // �������� ������
        if($this->setHook(__CLASS__,__FUNCTION__,$category,'START'))
            return true;

        $v=$_GET['v'];
        $sort=null;
        $disp=null;

        // ���������� �� ���������������
        if(is_array($v)) {
            foreach($v as $key=>$value) {
                if(PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($value)) {
                    $hash=$key."-".$value;
                    $sort.=" and vendor REGEXP 'i".$hash."i' ";
                    $v_str="v[".$key."]=".$value;
                }
                else return $this->setError404();
            }
        }
        else return $this->setError404();


        $this->PHPShopOrm->sql="select DISTINCT(category) from ".$this->getValue('base.products')." where  enabled='1' $sort";
        $this->PHPShopOrm->comment=__CLASS__.'.'.__FUNCTION__;
        $this->dataArray=$this->PHPShopOrm->select();

        if(is_array($this->dataArray)) {

            $PHPShopCategoryArray = new PHPShopCategoryArray();

            foreach($this->dataArray as $row) {
                $category=$row['category'];
                $parent = $PHPShopCategoryArray->getParam($category.'.parent_to');
                $catArray[$parent][]=$category;
            }

        }
        else $this->setError404();

        $this->add(PHPShopText::p(),true);
        if(is_array($catArray))
            foreach($catArray as $key=>$val) {
                $dis=null;

                foreach($val as $value)
                    $dis.=PHPShopText::li($PHPShopCategoryArray->getParam($value.'.name'),'/shop/CID_'.$value.'.html?'.$v_str);

                $this->add(PHPShopText::h2($PHPShopCategoryArray->getParam($key.'.name')).PHPShopText::ul($dis),true);
            }


        // ���������
        $this->title=__('����� ��������� �� ��������������')." - ".$this->PHPShopSystem->getParam('title');

        // �������� ������
        $this->setHook(__CLASS__,__FUNCTION__,$catArray,'END');

        // ���������� ������
        $this->parseTemplate($this->getValue('templates.product_selection_list'));
    }

}
?>