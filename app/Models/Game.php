<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'season',
        'week',
        'home_team_id',
        'away_team_id',
        'home_points',
        'away_points',
        'completed',
        'kickoff_date',
    ];

    protected $casts = [
        'season'       => 'integer',
        'week'         => 'integer',
        'home_points'  => 'integer',
        'away_points'  => 'integer',
        'completed'    => 'boolean',
        'kickoff_date' => 'date',     // stored as DATE in migration
    ];

    public function homeTeam() { return $this->belongsTo(Team::class, 'home_team_id'); }
    public function awayTeam() { return $this->belongsTo(Team::class, 'away_team_id'); }
}
