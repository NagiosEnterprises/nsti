
USE `snmptt`;

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
insert into filter_atom (comparison)
 (select eventnamequery from filters_1_4)
union
 (select eventidquery from filters_1_4)
union
 (select trapoidquery from filters_1_4)
union
 (select enterprisequery from filters_1_4)
union
 (select hostnamequery from filters_1_4)
union
 (select categoryquery from filters_1_4) 
union 
 (select severityquery from filters_1_4) 
union
 (select formatlinequery from filters_1_4);

insert into filter_atom (column_name)
 (select 'eventname' from filters_1_4 where (`eventname` != ''))
union
 (select 'eventid' from filters_1_4 where (`eventid` != ''))
union
 (select 'trapoid' from filters_1_4 where (`trapoid` != ''))
union
 (select 'enterprise' from filters_1_4 where (`enterprise` != ''))
union
 (select 'hostname' from filters_1_4 where (`hostname` != ''))
union
 (select 'category' from filters_1_4 where (`category` != '')) 
union 
 (select 'severity' from filters_1_4 where (`severity` != '')) 
union
 (select 'formatline' from filters_1_4 where (`formatline` != ''));

insert into filter_atom (val)
 (select eventname from filters_1_4)
union
 (select eventid from filters_1_4)
union
 (select trapoid from filters_1_4)
union
 (select enterprise from filters_1_4)
union
 (select hostname from filters_1_4)
union
 (select category from filters_1_4) 
union 
 (select severity from filters_1_4) 
union
 (select formatline from filters_1_4);


INSERT INTO `filter_atom` (`filter_id`)
SELECT 
  `id` as `filter_id`
FROM `filters_1_4`;
