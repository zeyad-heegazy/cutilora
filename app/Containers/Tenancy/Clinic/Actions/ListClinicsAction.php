<?php

namespace App\Containers\Tenancy\Clinic\Actions;

use App\Containers\Tenancy\Clinic\Tasks\ListClinicsTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListClinicsAction extends ParentAction
{
    public function __construct(
        private readonly ListClinicsTask $listClinicsTask,
    ) {
    }

    public function run(int $perPage = 15): LengthAwarePaginator
    {
        return $this->listClinicsTask->run($perPage);
    }
}
