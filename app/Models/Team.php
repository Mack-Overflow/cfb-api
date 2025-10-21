<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'cfbd_id',
        'school',
        'mascot',
        'abbreviation',
        'conference',
        'logo',
    ];

    public function homeGames() { return $this->hasMany(Game::class, 'home_team_id'); }
    public function awayGames() { return $this->hasMany(Game::class, 'away_team_id'); }
}
