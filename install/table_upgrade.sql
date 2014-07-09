--
-- Table structure for table `filter_atom`
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `filter_atom` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `column_name` varchar(100) DEFAULT NULL,
  `comparison` varchar(100) DEFAULT NULL,
  `val` varchar(100) DEFAULT NULL,
  `filter_id` mediumint(9),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `filter`
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `filter` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL UNIQUE,
  `combiner` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Insert old filter table data into new filter tables
--
USE `snmptt`;

--
-- filter table import
--
INSERT INTO `filter` (`id`, `name`)
SELECT 
  `id` as `id`, 
  `filtername` as `name`
FROM `filters_1_4`;

--
-- filter_atom table import
--
SET @column_name_array = 'eventname, eventid, trapoid, enterprise, hostname, category, severity, formatline';
SET @column_query_array = 'eventnamequery, eventidquery, trapoidquery, enterprisequery, hostnamequery, categoryquery, severityquery, formatlinequery';
SET @column_val_array = 'eventname, eventid, trapoid, enterprise, hostname, category, severity, formatline';

CREATE PROCEDURE atomwhile()
BEGIN

  WHILE(LOCATE(',', @column_name_array) > 0) DO

  END WHILE
  
END



INSERT INTO `filter_atom` (`filter_id`)
SELECT
  `id` AS `filter_id`
FROM `filters_1_4`;



INSERT INTO `filter_atom` (`comparison`)
SELECT
  
FROM `filters_1_4`;



INSERT INTO `filter_atom` (`val`)
SELECT
  
FROM `filters_1_4` WHERE * != '' OR NULL;

