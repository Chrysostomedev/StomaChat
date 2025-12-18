<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('reponses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('question_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->longText('contenu');
        $table->integer('votes')->default(0);
        $table->integer('favoris')->default(0);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('reponses');
    }
};
