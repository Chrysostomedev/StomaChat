@extends('layouts.app')

@section('content')
<style>
.card-sondage {
    background: rgba(255,255,255,0.9);
    border-radius: 12px;
    padding: 1rem;
}
body.dark-mode .card-sondage {
    background: rgba(255,255,255,0.6);
    border: 1px solid rgba(0,255,255,0.15);
}
.option-card {
    background: rgba(0,191,255,0.08);
    border: 1px solid rgba(0,191,255,0.2);
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: 0.3s ease;
}
.option-card:hover { background: rgba(0,191,255,0.15); transform: scale(1.02); }
.option-card.active {
    background: linear-gradient(90deg,#00FFFF,#007bff);
    color: #0B132B;
}
.text-cyan { color:#007bff; }
body.dark-mode .text-cyan { color:#00FFFF; }
.option-card.disabled {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="cover mb-4 rounded overflow-hidden shadow">
        @if($sondage->cover_image)
          <img src="{{ asset('storage/'.$sondage->cover_image) }}" alt="Cover" class="w-100">
        @endif
      </div>
      <h2 class="fw-bold text-cyan">{{ $sondage->titre }}</h2>
      <p class="opacity-75">{{ $sondage->description }}</p>

      <form id="vote-form" action="{{ route('sondage.vote', $sondage->id) }}" method="POST">
        @csrf
        <input type="hidden" name="option" id="option-input">
        <input type="hidden" name="from_show" value="1"> <!-- flag pour controller -->

        <div class="mt-4">
            @foreach($sondage->options as $i => $opt)
            <div class="option-card mb-3 d-flex align-items-center justify-content-between" data-index="{{ $i }}">
                <div class="d-flex align-items-center">
                    @if(!empty($opt['image']))
                    <img src="{{ asset('storage/'.$opt['image']) }}" class="me-3 rounded" width="45">
                    @endif
                    <strong>{{ $opt['label'] }}</strong>
                </div>
                <i class="bi bi-check2-circle text-cyan fs-4"></i>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-cyan w-100 mt-4">
            <i class="bi bi-send-check me-2"></i>Voter maintenant
        </button>
      </form>

      <!-- Zone pour afficher les messages -->
      <div id="vote-message" class="mt-3"></div>
    </div>

    <div class="col-lg-4">
      <div class="card-sondage">
        <p class="small text-muted">Créé par {{ $sondage->user->pseudo }}</p>
        <p class="small text-muted">Il y a {{ $sondage->created_at->diffForHumans() }}</p>

        <div class="mt-3">
          <i class="bi bi-clock text-cyan me-1"></i>Expire {{ $sondage->expires_at ? $sondage->expires_at->diffForHumans() : 'Jamais' }}
        </div>
        
        <div class="mt-3"><i class="bi bi-bar-chart-line text-cyan me-1"></i>
            <span id="total-votes">{{ $sondage->total_votes }}</span> votes
        </div>

        <div class="d-flex mt-4 gap-2 flex-wrap">
          <a href="{{ route('sondage.resultats', $sondage->id) }}" class="btn btn-sm btn-outline-info" title="Voir résultats"><i class="bi bi-graph-up"></i></a>
          @if(auth()->id() === $sondage->user_id)
            <form action="{{ route('sondage.destroy', $sondage->id) }}" method="POST" onsubmit="return confirm('Supprimer ce sondage ?')" style="display:inline-block;">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
            </form>
            <a href="{{ route('sondage.historique', auth()->id()) }}" class="btn btn-sm btn-outline-secondary" title="Mes sondages"><i class="bi bi-clock-history"></i></a>
          @endif
          <a href="{{ route('sondage.remix', $sondage->id) }}" class="btn btn-sm btn-outline-warning" title="Remixer"><i class="bi bi-magic"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('sondage.index') }}" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<script>
document.querySelectorAll('.option-card').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.option-card').forEach(o => o.classList.remove('active'));
        opt.classList.add('active');
        document.getElementById('option-input').value = opt.dataset.index;
    });
});

document.getElementById('vote-form').addEventListener('submit', function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let msgDiv = document.getElementById('vote-message');

        if(data.status === 'error'){
            msgDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        } else if(data.status === 'success'){
            msgDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;

            // Désactiver les options après vote
            document.querySelectorAll('.option-card').forEach(o => {
                o.classList.add('disabled');
            });

            // Mettre à jour le nombre total de votes
            if(data.stats && data.stats.total !== undefined){
                document.getElementById('total-votes').textContent = data.stats.total;
            }
        }
    })
    .catch(err => console.error(err));
});
</script>
@endsection
