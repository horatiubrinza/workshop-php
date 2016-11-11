<?php

namespace ZWorkshop\Models;

class ProfileModel
{
    /** @var \PDO */
    private $dbConnection;

    /**
     * ProfileModel constructor.
     *
     * @param \PDO $dbConnection
     */
    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function get($username)
    {
        // get user details
        $sql = "SELECT * FROM `users` WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $username
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $gender
     * @param $programmingLanguages
     * @param $userDescription
     * @return int
     */
    public function save(
        $username,
        $firstName,
        $lastName,
        $email,
        $gender,
        $programmingLanguages,
        $userDescription
    ) {
        // define query
        $sql = "UPDATE `users`
                SET `FirstName`= :firstName,
                      `LastName` = :lastName,
                      `Email` = :email,
                      `Gender` = :gender,
                      `ProgramingLanguages` = :programmingLanguages,
                      `Description` =  :userDescription
                WHERE `username` = :username;";

        // define query params
        $params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':gender' => $gender,
            ':programmingLanguages' => $programmingLanguages,
            ':userDescription' => $userDescription,
            ':username' => $username,
        ];

        // bind params to query and execute query
        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        // check for updated rows
        return $query->rowCount();
    }
}
