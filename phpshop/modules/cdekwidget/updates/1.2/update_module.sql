ALTER TABLE `phpshop_products` ADD `cdek_length` varchar(255) default '';
ALTER TABLE `phpshop_products` ADD `cdek_width` varchar(255) default '';
ALTER TABLE `phpshop_products` ADD `cdek_height` varchar(255) default '';
ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `fee` int(11) default 0;
ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `fee_type` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `test` enum('0','1')  DEFAULT '0';