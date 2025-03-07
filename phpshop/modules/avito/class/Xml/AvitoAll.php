<?php

include_once dirname(__DIR__) . '/Xml/AvitoAppliance.php';
include_once dirname(__DIR__) . '/Xml/AvitoHome.php';
include_once dirname(__DIR__) . '/Xml/AvitoSpare.php';
include_once dirname(__DIR__) . '/Xml/BaseAvitoXml.php';
include_once dirname(__DIR__) . '/Xml/AvitoPriceInterface.php';

/**
 * XML прайс Авито для всех категорий
 * @author PHPShop Software
 * @version 1.1
 */
class AvitoAll extends BaseAvitoXml implements AvitoPriceInterface
{
    static $categories;

    public static function getXml($product)
    {
        switch (static::getAvitoPriceIdByCategoryId((int) $product['category_avito'])) {
            case 1:
                $xml = AvitoAppliance::getXml($product);
                break;
            case 2:
                $xml = AvitoHome::getXml($product);
                break;
            case 3:
                $xml = AvitoSpare::getXml($product);
                break;
        }

        return $xml;
    }

    public static function getAvitoPriceIdByCategoryId($categoryId)
    {
        if(!is_array(static::$categories)) {
            $orm = new PHPShopOrm('phpshop_modules_avito_categories');
            $where = ['xml_price_id' => sprintf(' IN (%s)', implode(',', [1, 2, 3]))];
            $categories = array_column($orm->getList(['id', 'xml_price_id'], $where), 'xml_price_id', 'id');
        }

        return (int) $categories[$categoryId];
    }
}