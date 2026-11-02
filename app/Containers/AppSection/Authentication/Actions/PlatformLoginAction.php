<?php

namespace App\Containers\AppSection\Authentication\Actions;

use App\Containers\AppSection\Authentication\Data\DTOs\PasswordToken;
use App\Containers\AppSection\Authentication\Data\Factories\PasswordTokenFactory;
use App\Containers\AppSection\Authentication\Tasks\FindPlatformAdminByEmailTask;
use App\Containers\AppSection\Authentication\Values\Clients\WebClient;
use App\Containers\AppSection\Authentication\Values\RequestProxies\PasswordGrant\AccessTokenProxy;
use App\Containers\AppSection\Authentication\Values\UserCredential;
use App\Ship\Parents\Actions\Action as ParentAction;
use League\OAuth2\Server\Exception\OAuthServerException;

final class PlatformLoginAction extends ParentAction
{
    public function __construct(
        private readonly FindPlatformAdminByEmailTask $findPlatformAdminByEmailTask,
        private readonly PasswordTokenFactory $factory,
    ) {
    }

    public function run(string $email, string $password): PasswordToken
    {
        if (is_null($this->findPlatformAdminByEmailTask->run($email))) {
            abort(401);
        }

        try {
            return $this->factory->make(
                AccessTokenProxy::create(
                    UserCredential::create($email, $password),
                    WebClient::create(),
                ),
            );
        } catch (OAuthServerException $exception) {
            if ('invalid_grant' !== $exception->getErrorType()) {
                throw $exception;
            }

            abort(401);
        }
    }
}
