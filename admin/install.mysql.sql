CREATE TABLE IF NOT EXISTS `#__globalflash_galleries` (
	`id`			int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name`			varchar(255) NOT NULL,
	`title`			varchar(255) NOT NULL,
	`description`	text NOT NULL,
	`type`			varchar(20) NOT NULL,
	`width`			int unsigned NOT NULL DEFAULT '0',
	`height`		int unsigned NOT NULL DEFAULT '0',
	`wmode`			varchar(20) NOT NULL,
	`bgcolor`		varchar(10) NOT NULL,
	`bgimage`		varchar(255) NOT NULL DEFAULT '',
	`created`		datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
	`created_by`	int unsigned NOT NULL DEFAULT '0',
	`modified`		datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
	`modified_by`	int unsigned NOT NULL DEFAULT '0',
	`published`		tinyint(1) unsigned NOT NULL DEFAULT '0',
	`order`			int NOT NULL DEFAULT '0',
	INDEX			(`order`, `type`, `created`, `created_by`, `modified`),
	INDEX			(`title`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__globalflash_settings` (
	`id`			int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gallery_id`	int unsigned NOT NULL DEFAULT '0',
	`gallery_type`	varchar(20) NOT NULL,
	`name`			varchar(255) NOT NULL,
	`value`			varchar(255) NOT NULL,
	INDEX			(`gallery_id`, `gallery_type`, `name`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__globalflash_albums` (
	`id`			int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`title`			varchar(255) NOT NULL,
	`description`	text NOT NULL,
	`created`		datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
	`created_by`	int unsigned NOT NULL  DEFAULT '0',
	`modified`		datetime NOT NULL DEFAULT '2010-01-01 00:00:00',
	`modified_by`	int unsigned NOT NULL DEFAULT '0',
	`order`			int NOT NULL DEFAULT '0',
	INDEX 			(`order`, `created`, `created_by`, `modified`),
	INDEX			(`title`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__globalflash_images` (
	`id`			int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`album_id`		int unsigned NOT NULL DEFAULT '0',
	`gallery_id`	int unsigned NOT NULL DEFAULT '0',
	`type`			varchar(50) NOT NULL,
	`path`			varchar(255) NOT NULL,
	`name`			varchar(255) NOT NULL,
	`title`			varchar(255) NOT NULL,
	`description`	text NOT NULL,
	`link`			varchar(255) NOT NULL,
	`target`		varchar(50) NOT NULL,
	`width`			int unsigned NOT NULL DEFAULT '0',
	`height`		int unsigned NOT NULL DEFAULT '0',
	`size`			int unsigned NOT NULL DEFAULT '0',
	`order`			int NOT NULL DEFAULT '0',
	INDEX			(`order`, `album_id`, `gallery_id`, `type`, `size`),
	INDEX			(`path`),
	INDEX			(`title`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__globalflash_options` (
	`id`			int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name`			varchar(255) NOT NULL,
	`value`			varchar(255) NOT NULL,
	INDEX			(`name`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
