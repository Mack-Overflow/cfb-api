<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
    protected $fillable = [
        'user_id',
        'season',
        'week',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'season'  => 'integer',
        'week'    => 'integer',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(BallotItem::class)->orderBy('rank'); }
}
