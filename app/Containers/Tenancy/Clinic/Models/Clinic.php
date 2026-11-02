<?php

namespace App\Containers\Tenancy\Clinic\Models;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Clinic extends ParentModel
{
    public const STATUS_TRIAL = 'trial';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'country_id',
        'governorate_id',
        'city_id',
        'district_id',
        'timezone',
        'currency',
        'subscription_status',
        'plan_id',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'country_id' => 'integer',
        'governorate_id' => 'integer',
        'city_id' => 'integer',
        'district_id' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function isActive(): bool
    {
        return $this->subscription_status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->subscription_status === self::STATUS_SUSPENDED;
    }
}
