@extends('layouts.app')

@section('content')
<div class="container py-3">

    {{-- Message flash global --}}
    @if(session('success'))
        <div class="flash-message show" id="flashMessage">
            {{ session('success') }}
        </div>
    @endif

    {{-- Fil d’actualité --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-main-title"><i class="bi bi-newspaper"></i> Fil d’actualité</h4>
    </div>

    {{-- Suggestions d’amis --}}
    @auth
        <div class="dashboard-section mb-4 horizontal-scroll" id="suggestions-container">
            @include('friends._suggestions')
        </div>
    @endauth

    @php
        use Carbon\Carbon;
        Carbon::setLocale('fr');
    @endphp

    {{-- Publications --}}
    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h4 class="fw-bold mb-0 text-main-title mb-3"><i class="bi bi-stars text-success"></i>Top posts</h4>

        <div>
            <a href="{{ route('all.publication') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-grid"></i> Tous
            </a>
            <a href="{{ route('publication.create') }}" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-pencil-square"></i> Publier
            </a>
        </div>
    </div>

    <div class="publications-container horizontal-scroll mb-4">
        @forelse($publications as $pub)
            <div class="publication-card">
                {{-- En-tête --}}
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar-circle me-2">
                        <img src="{{ $pub->user->photo ? asset('storage/'.$pub->user->photo) : asset('images/default-avatar.png') }}" 
                             class="user-avatar shadow-sm">
                    </div>
                    <div>
                        <div class="fw-bold small text-main-title">{{ $pub->user->pseudo }}</div>
                        <small class="text-muted text-p">
                            Publié {{ $pub->created_at->translatedFormat('d F Y à H:i') }}
                        </small>
                    </div>
                    @if($pub->user_id === auth()->id())
                        <form action="{{ route('publication.destroy', $pub->id) }}" method="POST" class="ms-auto" onsubmit="return confirm('Supprimer cette publication ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                </div>

                {{-- Contenu --}}
                @if($pub->contenu)
                    <p class="text-p small mb-2">{{ $pub->contenu }}</p>
                @endif

                {{-- Médias --}}
                @if($pub->media)
                    @foreach($pub->media as $i => $file)
                        @php
                            $path = is_string($file) ? $file : (string)$file;
                            $ext  = pathinfo($path, PATHINFO_EXTENSION);
                            $url  = asset($path);
                        @endphp
                        @if(in_array($ext, ['jpeg','jpg','png','gif']))
                            <img src="{{ $url }}" class="media-preview">
                        @elseif($ext === 'mp4')
                            <video controls class="media-preview">
                                <source src="{{ $url }}" type="video/mp4">
                            </video>
                        @elseif($ext === 'pdf')
                            <div class="pdf-container" data-pdf="{{ $url }}">
                                <iframe src="{{ $url }}#page=1" class="pdf-page"></iframe>
                                <div class="pdf-controls mt-2 text-center">
                                    <span class="page-number mx-2"></span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Actions --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button class="btn btn-light btn-sm action-btn" onclick="toggleComments({{ $pub->id }})">
                        <i class="bi bi-chat-dots"></i> <span>{{ $pub->commentaires->count() }}</span>
                    </button>
                </div>

                {{-- Commentaires --}}
                <div class="mt-3 comments" id="comments-{{ $pub->id }}" style="display:none;">
                    @foreach($pub->commentaires as $comment)
                        <div class="d-flex align-items-start mt-3">
                            <img src="{{ $comment->user->photo ? asset('storage/'.$comment->user->photo) : asset('images/default-avatar.png') }}"
                                 alt="avatar"
                                 class="rounded-circle me-2"
                                 width="32" height="32"
                                 style="object-fit: cover;">

                            <div class="flex-grow-1 bg-light rounded-3 p-2">
                                <strong>{{ $comment->user->pseudo }}</strong>
                                <p class="mb-1">{{ $comment->contenu }}</p>

                                <button 
                                    type="button" 
                                    class="btn btn-link p-0 text-danger comment-like-btn" 
                                    data-id="{{ $comment->id }}" 
                                    data-liked="{{ $comment->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                                    <i class="bi bi-heart{{ $comment->isLikedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                    <span class="like-count">{{ $comment->likes }}</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    {{-- Formulaire pour poster un commentaire --}}
                    <div class="mt-2 d-flex align-items-center">
                        <input type="text" 
                               class="form-control form-control-sm me-2 comment-input" 
                               placeholder="Écrire un commentaire..." 
                               data-publication-id="{{ $pub->id }}">
                        <button type="button" class="btn btn-primary btn-sm btn-comment-submit" data-publication-id="{{ $pub->id }}">
                            Envoyer
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-p text-muted">Aucune publication pour le moment.</p>
        @endforelse
    </div>

    {{-- Questions populaires --}}
    @include('forum.partials._reponse', ['questionsPopulaires' => $questionsPopulaires])

    {{-- Sondages --}}
    <div class="sondage-preview my-5 p-3 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            <h4 class="fw-bold mb-0 text-main-title d-flex align-items-center"><i class="bi bi-stars text-success"></i>Top Sondages</h4>

            <div class="d-flex gap-2">
                <a href="{{ route('sondage.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-grid"></i>Tous
                </a>
                <a href="{{ route('sondage.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-pencil-square"></i> Lancer
                </a>
            </div>
        </div>

        <div class="d-flex gap-3 horizontal-scroll pb-2">
            @forelse($sondagesRecents as $sondage)
                <div class="card sondage-card flex-shrink-0">
                    <div class="card-body">
                        <h6 class="fw-bold text-main-title mb-1">
                            <i class="bi bi-clipboard-data text-info me-1"></i> {{ $sondage->thematique }}
                        </h6>
                        <p class="small text-p">{{ Str::limit($sondage->objectif, 60) }}</p>

                        <a href="{{ route('sondage.show', $sondage->id) }}" 
                           class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-graph-up-arrow"></i> Participer
                        </a>
                    </div>

                    <div class="d-flex align-items-center mt-2">
                        <div class="avatar-circle me-2">
                            <img src="{{ $sondage->user->photo ? asset('storage/'.$sondage->user->photo) : asset('images/default-avatar.png') }}" 
                                 class="user-avatar shadow-sm">
                        </div>
                        <div class="fw-bold small text-main-title">{{ $sondage->user->pseudo }}</div>
                    </div>
                    <div class="text-muted small"><i class="bi bi-clock"></i> {{ $sondage->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-p text-muted">Aucun sondage pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ========================== FLOATING CHATBOT ========================== --}}
{{-- <div id="chatbot-fab" title="Discuter avec StomaCp">
    <i class="bi bi-chat-dots-fill"></i>
</div>

<div id="chatbot-popup" class="chatbot-hidden">
    <div class="chatbot-header d-flex justify-content-between align-items-center px-1 py-1">
        <div class="fw-bold text-white d-flex align-items-center gap-2">
            <i class="bi bi-robot"></i> StomaCp
        </div>
        <button id="chatbot-close" class="btn btn-sm btn-light">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <iframe src="{{ route('chat.index') }}" class="chatbot-frame"></iframe>
</div> --}}

{{-- ========================== SCRIPT ========================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // === FLASH MESSAGE AUTO-HIDE ===
    const flash = document.getElementById('flashMessage');
    if (flash) setTimeout(() => flash.style.display = 'none', 3000);

    // === TOGGLE CHATBOT ===
    /*const chatbotFab = document.getElementById('chatbot-fab');
    const chatbotPopup = document.getElementById('chatbot-popup');
    const chatbotClose = document.getElementById('chatbot-close');

    chatbotFab.addEventListener('click', () => {
        chatbotPopup.classList.toggle('chatbot-show');
        chatbotPopup.classList.remove('chatbot-hidden');
    });

    chatbotClose.addEventListener('click', () => {
        chatbotPopup.classList.remove('chatbot-show');
        chatbotPopup.classList.add('chatbot-hidden');
    });
    */
});



setTimeout(() => {
    const flash = document.getElementById('flashMessage');
    if (flash) flash.style.display = 'none';
}, 3000);

function toggleComments(id) {
    const el = document.getElementById('comments-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// Gestion navigation PDF
document.addEventListener('click', function(e) {
    if (e.target.closest('.next-page') || e.target.closest('.prev-page')) {
        const container = e.target.closest('.pdf-container');
        const iframe = container.querySelector('iframe');
        const pageNumber = container.querySelector('.page-number');
        let current = parseInt(pageNumber.textContent.replace(/\D/g, '')) || 1;
        if (e.target.closest('.next-page')) current++;
        else if (e.target.closest('.prev-page') && current > 1) current--;
        iframe.src = container.dataset.pdf + '#page=' + current;
        pageNumber.textContent = 'Page ' + current;
    }
});

// LIKE COMMENTAIRE AJAX
document.querySelectorAll('.comment-like-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const commentId = this.dataset.id;
        const isLiked = this.dataset.liked === 'true';
        const heartIcon = this.querySelector('i');
        const likeCount = this.querySelector('.like-count');

        fetch(`/commentaire/${commentId}/toggle-like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            this.dataset.liked = data.liked ? 'true' : 'false';
            heartIcon.classList.toggle('bi-heart', !data.liked);
            heartIcon.classList.toggle('bi-heart-fill', data.liked);
            likeCount.textContent = data.likes_count;
        });
    });
});

// COMMENTAIRE AJAX
document.querySelectorAll('.btn-comment-submit').forEach(btn => {
    btn.addEventListener('click', function() {
        const pubId = this.dataset.publicationId;
        const input = document.querySelector(`.comment-input[data-publication-id='${pubId}']`);
        const contenu = input.value.trim();
        if (!contenu) return;

        fetch(`/publication/${pubId}/comment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contenu })
        })
        .then(res => res.json())
        .then(data => {
            const commentsContainer = document.getElementById(`comments-${pubId}`);
            const commentHTML = `
                <div class="d-flex align-items-start mt-3">
                    <img src="${data.photo}" alt="avatar" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                    <div class="flex-grow-1 bg-light rounded-3 p-2">
                        <strong>${data.pseudo}</strong>
                        <p class="mb-1">${data.contenu}</p>
                        <button type="button" class="btn btn-link p-0 text-danger comment-like-btn" 
                            data-id="${data.id}" data-liked="false">
                            <i class="bi bi-heart"></i>
                            <span class="like-count">0</span>
                        </button>
                    </div>
                </div>
            `;
            commentsContainer.insertAdjacentHTML('beforeend', commentHTML);
            input.value = '';
        });
    });
});

</script>

{{-- ========================== Styles ========================== --}}
<style>
.flash-message {
    position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%);
    background: #018613; color: white; padding: 10px 25px; border-radius: 25px;
    font-weight: 500; box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    opacity: 0; transition: opacity 0.4s ease; z-index: 9999;
}
.flash-message.show { opacity: 1; }

.horizontal-scroll { display: flex; gap: 15px; overflow-x: auto; scrollbar-width: none; }
.horizontal-scroll::-webkit-scrollbar { display: none; }

.publication-card {
    flex: 0 0 330px; background: rgba(255,255,255,0.6);
    border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 15px; transition: all 0.3s ease;
}
.publication-card:hover { transform: translateY(-3px); }

.avatar-circle { width: 42px; height: 42px; border-radius: 50%; overflow: hidden; }
.user-avatar { width: 100%; height: 100%; object-fit: cover; }

.media-preview { width: 100%; border-radius: 10px; margin-top: 5px; cursor: pointer; transition: transform 0.2s; }
.media-preview:hover { transform: scale(1.02); }

.text-main-title { color: #0b2150; }
[data-bs-theme="dark"] .text-main-title { color: #22f6f6 !important; }

.text-p { color: #04083a; }
[data-bs-theme="dark"] .text-p { color: #fff !important; }

.sondage-card {
    flex: 0 0 240px; background: rgba(255,255,255,0.6);
    border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 15px; transition: all 0.3s ease;
}
.sondage-card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(0,0,0,0.15); }

.btn-outline-primary:hover, .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 8px rgba(91,192,190,0.3);
}

/* === Floating Chatbot Button === */
#chatbot-fab {
    position: fixed;
    bottom: 80px;
    right: 30px;
    background: linear-gradient(135deg, #0a072eff, #46e6f1ff);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(13, 7, 65, 0.92);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1050;
    font-size: 1.6rem;
}
#chatbot-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 18px rgba(0,0,0,0.3);
}

/* === Chatbot Popup === */
#chatbot-popup {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 400px;
    height: 550px;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    overflow: hidden;
    transform: translateY(20px);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 2000;
}
#chatbot-popup.chatbot-show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
#chatbot-popup.chatbot-hidden {
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
}

/* === Header === */
.chatbot-header {
    background: linear-gradient(135deg, #0078ff, #00c4ff);
    height: 55px;
}
.chatbot-header .btn {
    border: none;
    background: rgba(255,255,255,0.8);
    transition: all 0.2s;
}
.chatbot-header .btn:hover {
    background: white;
}

/* === Iframe === */
.chatbot-frame {
    width: 100%;
    height: calc(100% - 55px);
    border: none;
    background: transparent;
}
</style>
@endsection
