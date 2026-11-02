<?php

namespace App\Containers\Geo\Country\UI\API\Transformers;

use App\Containers\Geo\Country\Models\Country;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class CountryTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Country $country): array
    {
        return [
            'type' => $country->getResourceKey(),
            'id' => $country->getHashedKey(),
            'name_en' => $country->name,
            'name_ar' => $country->name_ar,
            'code' => $country->code,
            'created_at' => $country->created_at,
            'updated_at' => $country->updated_at,
            'readable_created_at' => $country->created_at->diffForHumans(),
            'readable_updated_at' => $country->updated_at->diffForHumans(),
        ];
    }
}
