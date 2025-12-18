<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReponseController extends Controller
{
    // enregistrement d'une réponse 
   public function store(Request $request, Question $question)
{
    $data = $request->validate([
        'contenu' => 'required|string|max:2000',
    ]);

    $data['user_id'] = Auth::id();
    $data['question_id'] = $question->id;

    $reponse = Reponse::create($data)->load('user');

    // ✅ Si requête AJAX, renvoyer les données JSON brutes
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'reponse' => [
                'contenu' => e($reponse->contenu),
                'auteur' => $reponse->user->pseudo,
                'photo' => $reponse->user->photo
                    ? asset('storage/'.$reponse->user->photo)
                    : asset('images/default-avatar.png'),
                'date' => $reponse->created_at->diffForHumans(),
            ]
        ]);
    }

    return back()->with('success', 'Réponse publiée avec succès ✅');
}

}
