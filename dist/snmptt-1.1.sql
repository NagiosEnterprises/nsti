-- MySQL dump 10.9
--
-- Host: localhost    Database: snmptt
-- ------------------------------------------------------
-- Server version	4.1.10a
--
-- Table structure for table `snmptt`
--

DROP TABLE IF EXISTS `snmptt`;
CREATE TABLE `snmptt` (
  `id` mediumint(9) NOT NULL auto_increment,
  `eventname` varchar(50) default NULL,
  `eventid` varchar(50) default NULL,
  `trapoid` varchar(100) default NULL,
  `enterprise` varchar(100) default NULL,
  `community` varchar(20) default NULL,
  `hostname` varchar(100) default NULL,
  `agentip` varchar(16) default NULL,
  `category` varchar(20) default NULL,
  `severity` varchar(20) default NULL,
  `uptime` varchar(20) default NULL,
  `traptime` varchar(30) default NULL,
  `formatline` varchar(255) default NULL,
  `trapread` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

