
DROP TABLE IF EXISTS `phpshop_modules_partner_users`;
CREATE TABLE `phpshop_modules_partner_users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(64) default '',
  `password` varchar(64) default '',
  `name` varchar(64) default '',
  `date` varchar(64) default '',
  `money` varchar(255) default '',
  `enabled` enum('0','1') default '0',
  `content` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `phpshop_modules_partner_payment`;
CREATE TABLE `phpshop_modules_partner_payment` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `partner_id` varchar(11) NOT NULL default '0',
  `sum` float NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '0',
  `date_done` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_partner_log`;
CREATE TABLE `phpshop_modules_partner_log` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `order_id` int(11) default '0',
  `order_uid` varchar(64) default '0',
  `partner_id` varchar(11) default '0',
  `path` varchar(255) default '',
  `sum` float default '0',
  `enabled` enum('0','1') default '0',
  `percent` float default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `phpshop_modules_partner_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_partner_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1') NOT NULL default '0',
  `percent` float NOT NULL default '0',
  `order_status` tinyint(11) NOT NULL default '0',
  `rule` text NOT NULL,
  `cookies_day` int(11) default '360',
  `stat_day` int(11) default '30',
  `version` varchar(64)  DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_partner_system` VALUES (1, '1', 5, 4, '<h1>������� � ������� ���������� ���������</h1>\r\n\r\n<p>\r\n    <b>1. ��������� ���������� ���������.</b><br>\r\n    ����������� ���������� ��������� ����� ���� ���������� ����. ��� ����������� ������ ���������� �������� ��, ����������� ��������, ���� ��� �����������, � ��� �� ��������������� ��� ����������� ������������ ����.\r\n\r\n<p>\r\n    <b>2. ������ ����� �������.</b><br>\r\n    �� ����� ����������� ��� ��������, ������������� � ������� @partnerPercent@% �� ��������� ����������� ������.\r\n\r\n<p>\r\n    <b>3. ������� ������.</b><br>\r\n    ��� ���������� ������� ������������ � ������ ����� ����������� �������� ������� �Money, �� ������ ������������ � ������ �������� �� ������ https://yoomoney.ru/\r\n\r\n<p>\r\n    <b>4. ����������� ����� � ������.</b><br>\r\n    ����������� ����� � ������ ����������� � ������� 500 ���. � ������, ���� ������������ ���� ���������� �������� �� ��������� 500 ���, ������ �������� �� ����� �������� �� ��� ���, ���� ����� �������� �� ��������� �� ������� ���� 500 ���. ������ ���������� �������� ������������ ������ 2 ������.\r\n\r\n<p>\r\n    <b>5. ���������� ��������.</b><br>\r\n    ���������� �������� ����� ��������� ������ ���� ����� �������� � ������� �����������, �������� ������� ��.\r\n\r\n<p>\r\n    <b>���������� �������� ����������� ������ �� ���������� ������.</b>\r\n\r\n<p>\r\n    ���������� �������� �� ����� ���������, ����:<br>\r\n    �) ����������, ��������� � ������ ����� �� ����� ���� ����� �������� �� ����������� �������� (��������� "Cookies" � �.�.).<br>\r\n    �) ���������� ������� � ������� �� ���������� ������ ������� �������.<br>\r\n    �) �����������, ���������� ����� ����� ���� ���������� ������ �� ������� ���.<br>\r\n\r\n<p>\r\n    <b>6. �������.</b><br>\r\n    ����������, ����������� ������ ����� �������� ��������� ������ ������������ � ����������� �������� ������ ��������. ������� ������ �������� ����� ���� �������� ���� ��� ���������������� �����������.\r\n\r\n<p>\r\n    <b>7. �����������</b><br>\r\n    � ������ ������������� �����������, ������� ����� ���������� ������������� ��������� ����������� ����� �����������. � ������, ���� ������� �� ������ � ����������, �� ���� �������� ������������ � ���� ��.\r\n\r\n<p><a href="/partner/register_user.html" style="font-size:17px">����������� � ����������� ���������</a>',360,30,'2.0');
