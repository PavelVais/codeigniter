<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

ERROR - 2012-12-07 13:01:26 --> Could not find the language line "Úvod"
ERROR - 2012-12-07 13:01:26 --> Could not find the language line "Kontakt"
ERROR - 2012-12-07 13:01:26 --> Could not find the language line "Tým"
ERROR - 2012-12-07 13:01:26 --> 404 Page Not Found --> administrace/images
ERROR - 2012-12-07 13:01:26 --> 404 Page Not Found --> images
ERROR - 2012-12-07 13:01:27 --> 404 Page Not Found --> images
ERROR - 2012-12-07 13:01:43 --> Could not find the language line "mmenu_administration_dashboard_tag"
ERROR - 2012-12-07 13:03:46 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 31
ERROR - 2012-12-07 13:03:58 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 31
ERROR - 2012-12-07 13:03:59 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 31
ERROR - 2012-12-07 13:04:16 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 31
ERROR - 2012-12-07 13:04:33 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\dbbackup.php 31
ERROR - 2012-12-07 13:05:36 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\dbbackup.php 31
ERROR - 2012-12-07 13:05:46 --> Severity: Notice  --> Undefined property: dbbackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\dbbackup.php 31
ERROR - 2012-12-07 13:06:19 --> Severity: Notice  --> Undefined property: dbbackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\dbbackup.php 31
ERROR - 2012-12-07 13:06:31 --> Severity: Notice  --> Undefined property: Index::$dbbackup C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\controllers\administrace\index.php 48
ERROR - 2012-12-07 13:06:43 --> Severity: Notice  --> Undefined property: dbbackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\dbbackup.php 31
ERROR - 2012-12-07 13:07:14 --> Severity: Notice  --> Undefined property: DbBackup::$load C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 31
ERROR - 2012-12-07 13:11:35 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for test C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:12:48 --> Severity: Notice  --> Undefined property: DbBackup::$dbutil C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 115
ERROR - 2012-12-07 13:13:15 --> Severity: Notice  --> Undefined variable: prefs C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 115
ERROR - 2012-12-07 13:13:15 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: wishes
#

DROP TABLE IF EXISTS wishes;

