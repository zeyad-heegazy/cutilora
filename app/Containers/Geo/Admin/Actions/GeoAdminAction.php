<?php

namespace App\Containers\Geo\Admin\Actions;

use App\Containers\Geo\City\Actions\CityAction;
use App\Containers\Geo\Country\Actions\CountryAction;
use App\Containers\Geo\District\Actions\DistrictAction;
use App\Containers\Geo\Governorate\Actions\GovernorateAction;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class GeoAdminAction extends ParentAction
{
    public function __construct(
        private readonly CountryAction $countryAction,
        private readonly GovernorateAction $governorateAction,
        private readonly CityAction $cityAction,
        private readonly DistrictAction $districtAction,
    ) {
    }

    public function index(string $resource, array $filters): LengthAwarePaginator
    {
        return $this->actionFor($resource)->index($filters);
    }

    public function create(string $resource, array $data): Model
    {
        return $this->actionFor($resource)->create($data);
    }

    public function find(string $resource, int $entryId): Model
    {
        return $this->actionFor($resource)->find($entryId);
    }

    public function update(string $resource, int $entryId, array $data): Model
    {
        return $this->actionFor($resource)->update($entryId, $data);
    }

    public function delete(string $resource, int $entryId): void
    {
        $this->actionFor($resource)->delete($entryId);
    }

    private function actionFor(string $resource): CountryAction|GovernorateAction|CityAction|DistrictAction
    {
        return match ($resource) {
            'countries' => $this->countryAction,
            'governorates' => $this->governorateAction,
            'cities' => $this->cityAction,
            'districts' => $this->districtAction,
            default => abort(404),
        };
    }
}
