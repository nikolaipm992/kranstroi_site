ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `test` enum('0','1')  DEFAULT '0';
ALTER TABLE `phpshop_modules_cdekwidget_log` DROP `tracking`;
ALTER TABLE `phpshop_orders` CHANGE `cdek_order_data` `cdek_order_data` text default '';