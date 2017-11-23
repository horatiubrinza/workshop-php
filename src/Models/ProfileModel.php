<?php

namespace ZWorkshop\Models;

/**
 * The profile model.
 */
class ProfileModel
{
    /**
     * The DB connection.
     *
     * @var \PDO
     */
    private $dbConnection;

    /**
     * The profile model constructor.
     *
     * @param \PDO $dbConnection
     */
    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Gets the user with the given username.
     *
     * @param string $username
     *
     * @return array|null
     */
    public function get(string $username): ?array
    {
        $sql = 'SELECT * FROM `users` WHERE `username` = :username;';
        $params = [':username' => $username];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Updates the user with the given username.
     *
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $gender
     * @param string $programmingLanguages
     * @param string $userDescription
     *
     * @return bool True if the update was successful.
     */
    public function save(
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        string $gender,
        string $programmingLanguages,
        string $userDescription
    ): bool {
        $sql = 'UPDATE `users` '
                .'SET `FirstName`= :firstName,'
                    .'`LastName` = :lastName,'
                    .'`Email` = :email,'
                    .'`Gender` = :gender,'
                    .'`ProgramingLanguages` = :programmingLanguages,'
                    .'`Description` =  :userDescription '
                .'WHERE `username` = :username;';
        $params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':gender' => $gender,
            ':programmingLanguages' => $programmingLanguages,
            ':userDescription' => $userDescription,
            ':username' => $username,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return (bool) $query->rowCount();
    }
}
