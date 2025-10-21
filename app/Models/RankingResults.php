<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingResult extends Model
{
    protected $table = 'ranking_results';

    protected $fillable = [
        'group_id',
        'year',
        'week',
        'ranking',
        'school_name',
        'team_id',
    ];

    protected $casts = [
        'group_id' => 'integer',
        'year'     => 'integer',
        'week'     => 'integer',
        'ranking'  => 'integer',
        'team_id'  => 'integer',
    ];

    // Relationships
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
