<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaRanking extends Model
{
    protected $fillable = [
        'year', 'week', 'ranking', 'school_name', 'team_id',
    ];

    protected $casts = [
        'year'    => 'integer',
        'week'    => 'integer',
        'ranking' => 'integer',
        'team_id' => 'integer',
    ];

    public function team() { return $this->belongsTo(Team::class); }
}
