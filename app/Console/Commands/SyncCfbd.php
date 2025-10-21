<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;

class SyncCfbd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-cfbd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Cfbd $cfbd) {
        // teams + logos
        $fbs_fcs_conferences = [
            'ACC', 'American Athletic', 'Mid-American', 'SEC', 'Big Ten',
            'Big 12', 'Pac-12', 'Sun Belt', 'FBS Independents',
            'Mountain West', 'Conference USA', 'Big Sky', 'CAA', 'FCS Independents',
            'Ivy', 'MEAC', 'MVFC', 'NEC', 'Big South-OVC', 'Patriot', 'Pioneer', 'Southern',
            'SWAC', 'Southland', 'UAC', 
        ];
        $teams = collect($cfbd->teams());
        $fbsTeams = $teams->filter(function ($team) use ($fbs_fcs_conferences) {
            // Some APIs nest conference under another key or use null, handle both
            $conference = $team['conference'] ?? $team->conference ?? null;
        
            return $conference && in_array($conference, $fbs_fcs_conferences, true);
        })->values();
        $logos = collect($cfbd->logos())->keyBy(fn($t)=>$t['school'] ?? $t['team'] ?? '');
      
        $fbsTeams->each(function($t) use ($logos){
          $school = $t['school'];
          $logo = optional($logos->get($school))['logos'][0] ?? null;
          \App\Models\Team::updateOrCreate(
            ['cfbd_id' => (string)($t['id'] ?? $school)], // fallback to school key
            [
              'school' => $school,
              'mascot' => $t['mascot'] ?? null,
              'abbreviation' => $t['abbreviation'] ?? null,
              'conference' => $t['conference'] ?? null,
              'logo' => $logo,
            ]
          );
        });
      
        // games (season = current year)
        $season = now()->year; // adjust if needed
        $games = collect($cfbd->games($season));
        $teamsMap = \App\Models\Team::query()->get()->keyBy('school');
      
        $games->each(function($g) use ($season, $teamsMap){
          $home = $teamsMap[$g['homeTeam']] ?? null;
          $away = $teamsMap[$g['awayTeam']] ?? null;
          if(!$home || !$away) return;
          Game::updateOrCreate(
            [
              'season'=>$season,
              'week'=>$g['week'] ?? 0,
              'home_team_id'=>$home->id,
              'away_team_id'=>$away->id,
              'kickoff_date'=>optional(\Carbon\Carbon::parse($g['startDate'] ?? null))->toDateString(),
            ],
            [
              'home_points'=>$g['homePoints'] ?? null,
              'away_points'=>$g['awayPoints'] ?? null,
              'completed'=>($g['completed'] ?? false) ? true : (($g['homePoints'] ?? null)!==null && ($g['awayPoints'] ?? null)!==null),
            ]
          );
        });
      
        $this->info('Synced teams, logos, and games.');
    }
}