CREATE TABLE `wishes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `author_name` varchar(30) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO wishes (`id`, `author_id`, `author_name`, `title`, `message`, `url`, `created`, `modified`) VALUES (1, 1, NULL, 'test', 'ahoj bobku', NULL, '2012-11-23 00:25:04', '2012-11-23 00:25:04');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:13:15 --> Severity: Notice  --> Undefined variable: prefs C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 115
ERROR - 2012-12-07 13:13:15 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fcb_id` int(11) unsigned DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'registered',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) DEFAULT '0',
  `ban_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (1, 1060224904, 'Pavel Vais', '$2a$08$.COLn/FQ2oQ5T8YjQ5ZgOOyKRW1OyFgFP3Q9MHc6e.EI6zpeG68kC', 'vaispavel@gmail.com', 'administrator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-12-07 13:01:43', '2011-07-11 16:42:48', '2012-12-07 13:01:43');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (2, NULL, 'Mirek Pilek...', NULL, 'kacenka99@milasek.kom', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-11-18 22:37:25');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (3, NULL, 'Daww', '$2a$08$DqYeVzXxTg0GJau/Agc7Iu1rz86EUY0X374CilSxkEm.BY1ZMzPwe', 'daaw.hgk@seznael.cz', 'moderator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 22:31:44', '2012-11-18 00:00:07', '2012-11-19 13:53:21');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (4, NULL, 'Dummy Account', '$2a$08$yGrVdDkZRHeiMplsFQVPNOC3rC/sHjB5qPik8NiNWxvJ6Xi68sKk.', 'a@a.com', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 23:27:01', '2012-11-18 23:27:01', '2012-11-19 13:53:44');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (5, 1328123234, 'Marika Durdíková', NULL, '', 'registered', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-19 01:06:36', '2012-11-19 01:06:36', '2012-11-19 01:16:46');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:13:41 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: wishes
#

DROP TABLE IF EXISTS wishes;

CREATE TABLE `wishes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `author_name` varchar(30) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO wishes (`id`, `author_id`, `author_name`, `title`, `message`, `url`, `created`, `modified`) VALUES (1, 1, NULL, 'test', 'ahoj bobku', NULL, '2012-11-23 00:25:04', '2012-11-23 00:25:04');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:13:41 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fcb_id` int(11) unsigned DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'registered',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) DEFAULT '0',
  `ban_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (1, 1060224904, 'Pavel Vais', '$2a$08$.COLn/FQ2oQ5T8YjQ5ZgOOyKRW1OyFgFP3Q9MHc6e.EI6zpeG68kC', 'vaispavel@gmail.com', 'administrator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-12-07 13:01:43', '2011-07-11 16:42:48', '2012-12-07 13:01:43');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (2, NULL, 'Mirek Pilek...', NULL, 'kacenka99@milasek.kom', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-11-18 22:37:25');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (3, NULL, 'Daww', '$2a$08$DqYeVzXxTg0GJau/Agc7Iu1rz86EUY0X374CilSxkEm.BY1ZMzPwe', 'daaw.hgk@seznael.cz', 'moderator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 22:31:44', '2012-11-18 00:00:07', '2012-11-19 13:53:21');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (4, NULL, 'Dummy Account', '$2a$08$yGrVdDkZRHeiMplsFQVPNOC3rC/sHjB5qPik8NiNWxvJ6Xi68sKk.', 'a@a.com', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 23:27:01', '2012-11-18 23:27:01', '2012-11-19 13:53:44');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (5, 1328123234, 'Marika Durdíková', NULL, '', 'registered', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-19 01:06:36', '2012-11-19 01:06:36', '2012-11-19 01:16:46');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:13:53 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: wishes
#

DROP TABLE IF EXISTS wishes;

CREATE TABLE `wishes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `author_name` varchar(30) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO wishes (`id`, `author_id`, `author_name`, `title`, `message`, `url`, `created`, `modified`) VALUES (1, 1, NULL, 'test', 'ahoj bobku', NULL, '2012-11-23 00:25:04', '2012-11-23 00:25:04');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:13:53 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fcb_id` int(11) unsigned DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'registered',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) DEFAULT '0',
  `ban_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (1, 1060224904, 'Pavel Vais', '$2a$08$.COLn/FQ2oQ5T8YjQ5ZgOOyKRW1OyFgFP3Q9MHc6e.EI6zpeG68kC', 'vaispavel@gmail.com', 'administrator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-12-07 13:01:43', '2011-07-11 16:42:48', '2012-12-07 13:01:43');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (2, NULL, 'Mirek Pilek...', NULL, 'kacenka99@milasek.kom', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-11-18 22:37:25');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (3, NULL, 'Daww', '$2a$08$DqYeVzXxTg0GJau/Agc7Iu1rz86EUY0X374CilSxkEm.BY1ZMzPwe', 'daaw.hgk@seznael.cz', 'moderator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 22:31:44', '2012-11-18 00:00:07', '2012-11-19 13:53:21');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (4, NULL, 'Dummy Account', '$2a$08$yGrVdDkZRHeiMplsFQVPNOC3rC/sHjB5qPik8NiNWxvJ6Xi68sKk.', 'a@a.com', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 23:27:01', '2012-11-18 23:27:01', '2012-11-19 13:53:44');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (5, 1328123234, 'Marika Durdíková', NULL, '', 'registered', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-19 01:06:36', '2012-11-19 01:06:36', '2012-11-19 01:16:46');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:14:32 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: wishes
#

DROP TABLE IF EXISTS wishes;

CREATE TABLE `wishes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `author_name` varchar(30) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO wishes (`id`, `author_id`, `author_name`, `title`, `message`, `url`, `created`, `modified`) VALUES (1, 1, NULL, 'test', 'ahoj bobku', NULL, '2012-11-23 00:25:04', '2012-11-23 00:25:04');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:14:32 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for #
# TABLE STRUCTURE FOR: users
#

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fcb_id` int(11) unsigned DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `role` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'registered',
  `activated` tinyint(1) NOT NULL DEFAULT '1',
  `banned` tinyint(1) DEFAULT '0',
  `ban_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_password_requested` datetime DEFAULT NULL,
  `new_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new_email_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `last_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (1, 1060224904, 'Pavel Vais', '$2a$08$.COLn/FQ2oQ5T8YjQ5ZgOOyKRW1OyFgFP3Q9MHc6e.EI6zpeG68kC', 'vaispavel@gmail.com', 'administrator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-12-07 13:01:43', '2011-07-11 16:42:48', '2012-12-07 13:01:43');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (2, NULL, 'Mirek Pilek...', NULL, 'kacenka99@milasek.kom', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-11-18 22:37:25');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (3, NULL, 'Daww', '$2a$08$DqYeVzXxTg0GJau/Agc7Iu1rz86EUY0X374CilSxkEm.BY1ZMzPwe', 'daaw.hgk@seznael.cz', 'moderator', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 22:31:44', '2012-11-18 00:00:07', '2012-11-19 13:53:21');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (4, NULL, 'Dummy Account', '$2a$08$yGrVdDkZRHeiMplsFQVPNOC3rC/sHjB5qPik8NiNWxvJ6Xi68sKk.', 'a@a.com', 'registered', 1, 1, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-18 23:27:01', '2012-11-18 23:27:01', '2012-11-19 13:53:44');
INSERT INTO users (`id`, `fcb_id`, `username`, `password`, `email`, `role`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `last_login`, `created`, `modified`) VALUES (5, 1328123234, 'Marika Durdíková', NULL, '', 'registered', 1, 0, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', '2012-11-19 01:06:36', '2012-11-19 01:06:36', '2012-11-19 01:16:46');


 C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:16:38 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:16:38 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:17:09 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 13:17:09 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Could not find the language line "mmenu_administration_dashboard_tag"
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for ci_sessions.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for emails.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for login_attempts.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for subscribers.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_autologin.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_profiles.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:45:23 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for ci_sessions.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for emails.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for login_attempts.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for subscribers.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_autologin.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_profiles.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:46:19 --> Severity: Warning  --> file_get_contents(backup/2012-12-07-1445_dbbackup_test.zip.zip) [<a href='function.file-get-contents'>function.file-get-contents</a>]: failed to open stream: No such file or directory C:\Program Files (x86)\EasyPHP\www\myslimnatebe\application\libraries\DbBackup.php 101
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for ci_sessions.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for emails.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for login_attempts.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for subscribers.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_autologin.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_profiles.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:47:03 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for ci_sessions.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for emails.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for login_attempts.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for subscribers.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_autologin.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for user_profiles.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for users.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
ERROR - 2012-12-07 14:51:33 --> Severity: Warning  --> filemtime() [<a href='function.filemtime'>function.filemtime</a>]: stat failed for wishes.txt C:\Program Files (x86)\EasyPHP\www\myslimnatebe\system\libraries\Zip.php 91
