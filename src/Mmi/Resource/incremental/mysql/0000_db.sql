CREATE TABLE `mmi_changelog` (
  `filename` varchar(64) NOT NULL,
  `md5` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `mmi_session` (
  `id` varchar(64) NOT NULL,
  `data` mediumtext,
  `timestamp` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `mmi_cache` (
  `id` varchar(64) NOT NULL,
  `data` mediumtext,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;