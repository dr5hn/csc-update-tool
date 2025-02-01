<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'id',
        'name',
        'country_id',
        'country_code',
        'fips_code',
        'iso2',
        'type',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
        'flag',
        'wikiDataId',
    ];

    public static function getTableHeaders()
    {
        return [
            'id' => 'ID',
            'name' => 'State Name',
            'country_id' => 'Country ID',
            'country_code' => 'Country Code',
            'fips_code' => 'FIPS Code',
            'iso2' => 'ISO 2',
            'type' => 'Type',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'flag' => 'Flag',
            'wikiDataId' => 'Wiki Data ID',
        ];
    }

    public static function getTableData()
    {
        return self::with('country')->limit(10)->get();
    }

    public static function getDropdownData()
    {
        return self::with('country')->get();
    }

    // A state belongs to a country
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // A state has many cities
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // Get subregion through country
    public function subregion()
    {
        return $this->belongsTo(Subregion::class)->through('country');
    }

    // Get region through country and subregion
    public function region()
    {
        return $this->belongsTo(Region::class)->through('country.subregion');
    }
}

