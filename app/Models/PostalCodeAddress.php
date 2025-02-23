<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostalCodeAddress extends Eloquent
{
    protected $table = 'postal_zone';
    public $timestamps = false;

    public function parishes()
    {
        return $this->belongsTo(CountryAddress::class, 'parish_id', 'id');
    }

    public function city()
    {
        return $this->parish->municipality->cities;
    }
}
