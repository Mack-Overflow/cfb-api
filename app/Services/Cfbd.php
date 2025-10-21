<?php

namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Cfbd
{
    private string $base = 'https://api.collegefootballdata.com';
    private string $key;

    public function __construct() {
        $this->key = config('services.cfbd.key');
    }

    private function client() {
        return Http::withHeaders(['Authorization' => "Bearer {$this->key}"])
                ->acceptJson()->timeout(20);
    }

    public function rankings(int $season) {
        // CFBD supports ?year=; we’ll cache briefly
        return Cache::remember("cfbd:rankings:$season", 900, function() use ($season) {
            return $this->client()->get($this->base.'/rankings', ['year' => $season])->json();
        });
    }

    public function teams() {
        return Cache::remember('cfbd:teams', 86400, function(){
        return $this->client()->get($this->base.'/teams?year=2025')->json();
        });
    }

    public function games(int $season) {
        return Cache::remember("cfbd:games:$season", 3600, function() use($season){
        return $this->client()->get($this->base.'/games?classification=fbs', ['year'=>$season, 'seasonType'=>'regular'])->json();
        });
    }
}