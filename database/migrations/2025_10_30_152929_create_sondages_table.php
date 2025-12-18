<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSondagesTable extends Migration
{
    public function up()
    {
        Schema::create('sondages', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relation
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Métadonnées
            $table->string('titre');                  // titre court du sondage
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();  // objectif, contexte
            $table->string('thematique')->nullable(); // ex: "Tech", "Culture", "Startups"
            $table->string('cover_image')->nullable(); // url ou path de l'image
            $table->string('lang')->default('fr');    // langue du sondage

            // Options & votes (JSON)
            $table->json('options');   // tableau d'options : [{ "label":"Oui", "image":null }, ...]
            $table->json('votes')->nullable(); // { "0": 10, "1": 5 }
            $table->unsignedInteger('total_votes')->default(0);

            // Paramètres avancés
            $table->boolean('allow_multiple')->default(false);    // plusieurs choix autorisés
            $table->boolean('anonymous')->default(false);         // votes anonymes (pas d'enregistrement d'ids)
            $table->boolean('reactions_enabled')->default(true); // emojis en plus du vote
            $table->boolean('is_public')->default(true);          // visible par tous ou seulement amis
            $table->boolean('pinned')->default(false);           // épinglé dans le feed

            // Tracking & engagement
            $table->json('tags')->nullable();     // ex: ["IA","remote"]
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedInteger('boost_count')->default(0); // si modérateur/promotion
            $table->double('trending_score')->default(0); // calcul interne pour feed

            // Remise & remix
            $table->boolean('allow_remix')->default(true);
            $table->foreignId('remixed_from_id')->nullable()->constrained('sondages')->onDelete('set null');

            // Expiration
            $table->timestamp('expires_at')->nullable();

            // Optionnel : enregistrement des voters (si anonymous = false)
            $table->json('voter_ids')->nullable(); // stocke user ids ou tokens (attention privacy)

            $table->timestamps();
            $table->softDeletes();

            // Indexes utiles
            $table->index(['thematique', 'created_at']);
            $table->index('trending_score');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sondages');
    }
}
