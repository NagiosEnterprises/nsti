-- MySQL dump 10.13  Distrib 5.1.69, for redhat-linux-gnu (i386)
--
-- Host: localhost    Database: snmptt
-- ------------------------------------------------------
-- Server version       5.1.69

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0                          */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `snmptt_archive`
--

DROP TABLE IF EXISTS `snmptt_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snmptt_archive` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `snmptt_id` mediumint(9) NOT NULL,
  `eventname` varchar(50) DEFAULT NULL,
  `eventid` varchar(50) DEFAULT NULL,
  `trapoid` varchar(100) DEFAULT NULL,
  `enterprise` varchar(100) DEFAULT NULL,
  `community` varchar(20) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `agentip` varchar(16) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `uptime` varchar(20) DEFAULT NULL,
  `traptime` varchar(30) DEFAULT NULL,
  `formatline` varchar(255) DEFAULT NULL,
  `trapread` int(11) DEFAULT '0',
  `timewritten` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `snmptt_unknown`
--

DROP TABLE IF EXISTS `snmptt_unknown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snmptt_unknown` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `trapoid` varchar(100) DEFAULT NULL,
  `enterprise` varchar(100) DEFAULT NULL,
  `community` varchar(20) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `agentip` varchar(16) DEFAULT NULL,
  `uptime` varchar(20) DEFAULT NULL,
  `traptime` varchar(30) DEFAULT NULL,
  `formatline` varchar(255) DEFAULT NULL,
  `trapread` int(11) DEFAULT '0',
  `timewritten` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


--
-- Table structure for table `filter_atom`
--

DROP TABLE IF EXISTS `filter_atom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filter_atom` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `column_name` varchar(100) DEFAULT NULL,
  `comparison` varchar(100) DEFAULT NULL,
  `val` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `filter`
--

DROP TABLE IF EXISTS `filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filter` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `filter_linker`
--

DROP TABLE IF EXISTS `filter_linker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filter_linker` (
  `aid` mediumint(9) NOT NULL,
  `fid` mediumint(9) NOT NULL,
  PRIMARY KEY (`aid`, `fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
-- Dump completed on 2013-10-28  1:36:32
