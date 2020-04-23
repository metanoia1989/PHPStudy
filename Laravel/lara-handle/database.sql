create table students 
(
    id int(10) unsigned auto_increment primary key,
    name varchar(30) NOT NULL,
    age int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;