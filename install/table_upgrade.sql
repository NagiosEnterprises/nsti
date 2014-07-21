
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
  id as id, 
  filtername as name
FROM `filters_1_4`;

--
-- filter_atom table import
--
insert into filter_atom (comparison, column_name, val, filter_id)
select eventnamequery,
       'eventname',
       eventname,
       id
from filters_1_4 where (`eventname` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select eventidquery,
       'eventid',
       eventid,
       id
from filters_1_4 where (`eventid` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select trapoidquery,
       'trapoid',
       trapoid,
       id
from filters_1_4 where (`trapoid` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select enterprisequery,
       'enterprise',
       enterprise,
       id
from filters_1_4 where (`enterprise` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select hostnamequery,
       'hostname',
       hostname,
       id
from filters_1_4 where (`hostname` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select categoryquery,
       'category',
       category,
       id
from filters_1_4 where (`category` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select severityquery,
       'severity',
       severity,
       id
from filters_1_4 where (`severity` != '');


insert into filter_atom (comparison, column_name, val, filter_id)
select formatlinequery,
       'formatline',
       formatline,
       id
from filters_1_4 where (`formatline` != '');