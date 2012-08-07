-- This databse is a structure of the table to hold the exchange rate data
-- The actual SQL that is used is already been embeded in the php file.
-- The will enable maintainance a more efficient.

-- Author: Festus Sunday OMOLE
-- Email: omolesunday@gmail.com

CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `currencies` varchar(3) NOT NULL,
  `rates` double NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currencies`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;