<?php

namespace App\Containers\Geo\Admin\Tasks;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

final class GeoAdminTask extends ParentTask
{
    public function index(string $resource, array $filters): LengthAwarePaginator
    {
        $model = $this->modelFor($resource);
        $query = $model::query()->with($this->parentRelationFor($resource))->orderBy('name');
        $parentColumn = $this->parentColumnFor($resource);

        if (!is_null($parentColumn) && isset($filters[$parentColumn])) {
            $query->where($parentColumn, $filters[$parentColumn]);
        }

        return $query->paginate(min(max((int) ($filters['per_page'] ?? 15), 1), 100));
    }

    public function create(string $resource, array $data): Model
    {
        $model = $this->modelFor($resource);

        if ('countries' === $resource) {
            $data['code'] = strtoupper($data['code']);
        }

        return $model::query()->create($data)->load($this->parentRelationFor($resource));
    }

    public function find(string $resource, int $entryId): Model
    {
        $model = $this->modelFor($resource);

        return $model::query()
            ->with($this->parentRelationFor($resource))
            ->findOrFail($entryId);
    }

    public function update(string $resource, int $entryId, array $data): Model
    {
        $entry = $this->find($resource, $entryId);

        if ('countries' === $resource && isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $entry->fill($data);
        $entry->save();

        return $entry->load($this->parentRelationFor($resource));
    }

    public function delete(string $resource, int $entryId): void
    {
        $entry = $this->find($resource, $entryId);

        try {
            $entry->delete();
        } catch (QueryException $exception) {
            $sqlState = $exception->errorInfo[0] ?? (string) $exception->getCode();

            if ('23000' !== $sqlState) {
                throw $exception;
            }

            abort(409, 'This geo entry is assigned to a clinic and cannot be deleted.');
        }
    }

    public function parentColumn(string $resource): string|null
    {
        return $this->parentColumnFor($resource);
    }

    private function modelFor(string $resource): string
    {
        return match ($resource) {
            'countries' => Country::class,
            'governorates' => Governorate::class,
            'cities' => City::class,
            'districts' => District::class,
            default => abort(404),
        };
    }

    private function parentColumnFor(string $resource): string|null
    {
        return match ($resource) {
            'countries' => null,
            'governorates' => 'country_id',
            'cities' => 'governorate_id',
            'districts' => 'city_id',
            default => abort(404),
        };
    }

    private function parentRelationFor(string $resource): array
    {
        return match ($resource) {
            'countries' => [],
            'governorates' => ['country'],
            'cities' => ['governorate'],
            'districts' => ['city'],
            default => abort(404),
        };
    }
}
