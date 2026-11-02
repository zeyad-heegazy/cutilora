<?php

namespace App\Containers\Geo\Admin\Data\Seeders;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class GeoStarterSeeder extends ParentSeeder
{
    private string $dataPath;

    private array $stats = [
        'countries' => 0,
        'governorates' => 0,
        'cities' => 0,
        'districts' => 0,
    ];

    public function run(): void
    {
        $this->dataPath = __DIR__ . '/data';

        DB::transaction(function (): void {
            $this->seedEgypt();
            $this->seedSaudiArabia();
        });

        $this->command?->info('Geo seeding completed.');
        $this->command?->table(
            ['Entity', 'Imported'],
            [
                ['Countries', $this->stats['countries']],
                ['Governorates', $this->stats['governorates']],
                ['Cities', $this->stats['cities']],
                ['Districts', $this->stats['districts']],
            ]
        );
    }

    /**
     * Egypt:
     * Governorate -> Administrative District -> Shiyakha
     *
     * Your current City model represents the second level.
     */
    private function seedEgypt(): void
    {
        $data = $this->readJson('egypt.json');

        $country = Country::query()->updateOrCreate(
            ['code' => 'EG'],
            ['name' => 'Egypt']
        );

        $this->stats['countries']++;

        foreach ($data['data'] ?? [] as $governorateData) {
            $governorate = Governorate::query()->updateOrCreate(
                [
                    'country_id' => $country->id,
                    'name' => $this->englishName($governorateData),
                ],
                []
            );

            $this->stats['governorates']++;

            foreach ($governorateData['district'] ?? [] as $cityData) {
                $city = City::query()->updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'name' => $this->englishName($cityData),
                    ],
                    []
                );

                $this->stats['cities']++;

                foreach ($cityData['shiyakha'] ?? [] as $districtData) {
                    District::query()->updateOrCreate(
                        [
                            'city_id' => $city->id,
                            'name' => $this->englishName($districtData),
                        ],
                        []
                    );

                    $this->stats['districts']++;
                }
            }
        }
    }

    /**
     * Saudi Arabia:
     * Region -> City -> District
     *
     * Your Governorate model represents the administrative region.
     */
    private function seedSaudiArabia(): void
    {
        $regions = $this->readJson('saudi-regions.json');
        $cities = $this->readJson('saudi-cities.json');
        $districts = $this->readJson('saudi-districts.json');

        $country = Country::query()->updateOrCreate(
            ['code' => 'SA'],
            ['name' => 'Saudi Arabia']
        );

        $this->stats['countries']++;

        // Group cities by region.
        $citiesByRegion = [];

        foreach ($cities as $cityData) {
            $citiesByRegion[$cityData['region_id']][] = $cityData;
        }

        // Group districts by city.
        $districtsByCity = [];

        foreach ($districts as $districtData) {
            $districtsByCity[$districtData['city_id']][] = $districtData;
        }

        foreach ($regions as $regionData) {
            $governorate = Governorate::query()->updateOrCreate(
                [
                    'country_id' => $country->id,
                    'name' => $regionData['name_en'],
                ],
                []
            );

            $this->stats['governorates']++;

            foreach ($citiesByRegion[$regionData['region_id']] ?? [] as $cityData) {
                $city = City::query()->updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'name' => $cityData['name_en'],
                    ],
                    []
                );

                $this->stats['cities']++;

                foreach ($districtsByCity[$cityData['city_id']] ?? [] as $districtData) {
                    District::query()->updateOrCreate(
                        [
                            'city_id' => $city->id,
                            'name' => $districtData['name_en'],
                        ],
                        []
                    );

                    $this->stats['districts']++;
                }
            }
        }
    }

    /**
     * Read and decode a JSON dataset.
     */
    private function readJson(string $filename): array
    {
        $path = $this->dataPath . '/' . $filename;

        if (!is_file($path)) {
            throw new RuntimeException(
                "Geo dataset not found: {$path}"
            );
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException(
                "Unable to read geo dataset: {$path}"
            );
        }

        $data = json_decode(
            $contents,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($data)) {
            throw new RuntimeException(
                "Invalid geo dataset format: {$filename}"
            );
        }

        return $data;
    }

    /**
     * Get English location name from the Egypt dataset.
     */
    private function englishName(array $data): string
    {
        $name = trim(
            $data['name']['en']
            ?? $data['name']['local']
            ?? ''
        );

        if ($name === '') {
            throw new RuntimeException(
                'A geographic record has an empty name.'
            );
        }

        return $name;
    }
}
