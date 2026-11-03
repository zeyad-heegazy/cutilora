<?php

namespace App\Containers\Geo\City\Tasks;

use App\Containers\Geo\City\Models\City;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

final class CityTask extends ParentTask
{
    private function relations(): array
    {
        return ['governorate'];
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = City::query()->with($this->relations())->orderBy('name');
        if (isset($filters['governorate_id'])) {
            $query->where('governorate_id', $filters['governorate_id']);
        }

        return $query->paginate(min(max((int) ($filters['per_page'] ?? 15), 1), 100));
    }

    public function create(array $data): City
    {
        return City::query()->create($data)->load($this->relations());
    }

    public function find(int $id): City
    {
        return City::query()->with($this->relations())->findOrFail($id);
    }

    public function update(int $id, array $data): City
    {
        $entry = $this->find($id);

        $entry->fill($data);
        $entry->save();

        return $entry->load($this->relations());
    }

    public function delete(int $id): void
    {
        try {
            $this->find($id)->delete();
        } catch (QueryException $exception) {
            $sqlState = $exception->errorInfo[0] ?? (string) $exception->getCode();

            if ('23000' !== $sqlState) {
                throw $exception;
            }

            abort(409, 'This geo entry is assigned to a clinic or has dependent entries and cannot be deleted.');
        }
    }
}
