DROP TABLE IF EXISTS `phpshop_modules_grastinwidget_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_grastinwidget_system` (
  `id` int(11) NOT NULL auto_increment,
  `api` varchar(255) default '',
  `dev_mode` enum('0','1') NOT NULL default '0',
  `from_city` varchar(255) default '',
  `to_city` varchar(255) default '',
  `no_partners` varchar(255) default '',
  `city_from_hide` enum('0','1') NOT NULL default '0',
  `city_to_hide` enum('0','1') NOT NULL default '0',
  `duration_hide` enum('0','1') NOT NULL default '0',
  `weight_hide` enum('0','1') NOT NULL default '0',
  `fee` varchar(64) default '',
  `fee_type` enum('1','2') NOT NULL default '1',
  `delivery_add` int(11) default '0',
  `weight` varchar(64) default '1',
  `status` int(11),
  `delivery_id` varchar(64) default '',
  `payment_service` text,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_grastinwidget_system` VALUES (1, '', 0, 'Москва', 'Москва', '', 0, 0, 0, 0, '', 1, 0, 1, '', '', '', '1.0');

CREATE TABLE IF NOT EXISTS `phpshop_modules_grastinwidget_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_code` varchar(64) default 'success',
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `grastin_order_data` varchar(255) default '';