# put sql table schema queries here
CREATE DATABASE IF NOT EXISTS `php_workshop`;

USE `php_workshop`;

CREATE TABLE IF NOT EXISTS `users` (
	`IdUser` INT(11) NOT NULL AUTO_INCREMENT,
	`FirstName` VARCHAR(100) NOT NULL DEFAULT '',
	`LastName` VARCHAR(100) NOT NULL DEFAULT '',
	`Email` VARCHAR(100) NOT NULL DEFAULT '',
	`Gender` ENUM('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
	`ProgramingLanguages` VARCHAR(500) NOT NULL DEFAULT '' COMMENT 'Programming languages separated by | ex: PHP|JAVA|PYTHON',
	`Description` TEXT NULL,
	`Username` VARCHAR(100) NOT NULL DEFAULT '',
	`Password` VARCHAR(40) NOT NULL DEFAULT '' COMMENT 'SHA-1 algorithm.',
	PRIMARY KEY (`IdUser`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS `images` (
	`IdImage` INT(11) NOT NULL AUTO_INCREMENT,
	`IdUser` INT(11) NOT NULL,
	`FileName` VARCHAR(50) NOT NULL DEFAULT '',
	`ProcessingResult` TEXT NULL,
	PRIMARY KEY (`IdImage`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0;

