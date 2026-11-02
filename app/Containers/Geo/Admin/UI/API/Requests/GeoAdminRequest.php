<?php

namespace App\Containers\Geo\Admin\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class GeoAdminRequest extends ParentRequest
{
    protected array $decode = [
        'entry',
        'country_id',
        'governorate_id',
        'city_id',
    ];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_platform_admin;
    }

    public function rules(): array
    {
        $resource = $this->route('resource');
        $method = $this->method();

        if ('GET' === $method && is_null($this->route('entry'))) {
            $parentColumn = $this->parentColumn($resource);

            return array_filter([
                'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
                $parentColumn => $parentColumn ? ['sometimes', 'integer', Rule::exists($this->parentTable($resource), 'id')] : null,
            ]);
        }

        if ('GET' === $method || 'DELETE' === $method) {
            return [];
        }

        $table = match ($resource) {
            'countries' => 'countries',
            'governorates' => 'governorates',
            'cities' => 'cities',
            'districts' => 'districts',
        };
        $parentColumn = $this->parentColumn($resource);
        $nameIsUnique = Rule::unique($table, 'name')->ignore($this->route('entry'));

        if (!is_null($parentColumn)) {
            $parentId = $this->input($parentColumn);

            if (is_null($parentId) && 'PATCH' === $method) {
                $parentId = DB::table($table)->where('id', $this->route('entry'))->value($parentColumn);
            }

            $nameIsUnique->where($parentColumn, $parentId);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255', $nameIsUnique],
        ];

        if ('PATCH' === $method) {
            $rules['name'][0] = 'sometimes';
        }

        if ('countries' === $resource) {
            $rules['code'] = [
                'PATCH' === $method ? 'sometimes' : 'required',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
                Rule::unique('countries', 'code')->ignore($this->route('entry')),
            ];
        } else {
            $parentColumn = $this->parentColumn($resource);
            $rules[$parentColumn] = [
                'PATCH' === $method ? 'sometimes' : 'required',
                'integer',
                Rule::exists($this->parentTable($resource), 'id'),
            ];
        }

        return $rules;
    }

    private function parentColumn(string $resource): string|null
    {
        return match ($resource) {
            'governorates' => 'country_id',
            'cities' => 'governorate_id',
            'districts' => 'city_id',
            default => null,
        };
    }

    private function parentTable(string $resource): string|null
    {
        return match ($resource) {
            'governorates' => 'countries',
            'cities' => 'governorates',
            'districts' => 'cities',
            default => null,
        };
    }
}
