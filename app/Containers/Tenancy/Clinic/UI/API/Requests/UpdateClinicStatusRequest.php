<?php

namespace App\Containers\Tenancy\Clinic\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateClinicStatusRequest extends ParentRequest
{
    protected array $decode = [
        'clinic',
    ];

    public function rules(): array
    {
        return [
            'subscription_status' => ['required', 'in:active,suspended'],
            'reason' => ['required_if:subscription_status,suspended', 'nullable', 'string', 'min:3', 'max:1000', 'not_regex:/^\s*$/'],
        ];
    }

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_platform_admin;
    }
}
