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
        Schema::create('type_biens_appels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_bien_id')->constrained('type_biens')->onDelete('cascade');
            $table->foreignId('traite_appel_id')->constrained('traitements_appels')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_biens_appels');
    }
};
