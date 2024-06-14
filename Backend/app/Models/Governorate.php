<?php

namespace App\Models;

use App\Models\Scopes\EnabledStatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new EnabledStatusScope);
    }

    protected $hidden = ['created_at', 'updated_at']; // to prevent show in api response

    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function cities(){
        return $this->hasMany(City::class, 'governorate_id', 'id');
    }
}
