<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'season'        => 'required|integer',
            'week'          => 'required|integer|min:0',
            'home_team_id'  => 'required|exists:teams,id',
            'away_team_id'  => 'required|exists:teams,id|different:home_team_id',
            'home_points'   => 'nullable|integer|min:0',
            'away_points'   => 'nullable|integer|min:0',
            'completed'     => 'sometimes|boolean',
            'kickoff_date'  => 'nullable|date',
        ]);
    }
}
