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
        Schema::create('media_rankings', function (Blueprint $t) {
            $t->id();
            $t->unsignedSmallInteger('year');          // season
            $t->unsignedTinyInteger('week');           // 1..15-ish
            $t->unsignedTinyInteger('ranking');        // 1..25
            $t->string('school_name');                 // as returned by API
            $t->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $t->timestamps();

            // prevents duplicates for a given season/week/slot
            $t->unique(['year','week','ranking']);
            // help lookups by team or school
            $t->index(['year','week']);
            $t->index('team_id');
            $t->index('school_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_rankings');
    }
};
