<?php

namespace App\Containers\Geo\Admin\Data\Seeders;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;

final class GeoStarterSeeder extends ParentSeeder
{
    public function run(): void
    {
        $country = Country::query()->updateOrCreate(
            ['code' => 'EG'],
            ['name' => 'Egypt'],
        );
        $governorate = Governorate::query()->updateOrCreate(
            ['country_id' => $country->id, 'name' => 'Cairo'],
            [],
        );
        $city = City::query()->updateOrCreate(
            ['governorate_id' => $governorate->id, 'name' => 'Cairo'],
            [],
        );

        District::query()->updateOrCreate(
            ['city_id' => $city->id, 'name' => 'Heliopolis'],
            [],
        );
    }
}
