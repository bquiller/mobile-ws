<?php
namespace App\Security;

use App\Repository\AccessTokenRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        #[Autowire('%env(BEARER_TOKEN)%')] private string $token,
        #[Autowire('%env(DEFAULT_USERNAME)%')] private string $username) 
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        // e.g. query the "access token" database to search for this token
        if (null === $accessToken || $accessToken !== $this->token) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        // and return a UserBadge object containing the user identifier from the found token
        // (this is the same identifier used in Security configuration; it can be an email,
        // a UUUID, a username, a database ID, etc.)
        return new UserBadge($this->username);
    }
}