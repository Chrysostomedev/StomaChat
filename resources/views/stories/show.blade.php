@extends('layouts.app')

@section('content')
<div class="story-show-container position-relative" style="background:#000; width:100%; height:100vh; overflow:hidden;">

    <div id="storyContent" class="h-100 w-100 d-flex flex-column justify-content-between align-items-center text-center position-relative text-white">

        {{-- Barre supérieure avec photo, pseudo, flèche retour, pause, supprimer --}}
        <div class="story-top-bar position-absolute top-0 w-100 d-flex justify-content-between align-items-center p-2" style="z-index:20; background:rgba(0,0,0,0.3);">
            
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('stories.index') }}" class="text-white fs-4"><i class="bi bi-arrow-left"></i></a>
                <img src="{{ $userStories[0]->user->photo ? asset('storage/'.$userStories[0]->user->photo) : asset('images/default-user.png') }}" 
                     class="rounded-circle" style="width:40px;height:40px;object-fit:cover;border:2px solid #fff;">
                <span class="fw-bold">{{ $userStories[0]->user->pseudo }}</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if($userStories[0]->user_id === auth()->id())
                    <form action="{{ route('stories.destroy', $userStories[0]->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-light"><i class="bi bi-trash"></i></button>
                    </form>
                @endif
                <button id="pauseStory" class="btn btn-sm btn-outline-light"><i id="pauseIcon" class="bi bi-pause-fill"></i></button>
            </div>

            {{-- Heure de la story centrée --}}
            <div class="story-time position-absolute top-50 start-50 translate-middle text-white" style="z-index:21;">
                {{ \Carbon\Carbon::parse($userStories[0]->created_at)->locale('fr')->diffForHumans() }}
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="story-progress-bar position-absolute top-0 start-0 w-100 d-flex p-1" style="gap:4px; z-index:21;">
            @foreach($storiesForJS as $s)
                <div class="progress-segment flex-grow-1 bg-white bg-opacity-50 rounded" style="height:3px;"></div>
            @endforeach
        </div>

        {{-- Affichage story --}}
        <div id="storyDisplay" class="flex-grow-1 d-flex justify-content-center align-items-center w-100 position-relative"></div>
 {{-- Vue compteur œil en bas à gauche --}}
        <div class="story-views position-absolute bottom-3 start-3 d-flex align-items-center gap-1 px-2 py-1" 
             style="z-index:30; background:rgba(0,0,0,0.4); border-radius:12px; color:#fff; font-size:0.9rem;">
            <i class="bi bi-eye"></i>
            <span id="viewsCount">{{ $userStories[0]->vuesCount() }}</span>
        </div>

        {{-- Navigation flèches centrées --}}
        <button id="prevStory" class="story-nav-btn position-absolute top-50 start-0 translate-middle-y btn btn-light btn-sm"><i class="bi bi-chevron-left"></i></button>
        <button id="nextStory" class="story-nav-btn position-absolute top-50 end-0 translate-middle-y btn btn-light btn-sm"><i class="bi bi-chevron-right"></i></button>

    </div>
</div>

<script>
const stories = @json($storiesForJS);
let currentIndex = 0;
let storyTimer = null;
let isPaused = false;

const display = document.getElementById('storyDisplay');
const segments = document.querySelectorAll('.progress-segment');
const pauseIcon = document.getElementById('pauseIcon');
const viewsCountEl = document.getElementById('viewsCount');

