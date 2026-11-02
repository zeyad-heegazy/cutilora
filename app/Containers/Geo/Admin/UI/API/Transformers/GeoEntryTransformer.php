<?php

namespace App\Containers\Geo\Admin\UI\API\Transformers;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use Illuminate\Database\Eloquent\Model;

final class GeoEntryTransformer extends ParentTransformer
{
    public function transform(Model $entry): array
    {
        $data = [
            'id' => $entry->getHashedKey(),
            'name' => $entry->name,
        ];

        if ($entry instanceof Country) {
            $data['code'] = $entry->code;
        } elseif ($entry instanceof Governorate) {
            $data['country_id'] = $entry->country?->getHashedKey();
        } elseif ($entry instanceof City) {
            $data['governorate_id'] = $entry->governorate?->getHashedKey();
        } elseif ($entry instanceof District) {
            $data['city_id'] = $entry->city?->getHashedKey();
        }

        return $data + [
            'created_at' => $entry->created_at,
            'updated_at' => $entry->updated_at,
        ];
    }
}
