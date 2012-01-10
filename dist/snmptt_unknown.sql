-- phpMyAdmin SQL Dump
-- version 2.8.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 26, 2006 at 09:09 AM
-- Server version: 4.1.18
-- PHP Version: 4.4.2
-- 
-- Database: `snmptt`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `snmptt_unknown`
-- 

DROP TABLE IF EXISTS `snmptt_unknown`;
CREATE TABLE IF NOT EXISTS `snmptt_unknown` (
  `id` mediumint(9) NOT NULL auto_increment,
  `trapoid` varchar(100) default NULL,
  `enterprise` varchar(100) default NULL,
  `community` varchar(20) default NULL,
  `hostname` varchar(100) default NULL,
  `agentip` varchar(16) default NULL,
  `uptime` varchar(20) default NULL,
  `traptime` varchar(30) default NULL,
  `formatline` varchar(255) default NULL,
  `trapread` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

