<?php

namespace App\Containers\Geo\City\Models;

use App\Containers\Geo\District\Models\District;
use App\Containers\Geo\Governorate\Models\Governorate;
use App\Containers\Tenancy\Clinic\Models\Clinic;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class City extends ParentModel
{
    protected $fillable = [
        'governorate_id',
        'name',
    ];

    protected $casts = [
        'governorate_id' => 'integer',
    ];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class);
    }
}
