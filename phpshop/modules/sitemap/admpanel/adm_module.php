<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sitemap.sitemap_system"));

function sitemaptime($nowtime) {
    return PHPShopDate::dataV($nowtime, false, true);
}

// �������� sitemap
function setGeneration($ssl=false) {
    global $PHPShopModules;

    $stat_products = null;
    $stat_pages = null;
    $stat_news = null;
    $stat_catalog = null;
    $brands = null;
    $seourl_enabled = false;
    $seourlpro_enabled = false;
    $seo_news_enabled = false;
    $seo_page_enabled = false;
    $seo_brands_enabled = false;
    
    if($ssl)
        $http = 'https';
    else $http = 'http';

    // ���� ������ SEOURL
    if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
        $seourl_enabled = true;
    }

    // ���� ������ SEOURLPRO
    if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
        $seourlpro_enabled = true;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']);
        $settings = $PHPShopOrm->select(array('seo_news_enabled, seo_page_enabled', 'seo_brands_enabled'), array('id' => "='1'"));
        if ($settings['seo_news_enabled'] == 2)
            $seo_news_enabled = true;
        if ($settings['seo_page_enabled'] == 2)
            $seo_page_enabled = true;
        if($settings['seo_brands_enabled'] == 2)
            $seo_brands_enabled = true;

        include_once dirname(dirname(__DIR__)) .'/seourlpro/inc/option.inc.php';
    }

    // ����������
    $title = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $title.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // ��������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data = $PHPShopOrm->select(array('id,datas,link'), array('enabled' => "!='0'", 'category' => '!=2000'), array('order' => 'datas DESC'), array('limit' => 10000));

    if (is_array($data))
        foreach ($data as $row) {
            $stat_pages.= '<url>' . "\n";
            $stat_pages.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . '/page/' . $row['link'] . '.html</loc>' . "\n";
            $stat_pages.= '<lastmod>' . sitemaptime($row['datas']) . '</lastmod>' . "\n";
            $stat_pages.= '<changefreq>weekly</changefreq>' . "\n";
            $stat_pages.= '<priority>1.0</priority>' . "\n";
            $stat_pages.= '</url>' . "\n";
        }

    // �������� ��������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 10000));

    if (is_array($data))
        foreach ($data as $row) {

            // ����������� url
            $url = '/page/CID_' . $row['id'];

            if ($seourl_enabled)
                $url = '/page/CID_' . $row['id'] . '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if (!empty($seourlpro_enabled) && !empty($seo_page_enabled)) {
                if (empty($row['page_cat_seo_name']))
                    $url = '/page/' . PHPShopString::toLatin($row['name']);
                else
                    $url = '/page/' . $row['page_cat_seo_name'];
            }

            $stat_pages.= '<url>' . "\n";
            $stat_pages.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . $url . '.html</loc>' . "\n";
            $stat_pages.= '<changefreq>weekly</changefreq>' . "\n";
            $stat_pages.= '<priority>0.5</priority>' . "\n";
            $stat_pages.= '</url>' . "\n";
        }

    // �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'datas DESC'), array('limit' => 10000));

    if (is_array($data))
        foreach ($data as $row) {

            // ����������� url
            $url = '/news/ID_' . $row['id'];

            if ($seourl_enabled)
                $url = '/news/ID_' . $row['id'] . '_' . PHPShopString::toLatin($row['zag']);

            //  SEOURLPRO
            if (!empty($seourlpro_enabled) && !empty($seo_news_enabled)) {
                if (empty($row['news_seo_name']))
                    $url = '/news/' . PHPShopString::toLatin($row['zag']);
                else
                    $url = '/news/' . $row['news_seo_name'];
            }

            $stat_news.= '<url>' . "\n";
            $stat_news.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . $url . '.html</loc>' . "\n";
            $stat_news.= '<lastmod>' . sitemaptime(PHPShopDate::GetUnixTime($row['datas'])) . '</lastmod>' . "\n";
            $stat_news.= '<changefreq>daily</changefreq>' . "\n";
            $stat_news.= '<priority>0.5</priority>' . "\n";
            $stat_news.= '</url>' . "\n";
        }

    // ������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'parent_enabled' => "='0'"), array('order' => 'datas DESC'), array('limit' => 100000));


    if (is_array($data))
        foreach ($data as $row) {
            $stat_products.= '<url>' . "\n";


            // ����������� ���
            $url = '/shop/UID_' . $row['id'];

            // SEOURL
            if (!empty($seourl_enabled))
                $url.= '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if (!empty($seourlpro_enabled)) {
                if (empty($row['prod_seo_name']))
                    $url = '/id/' . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . '-' . $row['id'];
                else
                    $url = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];
            }


            $stat_products.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . $url . '.html</loc>' . "\n";
            $stat_products.= '<lastmod>' . sitemaptime($row['datas']) . '</lastmod>' . "\n";
            $stat_products.= '<changefreq>daily</changefreq>' . "\n";
            $stat_products.= '<priority>1.0</priority>' . "\n";
            $stat_products.= '</url>' . "\n";
        }

    // ��������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $data = $PHPShopOrm->select(array('*'), array('skin_enabled' => "='0'"), false, array('limit' => 10000));

    $seourl = null;
    if (is_array($data))
        foreach ($data as $row) {

            // ����������� ���
            $url = '/shop/CID_' . $row['id'];

            // SEOURL
            if ($seourl_enabled)
                $url.= '_' . PHPShopString::toLatin($row['name']);

            //  SEOURLPRO
            if (!empty($seourlpro_enabled)) {
                if (empty($row['cat_seo_name']))
                    $url = '/' . str_replace("_", "-", PHPShopString::toLatin($row['name']));
                else
                    $url = '/' . $row['cat_seo_name'];
            }

            $stat_products.= '<url>' . "\n";
            $stat_products.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . $url . '.html</loc>' . "\n";
            $stat_products.= '<changefreq>weekly</changefreq>' . "\n";
            $stat_products.= '<priority>0.5</priority>' . "\n";
            $stat_products.= '</url>' . "\n";
        }

    if($seourlpro_enabled && $seo_brands_enabled) {
        $brandsOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $brandsIds = array();
        $result = $brandsOrm->getList(array('id'), array('brand' => '="1"'));
        foreach ($result as $value) {
            $brandsIds[] = $value['id'];
        }
        if(count($brandsIds) > 0) {
            $brandValuesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

            $brandValues = $brandValuesOrm->getList(array('sort_seo_name'), array(
                'category' => sprintf(' IN(%s)', implode(',', $brandsIds)),
                'sort_seo_name' => '<> ""'
            ));

            foreach ($brandValues as $brandValue) {
                $brands.= '<url>' . "\n";
                $brands.= '<loc>'.$http.'://' . $_SERVER['SERVER_NAME'] . '/brand/' . $brandValue['sort_seo_name'] . '.html</loc>' . "\n";
                $brands.= '<changefreq>weekly</changefreq>' . "\n";
                $brands.= '<priority>0.5</priority>' . "\n";
                $brands.= '</url>' . "\n";
            }
        }
    }

    $sitemap = $title . $stat_pages . $stat_news . $stat_products . $brands  . '</urlset>';

    // ������ � ����
    if (fwrite(@fopen('../../UserFiles/Files/sitemap.xml', "w+"), $sitemap))
        echo '<div class="alert alert-success" id="rules-message"  role="alert">'.__('����').' <strong>sitemap.xml</strong> '.__('������� ������').'</div>';
    else
        echo '<div class="alert alert-danger" id="rules-message"  role="alert">'.__('������ ���������� ����� � �����').' UserFiles/File/ !</div>';
}