function showStory(index){
    const s = stories[index];
    display.innerHTML = '';

    // Reset segments
    segments.forEach((seg,i)=>seg.style.width=i<index?'100%':'0%');

    let duration = 5000; // texte 5s par défaut

    // Si media
    if(s.media.length>0){
        const first = s.media[0];
        const ext = first.split('.').pop().toLowerCase();

        if(['jpg','jpeg','png','gif'].includes(ext)){
            const img = document.createElement('img');
            img.src = '/storage/' + first;
            img.style.maxWidth='100%';
            img.style.maxHeight='100%';
            img.style.objectFit='cover';
            display.appendChild(img);

            // texte sur image
            if(s.texte){
                const textDiv = document.createElement('div');
                textDiv.textContent = s.texte;
                textDiv.style.position = 'absolute';
                textDiv.style.bottom = '10%';
                textDiv.style.left = '50%';
                textDiv.style.transform = 'translateX(-50%)';
                textDiv.style.color = '#fff';
                textDiv.style.fontSize='1.5rem';
                textDiv.style.textShadow = '1px 1px 5px rgba(0,0,0,0.7)';
                display.appendChild(textDiv);
            }

            duration = 10000; // 10s pour image
            startTimer(duration);

        } else if(ext==='mp4'){
            const vid = document.createElement('video');
            vid.src = '/storage/' + first;
            vid.autoplay = true;
            vid.controls = false;
            vid.style.maxWidth='100%';
            vid.style.maxHeight='100%';
            vid.style.objectFit='cover';
            display.appendChild(vid);

            if(s.texte){
                const textDiv = document.createElement('div');
                textDiv.textContent = s.texte;
                textDiv.style.position = 'absolute';
                textDiv.style.bottom = '10%';
                textDiv.style.left = '50%';
                textDiv.style.transform = 'translateX(-50%)';
                textDiv.style.color = '#fff';
                textDiv.style.fontSize='1.5rem';
                textDiv.style.textShadow = '1px 1px 5px rgba(0,0,0,0.7)';
                display.appendChild(textDiv);
            }

            vid.onloadedmetadata = ()=>{
                duration = Math.min(vid.duration*1000, 30000);
                startTimer(duration);
            }
        }
    } else {
        // texte seul
        const div = document.createElement('div');
        div.textContent = s.texte || '';
        div.style.width='100%';
        div.style.height='100%';
        div.style.display='flex';
        div.style.justifyContent='center';
        div.style.alignItems='center';
        div.style.fontSize='2rem';
        div.style.backgroundColor = s.background_color;
        display.appendChild(div);
        startTimer(duration);
    }

    // incrémentation front du compteur (optionnel AJAX côté serveur)
    viewsCountEl.textContent = parseInt(viewsCountEl.textContent) + 1;
}

function startTimer(duration){
    clearTimeout(storyTimer);
    const seg = segments[currentIndex];
    seg.style.transition = `width ${duration}ms linear`;
    seg.style.width='100%';
    storyTimer = setTimeout(nextStory, duration);
}

function pauseStory(){
    const seg = segments[currentIndex];
    if(!isPaused){
        clearTimeout(storyTimer);
        const computedWidth = window.getComputedStyle(seg).width;
        seg.style.transition = '';
        seg.style.width = computedWidth;
        pauseIcon.classList.replace('bi-pause-fill','bi-play-fill');
        isPaused = true;
    } else {
        const remaining = seg.parentElement.offsetWidth - parseFloat(seg.style.width);
        seg.style.transition = `width ${remaining}ms linear`;
        seg.style.width='100%';
        startTimer(remaining);
        pauseIcon.classList.replace('bi-play-fill','bi-pause-fill');
        isPaused = false;
    }
}

function nextStory(){
    currentIndex++;
    if(currentIndex >= stories.length) window.location.href="{{ route('stories.index') }}";
    else showStory(currentIndex);
}

function prevStory(){
    if(currentIndex>0) currentIndex--;
    showStory(currentIndex);
}

document.getElementById('pauseStory').addEventListener('click', pauseStory);
document.getElementById('nextStory').addEventListener('click', nextStory);
document.getElementById('prevStory').addEventListener('click', prevStory);

// Touch gestures pour mobile
let touchStartX = 0;
display.addEventListener('touchstart', e=>{ touchStartX = e.changedTouches[0].clientX; });
display.addEventListener('touchend', e=>{
    const diff = e.changedTouches[0].clientX - touchStartX;
    if(diff>50) prevStory();
    else if(diff<-50) nextStory();
});

showStory(currentIndex);
</script>

<style>
.story-show-container img, .story-show-container video { object-fit:cover; width:100%; height:100%; }
.story-nav-btn { opacity:0.7; transition:0.2s; top:50%; transform:translateY(-50%); font-size:1.5rem; z-index:25; }
.story-nav-btn:hover { opacity:1; }
.progress-segment { transition: width linear; background-color: #fff; height:3px; border-radius:2px; }
.story-views { font-weight:500; backdrop-filter:blur(3px); user-select:none; }
.story-views i { font-size:1rem; margin-right:0.2rem; }
.story-views span { font-size:0.9rem; }
</style>
@endsection
