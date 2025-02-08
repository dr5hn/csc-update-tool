<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'state_code',
        'country_id',
        'country_code',
        'latitude',
        'longitude',
        'wikiDataId'
    ];

    /**
     * Get the state that owns the city
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country that owns the city
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get table headers for display
     */
    public static function getTableHeaders(): array
    {
        return [
            'ID',
            'City Name',
            'State ID',
            'State Code',
            'Country ID',
            'Country Code',
            'Latitude',
            'Longitude',
            'Wiki Data ID'
        ];
    }

    /**
     * Get table data for display
     */
    public static function getTableData()
    {
        return self::with(['state', 'country'])->get();
    }
}
