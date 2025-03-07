<?php

/**
 * Redirect �� seo �������� �������
 */
function index_selection_hook($obj) {

    // ��������� ������
    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();;

    if ($seourl_option["seo_brands_enabled"] == 2) {
        header('Location: ' . $obj->getValue('dir.dir') . "/brand/", true, 301);
        return true;
    }
}

/**
 * Redirect �� seo �������� �������
 */
function v_hook($obj, $data, $rout) {

    if ($rout == "START") {

        // ��������� ������
        $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();;

        if ($seourl_option["seo_brands_enabled"] == 2) {

            if (!empty($_REQUEST['v'])) {

                foreach ($_REQUEST['v'] as $key => $value) {

                    if (PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($value)) {

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                        $vendorCategory = $PHPShopOrm->select(array("*"), array("id=" => $key));

                        if ($vendorCategory["brand"] == 1) {

                            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                            $vendor = $PHPShopOrm->select(array("*"), array("id=" => $value));

                            if (!empty($vendor["sort_seo_name"])) {
                                header('Location: ' . $obj->getValue('dir.dir') . "/brand/" . $vendor["sort_seo_name"] . '.html', true, 301);
                            } else {
                                $seoUrl = strtolower($GLOBALS['PHPShopSeoPro']->setLatin($vendor['name']));
                                $PHPShopOrm->update(array("sort_seo_name_new" => "$seoUrl"), array('id' => '=' . $vendor['id']));
                                header('Location: ' . $obj->getValue('dir.dir') . "/brand/" . $seoUrl . '.html', true, 301);
                            }
                          return true;   
                            
                        }
                    }
                }
            }
        }
    }
}

$addHandler = array(
    'v' => 'v_hook',
    'index' => 'index_selection_hook'
);