<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'id',
        'name',
        'state_id',
        'state_code',
        'country_id',
        'country_code',
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
            'name' => 'City Name',
            'state_id' => 'State ID',
            'state_code' => 'State Code',
            'country_id' => 'Country ID',
            'country_code' => 'Country Code',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'wikiDataId' => 'Wiki Data ID',
        ];
    }

    public static function getTableData()
    {
        return self::with('state')->limit(10)->get();
    }

     // A city belongs to a state
     public function state()
     {
         return $this->belongsTo(State::class);
     }
 
     // Get country through state
     public function country()
     {
         return $this->belongsTo(Country::class)->through('state');
     }
 
     // Get subregion through state and country
     public function subregion()
     {
         return $this->belongsTo(Subregion::class)->through('state.country');
     }
 
     // Get region through state, country, and subregion
     public function region()
     {
         return $this->belongsTo(Region::class)->through('state.country.subregion');
     }
}

