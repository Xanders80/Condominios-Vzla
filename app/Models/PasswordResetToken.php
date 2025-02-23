<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    // Define los campos que se pueden asignar masivamente
    protected $fillable = ['email', 'token', 'created_at'];
}
