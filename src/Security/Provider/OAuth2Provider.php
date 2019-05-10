<?php

namespace App\Security\Provider;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Security\Authentication\Token\OAuth2Token;

final class OAuth2Provider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var ResourceServer
     */
    private $resourceServer;

    public function __construct(UserProviderInterface $userProvider, ResourceServer $resourceServer)
    {
        $this->userProvider = $userProvider;
        $this->resourceServer = $resourceServer;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            throw new RuntimeException(
                sprintf(
                    'This authentication provider can only handle tokes of type \'%s\'.',
                    OAuth2Token::class
                )
            );
        }

        try {
            $request = $this->resourceServer->validateAuthenticatedRequest(
                $token->getAttribute('server_request')
            );
        } catch (OAuthServerException $e) {
            throw new AuthenticationException('The resource server rejected the request.', 0, $e);
        }

        $user = $this->getAuthenticatedUser(
            $request
        );

        $token = new OAuth2Token($request, $user);
        $token->setAuthenticated(true);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuth2Token;
    }

    private function getAuthenticatedUser($request): ?UserInterface
    {
        $userIdentifier = $request->getAttribute('oauth_user_id');
        //check is
        if ('' === $userIdentifier) {
            /*
             * If the identifier is an empty string, that means that the
             * access token isn't bound to a user defined in the system.
             * Try to load user by clientid
             */

            $userIdentifier = $request->getAttribute('oauth_client_id');

        }


        return $this->userProvider->loadUserByUsername($userIdentifier);
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
