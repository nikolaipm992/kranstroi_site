<?php

include_once dirname(__FILE__) . '/../class/Avito.php';

function addAvitoTab($data) {
    global $PHPShopGUI;

    // Проверяем на наличие любого поля, которое есть только у каталогов товаров. Иначе вкладка появляется и в каталогах страниц
    if(isset($data['skin_enabled'])) {

        $PHPShopGUI->addJSFiles('../modules/avito/admpanel/gui/script.js?v=1.0');
        $PHPShopGUI->field_col = 5;

        $tab = $PHPShopGUI->setField('Экспорт в Авито', $PHPShopGUI->setCheckbox('export_cat_avito_new', 1, '', $data['export_cat_avito']));
        $tab .= $PHPShopGUI->setField('Прайс-лист', $PHPShopGUI->setSelect('xml_price_avito', Avito::getAvitoCategoryTypes($data['category_avito'])));
        $tab .= $PHPShopGUI->setField('Категория товара', $PHPShopGUI->setSelect('category_avito_new', Avito::getAvitoCategories(null, $data['category_avito'])));
        $tab .= $PHPShopGUI->setField('Вид товара', $PHPShopGUI->setSelect('type_avito_new', Avito::getCategoryTypes($data['category_avito'], $data['type_avito']),false,false, false, true));
        $tab .= $PHPShopGUI->setField('Состояние товара', $PHPShopGUI->setSelect('condition_cat_avito_new', Avito::getConditions($data['condition_cat_avito'])));
        
        $tab .= $PHPShopGUI->setField('Тип товара', $PHPShopGUI->setSelect('subtype_avito_new', Avito::getCategorySubTypes($data['subtype_avito'],$data['type_avito'])));
        
        $PHPShopGUI->addTab(array("Авито", $tab, true));
    }
}

function avitoUpdate() {

    if (empty($_POST['export_cat_avito_new'])) {
        $_POST['export_cat_avito_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addAvitoTab',
    'actionDelete' => false,
    'actionUpdate' => 'avitoUpdate'
);
?>