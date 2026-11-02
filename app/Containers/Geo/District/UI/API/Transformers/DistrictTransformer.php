<?php

namespace App\Containers\Geo\District\UI\API\Transformers;

use App\Containers\Geo\District\Models\District;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class DistrictTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(District $district): array
    {
        return [
            'type' => $district->getResourceKey(),
            'id' => $district->getHashedKey(),
            'city_id' => $district->city_id,
            'name' => $district->name,
            'created_at' => $district->created_at,
            'updated_at' => $district->updated_at,
            'readable_created_at' => $district->created_at->diffForHumans(),
            'readable_updated_at' => $district->updated_at->diffForHumans(),
        ];
    }
}
