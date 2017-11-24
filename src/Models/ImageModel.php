<?php

namespace ZWorkshop\Models;

/**
 * The image model.
 */
class ImageModel
{
    /**
     * The DB connection.
     *
     * @var \PDO
     */
    private $dbConnection;

    /**
     * The image model constructor.
     *
     * @param \PDO $dbConnection
     */
    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Gets all images belonging to the given user.
     *
     * @param string $username
     *
     * @return array
     */
    public function getUserCollection(string $username): array
    {
        $sql = 'SELECT `images`.IdImage, `images`.FileName, `images`.ProcessingResult '
                .'FROM `users` '
                .'JOIN `images` USING(IdUser) '
                .'WHERE `username` = :username;';
        $params = [':username' => $username];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        //TODO: sort images by number of faces, ascending

        return $results;
    }

    /**
     * Saves a image for the user with the given id.
     *
     * @param int    $userId
     * @param string $fileName
     * @param string $emotions
     */
    public function save(int $userId, string $fileName): void
    {
        //TODO: e8 - save Emotion API results in db

        $sql = 'INSERT INTO `images` (`IdUser`, `FileName`) '
                .'VALUES (:idUser, :fileName)';
        $params = [
            ':idUser'           => $userId,
            ':fileName'         => $fileName,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);
    }

    /**
     * Gets the image with the given id.
     *
     * @param int $imageId
     *
     * @return array|null
     */
    public function get(int $imageId): ?array
    {
        $sql = 'SELECT * FROM `images` WHERE `IdImage` = :imageId;';
        $params = [':imageId' => $imageId];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    //TODO: e9 - delete image
}
