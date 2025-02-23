<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class StateAddress extends Eloquent
{
    protected $table = 'states';
    public $timestamps = false;

    public function municipalities()
    {
        return $this->hasMany(MunicipalityAddress::class, 'municipality_id');
    }
}
