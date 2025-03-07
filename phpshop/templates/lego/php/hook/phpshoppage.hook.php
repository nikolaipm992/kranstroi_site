<?php

function template_ListPage_hook($obj, $data, $rout) {
    if ($rout == 'END') {
        $dis = null;
        foreach ($data as $row) {


            // Определяем переменные
            $obj->set('pageLink', $row['link']);
            $obj->set('pageName', $row['name']);
            $obj->set('pageIcon', $row['icon']);
            $obj->set('pageData', PHPShopDate::get($row['datas']));
            $obj->set('pagePreview', Parser(stripslashes($row['preview'])));

            // Подключаем шаблон
            $dis .= parseTemplateReturn($obj->getValue('templates.page_forma'));
        }
     $obj->set('pageContent', $dis);   
    }
}

$addHandler = array
    (
    'ListPage' => 'template_ListPage_hook',
);
?>