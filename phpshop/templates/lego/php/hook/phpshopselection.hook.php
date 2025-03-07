<?php

// сохран€ем значени€ подбора, чтобы можно было применить сортировку...
// в стандартной реализации не сохран€етс€ и сортировка в подборе не используетс€.
function v_nt_hook($obj, $row, $rout) {
    if ($rout == 'START') {
        
        
        // —ортировка по характеристикам сохран€ем значени€
        if (is_array($_GET['v'])) {
            foreach ($_GET['v'] as $k => $v)
                $productVendor.='v[' . intval($k) . ']=' . intval($v) . '&';
            $productVendor = substr($productVendor, 0, strlen($productVendor) - 1);
        }
        if ($productVendor)
            $obj->set('productVendor', $productVendor);


        switch ($_GET['gridChange']) {
            case 1:
                $obj->set('gridSetAactive', 'active');
                break;
            case 2:
                $obj->set('gridSetBactive', 'active');
                break;
            default: $obj->set('gridSetAactive', 'active');
        }

/*
        switch ($_GET['s']) {
            case 1:
                $obj->set('sSetAactive', 'active');
                break;
            case 2:
                $obj->set('sSetBactive', 'active');
                break;
            default: $obj->set('sSetCactive', 'active');
        }*/

        switch ($_GET['f']) {
            case 1:
                $obj->set('fSetAactive', 'active');
                $obj->set('fSetAchecked', 'checked="checked"');
                break;
            case 2:
                $obj->set('fSetBactive', 'active');
                $obj->set('fSetBchecked', 'checked="checked"');
                break;
            
        }
        
        if(empty($_GET['f'])){
            $obj->set('fSetCactive', 'active');
            $obj->set('fSetCchecked', 'checked="checked"');
        }
    }
}

// делаем возможность выбора брендов по алфавиту при переходе на /selection/
function index_nt_hook($obj, $row, $rout) {
    global $SysValue;
    setlocale(LC_ALL, 'ru_RU.CP1251');
    // получаем все характеристики бренды
    // ћассив имен характеристик
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = $obj->debug;
    $result = $PHPShopOrm->query("select * from " . $SysValue['base']['table_name20'] . " where (brand='1' and goodoption!='1') order by num");
    while (@$row = mysqli_fetch_assoc($result)) {
        $arrayVendor[$row['id']] = $row;
    }


    if (is_array($arrayVendor))
        foreach ($arrayVendor as $key => $value) {
            if (is_numeric($key))
                $sortValue.=' category=' . $key . ' OR';
        }
    $sortValue = substr($sortValue, 0, strlen($sortValue) - 2);

    if (!empty($sortValue)) {

        // ћассив значений характеристик
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $obj->debug;
        $result = $PHPShopOrm->query("select id, name, category from " . $SysValue['base']['table_name21'] . " where $sortValue order by num");
        while (@$row = mysqli_fetch_array($result)) {
            $arrForSort[$row['name']] = $row['id'];
            $arrParentCat[$row['id']] = $row['category'];
        }
    }
    if (count($arrParentCat)) {
        ksort($arrForSort);
        $arrForSort = array_merge($arrForSort, array("" => "noId"));
        foreach ($arrForSort as $value => $key) {
            $charOld = $char;
            $char = substr(strtoupper($value), 0, 1);
            if ($charOld != $char) {
                $charList.= '&nbsp;&nbsp;&nbsp;<a href="#' . $char . '"><b>' . $char . '</b></a>';
                if (!empty($charOld)) {
                    $obj->set('brandChar', $charOld);
                    $obj->set('brands', $brands);
                    $brands = '';
                    $brandsList .= parseTemplateReturn('selection/page_selection_search_oneChar.tpl');
                }
            }
            $brands .= '<ul>
            <li><a href="/selection/?v[' . $arrParentCat[$key] . ']=' . $key . '">' . $value . '</a></li>
            </ul>';
        }
        $obj->set('brandsList', $brandsList);
        $obj->set('charList', $charList);
    }
    else
        $obj->set('charList', 'нет данных');

    // ѕодключаем шаблон
    $obj->parseTemplate('selection/page_selection_search_main.tpl');
    return true;
}

$addHandler = array
    (
    'index' => 'index_nt_hook',
    'v' => 'v_nt_hook',
);
?>