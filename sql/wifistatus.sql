CREATE DATABASE  IF NOT EXISTS `wifistatus`;
USE `wifistatus`;


DROP TABLE IF EXISTS `wifidata`;

CREATE TABLE `wifidata` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `label` varchar(25) NOT NULL,
  `pco` decimal(10,0) NOT NULL,
  `slack` decimal(10,0) NOT NULL,
  `google` decimal(10,0) NOT NULL,
  `coreswitch` decimal(10,0) NOT NULL,
  `northland` decimal(10,0) NOT NULL,
  `clientprobe` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=216957 DEFAULT CHARSET=latin1;
