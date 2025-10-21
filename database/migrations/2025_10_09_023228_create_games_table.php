<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $t) {
            $t->id();
            $t->integer('season');
            $t->integer('week');
            $t->foreignId('home_team_id')->constrained('teams');
            $t->foreignId('away_team_id')->constrained('teams');
            $t->integer('home_points')->nullable();
            $t->integer('away_points')->nullable();
            $t->boolean('completed')->default(false);
            $t->date('kickoff_date')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
