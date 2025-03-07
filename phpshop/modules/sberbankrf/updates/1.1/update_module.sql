ALTER TABLE `phpshop_modules_sberbankrf_system` ADD `token` varchar(64) DEFAULT NULL;
ALTER TABLE `phpshop_modules_sberbankrf_system` ADD `force_payment` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_sberbankrf_system` ADD `notification` enum('0','1') NOT NULL default '0';