@extends('layouts.app')

@section('content')
<div class="story-create-container d-flex flex-column justify-content-between" style="height:100vh; background:#000; color:#fff;">

    {{-- Header avec flèche retour --}}
    <div class="d-flex align-items-center p-2" style="z-index:10;">
        <a href="{{ route('stories.index') }}" class="text-white fs-4 me-2"><i class="bi bi-chevron-left"></i></a>
        <span class="fw-bold">Faire une story</span>
    </div>

    <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" class="flex-grow-1 d-flex flex-column justify-content-between">
        @csrf

        {{-- Preview --}}
        <div id="storyPreview" class="flex-grow-1 d-flex justify-content-center align-items-center text-center px-3" 
             style="font-size:2rem; transition:all 0.3s; word-break:break-word; color:#fff; position:relative;">
            Entrez votre texte ou ajoutez une image ou déo...
        </div>

        {{-- Controls --}}
        <div class="p-3 d-flex justify-content-between align-items-center bg-dark bg-opacity-70">
            <input type="text" id="storyText" name="texte" class="form-control me-2" placeholder="Votre texte" style="flex:1; color:#fff; background:rgba(255,255,255,0.1); border:none; border-radius:8px; padding:0.5rem 1rem;">
            
            <input type="color" id="backgroundColor" name="background_color" value="#000000ff" class="me-2" style="width:40px;height:40px;border:none;padding:0;">

            <label for="storyMedia" class="btn btn-outline-light mb-0 me-2">
                <i class="bi bi-image"></i>
            </label>
            <input type="file" name="media[]" id="storyMedia" multiple accept="image/*,video/*" hidden>

            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
        </div>
    </form>
</div>

<script>
const storyText = document.getElementById('storyText');
const backgroundColor = document.getElementById('backgroundColor');
const storyPreview = document.getElementById('storyPreview');
const storyMedia = document.getElementById('storyMedia');

storyText.addEventListener('input', ()=>{
    // Si pas de média, on montre le texte
    if(!storyMedia.files.length){
        storyPreview.innerHTML = storyText.value || 'Entrez votre texte ou ajoutez déo  ou image...';
    }
});

backgroundColor.addEventListener('input', ()=>{
    storyPreview.style.backgroundColor = backgroundColor.value;
});

storyMedia.addEventListener('change', ()=>{
    const files = storyMedia.files;
    storyPreview.innerHTML = ''; // clear preview
    if(files.length){
        Array.from(files).forEach(file=>{
            const ext = file.type.split('/')[0];
            if(ext === 'image'){
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '100%';
                img.style.maxHeight = '80%';
                img.style.objectFit = 'contain';
                img.style.borderRadius = '8px';
                storyPreview.appendChild(img);
            } else if(ext === 'video'){
                const vid = document.createElement('video');
                vid.src = URL.createObjectURL(file);
                vid.controls = true;
                vid.style.maxWidth = '100%';
                vid.style.maxHeight = '80%';
                vid.style.borderRadius = '8px';
                storyPreview.appendChild(vid);
            }
        });
    } else {
        storyPreview.innerHTML = storyText.value || 'Tapez votre texte ou ajoutez un média...';
    }
});
</script>

<style>
.story-create-container input[type="text"]::placeholder { color: rgba(255,255,255,0.6); }
</style>
@endsection
