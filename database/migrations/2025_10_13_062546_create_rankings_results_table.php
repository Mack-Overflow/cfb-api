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
        Schema::create('rankings_results', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('group_id');
            $t->unsignedSmallInteger('year');
            $t->unsignedTinyInteger('week');
            $t->unsignedTinyInteger('ranking');

            $t->string('school_name');
            $t->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();

            $t->timestamps();

            $t->unique(['group_id','year','week','ranking']);

            // Helpful lookups
            $t->index(['group_id','year','week']);
            $t->index('team_id');
            $t->index('school_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings_results');
    }
};
