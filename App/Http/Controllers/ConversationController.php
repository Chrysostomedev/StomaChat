<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // Liste des conversations
    public function index() {
        $conversations = auth()->user()->conversations()->with('users', 'messages')->get();
        return view('conversations.index', compact('conversations'));
    }

    // Ouvrir conversation avec un ami
    public function show(User $user) {
        $auth = auth()->user();

        $conversation = $auth->conversations()
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->where('est_groupe', false)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['est_groupe' => false, 'cree_par' => $auth->id]);
            $conversation->users()->attach([$auth->id, $user->id]);
        }

        $conversation->load('messages.expediteur');

        return view('conversations.show', compact('conversation', 'user'));
    }
}
