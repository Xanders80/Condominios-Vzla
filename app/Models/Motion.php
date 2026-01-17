<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motion extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'assembly_session_id',
        'text',
        'status',
    ];

    /**
     * Get the assembly session where this motion was proposed.
     */
    public function assemblySession()
    {
        return $this->belongsTo(AssemblySession::class);
    }

    /**
     * Get the votes for this motion.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
