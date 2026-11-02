<?php

namespace App\Containers\Tenancy\Clinic\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\Tenancy\Clinic\Actions\CreateClinicAction;
use App\Containers\Tenancy\Clinic\Actions\ListClinicsAction;
use App\Containers\Tenancy\Clinic\Actions\UpdateClinicStatusAction;
use App\Containers\Tenancy\Clinic\UI\API\Requests\CreateClinicRequest;
use App\Containers\Tenancy\Clinic\UI\API\Requests\ListClinicsRequest;
use App\Containers\Tenancy\Clinic\UI\API\Requests\UpdateClinicStatusRequest;
use App\Containers\Tenancy\Clinic\UI\API\Transformers\ClinicTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class ClinicController extends ApiController
{
    public function index(ListClinicsRequest $request, ListClinicsAction $action): JsonResponse
    {
        $perPage = (int) ($request->validated()['per_page'] ?? 15);
        $clinics = $action->run($perPage);

        return Response::create($clinics, ClinicTransformer::class)->ok();
    }

    public function store(CreateClinicRequest $request, CreateClinicAction $action): JsonResponse
    {
        $clinic = $action->run(
            $request->validated(),
            (int) $request->user()->getAuthIdentifier(),
        );

        return Response::create($clinic, ClinicTransformer::class)->created();
    }

    public function updateStatus(UpdateClinicStatusRequest $request, UpdateClinicStatusAction $action): JsonResponse
    {
        $data = $request->validated();
        $clinic = $action->run(
            (int) $request->clinic,
            (int) $request->user()->getAuthIdentifier(),
            $data['subscription_status'],
            $data['reason'] ?? null,
        );

        return Response::create($clinic, ClinicTransformer::class)->ok();
    }
}
