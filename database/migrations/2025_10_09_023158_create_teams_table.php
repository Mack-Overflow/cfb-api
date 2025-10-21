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
        Schema::create('teams', function (Blueprint $t) {
            $t->id();
            $t->string('cfbd_id')->unique(); // or "school" key
            $t->string('school');
            $t->string('mascot')->nullable();
            $t->string('abbreviation')->nullable();
            $t->string('conference')->nullable();
            $t->string('logo')->nullable(); // full URL
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
