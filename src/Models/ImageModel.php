<?php

namespace ZWorkshop\Models;

class ImageModel
{
    /** @var \PDO */
    private $dbConnection;

    /***
     * ImageModel constructor.
     *
     * @param \PDO $dbConnection
     */
    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param $username
     * @return array
     */
    public function getUserCollection($username)
    {
        $sql = "SELECT `images`.IdImage, `images`.FileName, `images`.ProcessingResult FROM `users`
                JOIN `images` USING(IdUser)
                WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save($userId, $fileName, $emotions)
    {
        $sql = "INSERT INTO `images` (`IdUser`, `FileName`, `ProcessingResult`)
                VALUES (:idUser, :fileName, :processingResult)";

        $params = [
            ':idUser'           => $userId,
            ':fileName'         => $fileName,
            ':processingResult' => $emotions,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->rowCount();
    }

    /**
     * @param $imageId
     * @return mixed
     */
    public function get($imageId)
    {
        $sql = "SELECT * FROM `images`
                WHERE `IdImage` = :imageId;";
        $params = [
            ':imageId' => $imageId,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $imageId
     * @return int
     */
    public function delete($imageId)
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
