CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_type` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_modules_ozonseller_system` ADD `log` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_products` ADD `sku_ozon` int(11) DEFAULT 0;

ALTER TABLE `phpshop_modules_ozonseller_system` ADD `export` enum('0','1','2') NOT NULL default '0';