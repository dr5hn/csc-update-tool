<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'name',
        'translations',
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

    public function subregions(): HasMany
    {
        return $this->hasMany(Subregion::class);
    }

    public static function getTableHeaders(): array
    {
        return [
            'ID',
            'Name',
            'Translations',
            'Wiki Data ID'
        ];
    }

    public static function getTableData()
    {
        return self::get();
    }
}
