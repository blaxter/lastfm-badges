CREATE TABLE `users` (
       `username` varchar(100) NOT NULL,
       `statsstart` bigint(11) NOT NULL,
       `playcount` int(10) unsigned NOT NULL,
       `lastupdate` bigint(11) unsigned NOT NULL,
       PRIMARY KEY  (`username`)
) ;

CREATE TABLE `badges` (
       `username` varchar(100) NOT NULL,
       `type` varchar(100) NOT NULL,
       `style` varchar(100) NOT NULL,
       `color` varchar(100) NOT NULL,
       `lastupdate` bigint(11) default NULL,
       `hits` bigint(20) unsigned NOT NULL,
       `lasthit` bigint(11) unsigned default NULL,
       `png` longblob,
       PRIMARY KEY  (`username`,`type`)
);

