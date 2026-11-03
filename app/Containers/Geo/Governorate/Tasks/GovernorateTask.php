<?php

namespace App\Containers\Geo\Governorate\Tasks;

use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;

final class GovernorateTask extends ParentTask
{
    private function relations(): array
    {
        return ['country'];
    }

    public function index(array $filters): LengthAwarePaginator
    {
        $query = Governorate::query()->with($this->relations())->orderBy('name');
        if (isset($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        return $query->paginate(min(max((int) ($filters['per_page'] ?? 15), 1), 100));
    }

    public function create(array $data): Governorate
    {
        return Governorate::query()->create($data)->load($this->relations());
    }

    public function find(int $id): Governorate
    {
        return Governorate::query()->with($this->relations())->findOrFail($id);
    }

    public function update(int $id, array $data): Governorate
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
