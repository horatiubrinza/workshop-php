<?php

namespace ZWorkshop\Models;

class ImageModel
{
    /** @var \PDO */
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getUserImages($username)
    {
        $sql = "SELECT `images`.IdImage, `images`.FilePath, `images`.ProcessingResut FROM `users`
                JOIN `images` USING(IdUser)
                WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function saveImage($userId, $filePath, $emotions)
    {
        $sql = "INSERT INTO `images` (`IdUser`, `FilePath`, `ProcessingResut`)
                VALUES (:idUser, :filePath, :processingResult)";

        $params = [
            ':idUser'           => $userId,
            ':filePath'         => $filePath,
            ':processingResult' => $emotions,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->rowCount();
    }

    public function deleteImage($imageId)
    {
        $sql = "DELETE FROM `images` WHERE `IdImage` = :imageId;";
        $params = [
            ':imageId' => $imageId,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->rowCount();
    }
}
