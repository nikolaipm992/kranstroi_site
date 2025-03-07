<?php

function addYandexcartCPA($data) {
    global $PHPShopGUI;

    // Только для конечных каталогов
    if (!is_array($GLOBALS['tree_array'][$data['id']]) and isset($data['icon'])) {

        // Добавляем значения в функцию actionStart
        if (empty($data['prod_seo_name'])) {
            PHPShopObj::loadClass("string");
            $data['prod_seo_name'] = PHPShopString::toLatin($data['name']);
            $data['prod_seo_name'] = str_replace("_", "-", $data['prod_seo_name']);
        }
        $Tab3 = $PHPShopGUI->setField("Ставка (Fee)", $PHPShopGUI->setInputText(null, 'fee_new', null, 100), 1, '1% комиссии соответствует значению 100');


        $Tab3.=$PHPShopGUI->setField(__('CPA модель'), 
                $PHPShopGUI->setRadio('cpa_new', 1, __('Включить'), 0) . 
                $PHPShopGUI->setRadio('cpa_new', 2, __('Выключить'), 0, false, 'text-warning').
                $PHPShopGUI->setRadio('cpa_new', 0, __('Не изменять'), 0, false, 'text-muted') );

        $PHPShopGUI->addTab(array("Яндекс", $Tab3, true));
    }
}

function updateYandexcartCPA() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    if(!empty($_POST['cpa_new']))
    $action = $PHPShopOrm->update(array('cpa_new' => $_POST['cpa_new'], 'fee_new' => $_POST['fee_new']), array('category' => '=' . intval($_POST['rowID'])));
}

$addHandler = array(
    'actionStart' => 'addYandexcartCPA',
    'actionDelete' => false,
    'actionUpdate' => 'updateYandexcartCPA'
);
?>