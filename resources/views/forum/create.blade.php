@extends('layouts.app')
@section('content')
<div class="container py-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-pencil-square text-primary"></i> Nouvelle question
        </h4>
        <a href="{{ route('forum.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('forum.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                {{-- Titre --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-type"></i> Titre de la question
                    </label>
                    <input type="text" name="titre" class="form-control form-control-lg" placeholder="Ex: Comment fonctionne le Machine Learning ?" required>
                </div>

                {{-- Thématique --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-tags"></i> Choisis une thématique
                    </label>
                    <select name="thematique" class="form-select form-select-lg">
                        <option disabled selected> Sélectionne une thématique</option>
                        <option>Informatique</option>
                        <option>Hacking Ethique</option>
                        <option>Blockchain</option>
                        <option>Big Data & DataScience</option>
                        <option>AFRIQUE</option>
<option>Informatique</option>
                        <option>IA</option>
                        <option>Blockchain</option>
                        <option>Science</option>
                        <option>Art</option>

<option>Mathématiques</option>
                        <option>Robotique</option>
                        <option>Domotique & IoT</option>
                        <option>Dévéloppement web</option>
                        <option>Dévéloppement Personnel</option>
                        <option>Lecture</option>
                        <option>Actuariat & Statistiques</option>
                        <option>Physique</option>
                        <option>Medecine</option>
                    </select>
                </div>

                {{-- Contenu --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-chat-text"></i> Détaille ta question
                    </label>
                    <textarea name="contenu" rows="6" class="form-control" placeholder="Décris ton problème ou ta curiosité ici..." required></textarea>
                </div>

                {{-- Bouton --}}
                <div class="text-end">
                    <button class="btn btn-primary px-4 rounded-pill">
                        <i class="bi bi-send-fill"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
  background: var(--bs-body-bg);
  color: var(--bs-body-color);
  transition: all .3s ease;
}
.card:hover { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
</style>
@endsection
