<?php

/**
 * ������������ ���������
 * @package PHPShopInc
 */
// �������� �� ������ /index.php/index.php
if (strstr($_SERVER['REQUEST_URI'], 'index.php')) {
    header('Location: /error/');
    exit();
}

// ������ ������� �� ���������
$PHPShopCoreElement = new PHPShopCoreElement();
$PHPShopCoreElement->init('service');
$PHPShopCoreElement->init('skin', false);
$PHPShopCoreElement->init('checkskin');
$PHPShopCoreElement->init('setdefault');

// ����������
$PHPShopPromotions = new PHPShopPromotions();

// ����� �������
$PHPShopSkinElement = new PHPShopSkinElement();
$PHPShopSkinElement->init('skinSelect');

// ����� ������� �������
$PHPShopCoreElement->init('pageCss', false);

// �������� �������
PHPShopObj::loadClass('modules');
$PHPShopModules = new PHPShopModules();
$PHPShopModules->doLoad();

// ���������� ����� autoload
foreach ($GLOBALS['SysValue']['autoload'] as $val)
    if (is_file($val))
        include_once($val);

// ��������� ����������
$mobil = new Mobile_Detect();
if ($mobil->isMobile() or $mobil->isTablet()) {
    define("isMobil", true);
    
    $isIOS = intval($mobil->version('iPad', $mobil::VERSION_TYPE_FLOAT) . $mobil->version('iPhone', $mobil::VERSION_TYPE_FLOAT));
    if (!empty($isIOS) and $isIOS < 14)
        define("isIOS", $isIOS);
}

// JS ���������
$PHPShopCoreElement->init('setjs');

// ����� ������
$PHPShopCurrencyElement = new PHPShopCurrencyElement();
$PHPShopCurrencyElement->init('valutaDisp');

// �������
$PHPShopCartElement = new PHPShopCartElement();
$PHPShopCartElement->init('miniCart');

// ������� � �����
$PHPShopProductIndexElements = new PHPShopProductIndexElements();
$PHPShopProductIndexElements->init('specMain');

// ��������� �������
$PHPShopProductIndexElements->init('nowBuy');

// ���� ���������
$PHPShopShopCatalogElement = new PHPShopShopCatalogElement();
$PHPShopShopCatalogElement->init('leftCatal');
$PHPShopShopCatalogElement->init('leftCatalTable');

// ������� � �������
$PHPShopProductIconElements = new PHPShopProductIconElements();
$PHPShopProductIconElements->init('specMainIcon');

// ���� ��������� �������
$PHPShopPageCatalogElement = new PHPShopPageCatalogElement();
if ($PHPShopNav->notPath(array('order', 'done')))
    $PHPShopPageCatalogElement->init('pageCatal');
$PHPShopPageCatalogElement->init('getLastPages');

// ����-�������
$PHPShopNewsElement = new PHPShopNewsElement();
$PHPShopNewsElement->init('miniNews');

// ����-������
$PHPShopGbookElement = new PHPShopGbookElement();
$PHPShopGbookElement->init('miniGbook');

// �������
$PHPShopSliderElement = new PHPShopSliderElement();
$PHPShopSliderElement->init('imageSlider');

// �������
$PHPShopBannerElement = new PHPShopBannerElement();
$PHPShopBannerElement->init('banersDisp');
$PHPShopBannerElement->init('banersDispHorizontal');
$PHPShopBannerElement->init('banersDispMenu');

// ���������
$PHPShopAnalitica = new PHPShopAnalitica();

// ������ �����
$PHPShopCloudElement = new PHPShopCloudElement();
$PHPShopCloudElement->init('cloud');

// ��������� ����
$PHPShopTextElement = new PHPShopTextElement();
$PHPShopTextElement->init('leftMenu', true);
$PHPShopTextElement->init('rightMenu', true);
$PHPShopTextElement->init('topMenu', true);
$PHPShopTextElement->init('bottomMenu', true);
$PHPShopShopCatalogElement->init('topcatMenu', true);
$PHPShopPageCatalogElement->init('topMenu', true);

// �����������
$PHPShopPhotoElement = new PHPShopPhotoElement();
$PHPShopPhotoElement->init('getPhotos');

// Captcha
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopRecaptchaElement->init('captcha');
PHPShopParser::set('pricemail_captcha', $PHPShopRecaptchaElement->captcha('pricemail'));
PHPShopParser::set('notice_captcha', $PHPShopRecaptchaElement->captcha('notice'));
PHPShopParser::set('review_captcha', $PHPShopRecaptchaElement->captcha('review'));
PHPShopParser::set('forma_captcha', $PHPShopRecaptchaElement->captcha('forma'));

// �������
$PHPShopDialogElement = new PHPShopDialogElement();
$PHPShopDialogElement->dialog();

// ����������� �������������
$PHPShopUserElement = new PHPShopUserElement();
$PHPShopUserElement->init('wishlist');
$PHPShopUserElement->init('usersDisp');

// RSS ������ ��������
new PHPShopRssParser();
?>