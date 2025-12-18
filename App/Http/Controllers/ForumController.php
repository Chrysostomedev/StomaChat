<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ForumController extends Controller
{
    // 🧩 Page principale avec filtres
    public function index(Request $request)
    {
        $sort = $request->get('sort');
        $thematique = $request->get('thematique');

        $query = Question::with(['user', 'reponses']);

        // 🎯 Filtre par thématique
        if ($thematique) {
            $query->where('thematique', $thematique);
        }

        //  Application des filtres intelligents
       switch ($sort) {

    case 'populaires':
        $query->withCount('reponses')
              ->having('reponses_count', '>=', 1)
              ->where('favoris', '>=', 2)
              ->orderByDesc('favoris')
              ->orderByDesc('reponses_count');
        break;

    case 'recentes':
        $query->where('created_at', '>=', now()->subDays(1))
              ->latest();
        break;

    case 'non_resolues':
        $query->doesntHave('reponses')
              ->latest();
        break;

    //  Toutes les questions
    case 'toutes':
        $query->latest();
        break;

    default:
        $query->latest();
        break;
}

        $questions = $query->paginate(10);

        $thematiques = [
            'Afrique','Informatique','Actuariat & Statistiques', 'IA', 'Hacking Ethique',
            'Blockchain', 'Science', 'Art','Domotique & IoT', 'Robotique',
            'Dévéloppement web','Dévéloppement Personnel',  'Mathématiques',
            'Physique','Medecine','Lecture'
        ];

        return view('forum.index', compact('questions', 'thematiques'));
    }

    // 🧩 Créer une question
    public function create()
    {
        return view('forum.create');
    }

    // 💾 Enregistrer une question
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'thematique' => 'required|string|max:100',
        ]);

        $data['user_id'] = Auth::id();
        $data['favoris'] = 0;
        $data['vues'] = 0;

        Question::create($data);

        return redirect()->route('forum.index')->with('success', 'Question publiée avec succès ✅');
    }

    //  Affichage d’une question et de ses réponses
    public function show(Question $question, Request $request)
    {
        //  Comptage de vues unique par session
        $viewKey = 'viewed_question_' . $question->id;

        if (!session()->has($viewKey)) {
            $question->increment('vues');
            session()->put($viewKey, true);
        }

        $question->load('reponses.user');

        //  Récupérer les questions populaires : autres que celle affichée, avec critères
        $questionsPopulaires = Question::with('user')
            ->withCount('reponses')
            ->where('id', '!=', $question->id)
            ->having('reponses_count', '>=', 1)
            ->where('favoris', '>=', 2)
            ->orderByDesc('favoris')
            ->orderByDesc('reponses_count')
            ->take(5)
            ->get();

        return view('forum.show', compact('question', 'questionsPopulaires'));
    }

    // Supprimer une question
    public function destroy(Question $question)
    {
        if ($question->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $question->delete();
        return back()->with('success', 'Question supprimée');
    }

    // Basculer favoris
    public function toggleFavori(Request $request, Question $question)
    {
        $favKey = 'fav_' . $question->id;

        if (session()->has($favKey)) {
            session()->forget($favKey);
            $question->decrement('favoris');
            $isFav = false;
        } else {
            session()->put($favKey, true);
            $question->increment('favoris');
            $isFav = true;
        }

        return response()->json([
            'success' => true,
            'favoris' => $question->favoris,
            'isFav' => $isFav
        ]);
    }
}
