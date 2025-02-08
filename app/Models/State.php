<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'country_id',
        'country_code',
        'fips_code',
        'iso2',
        'type',
        'latitude',
        'longitude',
        'wikiDataId'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public static function getTableHeaders(): array
    {
        return [
            'ID',
            'State Name',
            'Country ID',
            'Country Code',
            'FIPS Code',
            'ISO2',
            'Type',
            'Latitude',
            'Longitude',
            'Wiki Data ID'
        ];
    }

    public static function getTableData()
    {
        return self::with('country')->get();
    }

    public static function getDropdownData()
    {
        return self::select('id', 'name')->get();
    }
}
