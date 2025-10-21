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
        Schema::create('ballot_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ballot_id')->constrained('ballots')->cascadeOnDelete();
            $t->foreignId('team_id')->constrained('teams');
            $t->unsignedTinyInteger('rank'); // 1..25
            $t->timestamps();
            $t->unique(['ballot_id','rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballot_items');
    }
};
