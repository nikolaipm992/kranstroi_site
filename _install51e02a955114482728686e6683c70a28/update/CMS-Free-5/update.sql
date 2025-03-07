-- 
-- Структура таблицы `phpshop_page`
--
RENAME TABLE  `phpshop_pages` TO `phpshop_page` ;
ALTER TABLE  `phpshop_page` CHANGE  `date`  `datas` VARCHAR( 64 ) NOT NULL;
ALTER TABLE  `phpshop_page` ADD  `odnotip` TEXT NOT NULL AFTER  `datas` ;
ALTER TABLE  `phpshop_page` ADD  `secure` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0' AFTER  `enabled`;
ALTER TABLE  `phpshop_page` ADD  `secure_groups` varchar(255) NOT NULL default '' AFTER  `secure`;
ALTER TABLE  `phpshop_page` CHANGE  `enabled`  `enabled` ENUM(  '0',  '1' )  NOT NULL DEFAULT  '0';

-- 
-- Структура таблицы `phpshop_page_categories`
--
RENAME TABLE `phpshop_categories` TO `phpshop_page_categories` ;


-- 
-- Структура таблицы `phpshop_modules_key`
-- 
TRUNCATE TABLE `phpshop_modules`;

CREATE TABLE `phpshop_modules_key` (
  `path` varchar(64) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `key` text NOT NULL,
  `verification` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_1c_docs`
-- 
CREATE TABLE `phpshop_1c_docs` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `cid` varchar(64) NOT NULL default '',
  `datas` int(11) NOT NULL default '0',
  `datas_f` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_1c_jurnal`
-- 
CREATE TABLE `phpshop_1c_jurnal` (
  `id` int(11) NOT NULL auto_increment,
  `datas` varchar(64) NOT NULL default '0',
  `p_name` varchar(64) NOT NULL default '',
  `f_name` varchar(64) NOT NULL default '',
  `time` float NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_baners`
-- 
RENAME TABLE `phpshop_banners` TO `phpshop_baners` ;
ALTER TABLE  `phpshop_baners` CHANGE  `enabled`  `flag` ENUM(  '0',  '1' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_baners` CHANGE  `date`  `datas` VARCHAR( 32 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_baners` CHANGE  `limit_all`  `limit_all` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_baners` ADD  `dir` VARCHAR( 255 ) NOT NULL AFTER  `limit_all` ;
ALTER TABLE  `phpshop_baners` CHANGE  `count_all`  `count_all` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_baners` CHANGE  `count_today`  `count_today` INT( 11 ) NOT NULL DEFAULT  '0';


-- 
-- Структура таблицы `phpshop_categories`
-- 
CREATE TABLE `phpshop_categories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `num` int(11) NOT NULL default '0',
  `parent_to` int(11) NOT NULL default '0',
  `yml` enum('0','1') NOT NULL default '1',
  `num_row` enum('1','2','3','4') NOT NULL default '2',
  `num_cow` tinyint(11) NOT NULL default '0',
  `sort` blob NOT NULL,
  `content` text NOT NULL,
  `vid` enum('0','1') NOT NULL default '0',
  `name_rambler` varchar(255) NOT NULL default '',
  `servers` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `title_enabled` enum('0','1','2') NOT NULL default '0',
  `title_shablon` varchar(255) NOT NULL default '',
  `descrip` varchar(255) NOT NULL default '',
  `descrip_enabled` enum('0','1','2') NOT NULL default '0',
  `descrip_shablon` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `keywords_enabled` enum('0','1','2') NOT NULL default '0',
  `keywords_shablon` varchar(255) NOT NULL default '',
  `skin` varchar(64) NOT NULL default '',
  `skin_enabled` enum('0','1') NOT NULL default '0',
  `order_by` enum('1','2','3') NOT NULL default '3',
  `order_to` enum('1','2') NOT NULL default '1',
  `secure_groups` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_to` (`parent_to`),
  KEY `servers` (`servers`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_comment`
-- 
CREATE TABLE `phpshop_comment` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `datas` varchar(32) default NULL,
  `name` varchar(32) default NULL,
  `parent_id` int(11) NOT NULL default '0',
  `content` text,
  `user_id` int(11) NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_delivery`
-- 
CREATE TABLE `phpshop_delivery` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `city` varchar(255) NOT NULL default '',
  `price` float NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '1',
  `flag` enum('0','1') NOT NULL default '0',
  `price_null` float NOT NULL default '0',
  `price_null_enabled` enum('0','1') NOT NULL default '0',
  `PID` int(11) NOT NULL default '0',
  `taxa` int(11) NOT NULL default '0',
  `is_folder` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_delivery` VALUES
(1, 'Курьер', '0', '1', '', '0', '', 0, 0, '1'),
(3, 'Москва в пределах МКАД', '180', '1', '0', '0', '0', 1, 0, '0'),
(4, 'Москва за пределами МКАД', '300', '1', '0', '0', '0', 1, 0, '0');

-- 
-- Структура таблицы `phpshop_discount`
-- 
CREATE TABLE `phpshop_discount` (
  `id` tinyint(11) unsigned NOT NULL auto_increment,
  `sum` int(255) NOT NULL default '0',
  `discount` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_foto`
-- 
CREATE TABLE `phpshop_foto` (
  `id` int(11) NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `num` tinyint(11) NOT NULL default '0',
  `info` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_gbook`
-- 
ALTER TABLE  `phpshop_gbook` CHANGE  `date`  `datas` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_gbook` CHANGE  `title`  `tema` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_gbook` CHANGE  `question`  `otsiv` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_gbook` CHANGE  `answer`  `otvet` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_gbook` CHANGE  `enabled`  `flag` ENUM(  '0',  '1' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';


-- 
-- Структура таблицы `phpshop_links`
-- 
ALTER TABLE  `phpshop_links` CHANGE  `content`  `opis` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;


-- 
-- Структура таблицы `phpshop_menu`
-- 
ALTER TABLE  `phpshop_menu` CHANGE  `name`  `name` VARCHAR( 64 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_menu` CHANGE  `flag`  `flag` ENUM(  '0',  '1' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '1';
ALTER TABLE  `phpshop_menu` CHANGE  `element`  `element` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';

-- 
-- Структура таблицы `phpshop_messages`
-- 
CREATE TABLE `phpshop_messages` (
  `ID` int(11) NOT NULL auto_increment,
  `PID` int(11) NOT NULL default '0',
  `UID` int(11) NOT NULL default '0',
  `AID` int(11) NOT NULL default '0',
  `DateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Subject` text NOT NULL,
  `Message` text NOT NULL,
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_news`
--
ALTER TABLE  `phpshop_news` CHANGE  `date`  `datas` VARCHAR( 32 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_news` CHANGE  `title`  `zag` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_news` CHANGE  `description`  `kratko` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_news` CHANGE  `content`  `podrob` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL;
ALTER TABLE  `phpshop_news` ADD  `datau` INT NOT NULL AFTER  `podrob` ;

-- 
-- Структура таблицы `phpshop_notice`
--
CREATE TABLE `phpshop_notice` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `datas_start` varchar(64) NOT NULL default '',
  `datas` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_order_status`
--
CREATE TABLE `phpshop_order_status` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `color` varchar(64) NOT NULL default '',
  `sklad_action` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_order_status` VALUES
(1, 'Аннулирован', 'red', ''),
(2, 'Выполняется', '#99cccc', ''),
(3, 'Доставляется', '#ff9900', ''),
(4, 'Выполнен', '#ccffcc', '1'),
(100, 'Передано в бухгалтерию', '#ffff33', '');

-- 
-- Структура таблицы `phpshop_orders`
--
CREATE TABLE `phpshop_orders` (
  `id` int(11) NOT NULL auto_increment,
  `datas` varchar(64) NOT NULL default '',
  `uid` varchar(64) NOT NULL default '0',
  `orders` blob NOT NULL,
  `status` text NOT NULL,
  `user` int(11) unsigned NOT NULL default '0',
  `seller` enum('0','1') NOT NULL default '0',
  `statusi` tinyint(11) NOT NULL default '0',
  `admin` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_payment`
--
CREATE TABLE `phpshop_payment` (
  `uid` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `sum` float NOT NULL default '0',
  `datas` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `order` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_payment_systems`
--
CREATE TABLE `phpshop_payment_systems` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  `num` tinyint(11) NOT NULL default '0',
  `message` text NOT NULL,
  `message_header` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_payment_systems` VALUES
(1, 'Счет в банк', 'bank', '1', 0, 'Наши менеджеры свяжутся с вами. Счет доступен в личном кабинете.', 'ВАШ ЗАКАЗ УСПЕШНО ОФОРМЛЕН'),
(2, 'Квитанция Сбербанка', 'sberbank', '1', 0, 'Квитанция Сбербанка доступная в личном кабинете.', 'ВАШ ЗАКАЗ УСПЕШНО ОФОРМЛЕН'),
(3, 'Наличная оплата', 'message', '1', 0, 'В ближайшее время с вами свяжется наш менеджер.', 'ВАШ ЗАКАЗ УСПЕШНО ОФОРМЛЕН ');

-- 
-- Структура таблицы `phpshop_products`
--
CREATE TABLE `phpshop_products` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `content` text NOT NULL,
  `price` float NOT NULL default '0',
  `price_n` float NOT NULL default '0',
  `sklad` enum('0','1') NOT NULL default '0',
  `p_enabled` enum('0','1') NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '1',
  `uid` varchar(64) NOT NULL default '',
  `spec` enum('0','1') NOT NULL default '0',
  `odnotip` varchar(64) NOT NULL default '',
  `vendor` varchar(255) NOT NULL default '',
  `vendor_array` blob NOT NULL,
  `yml` enum('0','1') NOT NULL default '0',
  `num` int(11) NOT NULL default '1',
  `newtip` enum('0','1') NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `title_enabled` enum('0','1','2') NOT NULL default '0',
  `datas` int(11) NOT NULL default '0',
  `page` varchar(255) NOT NULL default '',
  `user` tinyint(11) NOT NULL default '0',
  `descrip` varchar(255) NOT NULL default '',
  `descrip_enabled` enum('0','1','2') NOT NULL default '0',
  `title_shablon` varchar(255) NOT NULL default '',
  `descrip_shablon` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `keywords_enabled` enum('0','1','2') NOT NULL default '0',
  `keywords_shablon` varchar(255) NOT NULL default '',
  `pic_small` varchar(255) NOT NULL default '',
  `pic_big` varchar(255) NOT NULL default '',
  `yml_bid_array` tinyblob NOT NULL,
  `parent_enabled` enum('0','1') NOT NULL default '0',
  `parent` text NOT NULL,
  `items` int(11) NOT NULL default '0',
  `weight` float NOT NULL default '0',
  `price2` float NOT NULL default '0',
  `price3` float NOT NULL default '0',
  `price4` float NOT NULL default '0',
  `price5` float NOT NULL default '0',
  `files` text NOT NULL,
  `baseinputvaluta` int(11) NOT NULL default '0',
  `ed_izm` varchar(255) NOT NULL default '',
  `dop_cat` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`),
  KEY `enabled` (`enabled`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_rating_categories`
--
CREATE TABLE `phpshop_rating_categories` (
  `id_category` int(11) NOT NULL auto_increment,
  `ids_dir` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '0',
  `revoting` enum('0','1') default NULL,
  PRIMARY KEY  (`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_rating_charact`
--
CREATE TABLE `phpshop_rating_charact` (
  `id_charact` int(11) NOT NULL auto_increment,
  `id_category` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `num` int(11) NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id_charact`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_rating_votes`
--
CREATE TABLE `phpshop_rating_votes` (
  `id_vote` int(11) NOT NULL auto_increment,
  `id_charact` int(11) NOT NULL default '0',
  `id_good` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `userip` varchar(16) NOT NULL default '',
  `rate` tinyint(4) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id_vote`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_search_base`
--
CREATE TABLE `phpshop_search_base` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `uid` varchar(255) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_search_jurnal`
--
CREATE TABLE `phpshop_search_jurnal` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `num` tinyint(32) NOT NULL default '0',
  `datas` varchar(11) NOT NULL default '',
  `dir` varchar(255) NOT NULL default '',
  `cat` tinyint(11) NOT NULL default '0',
  `set` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 ;

-- 
-- Структура таблицы `phpshop_servers`
--
CREATE TABLE `phpshop_servers` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `host` varchar(255) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_shopusers`
--
CREATE TABLE `phpshop_shopusers` (
  `id` int(64) unsigned NOT NULL auto_increment,
  `login` varchar(64) NOT NULL default '',
  `password` varchar(64) NOT NULL default '',
  `datas` varchar(64) NOT NULL default '',
  `mail` varchar(64) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `company` varchar(255) NOT NULL default '',
  `inn` varchar(64) NOT NULL default '',
  `tel` varchar(64) NOT NULL default '',
  `adres` text NOT NULL,
  `enabled` enum('0','1') NOT NULL default '0',
  `status` varchar(64) NOT NULL default '0',
  `kpp` varchar(64) NOT NULL default '',
  `tel_code` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_shopusers_status`
--
CREATE TABLE `phpshop_shopusers_status` (
  `id` tinyint(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `discount` float NOT NULL default '0',
  `price` enum('1','2','3','4','5') NOT NULL default '1',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- 
-- Структура таблицы `phpshop_sort`
--
CREATE TABLE `phpshop_sort` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `category` int(11) unsigned NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `page` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

        
-- 
-- Структура таблицы `phpshop_sort_categories`
--  
CREATE TABLE `phpshop_sort_categories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `flag` enum('0','1') NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `category` int(11) NOT NULL default '-1',
  `filtr` enum('0','1') NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `goodoption` enum('0','1') NOT NULL,
  `optionname` enum('0','1') NOT NULL,
  `page` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


-- 
-- Структура таблицы `phpshop_system`
--
DROP TABLE IF EXISTS `phpshop_system`;
CREATE TABLE `phpshop_system` (
  `id` int(32) NOT NULL auto_increment,
  `name` text,
  `company` text,
  `num_row` int(10) default NULL,
  `num_row_adm` int(10) default NULL,
  `dengi` tinyint(11) default NULL,
  `percent` varchar(16) NOT NULL default '',
  `skin` varchar(32) default NULL,
  `adminmail2` varchar(64) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `kurs` float NOT NULL default '0',
  `spec_num` tinyint(5) NOT NULL default '0',
  `new_num` tinyint(11) NOT NULL default '0',
  `tel` text NOT NULL,
  `bank` blob NOT NULL,
  `num_vitrina` enum('1','2','3') NOT NULL default '3',
  `width_icon` varchar(11) NOT NULL default '',
  `updateU` varchar(32) NOT NULL default '',
  `nds` varchar(64) NOT NULL default '',
  `nds_enabled` enum('0','1') NOT NULL default '1',
  `admoption` blob NOT NULL,
  `kurs_beznal` tinyint(11) NOT NULL default '0',
  `descrip` varchar(255) NOT NULL default '',
  `descrip_shablon` varchar(255) NOT NULL default '',
  `title_shablon` varchar(255) NOT NULL default '',
  `keywords_shablon` varchar(255) NOT NULL default '',
  `title_shablon2` varchar(255) NOT NULL default '',
  `descrip_shablon2` varchar(255) NOT NULL default '',
  `keywords_shablon2` varchar(255) NOT NULL default '',
  `logo` varchar(255) NOT NULL default '',
  `promotext` text NOT NULL,
  `title_shablon3` varchar(255) NOT NULL default '',
  `descrip_shablon3` varchar(255) NOT NULL default '',
  `keywords_shablon3` varchar(255) NOT NULL default '',
  `rss_use` int(1) unsigned NOT NULL default '1',
  `1c_load_accounts` enum('0','1') NOT NULL default '1',
  `1c_load_invoice` enum('0','1') NOT NULL default '1',
  `1c_option` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_system` (`id`, `name`, `company`, `num_row`, `num_row_adm`, `dengi`, `percent`, `skin`, `adminmail2`, `title`, `keywords`, `kurs`, `spec_num`, `new_num`, `tel`, `bank`, `num_vitrina`, `width_icon`, `updateU`, `nds`, `nds_enabled`, `admoption`, `kurs_beznal`, `descrip`, `descrip_shablon`, `title_shablon`, `keywords_shablon`, `title_shablon2`, `descrip_shablon2`, `keywords_shablon2`, `logo`, `promotext`, `title_shablon3`, `descrip_shablon3`, `keywords_shablon3`, `rss_use`, `1c_load_accounts`, `1c_load_invoice`, `1c_option`) VALUES
(1, 'Название лучшего интернет-магазина', 'Ваше имя', 6, 3, 6, '0', 'bootstrap', 'mail@localhost', 'Демо-версия скрипта интернет-магазина PHPShop', 'скрипт магазина, купить интернет-магазин', 6, 4, 3, '(495)111-22-33', 0x613a393a7b733a383a226f72675f6e616d65223b733a32333a22cecece2022d3f1efe5f8edfbe920cff0eee4e0e2e5f622223b733a31323a226f72675f75725f6164726573223b733a32333a22cceef1eae2e02c20f3eb2e20def0e8e4e8f7e5f1eae0ff223b733a393a226f72675f6164726573223b733a32323a22cceef1eae2e02c20f3eb2e20d4e8e7e8f7e5f1eae0ff223b733a373a226f72675f696e6e223b733a31343a223134313431343134313431343134223b733a373a226f72675f6b7070223b733a31363a2232333233323332333233323332323332223b733a393a226f72675f7363686574223b733a31373a223334333433343334333433343333343334223b733a383a226f72675f62616e6b223b733a31393a22c7c0ce2022cde0e4e5e6edfbe920c1e0edea22223b733a373a226f72675f626963223b733a383a223436373738383838223b733a31343a226f72675f62616e6b5f7363686574223b733a31393a2232343234323432343234323434363537353737223b7d, '2', '', '1395929787', '18', '1', 0x613a35333a7b733a31373a227072657670616e656c5f656e61626c6564223b733a313a2231223b733a31323a22736b6c61645f737461747573223b733a313a2231223b733a31343a2268656c7065725f656e61626c6564223b4e3b733a31333a22636c6f75645f656e61626c6564223b733a313a2231223b733a32333a226469676974616c5f70726f647563745f656e61626c6564223b4e3b733a31333a22757365725f63616c656e646172223b4e3b733a31393a22757365725f70726963655f6163746976617465223b4e3b733a32323a22757365725f6d61696c5f61637469766174655f707265223b4e3b733a31383a227273735f6772616265725f656e61626c6564223b733a313a2231223b733a31373a22696d6167655f736176655f736f75726365223b733a313a2231223b733a363a22696d675f776d223b4e3b733a353a22696d675f77223b733a333a22333030223b733a353a22696d675f68223b733a333a22333030223b733a363a22696d675f7477223b733a333a22313030223b733a363a22696d675f7468223b733a333a22313030223b733a31343a2277696474685f706f64726f626e6f223b733a323a223930223b733a31323a2277696474685f6b7261746b6f223b733a323a223930223b733a31353a226d6573736167655f656e61626c6564223b733a313a2231223b733a31323a226d6573736167655f74696d65223b733a323a223330223b733a31353a226465736b746f705f656e61626c6564223b4e3b733a31323a226465736b746f705f74696d65223b4e3b733a383a226f706c6174615f31223b733a313a2231223b733a383a226f706c6174615f32223b733a313a2231223b733a383a226f706c6174615f33223b733a313a2231223b733a383a226f706c6174615f34223b4e3b733a383a226f706c6174615f35223b733a313a2231223b733a383a226f706c6174615f36223b733a313a2231223b733a383a226f706c6174615f37223b733a313a2231223b733a383a226f706c6174615f38223b733a313a2231223b733a31343a2273656c6c65725f656e61626c6564223b4e3b733a31323a22626173655f656e61626c6564223b4e3b733a31313a22736d735f656e61626c6564223b4e3b733a31343a226e6f746963655f656e61626c6564223b4e3b733a31343a227570646174655f656e61626c6564223b733a313a2231223b733a373a22626173655f6964223b733a303a22223b733a393a22626173655f686f7374223b733a303a22223b733a343a226c616e67223b4e3b733a31333a22736b6c61645f656e61626c6564223b733a313a2231223b733a31303a2270726963655f7a6e616b223b733a313a2230223b733a31383a22757365725f6d61696c5f6163746976617465223b733a313a2231223b733a31313a22757365725f737461747573223b733a313a2230223b733a393a22757365725f736b696e223b733a313a2231223b733a31323a22636172745f6d696e696d756d223b733a343a2231303030223b733a31343a22656469746f725f656e61626c6564223b733a313a2231223b733a31333a2277617465726d61726b5f626967223b613a32313a7b733a31343a226269675f6d657267654c6576656c223b693a37303b733a31313a226269675f656e61626c6564223b733a313a2231223b733a383a226269675f74797065223b733a333a22706e67223b733a31323a226269675f706e675f66696c65223b733a33303a222f5573657246696c65732f496d6167652f73686f705f6c6f676f2e706e67223b733a31323a226269675f636f7079466c6167223b733a313a2230223b733a363a226269675f736d223b693a303b733a31363a226269675f706f736974696f6e466c6167223b733a313a2234223b733a31333a226269675f706f736974696f6e58223b693a303b733a31333a226269675f706f736974696f6e59223b693a303b733a393a226269675f616c706861223b693a37303b733a383a226269675f74657874223b733a303a22223b733a32313a226269675f746578745f706f736974696f6e466c6167223b693a303b733a383a226269675f73697a65223b693a303b733a393a226269675f616e676c65223b693a303b733a31383a226269675f746578745f706f736974696f6e58223b693a303b733a31383a226269675f746578745f706f736974696f6e59223b693a303b733a31303a226269675f636f6c6f7252223b693a303b733a31303a226269675f636f6c6f7247223b693a303b733a31303a226269675f636f6c6f7242223b693a303b733a31343a226269675f746578745f616c706861223b693a303b733a383a226269675f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31353a2277617465726d61726b5f736d616c6c223b613a32313a7b733a31363a22736d616c6c5f6d657267654c6576656c223b693a3130303b733a31333a22736d616c6c5f656e61626c6564223b733a313a2231223b733a31303a22736d616c6c5f74797065223b733a333a22706e67223b733a31343a22736d616c6c5f706e675f66696c65223b733a32353a222f5573657246696c65732f496d6167652f6c6f676f2e706e67223b733a31343a22736d616c6c5f636f7079466c6167223b733a313a2230223b733a383a22736d616c6c5f736d223b693a303b733a31383a22736d616c6c5f706f736974696f6e466c6167223b733a313a2231223b733a31353a22736d616c6c5f706f736974696f6e58223b693a303b733a31353a22736d616c6c5f706f736974696f6e59223b693a303b733a31313a22736d616c6c5f616c706861223b693a35303b733a31303a22736d616c6c5f74657874223b733a303a22223b733a32333a22736d616c6c5f746578745f706f736974696f6e466c6167223b693a303b733a31303a22736d616c6c5f73697a65223b693a303b733a31313a22736d616c6c5f616e676c65223b693a303b733a32303a22736d616c6c5f746578745f706f736974696f6e58223b693a303b733a32303a22736d616c6c5f746578745f706f736974696f6e59223b693a303b733a31323a22736d616c6c5f636f6c6f7252223b693a303b733a31323a22736d616c6c5f636f6c6f7247223b693a303b733a31323a22736d616c6c5f636f6c6f7242223b693a303b733a31363a22736d616c6c5f746578745f616c706861223b693a303b733a31303a22736d616c6c5f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31353a2277617465726d61726b5f6973686f64223b613a32313a7b733a31363a226973686f645f6d657267654c6576656c223b693a3130303b733a31333a226973686f645f656e61626c6564223b4e3b733a31303a226973686f645f74797065223b733a333a22706e67223b733a31343a226973686f645f706e675f66696c65223b733a303a22223b733a31343a226973686f645f636f7079466c6167223b733a313a2230223b733a383a226973686f645f736d223b693a303b733a31383a226973686f645f706f736974696f6e466c6167223b733a313a2231223b733a31353a226973686f645f706f736974696f6e58223b693a303b733a31353a226973686f645f706f736974696f6e59223b693a303b733a31313a226973686f645f616c706861223b693a303b733a31303a226973686f645f74657874223b733a303a22223b733a32333a226973686f645f746578745f706f736974696f6e466c6167223b693a303b733a31303a226973686f645f73697a65223b693a303b733a31313a226973686f645f616e676c65223b693a303b733a32303a226973686f645f746578745f706f736974696f6e58223b693a303b733a32303a226973686f645f746578745f706f736974696f6e59223b693a303b733a31323a226973686f645f636f6c6f7252223b693a303b733a31323a226973686f645f636f6c6f7247223b693a303b733a31323a226973686f645f636f6c6f7242223b693a303b733a31363a226973686f645f746578745f616c706861223b693a303b733a31303a226973686f645f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31303a2263616c69627261746564223b4e3b733a31343a226e6f776275795f656e61626c6564223b733a313a2232223b733a393a22786d6c656e636f6465223b733a303a22223b733a32343a22736d735f7374617475735f6f726465725f656e61626c6564223b4e3b733a363a22656469746f72223b733a373a2264656661756c74223b733a353a227468656d65223b733a373a2264656661756c74223b7d, 6, 'PHPShop – это готовое решение для быстрого создания интернет-магазина.', '@Podcatalog@, @Catalog@, @System@', '@Podcatalog@ - @Catalog@ - @System@', '@Podcatalog@, @Catalog@, @Generator@', '@Product@ - @Podcatalog@ - @Catalog@', '@Product@, @Podcatalog@, @Catalog@', '@Product@,@System@', '/UserFiles/Image/Trial/your_logo.png', '', '@Catalog@ - @System@', '@Catalog@', '@Catalog@', 0, '', '', 0x613a353a7b733a31313a227570646174655f6e616d65223b733a313a2231223b733a31343a227570646174655f636f6e74656e74223b733a313a2231223b733a31383a227570646174655f6465736372697074696f6e223b733a313a2231223b733a31353a227570646174655f63617465676f7279223b733a313a2231223b733a31313a227570646174655f736f7274223b733a313a2231223b7d);


-- 
-- Структура таблицы `phpshop_users`
-- 
ALTER TABLE  `phpshop_users` ADD  `content` TEXT NOT NULL AFTER  `enabled` ;
ALTER TABLE  `phpshop_users` ADD `skin` varchar(255) NOT NULL default '' AFTER  `content` ;
ALTER TABLE  `phpshop_users` ADD `skin_enabled` enum('0','1') NOT NULL default '0' AFTER  `skin` ;
ALTER TABLE  `phpshop_users` ADD `name` varchar(255) NOT NULL default '' AFTER  `skin_enabled` ;
ALTER TABLE  `phpshop_users` ADD `name_enabled` enum('0','1') NOT NULL default '0' AFTER  `name` ;

-- 
-- Структура таблицы `phpshop_valuta`
-- 
CREATE TABLE `phpshop_valuta` (
  `id` tinyint(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `code` varchar(64) NOT NULL default '',
  `iso` varchar(64) NOT NULL default '',
  `kurs` varchar(64) NOT NULL default '0',
  `num` tinyint(11) NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_valuta` VALUES (4, 'Гривны', 'гр.', 'UAH', '0.06', 4, '1');
INSERT INTO `phpshop_valuta` VALUES (5, 'Доллары', '$', 'USD', '0.03', 0, '1');
INSERT INTO `phpshop_valuta` VALUES (6, 'Рубли', 'руб', 'RUR', '1', 1, '1');

ALTER TABLE  `phpshop_delivery` ADD  `icon` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_payment_systems` ADD  `icon` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_comment` ADD  `rate` ENUM(  '0',  '1',  '2',  '3',  '4',  '5' ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_sort_categories` ADD  `brand` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_sort` ADD  `icon` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `phpshop_shopusers` ADD  `wishlist` BLOB NOT NULL;
ALTER TABLE  `phpshop_shopusers` ADD  `data_adres` BLOB NOT NULL;
ALTER TABLE  `phpshop_delivery` ADD  `city_select` ENUM(  '0',  '1',  '2' ) NOT NULL DEFAULT  '0', ADD  `data_fields` BLOB NOT NULL;
ALTER TABLE  `phpshop_payment_systems` ADD  `yur_data_flag` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_orders` 
ADD  `country` VARCHAR( 255 ) NOT NULL ,
ADD  `state` VARCHAR( 255 ) NOT NULL ,
ADD  `city` VARCHAR( 255 ) NOT NULL ,
ADD  `index` VARCHAR( 255 ) NOT NULL ,
ADD  `fio` VARCHAR( 255 ) NOT NULL ,
ADD  `tel` VARCHAR( 255 ) NOT NULL ,
ADD  `street` VARCHAR( 255 ) NOT NULL ,
ADD  `house` VARCHAR( 255 ) NOT NULL ,
ADD  `porch` VARCHAR( 255 ) NOT NULL ,
ADD  `door_phone` VARCHAR( 255 ) NOT NULL ,
ADD  `flat` VARCHAR( 255 ) NOT NULL ,
ADD  `org_name` VARCHAR( 255 ) NOT NULL ,
ADD  `org_inn` VARCHAR( 255 ) NOT NULL ,
ADD  `org_kpp` VARCHAR( 255 ) NOT NULL ,
ADD  `org_yur_adres` VARCHAR( 255 ) NOT NULL ,
ADD  `org_fakt_adres` VARCHAR( 255 ) NOT NULL ,
ADD  `org_ras` VARCHAR( 255 ) NOT NULL ,
ADD  `org_bank` VARCHAR( 255 ) NOT NULL ,
ADD  `org_kor` VARCHAR( 255 ) NOT NULL ,
ADD  `org_bik` VARCHAR( 255 ) NOT NULL ,
ADD  `org_city` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `phpshop_delivery` ADD  `num` SMALLINT( 3 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_delivery` CHANGE  `city_select`  `city_select` ENUM(  '0',  '1',  '2',  '3' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_delivery` CHANGE  `city_select`  `city_select` ENUM(  '0',  '1',  '2' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_orders` ADD  `dop_info` TEXT NOT NULL;
ALTER TABLE  `phpshop_orders` ADD  `delivtime` VARCHAR( 255 ) NOT NULL AFTER  `flat`;
ALTER TABLE  `phpshop_sort_categories` DROP  `flag`;
CREATE TABLE `phpshop_slider` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '0',
  `num` smallint(6) NOT NULL,
  `link` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
ALTER TABLE  `phpshop_products` ADD  `rate` FLOAT UNSIGNED NOT NULL ;
ALTER TABLE  `phpshop_products` CHANGE  `rate`  `rate` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_products` ADD  `rate_count` INT UNSIGNED NOT NULL DEFAULT  '0';


CREATE TABLE `phpshop_citylist_city` (
  `city_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(11) unsigned NOT NULL DEFAULT '0',
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`city_id`),
  KEY `country_id` (`country_id`),
  KEY `region_id` (`region_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=15789521 ;



ALTER TABLE  `phpshop_products` CHANGE  `rate`  `rate` FLOAT( 1.1 ) UNSIGNED NOT NULL DEFAULT  '0.0';

ALTER TABLE  `phpshop_shopusers_status` ADD  `cumulative_discount_check` INT NOT NULL AFTER  `enabled` ,
ADD  `cumulative_discount` BLOB NOT NULL AFTER  `cumulative_discount_check` ;

ALTER TABLE  `phpshop_shopusers` ADD  `cumulative_discount` INT NOT NULL AFTER  `data_adres` ;
ALTER TABLE  `phpshop_order_status` ADD  `cumulative_action` ENUM(  '0',  '1' ) NOT NULL AFTER  `sklad_action` ;

ALTER TABLE  `phpshop_comment` CHANGE  `rate`  `rate` SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `phpshop_orders` ADD  `sum` FLOAT ;
ALTER TABLE  `phpshop_categories` ADD  `dop_cat` varchar(255) DEFAULT '';
ALTER TABLE  `phpshop_sort_categories` ADD  `product` enum('0','1') NOT NULL DEFAULT '0' ;
ALTER TABLE `phpshop_servers` ADD `tel` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `company` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `adres` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `logo` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `adminmail` varchar(255) default '';
ALTER TABLE `phpshop_order_status` ADD `mail_message` text default '';
ALTER TABLE `phpshop_categories` ADD `sort_cache` blob;
ALTER TABLE `phpshop_categories` ADD `sort_cache_created_at` int(11);
ALTER TABLE `phpshop_servers` ADD `currency` int(11);
ALTER TABLE `phpshop_servers` ADD `lang` varchar(32);
ALTER TABLE `phpshop_news` ADD `odnotip` text;
ALTER TABLE `phpshop_servers` ADD `admoption` blob;
ALTER TABLE `phpshop_news` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_page_categories` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_delivery` ADD `sum_max` float DEFAULT '0';
ALTER TABLE `phpshop_modules` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_payment_systems` ADD `color` varchar(64) default '#000000';
ALTER TABLE `phpshop_system` CHANGE `width_icon` `icon` varchar(255) default ''; 
ALTER TABLE `phpshop_sort_categories` ADD `virtual` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_categories` ADD `menu` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_page_categories` ADD `menu` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_order_status` ADD `sms_action` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_search_base` ADD `link` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_sort` ADD `description` text;
ALTER TABLE `phpshop_sort` ADD `title` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_news` ADD `icon`  varchar(255) DEFAULT '';

CREATE TABLE IF NOT EXISTS `phpshop_warehouses` (
  `id` int(11) AUTO_INCREMENT,
  `name` varchar(64) ,
  `description` varchar(255) ,
  `uid` varchar(64),
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11) ,
  `servers` varchar(64) default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_servers` ADD `warehouse` int(11) DEFAULT '0';
ALTER TABLE `phpshop_page` ADD `icon`  varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page` ADD `preview` text;
ALTER TABLE `phpshop_servers` ADD `price` enum('1','2','3','4','5') DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `warehouse` int(11) DEFAULT '0';
ALTER TABLE `phpshop_sort_categories` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_baners` ADD `skin` varchar(64) default '';
ALTER TABLE `phpshop_page` ADD `footer` enum('0','1') DEFAULT '1';

CREATE TABLE IF NOT EXISTS `phpshop_promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') DEFAULT '0',
  `description` text NOT NULL,
  `label` text NOT NULL,
  `active_check` enum('0','1') NOT NULL,
  `active_date_ot` varchar(255) NOT NULL,
  `active_date_do` varchar(255) NOT NULL,
  `discount_check` enum('0','1') NOT NULL,
  `discount_tip` enum('0','1') NOT NULL,
  `discount` int(11) NOT NULL,
  `free_delivery` enum('0','1') NOT NULL,
  `categories_check` enum('0','1') NOT NULL,
  `categories` text NOT NULL,
  `status_check` enum('0','1') NOT NULL DEFAULT '0',
  `statuses` text NOT NULL DEFAULT '',
  `products_check` enum('0','1') NOT NULL,
  `products` text NOT NULL,
  `sum_order_check` enum('0','1') NOT NULL,
  `sum_order` int(11) NOT NULL,
  `delivery_method_check` enum('0','1') NOT NULL,
  `delivery_method` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `block_old_price` enum('0','1') DEFAULT '0',
  `hide_old_price` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_gbook` ADD `servers` varchar(64) default '';

CREATE TABLE `phpshop_push` (
  `token` text,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_discount` ADD `action` ENUM('1', '2') DEFAULT '1';
ALTER TABLE `phpshop_orders` ADD `admin` int(11) default 0;
ALTER TABLE `phpshop_parent_name` ADD `color` VARCHAR(255);
ALTER TABLE `phpshop_orders` ADD `servers` int(11) default 0;
ALTER TABLE `phpshop_servers` ADD `admin` int(11) default 0;

ALTER TABLE `phpshop_promotions` CHANGE `free_delivery` `num_check` int(11) DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_shopusers` ADD `servers` int(11) default 0;
ALTER TABLE `phpshop_shopusers` DROP INDEX `login`;
ALTER TABLE `phpshop_orders` ADD `paid` TINYINT(1) DEFAULT NULL;

CREATE TABLE `phpshop_bonus` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `comment` varchar(255) default '',
  `user_id` int(11),
  `order_id` int(11),
  `bonus_operation` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_shopusers` ADD `bonus` int(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `bonus_minus` int(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `bonus_plus` int(11) DEFAULT '0';

ALTER TABLE `phpshop_categories`  ADD `tile` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_sort_categories`  ADD `show_preview` ENUM('0','1') DEFAULT '0';
update `phpshop_categories`  set `tile`='1' where parent_to='0';

ALTER TABLE `phpshop_delivery` ADD `comment` TEXT;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `option` blob,
  `type` varchar(64),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `length` varchar(64) default '';
ALTER TABLE `phpshop_products` ADD `width` varchar(64) default '';
ALTER TABLE `phpshop_products` ADD `height` varchar(64) default '';

ALTER TABLE `phpshop_page_categories` ADD `icon` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `title` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `description` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `keywords` text;
ALTER TABLE `phpshop_photo_categories` ADD `count` tinyint(11) default 2;

ALTER TABLE `phpshop_users` ADD `token` VARCHAR(64);
ALTER TABLE `phpshop_slider` ADD `mobile` enum('0','1') default '0';
ALTER TABLE `phpshop_search_jurnal` ADD `ip` VARCHAR(64);

ALTER TABLE `phpshop_order_status` ADD `num` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `date` INT(11) DEFAULT '0';

CREATE TABLE `phpshop_notes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `message` text ,
  `status` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_promotions` ADD `action` ENUM('1', '2') DEFAULT '1';
ALTER TABLE `phpshop_shopusers` ADD `token` INT(11);
ALTER TABLE `phpshop_shopusers` ADD `token_time` INT(11);
ALTER TABLE `phpshop_servers` ADD `icon` VARCHAR(255);
ALTER TABLE `phpshop_notes` ADD `name` VARCHAR(64), ADD `tel` VARCHAR(64), ADD `mail` VARCHAR(64), ADD `content` TEXT;

ALTER TABLE `phpshop_payment_systems` ADD `company` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `company` INT(11) DEFAULT '0';
CREATE TABLE `phpshop_company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bank` blob,
  `enabled` enum('0','1') DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
ALTER TABLE `phpshop_servers` ADD `company_id` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_discount` ADD `block_old_price` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_discount` ADD `block_categories` text DEFAULT '';
ALTER TABLE `phpshop_sort` ADD `meta_description` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_baners` CHANGE `count_all` `type` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_baners` CHANGE `count_today` `display` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_baners` CHANGE `limit_all` `size` ENUM('0','1','2') DEFAULT '0';
ALTER TABLE `phpshop_shopusers` ADD `bot` VARCHAR(64) DEFAULT '';
UPDATE `phpshop_shopusers` SET `bot` = MD5(CONCAT(`id`,`login`));
ALTER TABLE `phpshop_order_status` ADD `bot_action` ENUM('0','1') DEFAULT '0';

CREATE TABLE `phpshop_dialog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `chat_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `bot` varchar(64) NOT NULL,
  `staffid` enum('0','1') DEFAULT '1',
  `isview` enum('0','1') DEFAULT '1',
  `order_id` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_system` ADD `sort_title_shablon` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_system` ADD `sort_description_shablon` varchar(255) DEFAULT '';

ALTER TABLE `phpshop_dialog` ADD `attachments` VARCHAR(255);
ALTER TABLE `phpshop_delivery` ADD `weight_max` int(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `weight_min` int(11) DEFAULT '0';
ALTER TABLE `phpshop_dialog` ADD `isview_user` enum('0','1') DEFAULT '1';

CREATE TABLE `phpshop_dialog_answer` (
  `id` int(11) NOT NULL,
  `name` varchar(64),
  `message` text,
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11),
  `servers` varchar(255),
  `view` enum('1','2') DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `price_purch` FLOAT DEFAULT '0';
ALTER TABLE `phpshop_shopusers` ADD `dialog_ban` ENUM('0','1') DEFAULT '0';

/*622*/
DROP TABLE IF EXISTS `phpshop_exchanges_log`;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `option` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

/*623*/
ALTER TABLE `phpshop_newsletter` ADD `servers` INT(11) DEFAULT '0';

/*625*/
ALTER TABLE `phpshop_baners` CHANGE `type` `type` ENUM('0','1','2','3') DEFAULT '0';

/*627*/
ALTER TABLE `phpshop_categories` ADD `length` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `width` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `height` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `weight` float DEFAULT '0';
ALTER TABLE `phpshop_categories` ADD `ed_izm` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_slider` ADD `color` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_baners` ADD `color` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `color` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_jurnal` CHANGE `ip` `ip` VARCHAR(64) DEFAULT '';
ALTER TABLE `phpshop_system` CHANGE `num_vitrina` `num_vitrina` ENUM('1','2','3','4','5','6') DEFAULT '3';
ALTER TABLE `phpshop_categories` CHANGE `num_row` `num_row` ENUM('1','2','3','4','5','6') DEFAULT '3';

/*634*/
ALTER TABLE `phpshop_payment_systems` ADD `status` INT(11) DEFAULT '0';

/*636*/
ALTER TABLE `phpshop_products` ADD `external_code` varchar(64) DEFAULT '';

/*637*/
ALTER TABLE `phpshop_products` ADD INDEX(`external_code`);

/*639*/
UPDATE `phpshop_system` SET `kurs_beznal` = '0';
ALTER TABLE `phpshop_system` CHANGE `kurs_beznal` `shop_type` ENUM('0','1','2') NULL DEFAULT '0';
ALTER TABLE `phpshop_servers` ADD `shop_type` ENUM('0','1','2') NULL DEFAULT '0';

/*643*/
ALTER TABLE `phpshop_payment_systems` ADD `sum_max` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `sum_min` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `discount_max` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `discount_min` float DEFAULT '0';

/*646*/
ALTER TABLE `phpshop_products` ADD `type` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_menu` ADD `dop_cat` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_menu` ADD `mobile` enum('0','1') DEFAULT '0';

/*649*/
ALTER TABLE `phpshop_categories` ADD `podcatalog_view` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_system` ADD `ai` BLOB NOT NULL;
ALTER TABLE `phpshop_search_base` ADD `url` VARCHAR(255) NOT NULL;
ALTER TABLE `phpshop_order_status` ADD `external_code` VARCHAR(64) NOT NULL;
ALTER TABLE `phpshop_delivery` ADD `external_code` VARCHAR(64) NOT NULL;
ALTER TABLE `phpshop_dialog` ADD `ai` ENUM('0','1') NOT NULL DEFAULT '0';

/*650*/
ALTER TABLE `phpshop_products` ADD INDEX(`spec`);
ALTER TABLE `phpshop_products` ADD INDEX(`newtip`);
ALTER TABLE `phpshop_products` ADD INDEX(`yml`);
ALTER TABLE `phpshop_products` ADD INDEX(`parent_enabled`);
ALTER TABLE `phpshop_products` ADD INDEX(`sklad`);
ALTER TABLE `phpshop_products` ADD INDEX(`dop_cat`);
ALTER TABLE `phpshop_categories` ADD INDEX(`vid`);
ALTER TABLE `phpshop_categories` ADD INDEX(`skin_enabled`);
ALTER TABLE `phpshop_categories` ADD INDEX(`menu`);
ALTER TABLE `phpshop_categories` ADD INDEX(`tile`);
ALTER TABLE `phpshop_categories` ADD INDEX(`podcatalog_view`);
ALTER TABLE `phpshop_categories` ADD INDEX(`dop_cat`);

/*652*/
ALTER TABLE `phpshop_products` ADD `import_id` VARCHAR(64) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_exchanges_log` ADD `import_id` VARCHAR(64) NOT NULL DEFAULT '';