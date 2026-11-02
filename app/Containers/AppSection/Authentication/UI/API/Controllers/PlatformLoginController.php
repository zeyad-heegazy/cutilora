<?php

namespace App\Containers\AppSection\Authentication\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\Authentication\Actions\PlatformLoginAction;
use App\Containers\AppSection\Authentication\UI\API\Requests\PlatformLoginRequest;
use App\Containers\AppSection\Authentication\UI\API\Transformers\PasswordTokenTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class PlatformLoginController extends ApiController
{
    public function __invoke(PlatformLoginRequest $request, PlatformLoginAction $action): JsonResponse
    {
        $credentials = $request->validated();
        $token = $action->run($credentials['email'], $credentials['password']);

        return Response::create($token, PasswordTokenTransformer::class)->ok()
            ->withCookie($token->refreshToken->asCookie());
    }
}
