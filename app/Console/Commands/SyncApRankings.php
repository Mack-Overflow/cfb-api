<?php

namespace App\Console\Commands;

use App\Models\MediaRanking;
use App\Models\Team;
use App\Services\Cfbd;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SyncApRankings extends Command
{
    protected $signature = 'rankings:sync-ap {--year=}';
    protected $description = 'Sync AP Top 25 rankings from CFBD';

    public function handle(Cfbd $cfbd): int
    {
        $year = (int)($this->option('year') ?? now()->year);
        $this->info("Fetching AP rankings for $year …");

        $payload = $cfbd->rankings($year);
        if (!is_array($payload)) {
            $this->error('Unexpected CFBD response.');
            return self::FAILURE;
        }

        // Build a quick Team lookup by school name (case-insensitive)
        $teamsBySchool = Team::query()->get()
            ->keyBy(fn($t) => mb_strtolower($t->school));

        $countUpserts = 0;

        foreach ($payload as $weekBlock) {
            // CFBD shape typically: ['season'=>2025,'week'=>N,'polls'=>[['poll'=>'AP Top 25','ranks'=>[...]]]]
            $season = (int)($weekBlock['season'] ?? $year);
            $week   = (int)($weekBlock['week']   ?? 0);
            $polls  = $weekBlock['polls'] ?? [];

            // find AP poll (handle slight naming variations)
            $ap = collect($polls)->first(function ($p) {
                $name = strtolower($p['poll'] ?? '');
                return str_contains($name, 'ap'); // matches 'ap top 25', 'ap'
            });

            if (!$ap || empty($ap['ranks'])) {
                continue;
            }

            foreach ($ap['ranks'] as $row) {
                // CFBD rank row typically has keys: rank, school, (sometimes 'team'/'id')
                $rank   = (int)($row['rank'] ?? 0);
                $school = (string)($row['school'] ?? $row['team'] ?? '');

                if ($rank < 1 || $rank > 25 || $school === '') {
                    continue;
                }

                $team = $teamsBySchool->get(mb_strtolower($school));
                $teamId = $team?->id;

                // Upsert by unique (year, week, ranking)
                MediaRanking::updateOrCreate(
                    ['year' => $season, 'week' => $week, 'ranking' => $rank],
                    ['school_name' => $school, 'team_id' => $teamId]
                );

                $countUpserts++;
            }
        }

        $this->info("AP rankings upserted: {$countUpserts}");
        return self::SUCCESS;
    }
}
