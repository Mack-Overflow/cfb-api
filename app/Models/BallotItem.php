<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallotItem extends Model
{
    protected $fillable = [
        'ballot_id',
        'team_id',
        'rank',
    ];

    protected $casts = [
        'ballot_id' => 'integer',
        'team_id'   => 'integer',
        'rank'      => 'integer',
    ];

    public function ballot() { return $this->belongsTo(Ballot::class); }
    public function team()   { return $this->belongsTo(Team::class); }
}
