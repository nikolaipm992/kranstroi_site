<?php

function addSeoUrlPro($data) {
    global $PHPShopGUI;

    if (isset($data['news_seo_name'])) {

        if (empty($data['news_seo_name']))
            $data['news_seo_name'] = PHPShopString::toLatin($data['zag']);

        $Tab3 = $PHPShopGUI->setField("SEO ссылка:", $PHPShopGUI->setInput("text", "news_seo_name_new", $data['news_seo_name'], "left", false, false, false, false, '/',  '.html'), 1);

        $PHPShopGUI->addTab(array("SEO", $Tab3, 450));
    }
}

$addHandler = array(
    'actionStart' => 'addSeoUrlPro',
    'actionDelete' => false,
    'actionUpdate' => false
);