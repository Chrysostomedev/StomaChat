@extends('layouts.app')

@section('content')
<style>
/* Harmonisation clair/sombre */
.card-sondage {
    background: rgba(255,255,255,0.85);
    border: 1px solid rgba(25,25,112,0.1);
    border-radius: 12px;
    color: inherit;
    transition: 0.3s ease;
}
body.dark-mode .card-sondage {
    background: rgba(255,255,255,0.6);
    border: 1px solid rgba(0,255,255,0.2);
}
.card-sondage:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 15px rgba(0,191,255,0.25);
}

.btn-cyan {
    background: linear-gradient(90deg,#00FFFF,#0088FF);
    border: none;
    color: #0B132B;
    font-weight: 600;
    transition: 0.3s;
}
.btn-cyan:hover { transform: scale(1.05); box-shadow: 0 0 10px #00FFFF; }

.badge-theme {
    background: rgba(0,191,255,0.1);
    color: #007bff;
    font-weight: 600;
}
body.dark-mode .badge-theme {
    background: rgba(0,255,255,0.15);
    color: #00FFFF;
}
.text-cyan { color: #007bff; }
body.dark-mode .text-cyan { color: #00FFFF; }

.fade-in { animation: fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
</style>

<div class="container py-5 fade-in">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="fw-bold text-cyan">
      <i class="bi bi-graph-up-arrow me-2"></i>SONDAGE COMMUNAUTAIRE
    </h1>
    <a href="{{ route('sondage.create') }}" class="btn btn-cyan">
      <i class="bi bi-lightning-fill me-1"></i> Lancer un sondage
    </a>
  </div>

  {{-- Sondage du moment --}}
  @if($featured ?? false)
  <div class="card-sondage mb-4 p-4 text-center">
      <h4 class="fw-semibold mb-2 text-cyan"><i class="bi bi-stars me-2"></i>Sondage du moment</h4>
      <h2>{{ $featured->titre }}</h2>
      <p class="text-muted">{{ Str::limit($featured->description, 120) }}</p>
      <a href="{{ route('sondage.show', $featured->id) }}" class="btn btn-outline-primary mt-2">
          <i class="bi bi-arrow-right-circle me-1"></i> Découvrir
      </a>
  </div>
  @endif

  <div class="row g-4">
    @foreach($sondages as $sondage)
    <div class="col-md-6 col-lg-4">
      <div class="card-sondage p-3 h-100 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center">
          <span class="badge badge-theme">{{ $sondage->thematique ?? 'Général' }}</span>
          <small class="text-muted">{{ $sondage->created_at->diffForHumans() }}</small>
        </div>
        <h5 class="mt-3">{{ $sondage->titre }}</h5>
        <p class="small text-muted flex-grow-1">{{ Str::limit($sondage->description, 80) }}</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <div>
            <i class="bi bi-person-vote me-1 text-cyan"></i>{{ $sondage->total_votes }}
            <i class="bi bi-eye ms-3 me-1 text-cyan"></i>{{ $sondage->views }}
          </div>
          <a href="{{ route('sondage.show', $sondage->id) }}" class="btn btn-sm btn-cyan">
            <i class="bi bi-play-circle"></i>
          </a>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="mt-5 d-flex justify-content-center">
    {{ $sondages->links() }}
  </div>
</div>
@endsection
