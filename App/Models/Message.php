<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model 
{
    protected $fillable = [
        'conversation_id',
        'expediteur_id',
        'contenu',
        'fichier',
        'modifie',
        'lu',
        'vue_unique'
    ];

    protected $casts = [
        'fichier' => 'array',
        'vue_unique' => 'boolean',
        'lu' => 'boolean'
    ];

    public function conversation() { return $this->belongsTo(Conversation::class); }
    public function expediteur() { return $this->belongsTo(User::class, 'expediteur_id'); }

    // --- Helpers pour simplifier la vue ---
    public function getIsMineAttribute(): bool
    {
        return auth()->id() === $this->expediteur_id;
    }

    public function isImage($path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif']);
    }

    public function isVideo($path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4','mov','avi']);
    }

    public function isPdf($path): bool
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
    }

    // --- Chiffrement contenu texte ---
    public function getContenuAttribute($value)
    {
        if (!$value) return null;
        try { return decrypt($value); }
        catch (\Exception $e) { return '[message illisible]'; }
    }

    public function setContenuAttribute($value)
    {
        $this->attributes['contenu'] = $value ? encrypt($value) : null;
    }
}
