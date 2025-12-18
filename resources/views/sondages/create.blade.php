@extends('layouts.app')

@section('content')
<style>
.form-container {
    background: rgba(255,255,255,0.9);
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid rgba(0,0,0,0.05);
}
body.dark-mode .form-container {
    background: rgba(255,255,255,0.6);
    border: 3px solid rgba(106, 228, 228, 0.95);
}
.border-cyan { border-color: #00bcd4 !important; }
.text-cyan { color: #007bff; }
body.dark-mode .text-cyan { color: #00FFFF; }
.btn-cyan {
    background: linear-gradient(90deg,#00FFFF,#0088FF);
    color: #0B132B;
    font-weight: 600;
    border:none;
}
.btn-cyan:hover { transform: scale(1.05); box-shadow: 0 0 10px #00FFFF; }
</style>

<div class="container py-5">
  <h1 class="fw-bold text-cyan mb-4"><i class="bi bi-plus-circle me-2"></i>Créer un nouveau Pulse</h1>

  <form action="{{ route('sondage.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
    @csrf
    <div class="row g-4">
      <div class="col-md-6">
        <label class="form-label"><i class="bi bi-type me-1"></i> Titre</label>
        <input type="text" name="titre" class="form-control border-cyan"
          placeholder="Ex: L’IA remplacera-t-elle les devs ?" value="{{ old('titre') }}" required>

        <label class="form-label mt-3"><i class="bi bi-chat-dots me-1"></i> Description</label>
        <textarea name="description" rows="4" class="form-control border-cyan"
          placeholder="Explique ton idée...">{{ old('description') }}</textarea>

        <label class="form-label mt-3"><i class="bi bi-tag me-1"></i> Thématique</label>
        <select name="thematique" class="form-select border-cyan">
          @foreach(['Tech','Culture','Startups','Gaming'] as $theme)
            <option value="{{ $theme }}">{{ $theme }}</option>
          @endforeach
        </select>

        <label class="form-label mt-3"><i class="bi bi-image me-1"></i> Image de couverture</label>
        <input type="file" name="cover_image" class="form-control border-cyan">
      </div>

      <div class="col-md-6">
        <label class="form-label"><i class="bi bi-list-check me-1"></i> Options</label>
        <div id="options-list">
          <div class="input-group mb-2">
            <input name="options[0][label]" class="form-control" placeholder="Option 1">
          </div>
          <div class="input-group mb-2">
            <input name="options[1][label]" class="form-control" placeholder="Option 2">
          </div>
        </div>
        <button type="button" id="add-option" class="btn btn-sm btn-outline-primary mt-2">
          <i class="bi bi-plus-lg me-1"></i> Ajouter option
        </button>

        <hr class="my-4">

        <label class="form-label"><i class="bi bi-clock me-1"></i> Durée</label>
        <select name="expires_in" class="form-select border-cyan">
          <option value="24">24h</option>
          <option value="72">3 jours</option>
          <option value="168">1 semaine</option>
        </select>
      </div>
    </div>

    <div class="mt-4">
      <button class="btn btn-cyan px-4"><i class="bi bi-send-fill me-2"></i>Publier</button>
    </div>
  </form>
</div>

<script>
document.getElementById('add-option').addEventListener('click', () => {
  let i = document.querySelectorAll('#options-list .input-group').length;
  let group = document.createElement('div');
  group.classList.add('input-group','mb-2');
  group.innerHTML = `<input name="options[${i}][label]" class="form-control" placeholder="Option ${i+1}">`;
  document.getElementById('options-list').appendChild(group);
});
</script>
@endsection
