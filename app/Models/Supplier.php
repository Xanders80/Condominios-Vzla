<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'rif',
        'email',
        'phone',
        'service_type',
    ];

    /**
     * Get the work orders assigned to the supplier.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
