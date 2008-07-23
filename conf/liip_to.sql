-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 23, 2008 at 02:31 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.6-dev
-- 
-- Database: `liip_to`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ids_lower`
-- 

CREATE TABLE `ids_lower` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `ids_mixed`
-- 

CREATE TABLE `ids_mixed` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `urls`
-- 

CREATE TABLE `urls` (
  `code` varchar(30) NOT NULL,
  `url` varchar(2000) NOT NULL,
  `md5` char(32) NOT NULL,
  `changed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`code`),
  UNIQUE KEY `md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
