ALTER TABLE `phpshop_modules_tinkoff_system` ADD `status` int(11) NOT NULL;
ALTER TABLE `phpshop_modules_tinkoff_system` ADD `title_end` text NOT NULL;
CREATE TABLE IF NOT EXISTS `phpshop_modules_tinkoff_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
ALTER TABLE `phpshop_modules_tinkoff_system` ADD `status_confirmed` int(11) NOT NULL;