<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CountryAddress extends Eloquent
{
    protected $table = 'parishes';
    public $timestamps = false;

    public function municipalities()
    {
        return $this->belongsTo(MunicipalityAddress::class, 'municipality_id', 'id');
    }
}
