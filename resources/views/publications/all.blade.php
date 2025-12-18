@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Toutes les publications</h2>
        <a href="{{ route('publication.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> 
        </a>
    </div>

    @forelse($publications as $pub)
    <div class="card mb-4 shadow-sm rounded-3 publication-card" style="background:rgba(255,255,255,0.6);">
        <div class="card-body">

            {{-- En-tête --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle me-2">
                        <img src="{{ $pub->user->photo ? asset('storage/'.$pub->user->photo) : asset('images/default-avatar.png') }}" 
                             class="user-avatar shadow-sm" width="40" height="40" style="object-fit: cover; border-radius:50%;">
                    </div>
                    <div>
                        <div class="fw-bold small text-main-title">{{ $pub->user->pseudo }}</div>
                        <small class="text-muted text-p">{{ $pub->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @if($pub->user_id === auth()->id())
                <form action="{{ route('publication.destroy', $pub->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette publication ?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </div>

            {{-- Contenu --}}
            @if($pub->contenu)
                <p class="text-p small mb-2">{{ $pub->contenu }}</p>
            @endif

            {{-- Médias --}}
            @if($pub->media)
            <div class="d-flex flex-wrap gap-2 mt-2">
                @foreach($pub->media as $i => $file)
                    @php
                        $path = is_string($file) ? $file : (string)$file;
                        $ext  = pathinfo($path, PATHINFO_EXTENSION);
                        $url  = asset($path);
                    @endphp

                    @if(in_array($ext,['jpeg','jpg','png','gif']))
                        <img src="{{ $url }}" class="img-thumbnail" style="width:150px; height:auto; border-radius:8px; cursor:pointer;" onclick="window.open('{{ $url }}','_blank')">
                    @elseif($ext === 'mp4')
                        <video width="250" controls style="border-radius:8px;">
                            <source src="{{ $url }}" type="video/mp4">
                        </video>
                    @elseif($ext === 'pdf')
                        <div class="pdf-container" data-pdf="{{ $url }}">
                            <iframe src="{{ $url }}#page=1" class="pdf-page"></iframe>
                           
                        </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- Actions --}}
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-light btn-sm action-btn" onclick="toggleComments({{ $pub->id }})">
                        <i class="bi bi-chat-dots"></i> <span>{{ $pub->commentaires->count() }}</span>
                    </button>
                </div>
            </div>

            {{-- Commentaires --}}
            <div class="mt-2" id="comments-{{ $pub->id }}" style="display:none;">
                @foreach($pub->commentaires as $comment)
                <div class="d-flex align-items-start mt-2">
                    <img src="{{ $comment->user->photo ? asset('storage/'.$comment->user->photo) : asset('images/default-avatar.png') }}"
                         alt="avatar"
                         class="rounded-circle me-2"
                         width="32" height="32"
                         style="object-fit: cover;">
                    <div class="flex-grow-1 bg-light rounded-3 p-2">
                        <strong>{{ $comment->user->pseudo }}</strong>
                        <p class="mb-1">{{ $comment->contenu }}</p>
                        <button type="button" class="btn btn-link p-0 text-danger comment-like-btn" 
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
                    <input type="text" class="form-control form-control-sm me-2 comment-input" 
                           placeholder="Écrire un commentaire..." 
                           data-publication-id="{{ $pub->id }}">
                    <button type="button" class="btn btn-primary btn-sm btn-comment-submit" data-publication-id="{{ $pub->id }}">
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
        <p class="text-muted text-center">Aucune publication pour le moment.</p>
    @endforelse

    {{-- <div class="d-flex justify-content-center mt-4">
        {{ $publications->links() }}
    </div> --}}
</div>

<script>
function toggleComments(pubId) {
    const el = document.getElementById('comments-' + pubId);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// ========================== EVENT DELEGATION ==========================
document.addEventListener('click', async function(e){
    // Like sur un commentaire
    const likeBtn = e.target.closest('.comment-like-btn');
    if(likeBtn){
        const commentId = likeBtn.dataset.id;
        const heartIcon = likeBtn.querySelector('i');
        const likeCount = likeBtn.querySelector('.like-count');
        try{
            const res = await fetch(`/commentaire/${commentId}/toggle-like`, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if(data.error) { alert(data.error); return; }
            likeBtn.dataset.liked = data.liked ? 'true' : 'false';
            heartIcon.classList.toggle('bi-heart-fill', data.liked);
            heartIcon.classList.toggle('bi-heart', !data.liked);
            likeCount.textContent = data.likes_count;
        }catch(err){console.error(err);}
    }

    // Envoi d'un commentaire
    const submitBtn = e.target.closest('.btn-comment-submit');
    if(submitBtn){
        const pubId = submitBtn.dataset.publicationId;
        const input = document.querySelector(`.comment-input[data-publication-id="${pubId}"]`);
        const contenu = input.value.trim();
        if(!contenu) return;
        try{
            const res = await fetch(`/publication/${pubId}/comment`, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({contenu})
            });
            const data = await res.json();
            // Ajouter le commentaire dans le DOM
            const commentsDiv = document.getElementById(`comments-${pubId}`);
            const div = document.createElement('div');
            div.className = 'd-flex align-items-start mt-2';
            div.innerHTML = `
                <img src="${data.photo}" alt="avatar" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                <div class="flex-grow-1 bg-light rounded-3 p-2">
                    <strong>${data.pseudo}</strong>
                    <p class="mb-1">${data.contenu}</p>
                    <button type="button" class="btn btn-link p-0 text-danger comment-like-btn" data-id="${data.id}" data-liked="false">
                        <i class="bi bi-heart"></i>
                        <span class="like-count">0</span>
                    </button>
                </div>
            `;
            commentsDiv.appendChild(div);
            input.value = '';
        }catch(err){console.error(err);}
    }

    // PDF navigation
    const pdfBtn = e.target.closest('.next-page, .prev-page');
    if(pdfBtn){
        const container = e.target.closest('.pdf-container');
        const iframe = container.querySelector('iframe');
        const pageNumber = container.querySelector('.page-number');
        let current = parseInt(pageNumber.textContent.replace(/\D/g, '')) || 1;
        if(pdfBtn.classList.contains('next-page')) current++;
        else if(pdfBtn.classList.contains('prev-page') && current>1) current--;
        iframe.src = container.dataset.pdf + '#page=' + current;
        pageNumber.textContent = 'Page ' + current;
    }
});

// Flash message 3s
setTimeout(() => {
    const flash = document.getElementById('flashMessage');
    if(flash) flash.style.display = 'none';
}, 3000);
</script>

<style>
.card { transition: transform 0.2s, box-shadow 0.2s; }
.card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
img.img-thumbnail, video { border-radius: 8px; object-fit: cover; cursor: pointer; }
.publication-card .comment-input { flex-grow:1; }
.pdf-page { width:100%; height:350px; border-radius:8px; border:none; }
.pdf-controls button { border-radius:50%; margin:0 4px; }
</style>
@endsection
