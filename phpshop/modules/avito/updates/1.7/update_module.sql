ALTER TABLE `phpshop_categories` ADD `condition_cat_avito` varchar(64) DEFAULT 'Новое';
ALTER TABLE `phpshop_categories` ADD `export_cat_avito` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `building_avito` text;

UPDATE `phpshop_modules_avito_types` SET `name` = 'Аудио и видеотехника' WHERE `id` = 203;

INSERT INTO `phpshop_modules_avito_subtypes` (`id`, `name`, `type_id`) VALUES
(13, 'Аксессуары для автоакустики', 203),
(14, 'Магнитолы', 203),
(15, 'Автоакустика', 203),
(16, 'Видеорегистраторы', 203),
(17, 'Усилители', 203),
(18, 'Переходные рамки', 203),
(19, 'Короба и подиумы', 203),
(20, 'Другое', 203);

ALTER TABLE `phpshop_modules_avito_system` ADD `latitude` varchar(255) default '';
ALTER TABLE `phpshop_modules_avito_system` ADD `longitude` varchar(255) default '';

/* 2.3 */
CREATE TABLE IF NOT EXISTS `phpshop_modules_avito_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` int(11) NOT NULL,
    `message` text CHARACTER SET utf8 NOT NULL,
    `order_id` varchar(64) NOT NULL DEFAULT '',
    `status` enum('1','2') NOT NULL DEFAULT '1',
    `path` varchar(64) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `avito_order_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_modules_avito_system` ADD `create_products` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `link` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `log` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `delivery_id` varchar(64) DEFAULT NULL;
ALTER TABLE `phpshop_modules_avito_system` ADD `export` enum('0','1','2') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `type` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_avito_system` ADD `status` int(11) NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `status_import` varchar(64) default '';
ALTER TABLE `phpshop_modules_avito_system` ADD `fee` int(11) NOT NULL;
ALTER TABLE `phpshop_modules_avito_system` ADD `fee_type` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_avito_system` ADD `price` int(11) NOT NULL;
ALTER TABLE `phpshop_products` ADD `export_avito_id` varchar(64) NOT NULL default '';
ALTER TABLE `phpshop_products` ADD `price_avito` float DEFAULT '0';