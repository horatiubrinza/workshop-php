<?php

namespace ZWorkshop\Services;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * The user provider.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var \PDO
     */
    private $conn;

    /**
     * The user provider constructor.
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $query = $this->conn->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute([':username' => strtolower($username)]);

        if (!$user = $query->fetch(\PDO::FETCH_ASSOC)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new User($user['Username'], $user['Password'], ['ROLE_USER'], true, true, true, true);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
