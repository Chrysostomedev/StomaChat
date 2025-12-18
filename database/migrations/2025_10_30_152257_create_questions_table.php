<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('titre');
        $table->longText('contenu');
        $table->string('thematique'); // IA, Blockchain, etc.
        $table->integer('vues')->default(0);
        $table->integer('votes')->default(0);
        $table->integer('favoris')->default(0);
        $table->boolean('resolue')->default(false);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
