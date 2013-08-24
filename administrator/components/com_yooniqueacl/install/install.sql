CREATE TABLE IF NOT EXISTS `#__yooniqueacl_config` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `value` varchar(255) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__yooniqueacl_addons` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `access` tinyint(3) NOT NULL,
  `iscore` tinyint(1) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  `version` varchar(255) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `author_url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_codes` (
  `id` int(255) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `times_allowed` varchar(11) NOT NULL,
  `hits` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_g2i` (
  `group_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`group_id`,`item_id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_groups` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_items` (
  `id` int(11) NOT NULL auto_increment,
  `site` enum('site','administrator') NOT NULL default 'site',
  `title` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `site_option` varchar(255) NOT NULL,
  `site_section` varchar(255) NOT NULL,
  `site_view` varchar(255) NOT NULL,
  `site_task` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` varchar(255) NOT NULL,
  `error_url_published` tinyint(1) NOT NULL default '0',
  `error_url` varchar(250) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  `option_exclude` tinyint(1) NOT NULL,
  `item_exclude` tinyint(1) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `content_category` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_u2g` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY  (`user_id`,`group_id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_v2i` (
  `var_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`var_id`,`item_id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__yooniqueacl_variables` (
  `id` int(11) NOT NULL auto_increment,
  `site` enum('site','administrator') NOT NULL default 'site',
  `site_option` varchar(255) NOT NULL,
  `variable` varchar(255) NOT NULL,
  `autoadd` tinyint(1) NOT NULL,
  `force_integer` tinyint(1) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;
