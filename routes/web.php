<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\SondageController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReponseController;
// use App\Http\Controllers\ChatBotController;

// Redirection vers le formulaire
Route::get('/', fn() => redirect()->route('register.form'));

// Formulaire inscription/connexion
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login-pseudo', [RegisterController::class, 'loginPseudo'])->name('login.pseudo');
// Vérification Ajax si le pseudo existe
Route::post('/check-pseudo', [RegisterController::class, 'checkPseudo'])->name('check.pseudo');

// Déconnexion
Route::post('/logout', [RegisterController::class, 'logout'])->name('logout');

// Dashboard 
Route::get('/dashboard', fn() => view('dashboard.index')) ->middleware('auth')->name('dashboard.index');

// profil et group
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // === PUBLICATIONS ===
    Route::get('/dashboard', [PublicationController::class, 'dashboard'])->name('dashboard.index');
    Route::get('/publication/create', [PublicationController::class, 'create'])->name('publication.create');
    Route::post('/publication/store', [PublicationController::class, 'store'])->name('publication.store');
    
    Route::get('/publications', [PublicationController::class, 'all'])->name('all.publication')->middleware('auth');
    Route::post('/publication/{publication}/comment', [PublicationController::class, 'storeComment'])->name('publication.comment');

    Route::get('/publication/{publication}/download/{fileIndex}', [PublicationController::class, 'download'])->name('publication.download');
    Route::delete('/publication/{publication}', [PublicationController::class, 'destroy'])->name('publication.destroy');
    Route::post('/commentaire/{commentaire}/toggle-like', [PublicationController::class, 'toggleCommentLike'])
    ->name('commentaire.toggle-like');


    //  === SONDAGES ===
    // Route resource personnalisée avec quelques extras
    Route::get('/sondage/{id}/resultats', [SondageController::class, 'resultats'])->name('sondage.resultats');
    Route::post('/sondage/{id}/vote', [SondageController::class, 'vote'])->name('sondage.vote');
    Route::get('/sondage/{id}/remix', [SondageController::class, 'remix'])->name('sondage.remix');
    Route::get('/sondage/historique/{user?}', [SondageController::class, 'historique'])->name('sondage.historique');

    //  routes CRUD  sondage
    Route::resource('sondage', SondageController::class)->except(['edit', 'update']);

// Gestion des amis
    Route::get('/amis', [App\Http\Controllers\FriendController::class, 'index'])->name('friends.index');
    Route::get('/demandes', [App\Http\Controllers\FriendController::class, 'demandes'])->name('friends.demandes');
    Route::get('/suggestions', [App\Http\Controllers\FriendController::class, 'suggestions'])->name('friends.suggestions');

    Route::post('/amis/envoyer/{id}', [App\Http\Controllers\FriendController::class, 'envoyer'])->name('friends.envoyer');
    Route::post('/amis/accepter/{id}', [App\Http\Controllers\FriendController::class, 'accepter'])->name('friends.accepter');
    Route::post('/amis/refuser/{id}', [App\Http\Controllers\FriendController::class, 'refuser'])->name('friends.refuser');

// Conversations
    Route::get('/conversations', [App\Http\Controllers\ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{user}', [App\Http\Controllers\ConversationController::class, 'show']) ->name('conversations.show');

    Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\MessageController::class, 'store'])
    ->name('messages.store');

Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
Route::put('/messages/{message}', [App\Http\Controllers\MessageController::class, 'update'])->name('messages.update');
Route::post('/messages/{message}/viewed', [MessageController::class, 'viewed'])->name('messages.viewed')
    ->middleware('auth');
// Marquer les messages comme lus
Route::post('/conversations/{conversation}/read', [App\Http\Controllers\MessageController::class, 'read'])->name('messages.read');
Route::get('/conversations/partials/message/{message}', function ($messageId) {
    $message = App\Models\Message::findOrFail($messageId);
    return view('conversations.partials.message', compact('message'));
})->name('conversations.partials.message');


// Stories

    Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
    Route::get('/stories/create', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/{story}', [StoryController::class, 'show'])->name('stories.show');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');
    Route::post('/stories/{story}/view', [StoryController::class, 'markAsViewed'])->name('stories.markAsViewed');


Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
Route::get('/forum/{question}', [ForumController::class, 'show'])->name('forum.show');
Route::post('/forum/{question}/favori', [ForumController::class, 'toggleFavori'])->name('forum.favori');
Route::delete('/forum/{question}', [ForumController::class, 'destroy'])->name('forum.destroy');
Route::post('/forum/{question}/reponse', [ReponseController::class, 'store'])->name('reponse.store');

//  chat bot
//  Route::get('/chatbot', [ChatBotController::class, 'index'])->name('chat.index');
//  Route::post('/chatbot/send', [ChatBotController::class, 'sendMessage'])->name('chat.send');
// });
