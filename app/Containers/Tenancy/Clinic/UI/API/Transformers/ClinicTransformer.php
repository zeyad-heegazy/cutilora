<?php

namespace App\Containers\Tenancy\Clinic\UI\API\Transformers;

use App\Containers\Geo\City\UI\API\Transformers\CityTransformer;
use App\Containers\Geo\Country\UI\API\Transformers\CountryTransformer;
use App\Containers\Geo\District\UI\API\Transformers\DistrictTransformer;
use App\Containers\Geo\Governorate\UI\API\Transformers\GovernorateTransformer;
use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class ClinicTransformer extends ParentTransformer
{
    protected array $availableIncludes = [
        'country',
        'governorate',
        'city',
        'district',
        // 'plan',
    ];

    protected array $defaultIncludes = [];

    public function transform(Clinic $clinic): array
    {
        return [
            'id' => $clinic->getHashedKey(),
            'name' => $clinic->name,
            'timezone' => $clinic->timezone,
            'currency' => $clinic->currency,
            'subscription_status' => $clinic->subscription_status,
            'created_at' => $clinic->created_at,
            'updated_at' => $clinic->updated_at,
        ];
    }

    public function includeCountry(Clinic $clinic)
    {
        return $this->item($clinic->country, new CountryTransformer());
    }

    public function includeGovernorate(Clinic $clinic)
    {
        return $this->item($clinic->governorate, new GovernorateTransformer());
    }

    public function includeCity(Clinic $clinic)
    {
        return $this->item($clinic->city, new CityTransformer());
    }

    public function includeDistrict(Clinic $clinic)
    {
        return $this->item($clinic->district, new DistrictTransformer());
    }

    // public function includePlan(Clinic $clinic)
    // {
    //     return $this->item($clinic->plan, new PlanTransformer());
    // }
}
