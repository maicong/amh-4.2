SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE `amh` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use `amh`;

CREATE TABLE `amh_backup_list` (
  `backup_id` int(10) NOT NULL AUTO_INCREMENT,
  `backup_file` varchar(100) NOT NULL,
  `backup_size` varchar(18) NOT NULL,
  `backup_password` int(1) NOT NULL,
  `backup_remote` int(1) NOT NULL,
  `backup_options` varchar(1) NOT NULL,
  `backup_comment` text NOT NULL,
  `backup_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`backup_id`),
  UNIQUE KEY `backup_file` (`backup_file`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_backup_remote` (
  `remote_id` int(10) NOT NULL AUTO_INCREMENT,
  `remote_type` varchar(10) NOT NULL,
  `remote_status` int(1) NOT NULL,
  `remote_ip` varchar(80) NOT NULL,
  `remote_path` varchar(100) NOT NULL,
  `remote_user` varchar(50) NOT NULL,
  `remote_pass_type` int(1) NOT NULL,
  `remote_password` text NOT NULL,
  `remote_comment` text NOT NULL,
  `remote_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`remote_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_config` (
  `config_id` int(10) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(30) NOT NULL,
  `config_value` varchar(100) NOT NULL,
  `config_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('HelpDoc','on');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('LoginErrorLimit','5');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('VerifyCode','on');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('AMHListen','8888');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('AMHDomain','Off');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('UpgradeSum','0');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('OpenCSRF','on');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('DataPrivate','Off');
INSERT INTO `amh_config` (`config_name`,`config_value`) VALUES ('OpenMenu','on');

CREATE TABLE `amh_crontab` (
  `crontab_id` int(10) NOT NULL AUTO_INCREMENT,
  `crontab_minute` varchar(100) NOT NULL,
  `crontab_hour` varchar(100) NOT NULL,
  `crontab_day` varchar(100) NOT NULL,
  `crontab_month` varchar(100) NOT NULL,
  `crontab_week` varchar(100) NOT NULL,
  `crontab_ssh` varchar(100) NOT NULL,
  `crontab_type` varchar(5) NOT NULL,
  `crontab_md5` varchar(50) NOT NULL,
  `crontab_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`crontab_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_ftp` (
  `ftp_id` int(10) NOT NULL AUTO_INCREMENT,
  `ftp_name` varchar(20) NOT NULL,
  `ftp_password` varchar(80) NOT NULL,
  `ftp_root` varchar(80) NOT NULL,
  `ftp_upload_bandwidth` varchar(20) NOT NULL,
  `ftp_download_bandwidth` varchar(20) NOT NULL,
  `ftp_upload_ratio` varchar(10) NOT NULL,
  `ftp_download_ratio` varchar(10) NOT NULL,
  `ftp_max_files` varchar(10) NOT NULL,
  `ftp_max_mbytes` varchar(20) NOT NULL,
  `ftp_max_concurrent` varchar(10) NOT NULL,
  `ftp_allow_time` varchar(10) NOT NULL,
  `ftp_type` varchar(5) NOT NULL,
  `ftp_directory_uname` varchar(30) NOT NULL,
  `ftp_uid_name` varchar(20) NOT NULL,
  `ftp_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ftp_id`),
  UNIQUE KEY `ftp_name` (`ftp_name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_host` (
  `host_id` int(10) NOT NULL AUTO_INCREMENT,
  `host_domain` varchar(50) NOT NULL,
  `host_server_name` varchar(300) NOT NULL,
  `host_root` varchar(100) NOT NULL,
  `host_index_name` varchar(100) NOT NULL,
  `host_rewrite` varchar(35) NOT NULL,
  `host_error_page` varchar(80) NOT NULL,
  `host_log` int(1) NOT NULL,
  `host_error_log` int(1) NOT NULL,
  `host_type` varchar(5) NOT NULL,
  `host_nginx` int(1) NOT NULL,
  `host_php` int(1) NOT NULL,
  `host_subdirectory` int(1) NOT NULL,
  `host_pagespeed` int(1) NOT NULL,
  `host_php_fpm` varchar(20) NOT NULL,
  `host_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`host_id`),
  UNIQUE KEY `host_domain` (`host_domain`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_user_id` int(10) NOT NULL,
  `log_text` text NOT NULL,
  `log_ip` varchar(15) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_login` (
  `login_id` int(10) NOT NULL AUTO_INCREMENT,
  `login_user_name` varchar(20) NOT NULL,
  `login_ip` varchar(15) NOT NULL,
  `login_success` int(1) NOT NULL,
  `login_error_tag` int(1) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `amh_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(15) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  `user_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


INSERT INTO `amh_user` (`user_name`, `user_password`) VALUES
('admin', md5(md5('AMHPass_amysql-amh')));
