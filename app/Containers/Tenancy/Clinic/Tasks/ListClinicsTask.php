<?php

namespace App\Containers\Tenancy\Clinic\Tasks;

use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListClinicsTask extends ParentTask
{
    public function run(int $perPage = 15): LengthAwarePaginator
    {
        return Clinic::query()
            ->with(['country', 'governorate', 'city', 'district'])
            ->orderBy('id')
            ->paginate(min(max($perPage, 1), 100));
    }
}
