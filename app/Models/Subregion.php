<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subregion extends Model
{
    protected $fillable = [
        'name',
        'translations',
        'region_id',
        'wikiDataId'
    ];

    protected $casts = [
        'translations' => 'array'
    ];

    public function getTranslationsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function setTranslationsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['translations'] = $value;
        } else {
            $this->attributes['translations'] = json_encode($value);
        }
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }

    public static function getTableHeaders(): array
    {
        return [
            'ID',
            'Name',
            'Translations',
            'Region ID',
            'Wiki Data ID'
        ];
    }

    public static function getTableData()
    {
        return self::with('region')->get();
    }
}
