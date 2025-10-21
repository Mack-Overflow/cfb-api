<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLoginCode extends Model
{
    protected $table = 'email_login_codes';

    public $timestamps = true;

    protected $fillable = [
        'email',
        'code_hash',
        'expires_at',
    ];

    protected $casts = [
        'email'      => 'string',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'code_hash',
    ];
}
