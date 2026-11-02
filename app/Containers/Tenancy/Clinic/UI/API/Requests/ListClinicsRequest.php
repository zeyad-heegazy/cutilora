<?php

namespace App\Containers\Tenancy\Clinic\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListClinicsRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_platform_admin;
    }
}
