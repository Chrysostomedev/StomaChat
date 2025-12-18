@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 800px; background: transparent">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 800px; background: rgba(255,255,255,0.6);">
        <div class="card-header text-blue py-3">
             <h2 class="mb-4" style="color: rgba(22, 16, 78, 0.94);">Créer une publication</h2>
        </div>

        <div class="card-body p-4">

            <!-- Flash message -->
            @if(session('success'))
                <div class="alert alert-success text-center" id="flashMessage">{{ session('success') }}</div>
            @endif

            <form action="{{ route('publication.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                @csrf

                <!-- Zone de texte -->
                <div class="mb-4 position-relative">
                    <textarea 
                        name="contenu" 
                        class="form-control form-control-lg rounded-4 shadow-sm ps-5" 
                        placeholder="Exprime-toi... Quoi de neuf aujourd'hui ?" 
                        rows="3" 
                        required>{{ old('contenu') }}</textarea>
                    <i class="bi bi-chat-text text-secondary position-absolute" style="left:15px; top:18px; font-size:1.4rem;"></i>
                </div>

                <!-- Zone des icônes d'ajout -->
                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                    <label for="mediaInput" class="btn btn-outline-info rounded-pill d-flex align-items-center gap-2">
                        <i class="bi bi-image-fill"></i> <span>Photo / Vidéo / PDF</span>
                    </label>
                    <small class="text-muted">Vous pouvez ajouter plusieurs fichiers (max 10)</small>
                </div>

                <input type="file" name="media[]" multiple accept="image/*,video/mp4,.pdf" class="d-none" id="mediaInput">

                <!-- Aperçu médias -->
                <div id="mediaPreview" class="d-flex flex-wrap gap-3 mb-4"></div>

                <!-- Bouton Publier -->
                <div class="text-center">
                    <button type="submit" class="btn btn-lg text-white px-5 py-2 rounded-pill shadow-sm" 
                        style="background: linear-gradient(90deg, #2ef8f5, #090546); border:none;">
                        <i class="bi bi-send-fill me-2"></i>Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Masquer le message flash après 3s
    setTimeout(() => {
        const flash = document.getElementById('flashMessage');
        if (flash) flash.style.opacity = '0';
    }, 3000);

    // Preview des médias
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.getElementById('mediaPreview');

    mediaInput.addEventListener('change', function() {
        mediaPreview.innerHTML = '';
        const files = this.files;

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            const ext = file.name.split('.').pop().toLowerCase();

            reader.onload = function(e) {
                let element;

                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    element = document.createElement('img');
                    element.src = e.target.result;
                    element.className = "rounded-3 shadow-sm media-thumb";
                } 
                else if (ext === 'mp4') {
                    element = document.createElement('video');
                    element.src = e.target.result;
                    element.controls = true;
                    element.className = "rounded-3 shadow-sm media-thumb";
                } 
                else if (ext === 'pdf') {
                    element = document.createElement('div');
                    element.innerHTML = `<div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-2">
                        <i class='bi bi-file-earmark-pdf text-danger fs-4'></i> 
                        <span>${file.name}</span>
                    </div>`;
                }

                mediaPreview.appendChild(element);
            };

            reader.readAsDataURL(file);
        });
    });
</script>

<style>
    textarea::placeholder {
        color: #888;
        font-style: italic;
    }

    .media-thumb {
        width: 140px;
        height: 120px;
        object-fit: cover;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .media-thumb:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0,0,0,0.25);
    }

    .btn-outline-info {
        border: 1px solid #40c2da;
        color: #0a0240;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        background: linear-gradient(90deg, #0a0240, #40c2da);
        color: #fff;
        border: none;
        box-shadow: 0 0 10px rgba(64,194,218,0.6);
    }
</style>
@endsection
