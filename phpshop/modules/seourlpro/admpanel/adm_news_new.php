<?php

function addSeoUrlPro($data) {
    global $PHPShopGUI;

    $Tab3 = $PHPShopGUI->setField("SEO ссылка:", $PHPShopGUI->setInput("text", "news_seo_name_new", @$data['news_seo_name'], "left", false, false, false, false, '/',  '.html'), 1);

    $PHPShopGUI->addTab(array("SEO", $Tab3, 450));
}

function updateSeoUrlPro($data) {
    if (empty($data['news_seo_name_new']))
        $data['news_seo_name_new'] = PHPShopString::toLatin($data['zag']);
}

$addHandler = array(
    'actionStart' => 'addSeoUrlPro',
    'actionDelete' => false,
    'actionUpdate' => 'updateSeoUrlPro'
);