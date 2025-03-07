CREATE TABLE IF NOT EXISTS `smspircompany_modules_sms_message` (
  `id` int(11) NOT NULL auto_increment,
  `domen_api` varchar(50) NOT NULL default '',
  `login_api` varchar(100) NOT NULL default '',
  `password_api` varchar(100) NOT NULL default '',
  `admin_phone` varchar(255) NOT NULL default '',
  `sender` varchar(50) NOT NULL default '',
  `order_template_sms` text NOT NULL default '',
  `done_order_template_sms` text NOT NULL default '',
  `order_template_admin_sms` text NOT NULL default '',
  `change_status_order_template_sms` text NOT NULL default '',
  `cascade_domen_api` varchar(50) NOT NULL default '',
  `cascade_sender` varchar(50) NOT NULL default '',
  `cascade_enabled` enum('0','1') NOT NULL default '0',
  `order_template_viber` text NOT NULL default '',
  `order_template_viber_button_text` text NOT NULL default '',
  `order_template_viber_button_url` text NOT NULL default '',
  `order_template_viber_image_url` text NOT NULL default '',
  `order_template_admin_viber` text NOT NULL default '',
  `order_template_admin_viber_button_text` text NOT NULL default '',
  `order_template_admin_viber_button_url` text NOT NULL default '',
  `order_template_admin_viber_image_url` text NOT NULL default '',
  `change_status_order_template_viber` text NOT NULL default '',
  `change_status_order_template_viber_button_text` text NOT NULL default '',
  `change_status_order_template_viber_button_url` text NOT NULL default '',
  `change_status_order_template_viber_image_url` text NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `smspircompany_modules_sms_message` VALUES (1,'phpshop.pir.company','','','','PirCompany','','','','','phpshop.pir.company','media-gorod','0','','','','','','','','','','','','','1.0');