<?php

/**
 * ���������� SEO ������ � �������� � ������, ����� ������� �� ���
 */
function index_news_seourl_hook($obj, $row, $rout) {

    // ��������� ������ �� ����
    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
    if ($seourl_option['seo_news_enabled'] != 2)
        return false;

    if ($rout == "START") {

        // �������� ��� ������ � ����� ������ ID
        $seo_name = explode(".", str_replace("/news/", "", $obj->PHPShopNav->objNav['truepath']));

        // ���������� ������ .html.html
        if (count($seo_name) > 2) {
            $obj->setError404();
            return true;
        }

        if ($seo_name[0] != "") {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
            $PHPShopOrm->mysql_error = false;

            $news = $PHPShopOrm->select(array("*"), array('news_seo_name' => "='" . PHPShopSecurity::TotalClean($seo_name[0]) . "'"));

            if (is_array($news)) {
                $obj->PHPShopNav->objNav['id'] = $news["id"];
                $obj->ID();
                return true;
            } elseif (!is_array($news) && ($obj->PHPShopNav->objNav['truepath'] != "/news/" && substr($obj->PHPShopNav->objNav['truepath'], 0, 10) != "/news/news")) {
                // 404 ���� ��� ������ ���
                $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
                $obj->set('newsZag', __('������ 404'));
                $obj->set('newsZag', '');
                $obj->setError404();
                return true;
            }
        }
    }

    if ($rout == 'END') {

        // ����� ������ ��������, ������ ������
        foreach ($row as $item) {
            if (!empty($item["news_seo_name"]))
                $obj->ListInfoItems = str_replace("news/ID_" . $item['id'] . ".html", "news/" . $item['news_seo_name'] . ".html", $obj->ListInfoItems);
            else {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
                $seoURL = $GLOBALS['PHPShopSeoPro']->setLatin($item['zag']);
                $PHPShopOrm->update(array("news_seo_name_new" => "$seoURL"), array("id=" => $item["id"]));

                $obj->ListInfoItems = str_replace("news/ID_" . $item['id'] . ".html", "news/" . $GLOBALS['PHPShopSeoPro']->setLatin($item['zag']) . ".html", $obj->ListInfoItems);
            }
        }

        // ���� ������ Mobile
        if (!empty($_GET['mobile']) and $_GET['mobile'] == 'true' and !empty($GLOBALS['SysValue']['base']['mobile']['mobile_system'])) {
            header('Location: ' . $obj->getValue('dir.dir') . '/news/ID_' . $obj->PHPShopNav->getId() . '.html', true, 302);
            return true;
        }
    }
}

/**
 * �������� ������������ SEO �������
 */
function ID_seourl_hook($obj, $row, $rout) {

    if ($rout == 'END') {

        // ��������� ������ �� ����
        $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
        if ($seourl_option['seo_news_enabled'] != 2)
            return false;

        $url = $obj->PHPShopNav->getName(true);
        $url_pack = '/news/ID_' . $obj->PHPShopNav->getId();

        if (!empty($row['news_seo_name']))
            $url_true = '/news/' . $row['news_seo_name'];
        else
            $url_true = '/news/' . $GLOBALS['PHPShopSeoPro']->setLatin($row['zag']);

        // ���� ������ �� ��������
        if ($url != $url_true and $url != $url_pack) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->set('newsZag', __('������ 404'));
            $obj->setError404();
        } elseif ($url == $url_pack) {
            header('Location: ' . $obj->getValue('dir.dir') . $url_true . '.html', true, 301);
            return true;
        }
    }
}

$addHandler = array(
    'ID' => 'ID_seourl_hook',
    'index' => 'index_news_seourl_hook'
);
?>