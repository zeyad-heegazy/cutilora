<?php

namespace App\Containers\Geo\Governorate\Models;

use App\Containers\Geo\City\Models\City;
use App\Containers\Geo\Country\Models\Country;
use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Governorate extends ParentModel
{
    protected $fillable = [
        'country_id',
        'name',
    ];

    protected $casts = [
        'country_id' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class);
    }
}
