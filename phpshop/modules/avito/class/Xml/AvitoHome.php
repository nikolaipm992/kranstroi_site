<?php

include_once dirname(__DIR__) . '/Xml/BaseAvitoXml.php';
include_once dirname(__DIR__) . '/Xml/AvitoPriceInterface.php';

/**
 * XML прайс Авито "Для дома и дачи"
 * @author PHPShop Software
 * @version 1.1
 */
class AvitoHome extends BaseAvitoXml implements AvitoPriceInterface {

    public static function getXml($product) {

        $xml = '<Ad>';

        // Ключ обновления
        if (Avito::getOption('type') == 1)
            $xml .= sprintf('<Id>%s</Id>', $product['id']);
        else
            $xml .= sprintf('<Id>%s</Id>', $product['uid']);

        $xml .= sprintf('<Category>%s</Category>', $product['category']);
        $xml .= sprintf('<AdType>%s</AdType>', $product['ad_type']);
        $xml .= sprintf('<GoodsType>%s</GoodsType>', $product['type']);
        if (!empty($product['subtype'])) {
            $xml .= sprintf('<GoodsSubType>%s</GoodsSubType>', $product['subtype']);

            $building = unserialize($product['building_avito']);
            $subtype_avito = $product['subtype_id'];

            // Стройматериалы - Листовые материалы
            if ($subtype_avito == 4) {
                $xml .= sprintf('<SheetMaterialsSubType>%s</SheetMaterialsSubType>', PHPShopString::win_utf8($building['SheetMaterialsSubType']));
                $xml .= sprintf('<SheetMaterialsType>%s</SheetMaterialsType>', PHPShopString::win_utf8($building['SheetMaterialsType']));
            }
            // Стройматериалы - Строительство стен
            elseif ($subtype_avito == 10) {
                $xml .= sprintf('<Walltype>%s</Walltype>', PHPShopString::win_utf8($building['Walltype']));

                // Блоки для строительств
                if ($building['Walltype'] == 'Блоки для строительства') {
                    $xml .= sprintf('<ConstructionBlocksType>%s</ConstructionBlocksType>', PHPShopString::win_utf8($building['ConstructionBlocksType']));
                    $xml .= sprintf('<Size>%s</Size>', PHPShopString::win_utf8($building['Size']));
                    $xml .= sprintf('<Brand>%s</Brand>', PHPShopString::win_utf8($building['Brand']));
                }
                // Кирпич
                elseif ($building['Walltype'] == 'Кирпич') {
                    $xml .= sprintf('<TypeBrick>%s</TypeBrick>', PHPShopString::win_utf8($building['TypeBrick']));
                    $xml .= sprintf('<PurposeBrick>%s</PurposeBrick>', PHPShopString::win_utf8($building['PurposeBrick']));
                    $xml .= sprintf('<Color>%s</Color>', PHPShopString::win_utf8($building['Color']));
                    $xml .= sprintf('<Size>%s</Size>', PHPShopString::win_utf8($building['Size']));
                    $xml .= sprintf('<HollownessBrick>%s</HollownessBrick>', PHPShopString::win_utf8($building['HollownessBrick']));
                }
            }
            // Стройматериалы - Строительные смеси
            elseif ($subtype_avito == 9) {
                $xml .= sprintf('<MixesType>%s</MixesType>', PHPShopString::win_utf8($building['MixesType']));
                $xml .= sprintf('<ConcreteGrade>%s</ConcreteGrade>', PHPShopString::win_utf8($building['ConcreteGrade']));
                $xml .= sprintf('<ProductKind>%s</ProductKind>', PHPShopString::win_utf8($building['ProductKind']));
            }
        }
        $xml .= sprintf('<Title>%s</Title>', $product['name']);
        $xml .= sprintf('<Description>%s</Description>', $product['description']);
        $xml .= sprintf('<Price>%s</Price>', $product['price']);
        $xml .= sprintf('<AdStatus>%s</AdStatus>', $product['status']);
        $xml .= sprintf('<ListingFee>%s</ListingFee>', $product['listing_fee']);
        $xml .= sprintf('<Condition>%s</Condition>', $product['condition']);
        $xml .= sprintf('<ManagerName>%s</ManagerName>', PHPShopString::win_utf8(Avito::getOption('manager')));
        $xml .= sprintf('<ContactPhone>%s</ContactPhone>', PHPShopString::win_utf8(Avito::getOption('phone')));

        if (!empty(Avito::getOption('latitude')) and ! empty(Avito::getOption('longitude'))) {
            $xml .= sprintf('<Latitude>%s</Latitude>', PHPShopString::win_utf8(Avito::getOption('latitude')));
            $xml .= sprintf('<Longitude>%s</Longitude>', PHPShopString::win_utf8(Avito::getOption('longitude')));
        } else
            $xml .= sprintf('<Address>%s</Address>', PHPShopString::win_utf8(static::getAddress()));

        if (count($product['images']) > 0) {
            $xml .= '<Images>';
            foreach ($product['images'] as $image) {
                $xml .= sprintf('<Image url="%s"/>', $image['name']);
            }
            $xml .= '</Images>';
        }

        $xml .= '</Ad>';

        return $xml;
    }

}
