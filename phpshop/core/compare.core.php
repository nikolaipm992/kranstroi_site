<?php

/**
 * ���������� ���������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopTest
 */
class PHPShopCompare extends PHPShopCore {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();

        // ��������� ������� ������
        $this->navigation(false, __('���������'));
    }

    /**
     * ��� ��������
     * @param int $id �� ���������
     * @return string 
     */
    function getfullname($id) {
        global $PHPShopShopCatalogElement;

        $parent = $PHPShopShopCatalogElement->CategoryArray[$id]['parent_to'];

        if (!empty($parent))
            $name = $PHPShopShopCatalogElement->CategoryArray[$parent]['name'] . ' / ' . $PHPShopShopCatalogElement->CategoryArray[$id]['name'];
        else
            $name = $PHPShopShopCatalogElement->CategoryArray[$id]['name'];

        return $name;
    }

    /**
     * ����� �� ���������
     */
    function index() {
        global $SysValue, $PHPShopSystem, $PHPShopValutaArray, $link_db;

        $limit = 40; //�������� ������� ��� ���������

        $LoadItems['Valuta'] = $PHPShopValutaArray->getArray();
        $LoadItems['System'] = $PHPShopSystem->getArray();

        if (!empty($_SESSION['compare']))
            $copycompare = $_SESSION['compare'];
        else
            $copycompare = array();


        if (!($SysValue['nav']['id'] == "ALL")) {
            $SysValue['nav']['id'] = intval($SysValue['nav']['id']);
        }

        $COMCID = 0;

        if ($SysValue['nav']['nav'] == "COMCID") {
            if (isset($SysValue['nav']['id']) && ($SysValue['nav']['id'])) {
                $COMCID = $SysValue['nav']['id'];
            }
        }

        // ���������� �������� ������� � ���������
        if (is_array($copycompare))
            sort($copycompare); //��������� �� ����������
        $oldcatid = ''; //�������������� ������������� ���������

        if (is_array($copycompare))
            foreach ($copycompare as $id => $val) {

                //���� ������������� ��������� ������ ���������, ������ ���������
                if ($oldcatid != $val['category']) {

                    $catid = $val['category'];
                    $oldcatid = $catid; //������ �������������.
                    $cats[$catid] = $this->getfullname($catid);
                }
                $goods[$oldcatid][$id]['name'] = $val['name'];
                $goods[$oldcatid][$id]['id'] = $val['id'];
                $compare[$id] = $goods[$oldcatid][$id];
            }



        $dis = "";

        if (empty($cats))
            $cats = 0;

        if (is_array($cats))
            foreach ($cats as $catid => $name) {
                if ((count($goods[$catid]) > 1) && (count($goods[$catid]) <= $limit)) {
                    if ($catid != $COMCID) {
                        $as = '<b>' . __('�������� � ���������') . '</b>: <a href="/compare/COMCID_' . $catid . '.html#list" title="' . __('�������� �') . ' ' . $name . '">';
                        $ae = '</a>';
                    } else {
                        $as = '<b>';
                        $ae = '</b>';
                    }
                    $dis.='

		<tr><td colspan="2">' . $as . $name . $ae . '</td></tr>';
                    $green[] = $catid; //�������� ������� � �����������
                } elseif (count($goods[$catid]) > $limit) {
                    $dis.='
		<tr><td><br><b>' . $name . '</b> <p class="text-danger">' . __('C������ ����� ������� � ���������, �������� <b>' . $limit . '</b>. ������� ������') . '.</p></td>
		<td width="50"></td></tr>';
                } else {
                    $dis.='
  <tr><td id=allspec colspan="2"><b>' . $name . '</b> </td>
  </tr>';
                }
                foreach ($goods[$catid] as $id => $val) {
                    $dis.='<tr><td>' . $val['name'] . ' </td><td width="50" class="text-center"><a href="/compare/DID_' . $val['id'] . '.html" class="btn btn-danger btn-xs" title="{�������}"><span>X</span></a></td></tr>';
                }
            }

        // ���������� - �������� �� ���� ����������
        if (is_array($cats) and count($cats) > 1) { //���� ������ ���� ���������
            $name = '�� ���� ����������';
            if ((count($compare) > 1) && (count($compare) <= $limit)) {
                if ($COMCID != "ALL") {
                    $as = '<a href="/compare/COMCID_ALL.html#list" title="' . __('�������� �� ���� ����������') . '"><span class="glyphicon glyphicon-ok"></span> ' . __('��������') . ' ';
                    $ae = '</a>';
                } else {
                    $as = '<b>' . __('������������') . ': ';
                    $ae = '</b>';
                }
                $dis.='
		<tr><td colspan="2" id=allspec>' . $as . $name . $ae . '</td>
		<!--<td>&nbsp;</td>--></tr>';
                $green[] = "ALL"; //�������� ������� � �����������
            } elseif (count($compare) > $limit) {
                $dis.='
		<tr><td colspan="2"><b>' . $name . '</b> <p class="text-danger">' . __('C������ ����� ������� � ���������, �������� <b>' . $limit . '</b>. ������� ������') . '.</p></td>
		</tr>';
            } else {
                $dis.='
		<tr><td colspan="2"><b>' . $name . '</b> <p class="text-danger">' . __('������������ ������� ��� ���������. �������� ��� ������ �� ���� ���������') . '</p></td>
		</tr>';
            }
        }

        // ����� ������������ ����������
        $disp = '<table class="table table-bordered ">' . $dis . '</table>';

        // ����� �������� ��� ������
        if (!$COMCID) { //���� �� ������ �������
            if (!empty($green) and is_array($green) and count($green) > 0) {//���� ���� ���� ������� ����� ��������
                krsort($green);
                foreach ($green as $c) {
                    $COMCID = $c;
                    break;
                }
            } else {
                $disp.='<p class="text-danger">' . __('�������� ������ ��� ���������') . '.</p>';
            }
        }

        // ��������� �������� �������������
        if ($SysValue['nav']['nav'] == "DID") {
            $id = $SysValue['nav']['id'];
            if ($id == "ALL") {
                $_SESSION['compare'] = null;
                unset($_SESSION['compare']);
                header("Location: ../compare/");
            } else {
                unset($_SESSION['compare'][$id]);
                header("Location: ../compare/");
            }
        }

        $catid = $COMCID;

        // ��������
        if (!empty($_SESSION['compare']))
            if (($COMCID && (is_array($goods[$catid]) and count($goods[$catid]) > 1) && (is_array($goods[$catid]) and count($goods[$catid]) <= $limit)) ||
                    ((($COMCID == "ALL") && (count($_SESSION['compare']) > 1) && (count($_SESSION['compare']) <= $limit)))) { //���� ������ ������� ���������
                if ($COMCID == "ALL") {
                    $comparing = __('��� ���������');
                } else {
                    $comparing = $this->getfullname($COMCID);
                }

                $disp.='<a name="list"></a><P><h4>' . $comparing . '</h4></P>';

                if ($COMCID != "ALL") {
                    $sql = 'select sort from ' . $SysValue['base']['table_name'] . ' where id=' . intval($COMCID);
                    $result = mysqli_query($link_db, $sql);
                    @$row = mysqli_fetch_array(@$result);
                    $sorts = unserialize($row['sort']);
                } else {
                    foreach ($cats as $catid => $name) {
                        $sql = 'select sort from ' . $SysValue['base']['table_name'] . ' where id=' . intval($catid);
                        $result = mysqli_query($link_db, $sql);
                        @$row = mysqli_fetch_array(@$result);
                        $tempsorts = unserialize($row['sort']);
                        if (is_array($tempsorts))
                            foreach ($tempsorts as $curtempsort) {
                                $sorts[] = $curtempsort;
                            }
                    }
                }
                if (is_array($sorts))
                    $sorts = array_unique($sorts); //��������� ������ ���������� ����������

                $sorts_name = array();

                if (is_array($sorts))
                    foreach ($sorts as $sort) {
                        $sql = 'select name from ' . $SysValue['base']['sort_categories'] . ' where id=' . intval($sort);
                        $result = mysqli_query($link_db, $sql);
                        @$row = mysqli_fetch_array(@$result);
                        $sorts_name[$sort] = $row['name'];
                    }

                /*
                 * ������ ����� ���� �� ������ ���������, � ������� ���������� �������������� ����� ����� ������ ��������
                 * ������� ����������� ������. �������� ������ ���_�������������� = ������ ���������������
                 */

                if (is_array($sorts_name))
                    foreach ($sorts_name as $sort => $name) {
                        $sql = 'select id from ' . $SysValue['base']['sort_categories'] . ' where name LIKE \'' . $name . '\'';
                        $result = mysqli_query($link_db, $sql);
                        while ($row = mysqli_fetch_array(@$result)) {
                            $sorts_name2[$name][$row['id']] = 1;
                        }
                    }

                if (empty($sorts_name2))
                    $sorts_name2 = 0;

                // ���������� ������� ��� ������� �������
                $tdR[0][] = '<div class="prod-title">�����</div>';
                $tdR[0][] = '<div class="prod-photo">����</div>';
                $tdR[0][] = '<div class="prod-price">����</div>';

                if (is_array($sorts_name2))
                    foreach ($sorts_name2 as $name => $id) {
                        $tdR[0][] = '<div class="prod-sort">' . $name . '</div>';
                    }
                $tdR[0][] = '<div class="prod-desc">��������</div>';
                $igood = 0;

                if ($COMCID != "ALL") {
                    $goodstowork = $goods[$COMCID];
                } else {
                    foreach ($cats as $catid => $name) {
                        foreach ($goods[$catid] as $curtempgood) {
                            $goodstowork[] = $curtempgood;
                        }
                    }
                }

                foreach ($goodstowork as $id => $val) {
                    $igood++;
                    $tdR[$igood][] = '<A class="prod-title" href="/shop/UID_' . $val['id'] . '.html" title="' . $val['name'] . '">' . $val['name'] . '</A>';

                    //�������� ����� �� ����
                    $sql = 'select id,price,pic_small,vendor_array,content,baseinputvaluta from ' . $SysValue['base']['products'] . ' where id=' . intval($val['id']);
                    $result = mysqli_query($link_db, $sql);
                    @$row = mysqli_fetch_array(@$result);
                    if (trim($row['pic_small'])) {
                        $tdR[$igood][] = '<div  class="prod-photo"><img class="media-object" src="' . $row['pic_small'] . '"></div>';
                    } else {
                        $tdR[$igood][] = '����������� �����������';
                    }

                    $id = $row['id'];

                    $admoption = unserialize($LoadItems['System']['admoption']);

                    // ���� ���� ���������� ������ ����� ����������
                    if ($admoption['user_price_activate'] == 1 and !$_SESSION['UsersId']) {
                        $price = "-";
                    }
                    else
                        $price = PHPShopProductFunction::GetPriceValuta($row['id'], array($row['price'], $row['price2'], $row['price3'], $row['price4'], $row['price5']), $row['baseinputvaluta']);

                    if($PHPShopSystem->getParam("shop_type") == 0){
                        
                    $tdR[$igood][] = '<div class="prod-price"><span class="new-price">' . number_format($price,$this->format, '.', ' ') . ' '.$this->PHPShopSystem->getValutaIcon().'</span></div>';
                    }
                    $chars = unserialize($row['vendor_array']);
                    foreach ($chars as $k => $char) {
                        $chars[$k] = array_unique($char);
                    }

                    if (is_array($sorts_name2))
                        foreach ($sorts_name2 as $name => $ids) {
                            $curchar = '';
                            foreach ($ids as $id => $true) {
                                @$ca = $chars[$id];
                                if (is_array($ca))
                                    foreach ($ca as $charid) {
                                        $sql2 = 'select name from ' . $SysValue['base']['sort'] . ' where id=' . intval($charid);
                                        $result2 = mysqli_query($link_db, $sql2);
                                        @$row2 = mysqli_fetch_array(@$result2);
                                        $curchar.=' ' . $row2['name'] . '<br>';
                                    }
                            }
                            $tdR[$igood][] = '<div class="prod-sort"><b>' . $name . '</b><br> ' . $curchar . '</div>';
                        }
                    $tdR[$igood][] = '<div class="prod-desc">' . stripslashes($row['content']) . '</div>';
                }

                // C����� ������� �� �������
                $rows = count($tdR[0]);
                $cols = count($goodstowork) + 1;
                $disp.='<div class="swiper-slider-wrapper compare-wrapper">                            
                    <div class="swiper-button-prev-block">
                                <div class="swiper-button-prev btn-prev10">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="swiper-button-next-block">
                                <div class="swiper-button-next btn-next10">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            </div><div class="swiper-container compare-slider"><div class="swiper-wrapper">';

                for ($col = 0; $col < $cols; $col++) {
                    $disp.='<div>';
                    for ($row = 0; $row < $rows; $row++) {
                        $value = trim($tdR[$col][$row]);
                        if (!$value) {
                            $value = '&nbsp;';
                        }

                        $disp.='<div>' . $value . '</div>';
                    }
                    $disp.='</div>';
                }
                $disp.='</div></div></div>';
            }

        //���� ��� �������, �������� �����. ������ ���� ��������� �������
        if (empty($cats)) {
            $disp = '<P><h5>' . __('����������, �������� ������ ��� ���������') . '</h5></P>';
        }

        // ���������� ���������
        $SysValue['other']['pageTitle'] = $SysValue['other']['pageTitl'] = $SysValue['other']['catalogCat'] = __("��������� �������");
        $SysValue['other']['pageContent'] = '<div class="compare_list">' . $disp . '</div>';
        $SysValue['other']['catalogCategory'] = __("������� ������ ��� ���������");

        // ����
        $this->description = $SysValue['other']['pageTitle'];
        $this->title = $SysValue['other']['pageTitle'] . ' - ' . $this->PHPShopSystem->getValue("name");


        // ���������� ���������
        $this->set('pageContent', $disp);
        $this->set('pageTitle', $SysValue['other']['pageTitle']);


        // ���������� ������
        if (PHPShopParser::checkFile("users/compare/compare_page_list.tpl"))
            $this->parseTemplate('users/compare/compare_page_list.tpl');
        else
            $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

}

?>