// ������� ����������
function actionUpdate() {
    setGeneration();
}

// ������� ����������
function actionUpdateSSl() {

    setGeneration($ssl=true);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->action_button['�������'] = array(
        'name' => __('�������').' '.'Sitemap',
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->action_button['������� SSL'] = array(
        'name' => __('�������').' '.'Sitemap SSL',
        'action' => 'saveIDssl',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->action_button['�������'] = array(
        'name' => __('�������').' '.'Sitemap',
        'action' => '../../UserFiles/Files/sitemap.xml',
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    );

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('�������','������� SSL', '�������', '�������'));

// �������
    $data = $PHPShopOrm->select();

    $Info = '
        <ol>
        <li>��� ��������������� �������� sitemap.xml ���������� ������ <kbd>Cron</kbd> � �������� � ���� ����� ������ � �������
        ������������ �����:<br>  <code>phpshop/modules/sitemap/cron/sitemap_generator.php</code> ��� <code>phpshop/modules/sitemap/cron/sitemap_generator.php?ssl</code> ��� ��������� HTTPS.
        <li>� ����������� (������.��������� � �.�.) ������� ����� <code>http://' . $_SERVER['SERVER_NAME'] . '/UserFiles/Files/sitemap.xml</code> ��� �������������� ��������� ���������� ������.         <li>��� ��������� ����� ����� � �������������� ������ ������� �������� ��������� ������ ����� ������ <kbd>Cron</kbd> � � ���������� ������ ������ ������� ��������� �������. ����� ����� ����� ������� ������ ��� <code>http://�����_�������/UserFiles/Files/sitemap_��.xml</code>, ��� �� - ID �������. ID ������� ����� ������� � ���������� ��������� ������� (1 - 10).
        <li>���������� ����� CHMOD 775 �� ����� /UserFiles/Files/ ��� ������ � ��� ����� sitemap.xml
        </ol>';
    $Tab1 = $PHPShopGUI->setInfo($Info);

    $Tab2 = $PHPShopGUI->setPay(false,true);

// ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1), array("� ������", $Tab2));

// ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit").
            $PHPShopGUI->setInput("submit", "saveIDssl", "���������", "right", 80, "", "but", "actionUpdateSSL.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>