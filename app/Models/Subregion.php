<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subregion extends Model
{

    protected $fillable = [
        'id',
        'name',
        'translations',
        'region_id',
        'created_at',
        'updated_at',
        'flag',
        'wikiDataId',
    ];

    public static function getTableHeaders()
    {
        return [
            'id' => 'ID',
            'name' => 'Subregion Name',
            'translations' => 'Translations',
            'region_id' => 'Region ID',
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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function countries()
    {
        return $this->hasMany(Country::class);
    }
}
