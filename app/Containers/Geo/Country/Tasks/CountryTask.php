<?php

namespace App\Containers\Geo\Country\Tasks;

use App\Containers\Geo\Country\Models\Country;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

final class CountryTask extends ParentTask
{
    private function relations(): array
    {
        return [];
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Country::query()->with($this->relations())->orderBy('name');

        return $query->paginate(min(max((int) ($filters['per_page'] ?? 15), 1), 100));
    }

    public function create(array $data): Country
    {
        $data['code'] = strtoupper($data['code']);
        return Country::query()->create($data)->load($this->relations());
    }

    public function find(int $id): Country
    {
        return Country::query()->with($this->relations())->findOrFail($id);
    }

    public function update(int $id, array $data): Country
    {
        $entry = $this->find($id);
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

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
