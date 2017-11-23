<?php

namespace ZWorkshop\Services;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * The application password encoder.
 */
class Sha1PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        return sha1($raw);
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === sha1($raw);
    }
}
