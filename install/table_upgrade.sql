
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
SET @column_name_array = 'eventname, eventid, trapoid, enterprise, hostname, category, severity, formatline';
SET @column_query_array = 'eventnamequery, eventidquery, trapoidquery, enterprisequery, hostnamequery, categoryquery, severityquery, formatlinequery';
SET @column_val_array = 'eventname, eventid, trapoid, enterprise, hostname, category, severity, formatline';

-- DELIMITER //
-- 
-- CREATE FUNCTION atomwhile()
-- BEGIN
--   DECLARE V;
--   WHILE(LOCATE(',', @column_val_array) > 0) DO

--     IF (LOCATE(',', @column_val_array) == '') THEN

--     END IF;

--   END WHILE;
-- END//
insert into filter_atom ('comparison')
select concat('eventnamequery',),
       ('eventidquery'), 
       ('trapoidquery'), 
       ('enterprisequery'), 
       ('hostnamequery'), 
       ('categoryquery'), 
       ('severityquery'), 
       ('formatlinequery')
from filters
where '%query' not in (select column_name from filter_atom);

select eventname,
       eventid,
       trapoid,
       enterprise,
       hostname,
       category,
       severity,
       formatline
from filters 
where trapoidquery not in (select column_name from filter_atom);


# Old insert into method
-- INSERT INTO `filter_atom` (`filter_id`, `comparison`, `val`)
-- SELECT
--   `id` AS `filter_id`
-- FROM `filters_1_4` WHERE * != '' OR NULL;


# new insert into method
-- insert into newTable
-- select oldNames.ItemID,
--        oldNames.Name,
--        oldStreets.data,
--        oldCities.data
-- from   oldNames
--     inner join oldData as oldStreets on oldNames.ItemID = oldStreets.ItemID
--     inner join oldData as oldCities on oldNames.ItemID = oldCities.ItemID
--     inner join oldFields as streetsFields 
--         on oldStreets.FieldID = streetsFields.FieldID
--         and streetsFields.Name = 'Street'
--     inner join oldFields as citiesFields 
--         on oldCities.FieldID = citiesFields.Field
--         and citiesFields.Name = 'City'
