<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'pseudo', 'email', 'age', 'description', 'photo', 'centre_interet', 'profession', 'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = ['password'];

    // === Relations ===
    public function publications() {
        return $this->hasMany(Publication::class);
    }

    public function commentaires() {
        return $this->hasMany(Commentaire::class);
    }

    public function stories() {
        return $this->hasMany(Story::class);
    }

    public function messages() {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function conversations() {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    // Relations d’amis
    public function demandesEnvoyees() {
        return $this->hasMany(Friend::class, 'demandeur_id');
    }

    public function demandesRecues() {
        return $this->hasMany(Friend::class, 'receveur_id');
    }

    public function friends()
    {
        $amisDemandeur = Friend::where('demandeur_id', $this->id)
            ->where('statut', Friend::STATUT_ACCEPTE)
            ->pluck('receveur_id');

        $amisReceveur = Friend::where('receveur_id', $this->id)
            ->where('statut', Friend::STATUT_ACCEPTE)
            ->pluck('demandeur_id');

        $ids = $amisDemandeur->merge($amisReceveur)->unique();

        return User::whereIn('id', $ids);
    }

    // Conversation 1-1 avec un utilisateur
    public function conversationWith(User $user)
    {
        return $this->conversations()
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->where('est_groupe', false)
            ->first();
    }
}
