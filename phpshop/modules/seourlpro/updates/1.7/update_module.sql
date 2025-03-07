ALTER TABLE `phpshop_sort` ADD `sort_seo_name` VARCHAR(255) NOT NULL;
ALTER TABLE `phpshop_modules_seourlpro_system` ADD `seo_brands_enabled` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_news` ADD `news_seo_name` VARCHAR(255) NOT NULL;
ALTER TABLE `phpshop_page_categories` ADD `page_cat_seo_name` VARCHAR(255) NOT NULL;
ALTER TABLE `phpshop_modules_seourlpro_system` ADD `seo_news_enabled` enum('1','2') NOT NULL default '2';
ALTER TABLE `phpshop_modules_seourlpro_system` ADD `seo_page_enabled` enum('1','2') NOT NULL default '2';
ALTER TABLE `phpshop_products` ADD `prod_seo_name_old` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `cat_seo_name_old` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_modules_seourlpro_system` ADD  `redirect_enabled` enum('1','2') default '1';
