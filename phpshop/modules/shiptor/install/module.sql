DROP TABLE IF EXISTS `phpshop_modules_shiptor_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_shiptor_system` (
    `id` int(11) NOT NULL auto_increment,
    `delivery_id` varchar(64) default '',
    `status` int(11),
    `api_key` varchar(255) default '',
    `private_api_key` varchar(255) default '',
    `add_days` int(11) default 0,
    `companies` text,
    `round` varchar(64) default 'math',
    `fee` int(11) default 0,
    `declared_percent` float NOT NULL,
    `cod` enum('0','1') DEFAULT '1',
    `length` varchar(64) default '',
    `weight` varchar(64) default '',
    `width` varchar(64) default '',
    `height` varchar(64) default '',
    `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_shiptor_system` VALUES (1, '', 0, '', '', 0, '', 'math', 0, '30.0', '1', 10, 1000, 10, 10, '1.0');

CREATE TABLE IF NOT EXISTS `phpshop_modules_shiptor_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` int(11) NOT NULL,
    `message` text NOT NULL,
    `order_id` int(11) NOT NULL,
    `status` varchar(255) NOT NULL,
    `status_code` varchar(64) default 'success',
    `type` varchar(64) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `shiptor_order_data` text default '';
ALTER TABLE `phpshop_delivery` ADD `is_mod` enum('1','2') DEFAULT '1';