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
        Schema::create('weekly_xp', function (Blueprint $table) {
            $table->id();
            $table->date('week_start')->index(); // Data de início da semana (ex: segunda-feira)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('xp_earned_this_week')->default(0);
            $table->integer('rank_position')->nullable();
            
            // Adicionamos index no user_id para acelerar consultas do usuário no ranking
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_xp');
    }
};
