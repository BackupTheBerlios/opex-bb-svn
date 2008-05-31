CREATE TABLE `modules` (
	id INTEGER NOT NULL AUTO_INCREMENT,
	name VARCHAR(50), -- module-name, name.php will be the file
	class VARCHAR(50)
);

-- Übersichtlichkeit, Jungs, Übersichtlichkeit!


CREATE TABLE `user` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR( 20 ) NOT NULL ,
	`password` VARCHAR( 20 ) NOT NULL ,
	`registered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	`status` INT NOT NULL,
	`active` bool not null default false,
	PRIMARY KEY ( `id` )
);


CREATE TABLE `topic` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL ,
	`titel` VARCHAR( 255 ) NOT NULL ,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	`status` INT NOT NULL ,
	PRIMARY KEY ( `id` )
);


CREATE TABLE `post` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL ,
	`text` LONGTEXT NOT NULL , -- BLOB = binary large object, sorry, mein fehler
	`topic_id` INT NOT NULL ,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( `id` )
);


CREATE TABLE `subboard` (
 	`id` INT NOT NULL AUTO_INCREMENT,
 	`name` VARCHAR ( 255 ) NOT NULL,
 	`description` TEXT NOT NULL,
 	`refboard` INT,
 	PRIMARY KEY ( `id` )
);


CREATE TABLE `uright` (
	id int not null auto_increment,
	user_id int not null,
	is_admin bool not null default false,
	is_mod bool not null default false,
	is_reg bool not null default false,
	is_guest bool not null default true,
	uright tinyint not null default 0,
	primary key(id)
);

CREATE TABLE `classes` (
	id INT NOT NULL auto_increment,
	name VARCHAR ( 50 ) NOT NULL,
	uright VARCHAR ( 50 ) NOT NULL
);