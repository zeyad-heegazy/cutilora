<?php

namespace App\Containers\Geo\City\UI\API\Transformers;

use App\Containers\Geo\City\Models\City;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class CityTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(City $city): array
    {
        return [
            'type' => $city->getResourceKey(),
            'id' => $city->getHashedKey(),
            'governorate_id' => $city->governorate_id,
            'name' => $city->name,
            'created_at' => $city->created_at,
            'updated_at' => $city->updated_at,
            'readable_created_at' => $city->created_at->diffForHumans(),
            'readable_updated_at' => $city->updated_at->diffForHumans(),
        ];
    }
}
