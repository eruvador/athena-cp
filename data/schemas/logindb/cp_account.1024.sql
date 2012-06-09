CREATE TABLE `cp_account` (
  `cluster_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(23) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(39) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  `confirm_code` varchar(32) NOT NULL DEFAULT '',
  `confirm_expire` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `del_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reg_ip` varchar(100) NOT NULL DEFAULT '',
  `last_ip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`cluster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;