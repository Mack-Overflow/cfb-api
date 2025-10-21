<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ballot;
use App\Models\MediaRanking;

class BallotController extends Controller
{
    public function show(Request $r)
    {
        // Pick the most recent season+week from your Rankings table
        $latest = MediaRanking::orderByDesc('year')->orderByDesc('week')->first();

        $season = (int) ($latest?->year ?? now()->year);
        $week   = (int) ($latest?->week ?? 1);

        $ballot = Ballot::query()
            ->where('user_id', $r->user()->id)
            ->where('season', $season)
            ->where('week', $week)
            ->with(['items' => fn($q) => $q->orderBy('rank')]) // returns rank 1..25
            ->first();

        if (!$ballot) {
            // No ballot this week yet — return a stable empty shape
            return response()->json([
                'season' => $season,
                'week'   => $week,
                'ranks'  => [],
            ], 200);
        }

        return response()->json([
            'season' => $ballot->season,
            'week'   => $ballot->week,
            'ranks'  => $ballot->items->map(fn($i) => [
                'rank'     => (int) $i->rank,
                'team_id'  => (int) $i->team_id,
            ])->all(),
        ], 200);
    }

    public function store(Request $r){
        $data = $r->validate([
            'ballot'         => 'required|array|size:25',
            'ranks.*.team_id'=> 'required|integer|exists:teams,id',
            'ranks.*.rank'   => 'required|integer|min:1|max:25',
        ]);

        $latest = MediaRanking::select(DB::raw('MAX(CONCAT(year, LPAD(week,2,"0"))) as latest_key'))
            ->first()
            ->latest_key;
        $latestYear = substr($latest, 0, 4);
        $latestWeek = (int) substr($latest, 4);

        $rankVals = collect($data['ballot'])->pluck('rank');
        $teamVals = collect($data['ballot'])->pluck('team_id');
        if ($rankVals->unique()->count() !== 25 || $teamVals->unique()->count() !== 25) {
            return response()->json(['message' => 'Ranks and team_ids must be unique'], 422);
        }
      
        $ballot = DB::transaction(function () use ($r, $data, $latestWeek, $latestYear) {
            $ballot = Ballot::updateOrCreate(
                ['user_id' => $r->user()->id, 'season' => $latestYear, 'week' => $latestWeek],
                []
            );
    
            // Replace items
            $ballot->items()->delete();
            $items = collect($data['ballot'])->map(fn ($i) => [
                'ballot_id'  => $ballot->id,
                'team_id'    => $i['team_id'],
                'rank'       => $i['rank'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();
    
            DB::table('ballot_items')->insert($items);
    
            return $ballot;
        });
      
        return response()->json(['ok'=>true,'ballot_id'=>$ballot->id]);
    }
}
