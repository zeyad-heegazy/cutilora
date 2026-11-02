<?php

namespace App\Containers\Geo\Governorate\UI\API\Transformers;

use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class GovernorateTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    public function transform(Governorate $governorate): array
    {
        return [
            'type' => $governorate->getResourceKey(),
            'id' => $governorate->getHashedKey(),
            'country_id' => $governorate->country_id,
            'name' => $governorate->name,
            'created_at' => $governorate->created_at,
            'updated_at' => $governorate->updated_at,
            'readable_created_at' => $governorate->created_at->diffForHumans(),
            'readable_updated_at' => $governorate->updated_at->diffForHumans(),
        ];
    }
}
