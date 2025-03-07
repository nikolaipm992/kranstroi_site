<?php

/**
 * SEO ������ � ����������
 */
function setPaginator_seourl_hook($obj, $nav, $rout) {
    static $count;

    if ($rout == 'START') {
        if (empty($obj->page))
            $obj->page = 1;
    }

    if ($rout == 'END' and $obj->PHPShopNav->getPath() == 'shop' and empty($count)) {

        // ���-�� ������� � ���������
        $num = ceil($obj->num_page / $obj->num_row);
        $replace_name = 'CID_' . $obj->category;
        $i = 1;

        if ($obj->seo_name == '' and @$obj->PHPShopCategory)
            $seo_name = str_replace("_", "-", PHPShopString::toLatin($obj->PHPShopCategory->getName()));
        else
            $seo_name = $obj->seo_name;

        $replace_old[] = $replace_name;
        $replace_new[] = $seo_name;
        $replace_old[] = $replace_name . '_ALL';
        $replace_new[] = $seo_name . '-ALL';


        while ($i <= intval($num)) {
            $replace_old[] = $replace_name . '_' . $i;
            $replace_new[] = $seo_name . '-' . $i;
            $i++;
        }


        $nav = strtr($nav, array_combine($replace_old, $replace_new));
        $obj->set('productPageNav', $nav);
        $obj->set('catalogFirstPage', strtr($obj->get('catalogFirstPage'), array_combine($replace_old, $replace_new)));

        $count++;
    }
}

/**
 * ���������� SEO ������ � �������
 */
function checkStore_seourlpro_hook($obj, $row) {

    if (!empty($row['prod_seo_name']))
        $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['prod_seo_name'], 2, false);
    else
        $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['name'], 2);

    return true;
}

$addHandler = array
    (
    'setPaginator' => 'setPaginator_seourl_hook',
    'checkStore' => 'checkStore_seourlpro_hook'
);
?>