<?php

// app/Models/Friend.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $fillable = ['demandeur_id', 'receveur_id', 'statut'];

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_ACCEPTE = 'accepte';
    const STATUT_REFUSE = 'refuse';

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function receveur()
    {
        return $this->belongsTo(User::class, 'receveur_id');
    }
}

