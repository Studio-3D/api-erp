<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\TypeNotificationEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('relances_rdvs_appels', function (Blueprint $table) {
            $table->id();

            $table->integer('type')->comment('//1 relance //2 rdv');
            $table->integer('type_traitement')->default(0)->comment('//0 manuelle //1manuelle //2 automatique par visite//3 automatique par reservation 4// nouvel relance/rdv');
            $table->string('commentaire')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->enum('mode_relance',[TypeNotificationEnum::Sms->value,TypeNotificationEnum::Appel->value,TypeNotificationEnum::Email->value])->nullable();
            $table->date('date_relance')->nullable();
            $table->timestamp('rdv')->nullable();
            $table->foreignId('user_id_traite')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('traite_appel_id')->constrained('traitements_appels')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relances_rdvs_appels');
    }
};
