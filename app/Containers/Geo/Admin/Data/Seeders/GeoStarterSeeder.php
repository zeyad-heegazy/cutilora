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
     */
    private function seedEgypt(): void
    {
        $data = $this->readJson('egypt.json');

        $country = Country::query()->updateOrCreate(
            ['code' => 'EG'],
            [
                'name' => 'Egypt',
                'name_ar' => 'مصر',
            ]
        );

        $this->stats['countries']++;

        foreach ($data['data'] ?? [] as $governorateData) {
            $governorateName = $this->englishName($governorateData);

            $governorateCode = $this->sourceCode(
                $governorateData,
                ['code', 'id', 'governorate_id']
            );

            $governorate = Governorate::query()->updateOrCreate(
                $this->identity(
                    ['country_id' => $country->id],
                    $governorateCode,
                    $governorateName
                ),
                [
                    'name' => $governorateName,
                    'name_ar' => $this->arabicName($governorateData),
                    'code' => $governorateCode,
                ]
            );

            $this->stats['governorates']++;

            foreach ($governorateData['district'] ?? [] as $cityData) {
                $cityName = $this->englishName($cityData);

                $cityCode = $this->sourceCode(
                    $cityData,
                    ['code', 'id', 'district_id']
                );

                $city = City::query()->updateOrCreate(
                    $this->identity(
                        ['governorate_id' => $governorate->id],
                        $cityCode,
                        $cityName
                    ),
                    [
                        'name' => $cityName,
                        'name_ar' => $this->arabicName($cityData),
                        'code' => $cityCode,
                    ]
                );

                $this->stats['cities']++;

                foreach ($cityData['shiyakha'] ?? [] as $districtData) {
                    $districtName = $this->englishName($districtData);

                    $districtCode = $this->sourceCode(
                        $districtData,
                        ['code', 'id', 'shiyakha_id']
                    );

                    District::query()->updateOrCreate(
                        $this->identity(
                            ['city_id' => $city->id],
                            $districtCode,
                            $districtName
                        ),
                        [
                            'name' => $districtName,
                            'name_ar' => $this->arabicName($districtData),
                            'code' => $districtCode,
                        ]
                    );

                    $this->stats['districts']++;
                }
            }
        }
    }

    /**
     * Saudi Arabia:
     * Region -> City -> District
     */
    private function seedSaudiArabia(): void
    {
        $regions = $this->readJson('saudi-regions.json');
        $cities = $this->readJson('saudi-cities.json');
        $districts = $this->readJson('saudi-districts.json');

        $country = Country::query()->updateOrCreate(
            ['code' => 'SA'],
            [
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
            ]
        );

        $this->stats['countries']++;

        // Group cities by region.
        $citiesByRegion = [];

        foreach ($cities as $cityData) {
            $citiesByRegion[(string) $cityData['region_id']][] = $cityData;
        }

        // Group districts by city.
        $districtsByCity = [];

        foreach ($districts as $districtData) {
            $districtsByCity[(string) $districtData['city_id']][] = $districtData;
        }

        foreach ($regions as $regionData) {
            $regionName = $this->englishName($regionData);

            $regionCode = $this->sourceCode(
                $regionData,
                ['region_id', 'code', 'id']
            );

            $governorate = Governorate::query()->updateOrCreate(
                $this->identity(
                    ['country_id' => $country->id],
                    $regionCode,
                    $regionName
                ),
                [
                    'name' => $regionName,
                    'name_ar' => $this->arabicName($regionData),
                    'code' => $regionCode,
                ]
            );

            $this->stats['governorates']++;

            foreach (
                $citiesByRegion[(string) $regionCode] ?? []
                as $cityData
            ) {
                $cityName = $this->englishName($cityData);

                $cityCode = $this->sourceCode(
                    $cityData,
                    ['city_id', 'code', 'id']
                );

                $city = City::query()->updateOrCreate(
                    $this->identity(
                        ['governorate_id' => $governorate->id],
                        $cityCode,
                        $cityName
                    ),
                    [
                        'name' => $cityName,
                        'name_ar' => $this->arabicName($cityData),
                        'code' => $cityCode,
                    ]
                );

                $this->stats['cities']++;

                foreach (
                    $districtsByCity[(string) $cityCode] ?? []
                    as $districtData
                ) {
                    $districtName = $this->englishName($districtData);

                    $districtCode = $this->sourceCode(
                        $districtData,
                        ['district_id', 'code', 'id']
                    );

                    District::query()->updateOrCreate(
                        $this->identity(
                            ['city_id' => $city->id],
                            $districtCode,
                            $districtName
                        ),
                        [
                            'name' => $districtName,
                            'name_ar' => $this->arabicName($districtData),
                            'code' => $districtCode,
                        ]
                    );

                    $this->stats['districts']++;
                }
            }
        }
    }

    /**
     * Build an upsert identity using source code when available.
     */
    private function identity(
        array $parent,
        ?string $code,
        string $name
    ): array {
        return $parent + ['name' => $name];
    }

    /**
     * Extract English name.
     */
    private function englishName(array $data): string
    {
        $name = $data['name'] ?? null;

        if (is_array($name)) {
            $name = $name['en']
                ?? $name['english']
                ?? $name['local']
                ?? null;
        }

        $name = $name
            ?? $data['name_en']
            ?? $data['nameEn']
            ?? null;

        $name = is_string($name) ? trim($name) : '';

        if ($name === '') {
            throw new RuntimeException(
                'Geographic record has no English name: '
                . json_encode($data, JSON_UNESCAPED_UNICODE)
            );
        }

        return $name;
    }

    /**
     * Extract Arabic name.
     */
    private function arabicName(array $data): ?string
    {
        $name = $data['name'] ?? null;

        if (is_array($name)) {
            $name = $name['ar']
                ?? $name['arabic']
                ?? null;
        } else {
            $name = null;
        }

        $name = $name
            ?? $data['name_ar']
            ?? $data['nameAr']
            ?? null;

        if (!is_string($name)) {
            return null;
        }

        $name = trim($name);

        return $name !== '' ? $name : null;
    }

    /**
     * Extract original source identifier.
     */

    private function sourceCode(
        array $data,
        array $keys
    ): ?string {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];

            // Handle nested localized identifiers.
            if (is_array($value)) {
                $value = $value['en']
                    ?? $value['id']
                    ?? $value['code']
                    ?? $value['local']
                    ?? null;
            }

            if (!is_string($value) && !is_numeric($value)) {
                continue;
            }

            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }


    /**
     * Read JSON dataset.
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
}
