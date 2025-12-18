@php
use Carbon\Carbon;
Carbon::setLocale('fr')
@endphp

<div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="fw-bold titre-populaire mb-3"><i class="bi bi-stars text-success"></i></i>Top Forum</h4>
         <div class="d-flex gap-2">
                <a href="{{ route('forum.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-grid"></i> Toutes
                </a>
                <a href="{{ route('forum.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-pencil-square"></i>poser
                </a>
            </div>
    </div>

    {{-- Conteneur scroll horizontal --}}
    <div class="questions-scroll d-flex gap-3 py-3">
        @php
            $populaires = $questionsPopulaires->filter(fn($q) => 
                ($q->reponses_count ?? $q->reponses->count()) >= 1 && $q->favoris >= 2
            );
        @endphp

        @forelse($populaires as $q)
            <div class="card border-0 shadow-sm rounded-4 flex-shrink-0" 
                 style="min-width: 260px; background: rgba(255,255,255,0.6); max-width: 320px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ $q->user->photo ? asset('storage/'.$q->user->photo) : asset('images/default-avatar.png') }}"
                             alt="avatar"
                             class="rounded-circle me-2"
                             width="36" height="36"
                             style="object-fit: cover;">
                        <div>
                            <strong>{{ $q->user->pseudo }}</strong>
                            <small class="text-muted d-block">
                                {{-- Affichage date en français avec heure --}}
                                Publié le {{ Carbon::parse($q->created_at)->translatedFormat('d F Y à H\hi') }}
                            </small>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-1">
                        <a href="{{ route('forum.show', $q->id) }}" 
                           class="text-decoration-none text-dark">
                            {{ Str::limit($q->titre, 60) }}
                        </a>
                    </h6>

                    <p class="small text-muted mb-2">{{ Str::limit($q->contenu, 100) }}</p>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-tag"></i> {{ $q->thematique }}
                        </span>
                       
                        <div class="d-flex align-items-center gap-3">
                            <span><i class="bi bi-eye text-secondary"></i> {{ $q->vues }}</span>

                            <a href="{{ route('forum.show', $q->id) }}" class="text-decoration-none text-primary">
                                <i class="bi bi-chat-dots"></i> {{ $q->reponses_count ?? $q->reponses->count() }}
                            </a>

                            <button class="btn btn-sm btn-outline-danger border-0 p-0 favori-btn" data-id="{{ $q->id }}">
                                <i class="bi {{ session()->has('fav_'.$q->id) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                                <span id="fav-count-{{ $q->id }}">{{ $q->favoris }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Aucune question populaire pour le moment.</p>
        @endforelse
    </div>
</div>

<style>
/* Scroll horizontal esthétique */
.questions-scroll {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    padding-bottom: 10px;

    -ms-overflow-style: none;  /* IE & Edge */
    scrollbar-width: none;     /* Firefox */
}
.questions-scroll::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Style général des cartes */
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
[data-bs-theme="dark"] .card {
    background-color: #1a1a2e;
    color: #ddd;
}
</style>
