<?php

namespace App\Containers\Tenancy\Clinic\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

final class CreateClinicRequest extends ParentRequest
{
    protected array $decode = [
        'country_id',
        'governorate_id',
        'city_id',
        'district_id',
    ];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')],
            'governorate_id' => [
                'nullable',
                'integer',
                Rule::exists('governorates', 'id')->where('country_id', $this->input('country_id')),
            ],
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where('governorate_id', $this->input('governorate_id')),
            ],
            'district_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id')->where('city_id', $this->input('city_id')),
            ],
            'timezone' => ['required', 'timezone'],
            'currency' => ['required', 'string', 'size:3'],
            'plan_id' => ['nullable', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_platform_admin;
    }
}
