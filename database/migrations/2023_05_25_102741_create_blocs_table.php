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
        Schema::create('blocs', function (Blueprint $table) {
             $table->id();
            $table->string('nom');
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('tranche_id');
            $table->string('titre_foncier');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('projet_id')->references('id')->on('projets');
            $table->foreign('tranche_id')->references('id')->on('tranches');
            $table->foreignId('projet_id')->constrained('projets')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocs');
    }
};
