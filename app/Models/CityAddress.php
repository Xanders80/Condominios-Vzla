<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CityAddress extends Eloquent
{
    protected $table = 'cities';
    public $timestamps = false;

    public function states()
    {
        return $this->belongsTo(StateAddress::class, 'state_id', 'id');
    }
}
