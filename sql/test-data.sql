# put insert,update queries here
USE `php_workshop`;

INSERT INTO `users` (`FirstName`, `LastName`, `Email`, `Sex`, `ProgramingLanguages`, `Description`, `Username`, `Password`)
VALUES ('Kristo', 'Godari', 'kristo.godari@gmail.com', 'MALE', 'PHP|JAVA|JAVASCRIPT|C#', 'Software Engineer', 'kristo.godari', 'dbbe0b9e0ffef386cbf307107379782883c0c50b');

INSERT INTO `images` (`IdUser`, `FilePath`, `ProcessingResut`)
VALUES ( 1, 'D:\programs\wamp64\www\workshop-php-a-zitec\upload\demo-emotions.jpg', 'json');

