<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MediaRanking;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $fbs_conferences = [
            'ACC', 'American Athletic', 'Mid-American', 'SEC', 'Big Ten',
            'Big 12', 'Pac-12', 'Sun Belt', 'FBS Independents',
            'Mountain West', 'Conference USA'
        ];

        $latest = MediaRanking::select(DB::raw('MAX(CONCAT(year, LPAD(week,2,"0"))) as latest_key'))
            ->first()
            ->latest_key;

        $latestYear = substr($latest, 0, 4);
        $latestWeek = (int) substr($latest, 4);

        // Build subquery: ranking per team (or null if unranked)
        $latestRankings = MediaRanking::select('team_id', 'ranking')
            ->where('year', $latestYear)
            ->where('week', $latestWeek);

        // Join it into teams
        $teams = Team::whereIn('conference', $fbs_conferences)
            ->leftJoinSub($latestRankings, 'r', 'r.team_id', '=', 'teams.id')
            ->select('teams.*', DB::raw('COALESCE(r.ranking, "") as ap_rank'))
            ->orderByRaw('CASE WHEN r.ranking IS NULL THEN 999 ELSE r.ranking END') // ranked first
            ->get();

        return response()->json(['teams' => $teams], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cfbd_id'     => 'required|string|max:191',
            'school'      => 'required|string|max:191',
            'mascot'      => 'nullable|string|max:191',
            'abbreviation'=> 'nullable|string|max:10',
            'conference'  => 'nullable|string|max:191',
            'logo'        => 'nullable|url|max:2048',
        ]);

        return response()->json(['team' => $data], 201);
    }
}
