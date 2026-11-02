<?php

namespace App\Containers\Geo\Admin\Actions;

use App\Containers\Geo\Admin\Tasks\GeoAdminTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class GeoAdminAction extends ParentAction
{
    public function __construct(
        private readonly GeoAdminTask $geoAdminTask,
    ) {
    }

    public function index(string $resource, array $filters): LengthAwarePaginator
    {
        return $this->geoAdminTask->index($resource, $filters);
    }

    public function create(string $resource, array $data): Model
    {
        return $this->geoAdminTask->create($resource, $data);
    }

    public function find(string $resource, int $entryId): Model
    {
        return $this->geoAdminTask->find($resource, $entryId);
    }

    public function update(string $resource, int $entryId, array $data): Model
    {
        return $this->geoAdminTask->update($resource, $entryId, $data);
    }

    public function delete(string $resource, int $entryId): void
    {
        $this->geoAdminTask->delete($resource, $entryId);
    }
}
