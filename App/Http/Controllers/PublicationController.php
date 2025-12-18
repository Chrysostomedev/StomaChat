<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Publication;
use App\Models\Question;
use App\Models\Friend;
use App\Models\User;
use App\Models\Sondage; // ✅ Import du modèle sondage
use App\Models\Commentaire;

class PublicationController extends Controller
{
  public function dashboard()
{
    $user = auth()->user();

    // 🔹 IDs des amis déjà acceptés
    $amisIds = Friend::where(function ($q) use ($user) {
            $q->where('demandeur_id', $user->id)
              ->orWhere('receveur_id', $user->id);
        })
        ->where('statut', Friend::STATUT_ACCEPTE)
        ->pluck('demandeur_id', 'receveur_id')
        ->flatten()
        ->toArray();

    // 🔹 IDs des demandes déjà envoyées
    $envoyeesIds = Friend::where('demandeur_id', $user->id)
        ->where('statut', Friend::STATUT_EN_ATTENTE)
        ->pluck('receveur_id')
        ->toArray();

    // 🔹 IDs des demandes déjà reçues
    $recuesIds = Friend::where('receveur_id', $user->id)
        ->where('statut', Friend::STATUT_EN_ATTENTE)
        ->pluck('demandeur_id')
        ->toArray();

    // 🔹 Fusion de tout ce qu’on doit exclure
    $exclusions = array_unique(array_merge($amisIds, $envoyeesIds, $recuesIds, [$user->id]));

    // 🔹 Suggestions finales
    $suggestions = User::whereNotIn('id', $exclusions)
        ->inRandomOrder()
        ->take(10)
        ->get();

    // 🔹 Publications & sondages
    $publications = Publication::with('user', 'commentaires.user')
        ->latest()
        ->take(5)
        ->get();

    $sondagesRecents = Sondage::with('user')
        ->latest()
        ->take(5)
        ->get();

    // 🔹 Questions populaires forum
    $questionsPopulaires = Question::with('user')
        ->withCount('reponses')
        ->orderByDesc('favoris')
        ->orderByDesc('reponses_count')
        ->take(3)
        ->get();

    return view('dashboard.index', compact(
        'publications',
        'suggestions',
        'sondagesRecents',
        'questionsPopulaires' // ✅ Ajouté pour _reponse.blade.php
    ));
}

    /**
     * Liste complète des publications
     */
    public function all()
    {
        $publications = Publication::with('user', 'commentaires.user')
            ->latest()
            ->paginate(10);

        return view('publications.all', compact('publications'));
    }

    /**
     *  Formulaire de création
     */
    public function create()
    {
        return view('publications.create');
    }

    /**
     *  Sauvegarde d’une nouvelle publication
     */
    public function store(Request $request)
    {
        $request->validate([
            'contenu' => 'nullable|string|max:1000',
            'media.*' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,gif,mp4,pdf',
        ]);

        $mediaFiles = [];

        if ($request->hasFile('media')) {
            $destination = public_path('uploads/publications');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($destination, $filename);
                $mediaFiles[] = 'uploads/publications/' . $filename;
            }
        }

        Publication::create([
            'user_id' => Auth::id(),
            'contenu' => $request->contenu,
            'media'   => $mediaFiles,
            'type'    => 'texte',
            'likes'   => 0
        ]);

        return redirect()->route('dashboard.index')
            ->with('success', 'Publication créée avec succès !');
    }

    
    public function download(Publication $publication, $fileIndex)
    {
        $file = $publication->media[$fileIndex] ?? null;
        if ($file && Storage::exists($file)) {
            return Storage::download($file);
        }
        return back()->withErrors('Fichier introuvable');
    }

  public function toggleCommentLike(Request $request, Commentaire $commentaire)
{
    $user = Auth::user();

    if ($commentaire->user_id === $user->id) {
        return response()->json(['error' => 'Vous ne pouvez pas liker votre propre commentaire.'], 403);
    }

    if ($commentaire->isLikedBy($user)) {
        $commentaire->likesUsers()->detach($user->id);
        $commentaire->decrement('likes');
        $liked = false;
    } else {
        $commentaire->likesUsers()->attach($user->id);
        $commentaire->increment('likes');
        $liked = true;
    }

    return response()->json([
        'liked' => $liked,
        'likes_count' => $commentaire->likes,
    ]);
}



    public function storeComment(Request $request, Publication $publication)
{
    $request->validate([
        'contenu' => 'required|string|max:500',
    ]);

    $comment = $publication->commentaires()->create([
        'user_id' => Auth::id(),
        'contenu' => $request->contenu,
        'likes' => 0,
    ]);

    $comment->load('user');

    return response()->json([
        'id' => $comment->id,
        'pseudo' => $comment->user->pseudo,
        'photo' => $comment->user->photo ? asset('storage/'.$comment->user->photo) : asset('images/default-avatar.png'),
        'contenu' => $comment->contenu,
        'likes' => $comment->likes,
    ]);
}


    /**
     *  Liker un commentaire
     */
    public function likeComment(Commentaire $commentaire)
    {
        $commentaire->increment('likes', 1);
        return back();
    }

    /**
     * ✅ Supprimer une publication
     */
    public function destroy(Publication $publication)
    {
        if ($publication->user_id !== Auth::id()) {
            return back()->withErrors("Vous ne pouvez supprimer que vos publications !");
        }

        if ($publication->media) {
            foreach ($publication->media as $file) {
                if (Storage::exists($file)) Storage::delete($file);
            }
        }

        $publication->delete();
        return back()->with('success', 'Publication supprimée avec succès !');
    }

    /**
     *  Supprimer un commentaire
     */
    public function destroyComment(Commentaire $commentaire)
    {
        if ($commentaire->user_id !== Auth::id()) {
            return back()->withErrors("Vous ne pouvez supprimer que vos commentaires !");
        }

        $commentaire->delete();
        return back()->with('success', 'Commentaire supprimé !');
    }
}
