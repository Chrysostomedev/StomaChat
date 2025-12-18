<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demandeur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receveur_id')->constrained('users')->onDelete('cascade');
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('friends');
    }
};
