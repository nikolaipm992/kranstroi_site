<?php

/**
 * ����� ������ ������ �� ��������
 * @package PHPShopCoreFunction
 * @param string $uid ������� ������
 * @return array
 */
function getExcelInfoUid($uid,$obj) {
    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.products'));
    $uid=PHPShopSecurity::true_search($uid);
    $row=$PHPShopOrm->select(array('id'),array('uid'=>"='$uid'"),false,array('limit'=>1));
    if(is_array($row)) return $row['id'];
}

/**
 * ��������� ������� �������
 * @package PHPShopCoreFunction
 */
function import($obj,$from) {
    switch($from) {

        // ��������� ������� �� Shop2CD
        case "html":
            if(PHPShopSecurity::true_num($_GET['id'])) {
                $obj->PHPShopCart->add($_GET['id'],1);
            }
            break;

        // ��������� ������� �� Excel OnLine Price
        case "onlineprice":
            $excel_cart=base64_decode($_GET['c']);
            parse_str($excel_cart,$order_array);
            if(is_array($order_array['c'])) {
                foreach ($order_array['c'] as $k=>$num) {
                    if(PHPShopSecurity::true_num($k)) {
                        $obj->PHPShopCart->add($k,$num);
                    }
                }
            }
            break;

        // ��������� ������� �� Excel 1C Price
        default:

            $excel_cart=base64_decode(@$_GET['c']);
            parse_str($excel_cart,$order_array);

            if(!empty($order_array['c']) and is_array($order_array['c'])) {
                foreach ($order_array['c'] as $k=>$num) {
                    $id=getExcelInfoUid($k,$obj);
                    if(PHPShopSecurity::true_num($id)) {
                        $obj->PHPShopCart->add($id,$num);
                    }
                }
            }

            break;
    }
}
?>