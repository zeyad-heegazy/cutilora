<?php

namespace App\Containers\Geo\Country\Models;

use App\Containers\Geo\Governorate\Models\Governorate;
use App\Ship\Parents\Models\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Country extends ParentModel
{
    protected $fillable = [
        'name',
        'name_ar',
        'code',
    ];

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }

    public function clinics(): HasMany
    {
        return $this->hasMany(\App\Containers\Tenancy\Clinic\Models\Clinic::class);
    }
}
