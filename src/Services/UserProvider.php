<?php

namespace ZWorkshop\Services;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /** @var \PDO */
    private $conn;

    /**
     * UserProvider constructor.
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Loads an user by username
     *
     * @param string $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $query = $this->conn->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute([
            ':username' => strtolower($username)
        ]);

        if (!$user = $query->fetch(\PDO::FETCH_ASSOC)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new User($user['Username'], $user['Password'], ['ROLE_USER'], true, true, true, true);
    }

    /**
     * @param UserInterface $user
     * @return User|UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
