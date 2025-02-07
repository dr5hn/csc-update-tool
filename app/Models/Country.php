<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'id',
        'name',
        'iso3',
        'numeric_code',
        'iso2',
        'phonecode',
        'capital',
        'currency',
        'currency_name',
        'currency_symbol',
        'tld',
        'native',
        'region',
        'region_id',
        'subregion',
        'subregion_id',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
        'created_at',
        'updated_at',
        'flag',
        'wikiDataId',
    ];

   public static function getTableHeaders()
    {
        return [
            'id' => 'ID',
            'name' => 'Country Name',
            'iso3' => 'ISO 3',
            'numeric_code' => 'Numeric Code',
            'iso2' => 'ISO 2',
            'phonecode' => 'Phone Code',
            'capital' => 'Capital',
            'currency' => 'Currency',
            'currency_name' => 'Currency Name',
            'currency_symbol' => 'Currency Symbol',
            'tld' => 'TLD',
            'native' => 'Native',
            'region' => 'Region',
            'region_id' => 'Region ID',
            'subregion' => 'Subregion',
            'subregion_id' => 'Subregion ID',
            'nationality' => 'Nationality',
            'timezones' => 'Timezones',
            'translations' => 'Translations',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'emoji' => 'Emoji',
            'emojiU' => 'Emoji Unicode',
            'wikiDataId' => 'Wiki Data ID',
        ];
    } 

    public static function getTableData()
    {
        return self::all();
    }

    // A country belongs to a subregion
    public function subregion()
    {
        return $this->belongsTo(Subregion::class);
    }

    // A country belongs to a region through subregion
    public function region()
    {
        return $this->belongsTo(Region::class)->through('subregion');
    }

    // A country has many states
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
