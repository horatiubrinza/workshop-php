USE `php_workshop`;

############################################
# Put sql table schema queries here        #
############################################

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

############################################
# Put insert,update queries here           #
############################################

# use http://www.sha1-online.com/ for password hashing
# password = test123#
#INSERT INTO `users` (`FirstName`, `LastName`, `Email`, `Gender`, `ProgramingLanguages`, `Description`, `Username`, `Password`) VALUES ('Kristo', 'Godari', 'kristo.godari@zitec.com', 'MALE', 'PHP|JAVA|JAVASCRIPT|C#', 'Software Engineer', 'kristo.godari', 'dbbe0b9e0ffef386cbf307107379782883c0c50b');

# password = test123#
#INSERT INTO `users` (`FirstName`, `LastName`, `Email`, `Gender`, `ProgramingLanguages`, `Description`, `Username`, `Password`) VALUES ('Horatiu', 'Brinza', 'horatiu.brinza@zitec.ro', 'MALE', 'PHP|JAVA|JAVASCRIPT|C#', 'Senior Software Engineer', 'horatiu.brinza', 'dbbe0b9e0ffef386cbf307107379782883c0c50b');

# password = test123#
INSERT INTO `users` (`FirstName`, `LastName`, `Email`, `Gender`, `ProgramingLanguages`, `Description`, `Username`, `Password`) VALUES ('Test', 'McDev', 'test@test.com', 'MALE', '', '', 'devuser', 'test123#');

