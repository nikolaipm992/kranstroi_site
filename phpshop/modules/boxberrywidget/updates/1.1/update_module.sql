ALTER TABLE `phpshop_modules_boxberrywidget_log` ADD `status_code` varchar(64) default 'success';
ALTER TABLE `phpshop_modules_boxberrywidget_log` ADD `tracking` varchar(64) default '';
ALTER TABLE `phpshop_modules_boxberrywidget_system` ADD `api_url` varchar(255) default 'http://api.boxberry.de';