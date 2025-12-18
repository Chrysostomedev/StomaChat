<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friend;
use App\Models\Message; 
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
 public function index()
{
    $authId = Auth::id();

    // Amis où je suis demandeur
    $amis1 = Friend::where('demandeur_id', $authId)
        ->where('statut', Friend::STATUT_ACCEPTE)
        ->pluck('receveur_id')
        ->toArray();

    // Amis où je suis receveur
    $amis2 = Friend::where('receveur_id', $authId)
        ->where('statut', Friend::STATUT_ACCEPTE)
        ->pluck('demandeur_id')
        ->toArray();

    $amisIds = array_unique(array_merge($amis1, $amis2));

    $friends = User::whereIn('id', $amisIds)
        ->get()
        ->map(function($friend) use ($authId) {
            // Récupérer la conversation 1-1 avec cet ami
            $conversation = Conversation::where('est_groupe', false)
                ->whereHas('users', function($q) use ($authId) {
                    $q->where('users.id', $authId);
                })
                ->whereHas('users', function($q) use ($friend) {
                    $q->where('users.id', $friend->id);
                })
                ->first();

            if ($conversation) {
                // Dernier message de la conversation
                $friend->lastMessage = $conversation->messages()->latest()->first();

                // Nombre de messages non lus pour l'utilisateur connecté
                $friend->unreadCount = $conversation->messages()
                    ->where('expediteur_id', $friend->id)
                    ->where('lu', false)
                    ->count();
            } else {
                $friend->lastMessage = null;
                $friend->unreadCount = 0;
            }

            return $friend;
        });

    return view('friends.index', compact('friends'));
}


   public function suggestions()
{
    $authId = Auth::id();

    // Amis existants
    $amis1 = Friend::where('demandeur_id', $authId)
        ->where('statut', Friend::STATUT_ACCEPTE)
        ->pluck('receveur_id')
        ->toArray();

    $amis2 = Friend::where('receveur_id', $authId)
        ->where('statut', Friend::STATUT_ACCEPTE)
        ->pluck('demandeur_id')
        ->toArray();

    $amisIds = array_unique(array_merge($amis1, $amis2));

    // Demandes en attente
    $demandes1 = Friend::where('demandeur_id', $authId)
        ->where('statut', Friend::STATUT_EN_ATTENTE)
        ->pluck('receveur_id')
        ->toArray();

    $demandes2 = Friend::where('receveur_id', $authId)
        ->where('statut', Friend::STATUT_EN_ATTENTE)
        ->pluck('demandeur_id')
        ->toArray();

    $demandesIds = array_unique(array_merge($demandes1, $demandes2));

    // Exclusion
    $exclusions = array_unique(array_merge([$authId], $amisIds, $demandesIds));

    $suggestions = User::whereNotIn('id', $exclusions)
        ->inRandomOrder()
        ->take(10)
        ->get();

    // ✅ Retourne du HTML directement (pas du JSON)
    if (request()->ajax()) {
        return view('friends._suggestions', compact('suggestions'))->render();
    }

    return view('friends._suggestions', compact('suggestions'));
}


    /**
     *  Liste des demandes reçues
     */
    public function demandes()
    {
        $demandes = Friend::with('demandeur')
            ->where('receveur_id', Auth::id())
            ->where('statut', Friend::STATUT_EN_ATTENTE)
            ->orderByDesc('created_at')
            ->get();

        return view('friends.demandes', compact('demandes'));
    }

    /**
     * ➕ Envoyer une demande d’ami
     */
    public function envoyer($id)
{
    $demandeur = auth()->user();
    $receveur = User::findOrFail($id);

    // Vérifie si la demande existe déjà
    $existe = Friend::where(function ($q) use ($demandeur, $receveur) {
        $q->where('demandeur_id', $demandeur->id)
          ->where('receveur_id', $receveur->id);
    })->orWhere(function ($q) use ($demandeur, $receveur) {
        $q->where('demandeur_id', $receveur->id)
          ->where('receveur_id', $demandeur->id);
    })->first();

    if ($existe) {
        return response()->json([
            'success' => false,
            'error' => 'Demande déjà existante.'
        ]);
    }

    Friend::create([
        'demandeur_id' => $demandeur->id,
        'receveur_id'  => $receveur->id,
        'statut'       => Friend::STATUT_EN_ATTENTE
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Demande d’ami envoyée avec succès.'
    ]);
}

    public function accepter($id)
    {
        $demande = Friend::where('id', $id)
            ->where('receveur_id', Auth::id())
            ->where('statut', Friend::STATUT_EN_ATTENTE)
            ->firstOrFail();

        $demande->update(['statut' => Friend::STATUT_ACCEPTE]);

        return back()->with('success', 'Demande acceptée ✅');
    }

    /**
     * ❌ Refuser une demande
     */
    public function refuser($id)
    {
        $demande = Friend::where('id', $id)
            ->where('receveur_id', Auth::id())
            ->where('statut', Friend::STATUT_EN_ATTENTE)
            ->firstOrFail();

        $demande->delete();

        return back()->with('success', 'Demande refusée ❌');
    }
}
