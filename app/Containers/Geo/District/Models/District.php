<?php

namespace App\Containers\Geo\District\Models;

use App\Containers\Geo\City\Models\City;
use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class District extends ParentModel
{
    protected $fillable = [
        'city_id',
        'name',
        'name_ar',
        'code',
    ];

    protected $casts = [
        'city_id' => 'integer',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class);
    }
}
