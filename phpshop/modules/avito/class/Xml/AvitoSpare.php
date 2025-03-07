<?php

include_once dirname(__DIR__) . '/Xml/BaseAvitoXml.php';
include_once dirname(__DIR__) . '/Xml/AvitoPriceInterface.php';

/**
 * XML прайс Авито "Запчасти и аксессуары"
 * @author PHPShop Software
 * @version 1.2
 */
class AvitoSpare extends BaseAvitoXml implements AvitoPriceInterface {

    public static function getXml($product) {

        $tier = unserialize($product['tiers']);

        $xml = '<Ad>';
        
         // Ключ обновления
        if (Avito::getOption('type') == 1) 
           $xml .= sprintf('<Id>%s</Id>', $product['id']);
        else $xml .= sprintf('<Id>%s</Id>', $product['uid']);
        
        $xml .= sprintf('<ListingFee>%s</ListingFee>', $product['listing_fee']);

        if (strstr($product['type'], " / "))
            $xml .= sprintf('<GoodsType>%s</GoodsType>', explode(" / ", $product['type'])[0]);
        else
            $xml .= sprintf('<GoodsType>%s</GoodsType>', $product['type']);

        $xml .= sprintf('<EquipmentType>%s</EquipmentType>', $product['subtype']);
        $xml .= sprintf('<AdStatus>%s</AdStatus>', $product['status']);
        $xml .= sprintf('<ManagerName>%s</ManagerName>', PHPShopString::win_utf8(Avito::getOption('manager')));
        $xml .= sprintf('<ContactPhone>%s</ContactPhone>', PHPShopString::win_utf8(Avito::getOption('phone')));

        if (!empty(Avito::getOption('latitude')) and ! empty(Avito::getOption('longitude'))) {
            $xml .= sprintf('<Latitude>%s</Latitude>', PHPShopString::win_utf8(Avito::getOption('latitude')));
            $xml .= sprintf('<Longitude>%s</Longitude>', PHPShopString::win_utf8(Avito::getOption('longitude')));
        } else
            $xml .= sprintf('<Address>%s</Address>', PHPShopString::win_utf8(static::getAddress()));

        $xml .= sprintf('<Category>%s</Category>', $product['category']);
        //$xml .= sprintf('<TypeId>%s</TypeId>', str_replace('[', '', explode(']', $product['type']))[0]);
        $xml .= sprintf('<AdType>%s</AdType>', $product['ad_type']);
        $xml .= sprintf('<Title>%s</Title>', $product['name']);
        $xml .= sprintf('<Description>%s</Description>', $product['description']);
        $xml .= sprintf('<Price>%s</Price>', $product['price']);
        $xml .= sprintf('<Condition>%s</Condition>', $product['condition']);
        $xml .= sprintf('<OEM>%s</OEM>', $product['oem']);
        if (isset($tier['brand']) && !empty($tier['brand'])) {
            $xml .= sprintf('<Brand>%s</Brand>', PHPShopString::win_utf8($tier['brand']));
        }
        if (isset($tier['diameter']) && !empty($tier['diameter'])) {
            $xml .= sprintf('<RimDiameter>%s</RimDiameter>', $tier['diameter']);
        }
        if (isset($tier['tier-type']) && !empty($tier['tier-type'])) {
            $xml .= sprintf('<TireType>%s</TireType>', PHPShopString::win_utf8($tier['tier-type']));
        }
        if (isset($tier['wheel-axle']) && !empty($tier['wheel-axle'])) {
            $xml .= sprintf('<WheelAxle>%s</WheelAxle>', PHPShopString::win_utf8($tier['wheel-axle']));
        }
        if (isset($tier['rim-type']) && !empty($tier['rim-type'])) {
            $xml .= sprintf('<RimType>%s</RimType>', PHPShopString::win_utf8($tier['rim-type']));
        }
        if (isset($tier['tire-section-width']) && !empty($tier['tire-section-width'])) {
            $xml .= sprintf('<TireSectionWidth>%s</TireSectionWidth>', $tier['tire-section-width']);
        }
        if (isset($tier['tire-aspect-ratio']) && !empty($tier['tire-aspect-ratio'])) {
            $xml .= sprintf('<TireAspectRatio>%s</TireAspectRatio>', PHPShopString::win_utf8($tier['tire-aspect-ratio']));
        }
        if (isset($tier['rim-width']) && !empty($tier['rim-width'])) {
            $xml .= sprintf('<RimWidth>%s</RimWidth>', $tier['rim-width']);
        }
        if (isset($tier['rim-bolts']) && !empty($tier['rim-bolts'])) {
            $xml .= sprintf('<RimBolts>%s</RimBolts>', $tier['rim-bolts']);
        }
        if (isset($tier['rim-bolts-diameter']) && !empty($tier['rim-bolts-diameter'])) {
            $xml .= sprintf('<RimBoltsDiameter>%s</RimBoltsDiameter>', $tier['rim-bolts-diameter']);
        }
        if (isset($tier['rim-offset']) && !empty($tier['rim-offset'])) {
            $xml .= sprintf('<RimOffset>%s</RimOffset>', $tier['rim-offset']);
        }

        // Магнитолы
        if ($product['subtype_id'] == 14) {

            if (isset($tier['Size']) && !empty($tier['Size'])) {
                $xml .= sprintf('<Size>%s</Size>', PHPShopString::win_utf8($tier['Size']));
            }
            if (isset($tier['AndroidOS']) && !empty($tier['AndroidOS'])) {
                $xml .= sprintf('<AndroidOS>%s</AndroidOS>', PHPShopString::win_utf8($tier['AndroidOS']));
            }
            if (isset($tier['RAM']) && !empty($tier['RAM'])) {
                $xml .= sprintf('<RAM>%s</RAM>', $tier['RAM']);
            }
            if (isset($tier['ROM']) && !empty($tier['ROM'])) {
                $xml .= sprintf('<ROM>%s</ROM>', $tier['ROM']);
            }
            if (isset($tier['CPU']) && !empty($tier['CPU'])) {
                $xml .= sprintf('<CPU>%s</CPU>', $tier['CPU']);
            }
        }

        // Автоакустика
        if ($product['subtype_id'] == 15) {

            if (isset($tier['Size']) && !empty($tier['Size'])) {
                $xml .= sprintf('<Size>%s</Size>', PHPShopString::win_utf8($tier['Size']));
            }
            if (isset($tier['AudioType']) && !empty($tier['AudioType'])) {
                $xml .= sprintf('<AudioType>%s</AudioType>', PHPShopString::win_utf8($tier['AudioType']));
            }
            if (isset($tier['VoiceCoil']) && !empty($tier['VoiceCoil'])) {
                $xml .= sprintf('<VoiceCoil>%s</VoiceCoil>', $tier['VoiceCoil']);
            }
            if (isset($tier['RMS']) && !empty($tier['RMS'])) {
                $xml .= sprintf('<RMS>%s</RMS>', $tier['RMS']);
            }
            if (isset($tier['Impedance']) && !empty($tier['Impedance'])) {
                $xml .= sprintf('<Impedance>%s</Impedance>', $tier['Impedance']);
            }
        }

        // Видеорегистраторы
        if ($product['subtype_id'] == 16) {

            if (isset($tier['Size']) && !empty($tier['Size'])) {
                $xml .= sprintf('<Size>%s</Size>', PHPShopString::win_utf8($tier['Size']));
            }
            if (isset($tier['Design']) && !empty($tier['Design'])) {
                $xml .= sprintf('<Design>%s</Design>', PHPShopString::win_utf8($tier['Design']));
            }
            if (isset($tier['CamsNumber']) && !empty($tier['CamsNumber'])) {
                $xml .= sprintf('<CamsNumber>%s</CamsNumber>', $tier['CamsNumber']);
            }
            if (isset($tier['Resolution']) && !empty($tier['Resolution'])) {
                $xml .= sprintf('<Resolution>%s</Resolution>', $tier['Resolution']);
            }
        }

        // Усилители
        if ($product['subtype_id'] == 17) {

            if (isset($tier['AmplifierType']) && !empty($tier['AmplifierType'])) {
                $xml .= sprintf('<AmplifierType>%s</AmplifierType>', PHPShopString::win_utf8($tier['AmplifierType']));
            }
            if (isset($tier['ChannelsNumber']) && !empty($tier['ChannelsNumber'])) {
                $xml .= sprintf('<ChannelsNumber>%s</ChannelsNumber>', PHPShopString::win_utf8($tier['ChannelsNumber']));
            }
            if (isset($tier['RMSfour']) && !empty($tier['RMSfour'])) {
                $xml .= sprintf('<RMSfour>%s</RMSfour>', $tier['RMSfour']);
            }
            if (isset($tier['RMStwo']) && !empty($tier['RMStwo'])) {
                $xml .= sprintf('<RMStwo>%s</RMStwo>', $tier['RMStwo']);
            }
        }

        // Противоугонные устройств
        if (in_array($product['type_avito'], [207, 208, 209, 210]))
            $xml .= sprintf('<DeviceType>%s</DeviceType>', explode(" / ", $product['type'])[1]);

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

?>