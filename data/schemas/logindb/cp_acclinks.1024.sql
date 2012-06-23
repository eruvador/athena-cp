CREATE TABLE `cp_acclinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cluster_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `confirm_code` varchar(32) NOT NULL DEFAULT '',
  `confirm_expire` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
