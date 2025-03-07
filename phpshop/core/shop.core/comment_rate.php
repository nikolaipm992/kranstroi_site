<?php

/**
 * ����� ������� ������ � ������ �� ������� �������������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 * @param args $args ������ ������ � ����������.
 * @return mixed
 */
function comment_rate($obj, $args) {
    $row = $args['row'];
    $type = $args['type'];

    // ������ glyphicon
    if ($row['rate'] > 0)
        $obj->set('rateStarA', 'glyphicon-star');
    else
        $obj->set('rateStarA', 'glyphicon-star-empty');
    if ($row['rate'] > 1)
        $obj->set('rateStarB', 'glyphicon-star');
    else
        $obj->set('rateStarB', 'glyphicon-star-empty');
    if ($row['rate'] > 2)
        $obj->set('rateStarC', 'glyphicon-star');
    else
        $obj->set('rateStarC', 'glyphicon-star-empty');
   if ($row['rate'] > 3)
        $obj->set('rateStarD', 'glyphicon-star');
    else
        $obj->set('rateStarD', 'glyphicon-star-empty');
   if ($row['rate'] > 4)
        $obj->set('rateStarE', 'glyphicon-star');
    else
        $obj->set('rateStarE', 'glyphicon-star-empty');
    
    $rate = new rateForComment($row['rate'], $row['rate_count']);
    if ($type)
        $obj->set("rateCid", $rate->parseCID());
    else
        $obj->set("rateUid", $rate->parseUid());
}

/**
 * ����� ������ ��������� ��� ���������� �������� � ��� ������ �������.
 * @package PHPShopElementsDepricated
 */
class rateForComment {

    function __construct($rate, $num) {
        $oneStarWidth = 20; // ������ ����� ��������
        $oneSpaceWidth = 0; // ������ ����� ����������
        
        /*
        // ���� ��������� � �������, ���� ������
        if (@$_SESSION['Memory']["rateForComment"]["oneStarWidth"])
            $oneStarWidth = $_SESSION['Memory']["rateForComment"]["oneStarWidth"];
        if (@$_SESSION['Memory']["rateForComment"]["oneSpaceWidth"])
            $oneSpaceWidth = $_SESSION['Memory']["rateForComment"]["oneSpaceWidth"];
        */

        if ($num) {
            $rate = round($rate, 1);
            $GLOBALS['SysValue']['other']['avgRateWidth'] = $oneStarWidth * $rate + $oneSpaceWidth * ceil($rate);
            $GLOBALS['SysValue']['other']['avgRateNum'] = $num;
            $GLOBALS['SysValue']['other']['avgRate'] = $rate;
        } else {
            $GLOBALS['SysValue']['other']['avgRateWidth'] = 0;
            $GLOBALS['SysValue']['other']['avgRateNum'] = 0;
            $GLOBALS['SysValue']['other']['avgRate'] = 0;
        }
    }

    function parseCid() {
        global $SysValue;
        // ���������� ������
        $path = './' . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/comment/avg_rate_cid.tpl";
        if (PHPShopParser::checkFile($path, true))
            return PHPShopParser::file($path, true);
        return null;
    }

    function parseUid() {
        global $SysValue;
        // ���������� ������
        $path = './' . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/comment/avg_rate_uid.tpl";
        if (PHPShopParser::checkFile($path, true))
            return PHPShopParser::file($path, true);
        return null;
    }

}

?>