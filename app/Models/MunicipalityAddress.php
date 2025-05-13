<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MunicipalityAddress extends Eloquent
{
    protected $table = 'municipalities';
    public $timestamps = false;

    public function states()
    {
        return $this->belongsTo(StateAddress::class, 'state_id', 'id');
    }

    public function parishes()
    {
        return $this->hasMany(CountryAddress::class, 'parish_id', 'id');
    }
}
