<?php

namespace App\Containers\Geo\Admin\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\Geo\Admin\Actions\GeoAdminAction;
use App\Containers\Geo\Admin\UI\API\Requests\GeoAdminRequest;
use App\Containers\Geo\Admin\UI\API\Transformers\GeoEntryTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class GeoAdminController extends ApiController
{
    public function index(GeoAdminRequest $request, GeoAdminAction $action): JsonResponse
    {
        $entries = $action->index($request->route('resource'), $request->validated());

        return Response::create($entries, GeoEntryTransformer::class)->ok();
    }

    public function store(GeoAdminRequest $request, GeoAdminAction $action): JsonResponse
    {
        $entry = $action->create($request->route('resource'), $request->validated());

        return Response::create($entry, GeoEntryTransformer::class)->created();
    }

    public function show(GeoAdminRequest $request, GeoAdminAction $action): JsonResponse
    {
        $entry = $action->find($request->route('resource'), (int) $request->route('entry'));

        return Response::create($entry, GeoEntryTransformer::class)->ok();
    }

    public function update(GeoAdminRequest $request, GeoAdminAction $action): JsonResponse
    {
        $entry = $action->update(
            $request->route('resource'),
            (int) $request->route('entry'),
            $request->validated(),
        );

        return Response::create($entry, GeoEntryTransformer::class)->ok();
    }

    public function destroy(GeoAdminRequest $request, GeoAdminAction $action): JsonResponse
    {
        $action->delete($request->route('resource'), (int) $request->route('entry'));

        return Response::noContent();
    }
}
