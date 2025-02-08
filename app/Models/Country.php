<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
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
        'region_id',
        'subregion_id',
        'nationality',
        'timezones',
        'translations',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
        'wikiDataId'
    ];

    protected $casts = [
        'timezones' => 'array',
        'translations' => 'array'
    ];

    public function subregion(): BelongsTo
    {
        return $this->belongsTo(Subregion::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public static function getTableHeaders(): array
    {
        return [
            'ID',
            'Name',
            'ISO3',
            'Numeric Code',
            'ISO2',
            'Phone Code',
            'Capital',
            'Currency',
            'Currency Name',
            'Currency Symbol',
            'TLD',
            'Native',
            'Region',
            'Region ID',
            'Subregion',
            'Subregion ID',
            'Nationality',
            'Timezones',
            'Translations',
            'Latitude',
            'Longitude',
            'Emoji',
            'EmojiU',
            'Wiki Data ID'
        ];
    }

    public static function getTableData()
    {
        return self::with(['subregion.region'])->get();
    }
}
