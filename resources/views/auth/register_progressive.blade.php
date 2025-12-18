<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion / Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #52daf1ff, #060230ff);
      overflow: hidden;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #2d3e50;
      font-family: 'Poppins', sans-serif;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(91,192,190,0.15) 20%, transparent 60%) repeat;
      animation: rotate 20s linear infinite;
      z-index: 0;
    }

    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .auth-container {
      width: 550px;
      height: 450px;
      background: rgba(255,255,255,0.95);
      border-radius: 25px;
      box-shadow: 0 0 40px rgba(19, 2, 80, 0.93);
      overflow: hidden;
      perspective: 1000px;
      position: relative;
      z-index: 1;
    }

    .welcome-banner {
      position: absolute;
      top: 0;
      width: 100%;
      background: linear-gradient(90deg, rgba(64, 194, 218, 0.87), rgb(10, 2, 48));
      text-align: center;
      padding: 12px;
      font-size: 1.1rem;
      font-weight: 500;
      color: #fff;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 10px rgba(96, 207, 235, 1);
      z-index: 2;
    }

    .flip-card { width: 100%; height: 100%; transition: transform 0.8s ease-in-out; transform-style: preserve-3d; position: relative; }
    .flipped { transform: rotateY(180deg); }
    .flip-face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 5rem 2rem 3rem; }
    .flip-back { transform: rotateY(180deg); }
    h2 { margin-bottom: 25px; font-weight: 600; color: #150552ff; }
    input, textarea { background: #f8f9fa; border: 1px solid #d1e7e7; color: #090d11ff; text-align: center; border-radius: 10px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); }
    input:focus, textarea:focus { outline: none; border-color: #62e9e7ff; box-shadow: 0 0 8px rgba(91,192,190,0.3); }
    .form-step { display: none; opacity: 0; transform: translateY(20px); transition: all 0.6s ease; width: 80%; max-width: 400px; }
    .form-step.active { display: block; opacity: 1; transform: translateY(0); }
    .btn-modern { background: linear-gradient(90deg, #57e4e1ff, #14084bff); border: none; color: white; border-radius: 30px; padding: 10px 30px; font-weight: 500; transition: 0.3s; box-shadow: 0 0 10px rgba(91,192,190,0.3); }
    .btn-modern:hover { background: linear-gradient(90deg, #090546ff, #2ef8f5ff); transform: scale(1.04); box-shadow: 0 0 18px rgba(63, 179, 233, 0.95); }
    .progress-bar-range { width: 100%; max-width: 400px; margin-top: 10px; accent-color: #1c0c49; height: 6px; border-radius: 10px; }
    .toggle-slider { position: absolute; bottom: 0; display: flex; width: 100%; height: 55px; border-top: 1px solid #e0f0f0; }
    .toggle-option { flex: 1; background: #f7fafa; color: #051120ff; display: flex; justify-content: center; align-items: center; cursor: pointer; font-weight: 500; transition: all 0.3s ease; }
    .toggle-option.active { background: linear-gradient(90deg, #5bc0be, #3a506b); color: #fff; box-shadow: 0 0 10px rgba(91,192,190,0.3); }
    .photo-upload { background: rgba(91,192,190,0.1); border: 2px dashed #5bc0be; padding: 20px; border-radius: 15px; cursor: pointer; transition: 0.3s; }
    .photo-upload:hover { background: rgba(91,192,190,0.2); }
    #field-warning { color: red; margin-top: 5px; font-size: 0.9rem; }
    .buttons-row { display: flex; justify-content: space-between; gap: 10px; margin-top: 20px; width: 100%; }

    /* --- Loader "SC" --- */
    #loaderSC {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      flex-direction: column;
      color: white;
      font-size: 4rem;
      font-weight: bold;
      letter-spacing: 4px;
      opacity: 0;
      visibility: hidden;
      transition: all 0.5s ease;
    }
    #loaderSC.show {
      opacity: 1;
      visibility: visible;
    }
    .sc-letters span {
      display: inline-block;
      animation: bounceSC 1.2s infinite alternate ease-in-out;
    }
    .sc-letters span:nth-child(1) {
      color: #40c2da;
      text-shadow: 0 0 15px #40c2da;
      animation-delay: 0s;
    }
    .sc-letters span:nth-child(2) {
      color: #2ef8f5;
      text-shadow: 0 0 15px #2ef8f5;
      animation-delay: 0.3s;
    }
    @keyframes bounceSC {
      from { transform: translateY(0); opacity: 0.8; }
      to { transform: translateY(-20px); opacity: 1; }
    }
    .loading-text {
      font-size: 1.2rem;
      margin-top: 10px;
      color: #cce7f1;
      letter-spacing: 1px;
      animation: fadePulse 2s infinite;
    }
    @keyframes fadePulse {
      0%,100% { opacity: 0.4; }
      50% { opacity: 1; }
    }
  </style>
</head>

<body>

<div class="auth-container">
  <div class="welcome-banner">Bienvenue sur StomaChat — l'app web 100% Made in CI pour les jeunes curieux !</div>

  <div class="flip-card" id="flipCard">

    {{-- Connexion --}}
    <div class="flip-face flip-front text-center">
      <h2>Connexion</h2>
      @if(session('success'))
        <div class="alert alert-success w-75 mx-auto mt-3 text-center">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger w-75 mx-auto mt-3 text-center">
          @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
        </div>
      @endif

      <form action="{{ route('login.pseudo') }}" method="POST" class="w-75 mx-auto mt-4">
        @csrf
        <input type="text" name="pseudo" class="form-control text-center mb-3" placeholder="Votre pseudo" required>
        <input type="password" name="password" class="form-control text-center mb-3" placeholder="Votre mot de passe" required>
        <button type="submit" class="btn-modern w-100">Se connecter</button>
      </form>
    </div>

    {{-- Inscription --}}
    <div class="flip-face flip-back text-center">
      <h2>Créer un compte</h2>
      <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="registerForm" class="w-75 mx-auto">
        @csrf
        <input type="range" min="1" max="8" value="1" class="progress-bar-range" id="progressRange" disabled>
        <div class="form-step active"><input type="text" name="pseudo" class="form-control text-center" placeholder="Pseudo(ex: Gabi225 si vous vous appelez Gabriel)" required></div>
        <div class="form-step"><input type="email" name="email" class="form-control text-center" placeholder="Votre email" required></div>
        <div class="form-step"><input type="number" name="age" class="form-control text-center" placeholder="Votre âge (min 16 ans)" required></div>
        <div class="form-step"><textarea name="description" class="form-control text-center" placeholder="Dis nous un peu sur vous..." rows="2"></textarea></div>
        <div class="form-step"><input type="text" name="centre_interet" class="form-control text-center" placeholder="Vos centres d'intérêt(science, tech, Maths? IA)" required></div>
        <div class="form-step"><input type="text" name="profession" class="form-control text-center" placeholder="Vous êtes  étudiant, élève, entrepreneur ?"required></div>

        <!-- Étape Photo avec preview et taille -->
        <div class="form-step" id="photoStep">
          <label class="photo-upload w-100" id="photoUploadLabel">
            <i class="bi bi-camera fs-1 text-info d-block mb-2"></i>
            <span class="text-secondary" id="photoLabelText">Choisir votre photo de profil</span>
            <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*">
          </label>

          <div id="photoPreviewContainer" class="mt-3" style="display:none;">
            <img id="photoPreview" src="#" alt="Aperçu de votre photo" class="img-thumbnail rounded-circle shadow-sm" style="width:120px;height:120px;object-fit:cover;">
            <p id="photoSizeInfo" class="text-muted mt-2 small"></p>
          </div>
        </div>

        <div class="form-step">
          <input type="password" name="password" class="form-control text-center mb-2" placeholder="Mot de passe (min. 6 caractères)" required>
          <input type="password" name="password_confirmation" class="form-control text-center" placeholder="Confirmez le mot de passe" required>
        </div>

        <div id="field-warning"></div>

        <div class="buttons-row">
          <button type="button" class="btn-modern flex-fill" id="prevStepBtn" style="display:none;">Précédent</button>
          <button type="button" class="btn-modern flex-fill" id="nextStepBtn">Suivant</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Toggle bas --}}
  <div class="toggle-slider">
    <div class="toggle-option active" id="toLogin">Se connecter</div>
    <div class="toggle-option" id="toRegister">Créer un compte</div>
  </div>
</div>

<div class="stomachat-bounce position-absolute w-100 text-center" style="top:10%; z-index:0;">
  <h1 class="fw-bold text-white display-4 bouncing-text">StomaChat</h1>
</div>

{{-- Loader SC --}}
<div id="loaderSC">
  <div class="sc-letters"><span>S</span><span>C</span></div>
  <div class="loading-text">Chargement...</div>
</div>

<script>
const title=document.querySelector('.bouncing-text');
let scale=1,up=true;
setInterval(()=>{scale=up?scale+0.02:scale-0.02;title.style.transform=`scale(${scale}) rotate(${Math.sin(Date.now()/200)*3}deg)`;if(scale>=1.2)up=false;if(scale<=1)up=true;},50);

const flipCard=document.getElementById('flipCard');
const toRegister=document.getElementById('toRegister');
const toLogin=document.getElementById('toLogin');
const nextStepBtn=document.getElementById('nextStepBtn');
const prevStepBtn=document.getElementById('prevStepBtn');
const steps=document.querySelectorAll('.form-step');
const progressRange=document.getElementById('progressRange');
const fieldWarning=document.getElementById('field-warning');
const loader=document.getElementById('loaderSC');
let currentStep=0;

function updateProgress(){progressRange.value=currentStep+1;}

toRegister.onclick=()=>{flipCard.classList.add('flipped');toRegister.classList.add('active');toLogin.classList.remove('active');}
toLogin.onclick=()=>{flipCard.classList.remove('flipped');toLogin.classList.add('active');toRegister.classList.remove('active');}

nextStepBtn.onclick=async ()=>{
  let currentInput=steps[currentStep].querySelector('input, textarea');

  if(currentInput && currentInput.name==='pseudo'){
    let pseudo=currentInput.value.trim();
    if(pseudo.length<3){fieldWarning.textContent="Le pseudo doit contenir au moins 3 caractères";return;}
    try{
      let token=document.querySelector('input[name=_token]').value;
      let res=await fetch("{{ route('check.pseudo') }}",{
        method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
        body:JSON.stringify({pseudo})
      });
      let data=await res.json();
      if(data.exists){fieldWarning.textContent=data.message;return;} else fieldWarning.textContent="";
    }catch(e){console.error(e);}
  }

  if(currentInput && currentInput.name==='age'){
    let age=parseInt(currentInput.value);
    if(isNaN(age)||age<16){fieldWarning.textContent="Vous devez avoir au moins 16 ans";return;} else fieldWarning.textContent="";
  }

  if(currentInput && currentInput.name==='password'){
    let pass=currentInput.value.trim();
    let confirm=steps[currentStep].querySelector('input[name=password_confirmation]').value.trim();
    if(pass.length<6){fieldWarning.textContent="Le mot de passe doit contenir au moins 6 caractères";return;}
    if(pass!==confirm){fieldWarning.textContent="Les mots de passe ne correspondent pas";return;}
  }

  if(currentStep===steps.length-1){
    loader.classList.add('show');
    setTimeout(()=>document.getElementById('registerForm').submit(),800);
    return;
  }

  steps[currentStep].classList.remove('active');
  currentStep++;
  steps[currentStep].classList.add('active');
  updateProgress();
  nextStepBtn.textContent=currentStep===steps.length-1?"S'inscrire":"Suivant";
  prevStepBtn.style.display=currentStep>0?"block":"none";
  fieldWarning.textContent="";
};

prevStepBtn.onclick=()=>{
  if(currentStep>0){
    steps[currentStep].classList.remove('active');
    currentStep--;
    steps[currentStep].classList.add('active');
    updateProgress();
    nextStepBtn.textContent="Suivant";
    prevStepBtn.style.display=currentStep>0?"block":"none";
    fieldWarning.textContent="";
  }
};

// === Gestion de l'upload photo ===
const photoInput=document.getElementById('photoInput');
const photoUploadLabel=document.getElementById('photoUploadLabel');
const photoPreviewContainer=document.getElementById('photoPreviewContainer');
const photoPreview=document.getElementById('photoPreview');
const photoSizeInfo=document.getElementById('photoSizeInfo');

photoUploadLabel.onclick=()=>photoInput.click();

photoInput.addEventListener('change',e=>{
  const file=e.target.files[0];
  if(!file)return;

  const fileSizeMB=(file.size/(1024*1024)).toFixed(2);
  photoSizeInfo.textContent=`Taille : ${fileSizeMB} Mo`;

  if(file.size>2*1024*1024){
    fieldWarning.textContent="⚠️ L'image dépasse 2 Mo. Choisissez une photo plus légère.";
    photoPreviewContainer.style.display="none";
    photoInput.value="";
    return;
  }

  const reader=new FileReader();
  reader.onload=function(e){
    photoPreview.src=e.target.result;
    photoPreviewContainer.style.display="block";
    fieldWarning.textContent="";
  };
  reader.readAsDataURL(file);
});

updateProgress();
</script>

</body>
</html
