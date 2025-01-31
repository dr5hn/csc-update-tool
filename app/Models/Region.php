<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'id',
        'name',
        'translations',
        'created_at',
        'updated_at',
        'flag',
        'wikiDataId',
    ];

    public static function getTableHeaders()
    {
        return [
            'id' => 'ID',
            'name' => 'Region Name',
            'translations' => 'Translations',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'flag' => 'Flag',
            'wikiDataId' => 'Wiki Data ID',
        ];
    }

    public static function getTableData()
    {
        return self::all();
    }

    // A region has many subregions
    public function subregions()
    {
        return $this->hasMany(Subregion::class);
    }

    // A region has many countries through subregions
    public function countries()
    {
        return $this->hasManyThrough(Country::class, Subregion::class);
    }
}

