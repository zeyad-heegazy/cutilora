<?php

namespace App\Containers\Geo\City\Actions;

use App\Containers\Geo\City\Tasks\CityTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class CityAction extends ParentAction
{
    public function __construct(private readonly CityTask $task)
    {
    }

    public function index(array $filters): LengthAwarePaginator
    {
        return $this->task->index($filters);
    }

    public function create(array $data): Model
    {
        return $this->task->create($data);
    }

    public function find(int $id): Model
    {
        return $this->task->find($id);
    }

    public function update(int $id, array $data): Model
    {
        return $this->task->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->task->delete($id);
    }
}
