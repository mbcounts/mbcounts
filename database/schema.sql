CREATE TABLE `councils` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meritbadges.name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `counselors` (
  `ID` int(11) NOT NULL,
  `Council` int(11) DEFAULT NULL,
  `District` int(11) DEFAULT NULL,
  `Prefix` varchar(15) DEFAULT NULL,
  `FirstName` varchar(30) DEFAULT NULL,
  `MiddleName` varchar(30) DEFAULT NULL,
  `LastName` varchar(30) DEFAULT NULL,
  `Suffix` varchar(10) DEFAULT NULL,
  `Address1` varchar(50) DEFAULT NULL,
  `Address2` varchar(50) DEFAULT NULL,
  `Address3` varchar(50) DEFAULT NULL,
  `Address4` varchar(50) DEFAULT NULL,
  `Address5` varchar(50) DEFAULT NULL,
  `City` varchar(25) DEFAULT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZIPCode` varchar(10) DEFAULT NULL,
  `PhoneType` varchar(1) DEFAULT NULL,
  `PhoneNo` varchar(20) DEFAULT NULL,
  `PhoneExt` varchar(10) DEFAULT NULL,
  `PhoneType1` varchar(1) DEFAULT NULL,
  `PhoneNo1` varchar(50) DEFAULT NULL,
  `PhoneExt1` varchar(10) DEFAULT NULL,
  `EffectiveDate` datetime DEFAULT NULL,
  `ExpireDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `geodatas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `counselors_id` int(11) DEFAULT NULL,
  `lat` decimal(9,6) NOT NULL,
  `lon` decimal(9,6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_idx_counselors_id` (`counselors_id`),
  KEY `geodatas_lat` (`lat`),
  KEY `geodatas_lon` (`lon`)
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=latin1;

CREATE TABLE `meritbadgecounselors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `counselors_id` int(11) NOT NULL,
  `meritbadges_id` int(11) NOT NULL,
  `troop_only` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique merit badge counselor combo` (`counselors_id`,`meritbadges_id`),
  KEY `counselors` (`counselors_id`),
  KEY `meritbadges` (`meritbadges_id`),
  KEY `troop_only` (`troop_only`)
) ENGINE=InnoDB AUTO_INCREMENT=7114 DEFAULT CHARSET=latin1;

CREATE TABLE `meritbadges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meritbadges.name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=latin1;

CREATE TABLE `raw_counselors` (
  `Council Name` varchar(50) DEFAULT NULL,
  `District Name` varchar(50) DEFAULT NULL,
  `Badge` varchar(50) DEFAULT NULL,
  `Troop Only` varchar(50) DEFAULT NULL,
  `Prefix` varchar(50) DEFAULT NULL,
  `First Name` varchar(50) DEFAULT NULL,
  `Middle Name` varchar(50) DEFAULT NULL,
  `Last Name` varchar(50) DEFAULT NULL,
  `Suffix` varchar(50) DEFAULT NULL,
  `Address 1` varchar(50) DEFAULT NULL,
  `Address 2` varchar(50) DEFAULT NULL,
  `Address 3` varchar(50) DEFAULT NULL,
  `Address 4` varchar(50) DEFAULT NULL,
  `Address 5` varchar(50) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `ZIP Code` varchar(50) DEFAULT NULL,
  `Phone Type` varchar(50) DEFAULT NULL,
  `Phone No` varchar(50) DEFAULT NULL,
  `Phone Ext` varchar(50) DEFAULT NULL,
  `Phone Type1` varchar(50) DEFAULT NULL,
  `Phone No1` varchar(50) DEFAULT NULL,
  `Phone Ext1` varchar(50) DEFAULT NULL,
  `Effective Date` varchar(50) DEFAULT NULL,
  `Expire Date` varchar(50) DEFAULT NULL,
  `Person ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwcounselorslatlon` AS select `v`.`counselors_id` AS `counselors_id`,`v`.`meritbadges_id` AS `meritbadges_id`,`v`.`FirstName` AS `FirstName`,`v`.`MiddleName` AS `MiddleName`,`v`.`LastName` AS `LastName`,`v`.`Address1` AS `Address1`,`v`.`Address2` AS `Address2`,`v`.`City` AS `City`,`v`.`State` AS `State`,`v`.`Zip` AS `Zip`,`v`.`Phone` AS `Phone`,`g`.`lat` AS `lat`,`g`.`lon` AS `lon` from (`vwmeritbadgecounselors` `v` join `geodatas` `g` on((`v`.`counselors_id` = `g`.`counselors_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwmeritbadgecounselors` AS select `mbc`.`counselors_id` AS `counselors_id`,`mbc`.`meritbadges_id` AS `meritbadges_id`,`c`.`FirstName` AS `FirstName`,left(`c`.`MiddleName`,1) AS `MiddleName`,`c`.`LastName` AS `LastName`,`c`.`Address1` AS `Address1`,`c`.`Address2` AS `Address2`,`c`.`City` AS `City`,`c`.`State` AS `State`,`c`.`ZIPCode` AS `Zip`,`c`.`PhoneNo` AS `Phone`,`g`.`lat` AS `Lat`,`g`.`lon` AS `Lon` from ((`counselors` `c` join `meritbadgecounselors` `mbc` on((`c`.`ID` = `mbc`.`counselors_id`))) left join `geodatas` `g` on((`g`.`counselors_id` = `c`.`ID`))) where ((now() <= `c`.`ExpireDate`) and (`mbc`.`troop_only` = 'N'));

